<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Viewheader.include.php'; 
	
	//build offers
	$ovaultSavelist['righttable'] = '';
	if($ovaultSavelist['current'] == 'new') { //if no lists exist yet
		
		/*
		
		CONTINUE HERE
		
		*/
	
	} else { //if we have lists, fetch the offers for the current one
		
	
	}//endif isset current list

	//build lists
	$ovaultSavelist['lefttable'] = '';	
	if(isset($ovaultSavelist['lists']) && is_array($ovaultSavelist['lists']) && !empty($ovaultSavelist['lists'])) {
		$listcount = 0;
		$offerz = 0;
					
		foreach($ovaultSavelist['lists'] as $list) {
						
			$listcount++;
						
			//format date
			if(date('Y') == date('Y', strtotime($list->created)))
				$listdate = 'M j';
			else	$listdate = 'M j, Y';
			
			//re-used values
			$truncname = $this->PageHelper->TruncTxt($list->name,27);
			$nicedate = date($listdate, strtotime($list->created));
			
			//build output
			$ovaultSavelist['lefttable'] .= '<tr class="oleftrow j_list-'.$list->id;
			$ovaultSavelist['lefttable'] .= $ovaultSavelist['current'] == $list->id ? ' active' : '';
			$ovaultSavelist['lefttable'] .= '" data-listid="'.$list->id.'" data-listname="'.$list->name.'" data-listcount="'.$listcount.'">
				<td class="hhl">&nbsp;</td>
				<td class="td_oleft">
					<h3><span class="no">'.$listcount.'</span> '.$truncname.'</h3>
					<span class="created">Created: '.$nicedate.'</span>
					<div class="offercount">'.$list->num_offers.'</div>
					<div class="connector hide"></div>
				</td>
				<td class="hhr">&nbsp;</td>';
		
			//count offers in all lists
			$offerz = $list->num_offers ? $offerz + $list->num_offers : $offerz;
			
		} //endforeach lists
					
		$ovaultSavelist['stats'] = array('lists'=>$listcount, 'offers'=>$offerz);
		
					
	} else { //if no lists
		
		$ovaultSavelist['lefttable'] = '<tr class="oleftrow disabled">
				<td class="hhl">&nbsp;</td>
				<td class="td_oleft">
					<p class="center">You haven\'t created any Offer Lists yet. Why not <a class="j_expand" href="#" data-target="savelists_oleft_createnewlistform">create one now?</a></p>
				</td>
				<td class="hhr">&nbsp;</td>';
				
		$ovaultSavelist['stats'] = array('lists'=>'no', 'offers'=>0);
		
	}//endif isset lists

?>
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
								<input type="text" class="formtxt" id="ovault_newlistname" name="newlistname" value="Enter a name for your new list..." />
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
	<?php if($ovaultSavelist['righttable'] == '') : ?>
		
		<div class="tabs">
			<ul>
				<li><a class="active" href="#">Start<span></span></a></li>
			</ul>
		</div><!--close tabs-->
		<div class="content" id="ovault_nolistsyet">
			<div class="conttop">
				<div class="top top1">&nbsp;</div>
				<div class="top top2">
					<label class="hide">Give your first list a name!</label>
					<input type="text" class="formtxt" id="ovault_newlistname_intab" name="newlistname" value="Give your first list a name!" />
					<a class="btn ovault_savenewlist" href="#">Create</a>
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
		
	<?php endif; //endif no righttable
	?>
	</div><!--close #oright-->
	<div class="clear"></div>	
</div><!--close .pagecontent#ovault-->