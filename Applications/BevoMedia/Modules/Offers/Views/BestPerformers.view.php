<?php
	/*recommended offer*/
	$offer_id = '39324';
	
	$sql = "SELECT 	offers.*,
			categories.title AS categoryTitle,
			networks.title AS networkName,
			networks.detail AS networkDescription
			
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
	$sql = "SELECT
			rating
		FROM
			bevomedia_user_aff_network_rating
		WHERE
			(bevomedia_user_aff_network_rating.network__id = {$offer->network__id}) AND
			(bevomedia_user_aff_network_rating.user__id = {$_SESSION['User']['ID']})			
		";
	$userRating = mysql_query($sql);
	
	if (mysql_num_rows($userRating)>0) {
		$userRating = mysql_fetch_assoc($userRating);
		$offer->userRating = $userRating['rating'];
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
	
	//formatting
	$offer->dateAdded_nice = date('F j, Y', strtotime($offer->dateAdded));
	$offer->payout = !stristr($offer->payout, '$')
			? '$'.number_format(intval($offer->payout), 2)
			: number_format(intval($offer->payout), 2);
			
	/*latest offers*/
	$sql = "SELECT 	offers.*,
			categories.title AS categoryTitle,
			networks.title AS networkName,
			networks.detail AS networkDescription
			
		FROM	bevomedia_offers as offers
			LEFT JOIN bevomedia_aff_network AS networks
				ON networks.id = offers.network__id
			LEFT JOIN bevomedia_category AS categories
				ON categories.id = offers.category__id
				
		WHERE	offers.archived = 0
		
		ORDER BY offers.dateAdded DESC
		
		LIMIT 30	
	";
	
	$rawLatest = mysql_query($sql);
	$offers = array();
	while($latest = mysql_fetch_object($rawLatest)) {
	
		//aff network user
		$sql = "SELECT 
				id
			FROM 
				bevomedia_user_aff_network 
			WHERE 
				(bevomedia_user_aff_network.network__id = {$latest->network__id}) AND
				(bevomedia_user_aff_network.user__id = {$_SESSION['User']['ID']})	
		";
		$isMemberOfNetwork = mysql_query($sql);
		$latest->isNetworkMember = (mysql_num_rows($isMemberOfNetwork))?1:0;
		
		//savelist
		$sql = "SELECT offers.offer__id
			FROM bevomedia_user_offerlists_offers AS offers
				LEFT JOIN bevomedia_user_offerlists AS lists
					ON offers.list__id = lists.id
			WHERE lists.user__id = {$_SESSION['User']['ID']}
				AND offers.offer__id = $latest->id
		";
		$savelistLatest = mysql_query($sql);
		$latest->saved2list = mysql_num_rows($savelistLatest) ? 1 : 0;
		
		//formatting
		$latest->dateAdded_nice = date('F j, Y', strtotime($latest->dateAdded));
		$latest->payout = !stristr($latest->payout, '$')
				? '$'.number_format(intval($latest->payout), 2)
				: number_format(intval($latest->payout), 2);
				
		$offers[] = $latest;
	
	}//endwhile $latest
	

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
				<p><?php echo $offer->title; ?><span><?php echo $offer->dateAdded_nice; ?></span></p>
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
						<a class="ovault_othumb" href="<?php echo $offer->previewUrl; ?>" title="Preview offer in a new tab" target="_blank">
							<img src="/Themes/BevoMedia/img_new/othumb_default.gif" alt="" />
							<span></span>
						</a>
						<?php if(property_exists($offer, 'importUrl') && $offer->importUrl)
							echo '<a class="btn ovault_importoffer" href="'.$offer->importUrl.'">Import this offer into my network</a>';
						?>
						<div class="clear"></div>
					</div><!--close floatleft-->
					<div class="floatright">
						<h3><?php echo $offer->title; ?></h3>
						<small>Added <?php echo $offer->dateAdded_nice?></small>
						
						<div class="otitle otitle_offerdesc"></div>
						<p><?php echo $offer->detail; ?></p>
						
						<div class="olink">
							<input type="text" class="formtxt" readonly value="<?php echo $offer->previewUrl; ?>"/>
						</div>
					</div><!--close floatleft-->
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
						<a class="btn ovault_gotomystats_trans" href="#">Go to my stats</a>
						
					<?php } else { ?>
						
						<p>You're not a member of this network yet! Become one now:</p>
						<a class="btn nw_applyadd" href="/BevoMedia/Publisher/ApplyAdd.html?network=<?php echo $offer->network__id; ?>" rel="shadowbox;width=640;height=480;player=iframe">Apply to join this network</a>
					
					<?php } ?>
				</div>
			</td><!--close td_nw-->
			<td class="td_nwdesc" colspan="3">
				<div class="td_inner">
					<div class="otitle otitle_networkdesc"></div>
					<p><?php echo $offer->networkDescription; ?></p>
					
					<?php if(is_array($offer->ratings) && !empty($offer->ratings)) { ?>
						
						<div class="otitle otitle_latestnwreviews noborder"></div>
						<ul class="ovault_boxlist hastitme">
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
						<p><?php echo $o->title; ?><span><?php echo $o->dateAdded_nice; ?></span></p>
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