<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Viewheader.include.php'; 
	
	//build markup
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
			$ovaultSavelist['lefttable'] .= '<tr class="j_list-'.$list->id.'" data-listid="'.$list->id.'" data-listname="'.$list->name.'">
				<td class="hhl">&nbsp;</td>
				<td class="oleft">
					<span class="no">'.$listcount.'</span>
					<h3>'.$truncname.'</h3>
					<span class="created">Created: '.$nicedate.'</span>
					<div class="offercount">'.$list->num_offers.'</div>
				</td>
				<td class="hhr">&nbsp;</td>';
		
			//count offers in all lists
			$offerz = $list->num_offers ? $offerz + $list->num_offers : $offerz;
			
		} //endforeach lists
					
		$ovaultSavelist['stats'] = array('lists'=>$listcount, 'offers'=>$offerz);
		
					
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

<div class="pagecontent" id="ovault">
	<div id="oleft">
		<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable">
			<thead>
				<tr class="table_header">
					<td class="hhl">&nbsp;</td>
					<td class="oleft">My Offer Lists</td>
					<td class="hhr">&nbsp;</td>
				</tr>
				<tr>
					<td class="hhl">&nbsp;</td>
					<td class="oleft">
						<a class="btn ovault_yell_createnewlist j_expand" href="#" data-target="savelists_oleft_createnewlistform">Create New List</a>
						
						<form method="post" action="" id="savelists_oleft_createnewlistform" class="hide">
							<div class="row">
								<label class="hide">Enter a name for your new list...</label>
								<input type="text" class="formtxt" id="ovault_newlistname" name="newlistname" value="Enter a name for your new list..." />
								<input type="submit" class="btn formsubmit ovault_savenewlist" value="Save" />
							</div>
							<a class="btn ovault_olay_close j_close" href="#" data-target="ovault_createnewlistform">Close</a>
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
		<div class="footstats">
			<div class="hilite">
				<p>You have</p>
				<h3 class="j_savelists_listnum"><?php echo $ovaultSavelist['stats']['lists'] ?></h3>
				<p>List<?php if($ovaultSavelist['stats']['lists'] != 1) echo 's'; ?></p>
			</div>
			<div class="hilite j_hideKidsOnListDelete">
				<p>and a total of</p>
				<h3><?php echo $ovaultSavelist['stats']['offers']; ?></h3>
				<p>Offer<?php if($ovaultSavelist['stats']['offers'] != 1) echo 's'; ?></p>
			</div>
		</div>
		<a class="btn ovault_smallgray_deleteall" href="#">Delete All Lists</a>
		
	</div><!--close #oleft-->
	<div id="oright">
	
	</div><!--close #oright-->
	<div class="clear"></div>	
</div><!--close .pagecontent#ovault-->