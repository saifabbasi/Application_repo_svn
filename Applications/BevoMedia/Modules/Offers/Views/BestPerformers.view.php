<?php
	include 'Applications/BevoMedia/Modules/Offers/Views/Ovault_ConstructAjaxOutput_class.view.php';
	$construct = new ConstructAjaxOutput();

	/*recommended offer*/
	$offer_id = '20726';
	
	$sql = "SELECT 	offers.*,
			categories.title AS categoryTitle,
			networks.title AS networkName,
			networks.detail AS networkDescription,
			networks.offerUrl as network_affUrl
			
		FROM	bevomedia_offers as offers
			LEFT JOIN bevomedia_aff_network AS networks
				ON networks.id = offers.network__id
			LEFT JOIN bevomedia_category AS categories
				ON categories.id = offers.category__id
		
		WHERE	offers.id = $offer_id
		
		LIMIT 1	
	";
	
	$raw = mysql_query($sql);
	$offer = mysql_fetch_object($raw);
	
	//aff network user
	$sql = "SELECT 
			id
		FROM 
			bevomedia_user_aff_network 
		WHERE 
			(bevomedia_user_aff_network.network__id = {$offer->network__id}) AND
			(bevomedia_user_aff_network.user__id = {$_SESSION['User']['ID']})	
	";
	$isMemberOfNetwork = mysql_query($sql);
	$offer->isNetworkMember = (mysql_num_rows($isMemberOfNetwork))?1:0;
	
	//rating
	$sql = "SELECT	rating
		FROM	bevomedia_user_aff_network_rating
		WHERE	bevomedia_user_aff_network_rating.network__id = {$offer->network__id}			
	";
	$userRating = mysql_query($sql);
	
	if(mysql_num_rows($userRating) > 0) {
		$userRating = mysql_fetch_object($userRating);
		$offer->userRating = $userRating->rating;
		
	} else {
		$offer->userRating = 0;
	}
	
	//user reviews
	$sql = "SELECT
				*
			FROM
				bevomedia_user_aff_network_rating
			WHERE
				(bevomedia_user_aff_network_rating.network__id = {$offer->network__id}) AND
				(bevomedia_user_aff_network_rating.approved = 1) AND
				(bevomedia_user_aff_network_rating.userComment != '')
			ORDER BY 
				bevomedia_user_aff_network_rating.id DESC
			LIMIT 3
			";
	$ratings = mysql_query($sql);
	
	$offer->ratings = array();
	if(mysql_num_rows($ratings)>0) {
		while ($rating = mysql_fetch_object($ratings)) {
			$offer->ratings[] = $rating;
		}
	}
	
	//savelist
	$sql = "SELECT offers.offer__id
		FROM bevomedia_user_offerlists_offers AS offers
			LEFT JOIN bevomedia_user_offerlists AS lists
				ON offers.list__id = lists.id
		WHERE lists.user__id = {$_SESSION['User']['ID']}
			AND offers.offer__id = $offer->id
	";
	$savelist = mysql_query($sql);
	$offer->saved2list = mysql_num_rows($savelist) ? 1 : 0;
	
	//affurl
	$affId = false;
	$offer->affUrl = false;
	$offer->affUrlNotice = false; //fill with notice in case we show a placeholder in their aff URL
	
	if($offer->isNetworkMember == 1 && ($offer->affiliateUrl || $offer->network_affUrl)) {
		
		$sql = "SELECT 	userIdLabel,
				otherIdLabel
			FROM	bevomedia_aff_network 
			WHERE	id = $offer->network__id
			LIMIT 1
		";
		$labelresult = mysql_query($sql);
		while($field = mysql_fetch_object($labelresult)) {
			
			$affField = false;
			
			if($field->userIdLabel == 'Affiliate ID' || $field->userIdLabel == 'Account ID')
				$affField = 'loginId';
			elseif($field->otherIdLabel == 'Affiliate ID' || $field->otherIdLabel == 'Account ID')
				$affField = 'otherId';
			
			if($affField) {						
				//get affiliate ID
				$sql = "SELECT `$affField`
					FROM	bevomedia_user_aff_network
					WHERE	user__id = {$_SESSION['User']['ID']}
					AND	network__id = {$offer->network__id}
					LIMIT 	1
				";
				$affresult = mysql_query($sql);
				if(mysql_num_rows($affresult) == 1) {
					while($affId = mysql_fetch_object($affresult)) {
						$affId = $affId->$affField;
						break;
					}
				}
			}
		}//endwhile labelresult
		
		//if we dont have an affId, use placeholder and tell them
		if(!$affId) {
			$affId = '{YOUR_AFFID}';
			$offer->affUrlNotice = 'Replace <strong>'.$affId.'</strong> with your affiliate ID for '.$offer->networkName.'!';
		}
		
		//get urlstructure: see if offer->affiliateUrl exists, else use networks.offerUrl
		if($offer->affiliateUrl && $offer->affiliateUrl != '') 
			$urlstructure = $offer->affiliateUrl;
		else	$urlstructure = $offer->network_affUrl;	
		
		//replace placeholders
		$offer->affUrl = str_replace(array('{$OfferID}','{$AffiliateID}'), array($offer->offer__id, $affId), $urlstructure);
		
	}//endif affurl
	
	$offer->dateAdded = $construct->formatDate($offer->dateAdded);
	$offer->payout = $construct->formatPayout($offer->payout);
			
	/*latest offers*/
	//get all nws except clickbank
	$sql = "SELECT	*
		FROM	bevomedia_aff_network
		WHERE	model = 'CPA'
			AND isValid = 'Y'
			AND id <> '1040'
		ORDER BY title
	";
	
	$rawNws = mysql_query($sql);
	
	$offers = array();
	while($nw = mysql_fetch_object($rawNws)) {
		
		//aff network user
		$sql = "SELECT 	id
			FROM 	bevomedia_user_aff_network 
			WHERE 	(bevomedia_user_aff_network.network__id = {$nw->id}) 
				AND (bevomedia_user_aff_network.user__id = {$_SESSION['User']['ID']})	
		";
		$isMemberOfNetwork = mysql_query($sql);
		$nw->isNetworkMember = (mysql_num_rows($isMemberOfNetwork)) ? 1 : 0;
		
		//get latest 3 offers
		$sql = "SELECT 	offers.*,
				categories.title AS categoryTitle
				
			FROM	bevomedia_offers as offers
				LEFT JOIN bevomedia_category AS categories
					ON categories.id = offers.category__id
					
			WHERE	offers.archived = 0
				AND offers.network__id = {$nw->id}
			
			ORDER BY offers.dateAdded DESC
			
			LIMIT 3	
		";
		$rawNwOffers = mysql_query($sql);
		while($nwOffer = mysql_fetch_object($rawNwOffers)) {
			
			//add network info
			$nwOffer->networkName = $nw->title;
			$nwOffer->networkDescription = $nw->detail;
			$nwOffer->isNetworkMember = $nw->isNetworkMember;
			
			//format
			$nwOffer->dateAdded = $construct->formatDate($nwOffer->dateAdded);
			$nwOffer->payout = $construct->formatPayout($nwOffer->payout);
			if($nwOffer->offerType == '')
				$nwOffer->offerType = 'Lead';
			
			//get savelist info
			$sql = "SELECT	offers.offer__id
				FROM	bevomedia_user_offerlists_offers AS offers
					LEFT JOIN bevomedia_user_offerlists AS lists
						ON offers.list__id = lists.id
				WHERE	lists.user__id = {$_SESSION['User']['ID']}
					AND offers.offer__id = $nwOffer->id
			";
			$rawSavelist = mysql_query($sql);
			$nwOffer->saved2list = mysql_num_rows($rawSavelist) ? 1 : 0;
			
			$offers[$nwOffer->title] = $nwOffer; //add to $offers with the name as the index so that we can sort it later
			
		} //endwhile offers
	}//endwhile networks
	
	//sort by offer title
	ksort($offers);	

include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Viewheader.include.php'; ?>
<script src="/Themes/BevoMedia/ovault.index.js" type="text/javascript"></script>
 
<div id="pagemenu">
	<ul>
		<li><a class="active" href="/BevoMedia/Offers/BestPerformers.html">Best Performing Offers<span></span></a></li>
		<li><a href="/BevoMedia/Offers/Index.html">Search<span></span></a></li>
		<li><a href="/BevoMedia/Offers/MySavedLists.html">My Saved Lists<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false, false, false, 'ovault'); //disable toggle, custom css class
?>

<?php 	$hideOdialExtras = true; //show only the search
	include 'Applications/BevoMedia/Modules/Offers/Views/Ovault_Odial_include.view.php'; ?>

<div class="pagecontent" id="ovault">
	<div class="icon icon_ovault_ootm_tabletop"></div>
	<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable" id="ootm">
		<tbody>	
		<tr class="orow expanded j_oid-<?php echo $offer->id; ?>" title="Click to expand/collapse this offer">
			<td class="border">&nbsp;</td>
			<td class="td_saved2list">
				<div class="icon icon_ovault_added2list<?php if(!$offer->saved2list) echo ' hide'; ?>" title="You have already saved this offer"></div>
			</td>
			<td class="td_savelist">
				<a class="btn ovault_add2list j_orowSelect" href="#" data-oid="<?php echo $offer->id; ?>" title="Add this offer to the active list">Add</a>
				<a class="btn ovault_add2list_select j_orowSelect" href="#" data-oid="<?php echo $offer->id; ?>" title="Select a list to add this offer to...">Select</a>
				<div class="olay_container"></div>
			</td>
			<td class="td_offername">
				<p><?php echo $offer->title.$offer->dateAdded; ?></p>
			</td>
			<td class="td_payout">
				<p><?php echo $offer->payout; ?></p>
			</td>
			<td class="td_type">
				<p><?php echo $offer->offerType; ?></p>
			</td>
			<td class="td_vertical">
				<p><?php echo $offer->categoryTitle; ?></p>
			</td>
			<td class="td_network" colspan="2">
				<p class="icon<?php if($offer->isNetworkMember) echo ' icon_nwmember'; ?>"><?php echo $offer->networkName; ?></p>
			</td>
		</tr>
	
		<tr class="orowbig">
			<td class="border"></td>
			<td class="td_info" colspan="3">
				<div class="td_inner">
					<div class="floatleft">
						<?php
						//image url
						if(property_exists($offer, 'imageUrl') && $offer->imageUrl && $offer->imageUrl != '')
							$imageTag = '<img src="'.$offer->imageUrl.'" alt="" />';
						else	$imageTag = '<img src="/Themes/BevoMedia/img_new/othumb_default.gif" alt="" />';
						
						//preview url
						if(property_exists($offer, 'previewUrl') && $offer->previewUrl && $offer->previewUrl != '') { ?>
							<a class="ovault_othumb" href="<?php echo $offer->previewUrl; ?>" title="Preview offer in a new tab" target="_blank">
								<?php echo $imageTag; ?>
								<span class="btn ovault_visiticon_transyell"></span>
							</a>
							
						<?php } else { ?>
							<div class="ovault_othumb"><?php echo $imageTag; ?></div>
							
						<?php } ?>
						
						<div class="olinkbox">
						<?php if($offer->affUrl) { ?>
							<div class="otitle otitle_olink noborder"></div>
							<?php echo ($offer->affUrlNotice ? '' : '<a class="btn ovault_transgray_testit_link" href="'.$offer->affUrl.'" target="_blank" title="Test your Affilate URL (opens in a new tab)">Test Link</a>'); ?>
							<textarea class="formtxtarea j_hiliteall" rows="1" cols="1" readonly><?php echo $offer->affUrl; ?></textarea>
							<?php echo ($offer->affUrlNotice ? '<p class="affurlnotice">'.$offer->affUrlNotice.'</p>' : ''); ?>
							<p class="disclaimer">Note: Make sure your link works! If it does't, it means that this offer requires network approval before running.</p>
						
						<?php } elseif($offer->isNetworkMember == 1) { ?>
							<p>You can find this offer in <?php echo $offer->networkName; ?>'s interface by searching for the <strong>Offer ID</strong> (to the right).</p>
							
						<?php } else { ?>
							<p>We'd love to give you your Affiliate Link for this offer right now, but you're not a member of <?php echo $offer->networkName; ?> yet! <a class="nw_applyadd" href="/BevoMedia/Publisher/ApplyAdd.html?network=<?php echo $offer->network__id; ?>"><strong>Click Here</strong> to apply now</a>.</p>
							
						<?php } ?>
					</div>
						
						<div class="clear"></div>
					</div><!--close floatleft-->
					<div class="floatright">
						<h3><?php echo $offer->title; ?></h3>
						<small><?php echo $offer->dateAdded ?></small>
						
						<div class="otitle otitle_offerdesc"></div>
						<p><?php echo $offer->detail; ?></p>
						
						<div class="otitle otitle_onwid noborder"></div>
						<div class="clear"></div>
						<input type="text" class="formtxt onwid j_hiliteall" readonly value="<?php echo $offer->offer__id; ?>" />
						<p class="nolineheight">This offer's ID in <?php echo $offer->networkName; ?>'s interface</p>
						<div class="clear"></div>
						
						<?php /*
						//olink
						if(property_exists($offer, 'affUrl') && $offer->affUrl) { ?>
							<div class="olink">
								<input type="text" class="formtxt" readonly value="<?php echo $offer->affUrl; ?>" />
								<a class="btn ovault_visiticon" href="<?php echo $offer->affUrl; ?>" title="Click to test your affiliate link (opens in new tab)" target="_blank">Visit</a>
							</div>
						<?php } */ ?>
					</div><!--close floatright-->
					<div class="clear"></div>
				</div>
			</td><!--close td_info-->
			<td class="td_nw" colspan="2">
				<div class="td_inner">
					<div class="otitle otitle_network noborder"></div>
					<div class="onwpic">
						<img class="nwpic w120" src="/Themes/BevoMedia/img/networklogos/uni/<?php echo $offer->network__id; ?>.png" alt="<?php echo $offer->networkName; ?>" />
						<p class="bordertop aligncenter">
							Publisher's Rating: <?php echo $offer->userRating; ?><br />
							<?php for($i=1; $i<=5; $i++) {
								$ratingstate = $offer->userRating >= $i ? 'on' : 'off'; 		
								echo '<img src="/Themes/BevoMedia/img/star-'.$ratingstate.'.gif" align="absbottom" />';
							} ?>
						</p>
					</div>
					
					<?php if($offer->isNetworkMember == 1) { ?>
						
						<p>You're already a member of this network!</p>
						<div class="icon icon_ovault_nwmember_bigwhite"></div>
						<a class="btn ovault_gotomystats_trans" href="/BevoMedia/Offers/MyStats.html?network=<?php echo $offer->network__id; ?>">Go to my stats</a>
						
					<?php } else { ?>
						
						<p>You're not a member of this network yet! Become one now:</p>
						<a class="btn nw_applyadd" href="/BevoMedia/Publisher/ApplyAdd.html?network=<?php echo $offer->network__id; ?>" rel="shadowbox;width=640;height=480;player=iframe">Apply to join this network</a>
					
					<?php } ?>
				</div>
			</td><!--close td_nw-->
			<td class="td_nwdesc" colspan="3">
				<div class="td_inner">
					<?php if($offer->networkDescription) { ?>
						<div class="otitle otitle_networkdesc"></div>
						<p><?php echo $offer->networkDescription; ?></p>
					<?php } ?>
					
					<?php if(is_array($offer->ratings) && !empty($offer->ratings)) { ?>
						
						<div class="otitle otitle_latestnwreviews noborder"></div>
						<?php if(count($offer->ratings) >= 3)
							echo '<a class="btn ovault_transgray_readall_reviews" href="/BevoMedia/Publisher/Reviews.html?NetworkID='.$offer->network__id.'" title="Go to the review page for '.$offer->networkName.'">Read all reviews</a>'; ?>
						<div class="clear"></div>
						
						<ul class="ovault_boxlist hastitle">
							<?php foreach($offer->ratings as $review) {
								echo ($review->userComment != '' ? '<li>'.$review->userComment.'</li>' : '');
							} ?>
						</ul>
						
					<?php } ?>
				</div>
			</td><!--close td_nwdesc-->
			
		</tr><!--close .orowbig-->
		</tbody>
	</table><!--close btable-->

	<?php /*
	<div class="icon icon_ovault_bevoalsorecommends"></div> 
	
	later: add 3 offers in a row here
	
	*/?>

	<div class="icon icon_ovault_hotnewestoffers"></div>

	<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable" id="j_otable">
		<thead>
			<tr class="table_header">
				<td class="hhl">&nbsp;</td>
				<td class="td_saved2list" style="width:15px;">&nbsp;</td>
				<td class="td_savelist" style="width:40px;">&nbsp;</td>
				<td class="td_offername" style="width:465px;"><a class="tcol tcol_sortby <?php /*asc*/?>" href="#" data-value="offername">Offer Name <span class="nobold">(Date Added)</span></a></td>
				<td class="td_payout" style="width:54px;"><a class="tcol tcol_sortby" href="#" data-value="payout">Payout</a></td>
				<td class="td_type" style="width:41px;"><a class="tcol tcol_sortby" href="#" data-value="type">Type</a></td>
				<td class="td_vertical" style="width:123px;"><a class="tcol tcol_sortby" href="#" data-value="vertical">Vertical</a></td>
				<td class="td_network" style="width:120px;"><a class="tcol tcol_sortby" href="#" data-value="network">Network</a></td>
				<td class="hhr">&nbsp;</td>
			</tr>
		</thead>
		
		<tbody>	
			<?php foreach($offers as $o) { ?>
				<tr class="orow j_oid-<?php echo $o->id; ?>" data-oid="<?php echo $o->id; ?>" title="Click to expand or collapse this offer">
					<td class="border">&nbsp;</td>
					<td class="td_saved2list">
						<div class="icon icon_ovault_added2list<?php if(!$o->saved2list) echo ' hide'; ?>" title="You have already saved this offer"></div>
					</td>
					<td class="td_savelist">
						<a class="btn ovault_add2list j_orowSelect" href="#" data-oid="<?php echo $o->id; ?>" title="Add this offer to the active list">Add</a>
						<a class="btn ovault_add2list_select j_orowSelect" href="#" data-oid="<?php echo $o->id; ?>" title="Select a list to add this offer to...">Select</a>
						<div class="olay_container"></div>
					</td>
					<td class="td_offername">
						<p><?php echo $o->title.$o->dateAdded; ?></p>
					</td>
					<td class="td_payout">
						<p><?php echo $o->payout; ?></p>
					</td>
					<td class="td_type">
						<p><?php echo $o->offerType; ?></p>
					</td>
					<td class="td_vertical">
						<p><?php echo $o->categoryTitle; ?></p>
					</td>
					<td class="td_network" colspan="2">
						<p class="icon<?php if($o->isNetworkMember) echo ' icon_nwmember'; ?>"><?php echo $o->networkName; ?></p>
					</td>
				</tr>
			<?php } ?>
		</tbody>
		
		<tfoot>
			<tr class="table_footer">
				<td class="hhl"></td>
				<td style="border-left: none;" colspan="7"></td>
				<td class="hhr"></td>
			</tr>
		</tfoot>
	</table>	

</div><!--close pagecontent#ovault-->