<?php
	//quicksearch form in topdrop sends POST
	//something doesnt work about this... need to redo the topdrop search later when GET + HASH navi is implemented in search!
	
	/*if(isset($_GET['topdrop_quicksearch_submit']) && isset($_GET['topdrop_osearch']) && !empty($_GET['topdrop_osearch'])) {
				
		$search = urlencode(str_replace(array('\'','"'), '', strip_tags(trim($_GET['topdrop_osearch']))));
		
		if($search) {
			//look for cookie to apply all the other options, of no cookie load default
			if(isset($_COOKIE['__bevoOLSearch'])) {
				$lastsearch = trim($_COOKIE['__bevoOLSearch']);
				
				$searchparams = explode('&', $lastsearch);
				if(strpos($searchparams[1], 'search=') === 0) { //overwrite with new value
					$searchparams[1] = 'search='.$search;
					//setcookie('__bevoOLSearch', implode('&', $searchparams), time()+60*60*24*30*12, '/');
					setcookie('__bevoOLSearch', '', time()+60*60*24*30*12, '/');
					
				}
				
			} else { //if no cookie, apply default values. search in all networks.
				
				$searchstring = 'get=searchresults&search='.$search.'&type=lead&include_mysaved=1&include_networks=ALL&numresults=100';
				setcookie('__bevoOLSearch', $searchstring, time()+60*60*24*30*12, '/');
			
			}//endif cookie
			
			//now refresh page for the JS to grab the new cookie
			header('Location: /BevoMedia/Offers/Index.html');
			/*
			LATER: as soon as we implement HASH navigation instead of cookie nav, remove header and just change hash.
			* /
			
		}//endif search
	}//endif post*/
	
	include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Viewheader.include.php';
?>
<script src="/Themes/BevoMedia/ovault.index.js" type="text/javascript"></script>

<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Offers/BestPerformers.html">Best Performing Offers<span></span></a></li>
		<li><a class="active" href="/BevoMedia/Offers/Index.html">Search<span></span></a></li>
		<li><a href="/BevoMedia/Offers/MySavedLists.html">My Saved Lists<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false, false, false, 'ovault'); //disable toggle, custom css class
?>

<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault_Odial_include.view.php'; ?>

<div class="pagecontent" id="ovault">
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
			<tr class="message loading">
				<td class="border">&nbsp;</td>
				<td colspan="7" style="padding:25px 0;text-align:center;">Search for offers using the search dial at the top!</td>
			</tr>			
		</tbody>		
		<tfoot>
			<tr class="table_footer">
				<td class="hhl"></td>
				<td style="border-left: none;" colspan="7"></td>
				<td class="hhr"></td>
			</tr>
		</tfoot>
	</table><!--close outer .btable-->
</div><!--close pagecontent#ovault-->

<script type="text/javascript">
	$('.tcol_sortby').click(function() {
		
	});
</script>
