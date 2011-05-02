<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Viewheader.include.php'; 
	
	//build lists
	$ovaultSavelist['lefttable'] = '';	
	if(isset($ovaultSavelist['lists']) && is_array($ovaultSavelist['lists']) && !empty($ovaultSavelist['lists'])) {
		$listcount = 0;
		$offerz = 0;
					
		foreach($ovaultSavelist['lists'] as $list) {
						
			$listcount++;
			
			$ovaultSavelist['lists'][$list->id]->listcount = $listcount; //add to arr
						
			//format date
			if(date('Y') == date('Y', strtotime($list->created)))
				$listdate = 'M j';
			else	$listdate = 'M j, Y';
			
			//re-used values
			$truncname = $this->PageHelper->TruncTxt($list->name,27);
			$nicedate = date($listdate, strtotime($list->created));
			
			$ovaultSavelist['lists'][$list->id]->nicedate = $nicedate; //add to arr
			
			//build output
			$ovaultSavelist['lefttable'] .= '<tr class="oleftrow j_list-'.$list->id;
			$ovaultSavelist['lefttable'] .= $ovaultSavelist['current'] == $list->id ? ' active' : '';			
			$ovaultSavelist['lefttable'] .= '" data-listid="'.$list->id.'" data-listname="'.$list->name.'" data-listcount="'.$listcount.'" data-num_offers="'.$list->num_offers.'" data-created="'.$nicedate.'">
				<td class="hhl">&nbsp;</td>
				<td class="td_oleft">
					<h3><span class="no">'.$listcount.'</span> '.$truncname.'</h3>
					<span class="created">Created: '.$nicedate.'</span>
					<div class="offercount">'.$list->num_offers.'</div>
					<div class="connector hide"></div>
				</td>
				<td class="hhr">&nbsp;</td></tr>';
		
			//count offers in all lists
			$offerz = $list->num_offers ? $offerz + $list->num_offers : $offerz;
			
		} //endforeach lists
					
		$ovaultSavelist['stats'] = array('lists'=>$listcount, 'offers'=>$offerz);
		
					
	} else { //if no lists
		
		$ovaultSavelist['lefttable'] = '<tr class="oleftrow disabled j_list-new active">
				<td class="hhl">&nbsp;</td>
				<td class="td_oleft">
					<p class="center">You haven\'t created any Offer Lists yet. Why not <a class="j_expand" href="#" data-target="savelists_oleft_createnewlistform">create one now?</a></p>
					<div class="connector hide"></div>
				</td>
				<td class="hhr">&nbsp;</td></tr>';
				
		$ovaultSavelist['stats'] = array('lists'=>'no', 'offers'=>0);
		
	}//endif isset lists
	
	//build current list offers
	$ovaultSavelist['righttable'] = '';
	if($ovaultSavelist['current'] != 'new') {
		
		$ovaultSavelist['righttable'] = new stdClass();
		
		$ovaultSavelist['righttable']->id = $ovaultSavelist['current'];
		$ovaultSavelist['righttable']->name = $ovaultSavelist['lists'][$ovaultSavelist['current']]->name;
		$ovaultSavelist['righttable']->listcount = $ovaultSavelist['lists'][$ovaultSavelist['current']]->listcount;
		$ovaultSavelist['righttable']->num_offers = $ovaultSavelist['lists'][$ovaultSavelist['current']]->num_offers;
		$ovaultSavelist['righttable']->nicedate = $ovaultSavelist['lists'][$ovaultSavelist['current']]->nicedate;
	
		
		//fetch offers
		$sql = "SELECT	offers.id,
				offers.title,
				offers.detail,
				offers.payout,
				offers.offerType AS type,
				offers.dateAdded,
				offers.network__id AS network__id,
				
				networks.title AS networkName,				
				category.title AS categoryTitle
				
			FROM 	bevomedia_offers AS offers
				
				LEFT JOIN bevomedia_aff_network AS networks
					ON networks.id = offers.network__id
					
				LEFT JOIN bevomedia_category  AS category
					ON category.id = offers.category__id
				
				LEFT JOIN bevomedia_user_offerlists_offers AS listoffers
					ON offers.id = listoffers.offer__id
				
				LEFT JOIN bevomedia_user_offerlists AS lists
					ON listoffers.list__id = lists.id
				
			WHERE	lists.id = {$ovaultSavelist['current']}
				AND lists.user__id = {$_SESSION['User']['ID']}
				
			GROUP BY offers.id
			ORDER BY offers.payout
		";
		
		$raw = mysql_query($sql);
		
		$offersArray = array();
		if(mysql_num_rows($raw) > 0) {
			while($offer = mysql_fetch_object($raw)) {
				
				
				$sql = "SELECT 
							id
						FROM 
							bevomedia_user_aff_network 
						WHERE 
							(bevomedia_user_aff_network.network__id = {$offer->network__id}) AND
							(bevomedia_user_aff_network.user__id = {$_SESSION['User']['ID']})
						";
				$isMemberOfNetwork = mysql_query($sql);
				$offer->isNetworkMember = (mysql_num_rows($isMemberOfNetwork)) ? ' icon_nwmember' : '';
				
				$offer->dateAdded_nice = date('M j, Y', strtotime($offer->dateAdded));				
				$offer->payout = !stristr($offer->payout, '$')
					? '$'.number_format(intval($offer->payout), 2)
					: number_format(intval($offer->payout), 2);
				
				$offersArray[] = $offer;
			}
			$ovaultSavelist['righttable']->offers = array();
			$ovaultSavelist['righttable']->offers = $offersArray;
		}
	
	}//endif isset current
	
	/*html content*/ //we need to echo all of the following in hidden wrappers, to allow the JS to use it, and one of the following will be echoed on pageload
	$ovaultSavelist['oright_defaults'] = array();
	
	$ovaultSavelist['oright_defaults']['body_nolists'] = '	
		<div class="tabs">
			<ul>
				<li><a class="active" href="#">Start<span></span></a></li>
			</ul>
		</div><!--close tabs-->
		<div class="content">
			<div class="conttop">
				<div class="top top1">&nbsp;</div>
				<div class="top top2">
					<label class="hide">Give your first list a name!</label>
					<form method="post" action="ovault_newlistname_intabform">
						<input type="text" class="formtxt ovault_newlistname" id="" name="newlistname" value="Give your first list a name!" />
						<input class="btn formsubmit ovault_savenewlist" value="Create" />
					</form>					
					<div class="clear"></div>
					
					<h2>Welcome to the Bevo Offer Lists!</h2>												
					
					<p><strong>Offer Lists</strong> make it easy for you to save offers you are considering for a campaign. Use the yellow button to the left of any offer to add that offer to your lists. You can add as many offers as you like to any of your lists.</p>				
					
					<p>Lists you create are always private. No one will ever be able to see how many lists you have or which offers you have saved.</p>
					
					<p>Create your first Offer List now by giving it a name in the field at the top.</p>
					
					<img src="/Themes/BevoMedia/img_new/ovault_savedlists_nolistyet_hint.gif" alt="" />
				</div><!--close left2-->
				<div class="top top3">
					<div class="footfeat">
						<div class="hilite second">
							<h3>0</h3>
							<p class="dark">Offers</p>
						</div>						
						<div class="clear"></div>
					</div>
				</div>
				<div class="clear"></div>
			</div><!--close conttop-->
		</div><!--close content-->
	'; //defaultcontent-nolists
	
	$ovaultSavelist['oright_defaults']['bodytop'] = '
		<div class="tabs">
			<ul>
				<li><a class="active" href="#">Offers<span></span></a></li>
			</ul>
		</div><!--close tabs-->
		<div class="content">
			<div class="conttop">
				<div class="top top1"><p></p></div>
				<div class="top top2">
					<h2></h2>
					<a class="btn ovault_transgray_rename j_expand" data-targetclass="ovault_mysaved_renamelistform" href="#">Rename</a>
					
					<form method="post" action="" class="ovault_mysaved_renamelistform hide">
						<div class="row">
							<input type="text" class="formtxt renamelistname" name="renamelistname" value="" />
							<input type="hidden" class="renamelistid" name="listid" value="" />
							<input type="submit" class="btn formsubmit ovault_savenewlist" value="Save" />
						</div>
						<a class="btn ovault_olay_close j_close" href="#" data-targetclass="ovault_mysaved_renamelistform">Close</a>
					</form>
					
					<div class="clear"></div>
					
					<div class="subsmall"></div>
					
				</div><!--close top2-->
				<div class="top top3">
					<div class="footfeat">
						<div class="hilite second">
							<h3>0</h3>
							<p class="dark">Offers</p>
						</div>						
						<div class="clear"></div>
					</div>
					<a class="btn ovault_transgray_delete" href="#">Delete this list</a>
					<div class="clear"></div>
				</div>
				<div class="top top4">
					<a class="tbtn" href="#">Export to CSV</a>
				</div>
				<div class="clear"></div>
			</div><!--close conttop-->
	';//bodytop
	
	$ovaultSavelist['oright_defaults']['tabletop_tabletag'] = '<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable">'; //no id. added later by ajax to avoid 2 same IDs on the page, which will mess stuff up
	$ovaultSavelist['oright_defaults']['tabletop'] = '
		<thead>
				<tr class="table_header">
					<td class="hhl">&nbsp;</td>
					<td class="td_offername" style="width:465px;"><a class="tcol tcol_sortby <?php /*asc*/?>" href="#" data-value="offername">Offer Name <span class="nobold">(Date Added)</span></a></td>
					<td class="td_payout" style="width:54px;"><a class="tcol tcol_sortby" href="#" data-value="payout">Payout</a></td>
					<td class="td_type" style="width:41px;"><a class="tcol tcol_sortby" href="#" data-value="type">Type</a></td>
					<td class="td_vertical" style="width:123px;"><a class="tcol tcol_sortby" href="#" data-value="vertical">Vertical</a></td>
					<td class="td_network" style="width:120px;"><a class="tcol tcol_sortby" href="#" data-value="network">Network</a></td>
					<td class="td_delete">&nbsp;</td>
					<td class="hhr">&nbsp;</td>
				</tr>
			</thead>
			
			<tbody>';//listtabletop
			
	$ovaultSavelist['oright_defaults']['tablebutt'] = '
		</tbody>				
			<tfoot>
				<tr class="table_footer">
					<td class="hhl"></td>
					<td style="border-left: none;" colspan="6"></td>
					<td class="hhr"></td>
				</tr>
			</tfoot>
		</table><!--close .btable-->
	</div><!--close content-->'; //listtablebutt
		
	$ovaultSavelist['oright_defaults']['tablecont_nooffers'] = '
		<tr class="message">
			<td class="border">&nbsp;</td>
			<td colspan="6" style="padding:25px 0;text-align:center;">You can start adding offers to this list! To find new offers, use the Bevo Search Sphere at the top. Then use the yellow button to the left of every offer in the search results to add that offer to your list.</td>
			<td class="tail">&nbsp;</td>
		</tr>
	';
	
?>
<script src="/Themes/BevoMedia/ovault.mysavedlists.js" type="text/javascript"></script>
<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Offers/BestPerformers.html">Best Performing Offers<span></span></a></li>
		<li><a href="/BevoMedia/Offers/Index.html">Search<span></span></a></li>
		<li><a class="active" href="/BevoMedia/Offers/MySavedLists.html">My Saved Lists<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false, false, false, 'ovault'); //disable toggle, custom css class
?>
<?php 	$hideOdialExtras = true;
	include 'Applications/BevoMedia/Modules/Offers/Views/Ovault_Odial_include.view.php'; ?>

<div class="pagecontent secondarypage" id="ovault">
	<div id="oleft">
		<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable">
			<thead>
				<tr class="table_header">
					<td class="hhl">&nbsp;</td>
					<td style="text-align:center;">My Offer Lists</td>
					<td class="hhr">&nbsp;</td>
				</tr>
				<tr>
					<td class="hhl">&nbsp;</td>
					<td class="td_oleft">
						<a class="btn ovault_yell_createnewlist j_expand" href="#" data-target="savelists_oleft_createnewlistform">Create New List</a>
						<form method="post" action="" id="savelists_oleft_createnewlistform" class="hide">
							<div class="row">
								<label class="hide">Enter a name for your new list...</label>
								<input type="text" class="formtxt ovault_newlistname" id="" name="newlistname" value="Enter a name for your new list..." />
								<input type="submit" class="btn formsubmit ovault_savenewlist" value="Save" />
							</div>
							<a class="btn ovault_olay_close_gray j_close" href="#" data-target="savelists_oleft_createnewlistform">Close</a>
						</form>
					</td>
					<td class="hhr">&nbsp;</td>
				</tr>
			</thead>
			
			<tbody>
				<?php echo $ovaultSavelist['lefttable']; ?>
			</tbody>
			
			<tfoot>
				<tr class="table_footer">
					<td class="hhl"></td>	
					<td style="border-left: none;"></td>
					<td class="hhr"></td>
				</tr>
			</tfoot>
		</table><!--close outer .btable-->
		<div class="footfeat">
			<div class="hilite first">
				<p>You have</p>
				<h3 class="j_savelists_listnum"><?php echo $ovaultSavelist['stats']['lists'] ?></h3>
				<p class="dark">List<?php if($ovaultSavelist['stats']['lists'] != 1) echo 's'; ?></p>
			</div>
			<div class="hilite second j_hideKidsOnListDelete">
				<p>and</p>
				<h3><?php echo $ovaultSavelist['stats']['offers']; ?></h3>
				<p class="dark">Offer<?php if($ovaultSavelist['stats']['offers'] != 1) echo 's'; ?></p>
			</div>
			<div class="clear"></div>
		</div>
		<a class="btn ovault_smallgray_deleteall" href="#">Delete All Lists</a>
		
	</div><!--close #oleft-->
	<div id="oright">
	
	<?php if($ovaultSavelist['current'] == 'new') : //if no lists exist
		echo $ovaultSavelist['oright_defaults']['body_nolists'];
	
	else : //if we have a list
	?>
		<div class="tabs">
			<ul>
				<li><a class="active" href="#">Offers<span></span></a></li>
			</ul>
		</div><!--close tabs-->
		<div class="content">
			<div class="conttop">
				<div class="top top1"><p><?php echo $ovaultSavelist['righttable']->listcount; ?>.</p></div>
				<div class="top top2">
					<h2><?php echo $ovaultSavelist['righttable']->name; ?></h2>
					<a class="btn ovault_transgray_rename j_expand" data-targetclass="ovault_mysaved_renamelistform" href="#">Rename</a>
					
					<form method="post" action="" class="ovault_mysaved_renamelistform hide">
						<div class="row">
							<input type="text" class="formtxt renamelistname" name="renamelistname" value="<?php echo $ovaultSavelist['righttable']->name; ?>" />
							<input type="hidden" class="renamelistid" name="renamelistid" value="<?php echo $ovaultSavelist['righttable']->id; ?>" />
							<input type="submit" class="btn formsubmit ovault_savenewlist" value="Save" />
						</div>
						<a class="btn ovault_olay_close j_close" href="#" data-targetclass="ovault_mysaved_renamelistform">Close</a>
					</form>
					
					<div class="clear"></div>
					
					<div class="subsmall">Created: <?php echo $ovaultSavelist['righttable']->nicedate; ?></div>
					<div class="clear"></div>
					
				</div><!--close top2-->
				<div class="top top3">
					<div class="footfeat">
						<div class="hilite second">
							<h3><?php echo $ovaultSavelist['righttable']->num_offers; ?></h3>
							<p class="dark">Offers</p>
						</div>						
						<div class="clear"></div>
					</div>
					<a class="btn ovault_transgray_delete" href="#">Delete this list</a>
					<div class="clear"></div>
				</div>
				<div class="top top4">
					<a class="tbtn" href="#">Export to CSV</a>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div><!--close conttop-->
			
			<?php 
			
			echo '<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable" id="j_otable">'; //this has no id in the wrap before ajax changes it!
			echo $ovaultSavelist['oright_defaults']['tabletop'];
			
			if(isset($ovaultSavelist['righttable']->offers) && !empty($ovaultSavelist['righttable']->offers)) {
					
					$out = '';
					foreach($ovaultSavelist['righttable']->offers as $offer) {
						$out .= '<tr class="orow j_oright j_oid-'.$offer->id.'" data-oid="'.$offer->id.'" title="Click to expand or collapse this offer">';
						
						$out .= '<td class="border">&nbsp;</td>';
						$out .= '<td class="td_offername"><p>'.$offer->title.'<span>Added '.$offer->dateAdded_nice.'</span></p></td>';
						$out .= '<td class="td_payout"><p>'.$offer->payout.'</p></td>';
						$out .= '<td class="td_type"><p>'.$offer->type.'</p></td>';
						$out .= '<td class="td_vertical"><p>'.$offer->categoryTitle.'</p></td>';
						
						$out .= '<td class="td_network"><p class="icon'.$offer->isNetworkMember.'">'.$offer->networkName.'</p></td>';
						
						$out .= '<td class="td_delete"><a class="btn ovault_olay_close_gray" href="#">Delete this offer from list</a>';
						$out .= '<td class="tail">&nbsp;</td>';
						
						$out .= '</tr>';
					
					}//endforeach offer
					
					echo $out;
				
			} else	echo $ovaultSavelist['oright_defaults']['tablecont_nooffers'];
			
		echo $ovaultSavelist['oright_defaults']['tablebutt'];
		?>				
		
	<?php endif; //endif list or no list
	?>
	</div><!--close #oright-->
	<div class="clear"></div>	
	
<div id="j_oright_defaults_body_nolists" class="hide">
	<?php echo $ovaultSavelist['oright_defaults']['body_nolists']; ?>
</div>
<div id="j_oright_defaults_body" class="hide">
	<?php echo 
		$ovaultSavelist['oright_defaults']['bodytop']
		.$ovaultSavelist['oright_defaults']['tabletop_tabletag']
		.$ovaultSavelist['oright_defaults']['tabletop']
		.$ovaultSavelist['oright_defaults']['tablebutt']; ?>
</div>	
</div><!--close .pagecontent#ovault-->