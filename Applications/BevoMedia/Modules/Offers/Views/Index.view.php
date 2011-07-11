<?php
	//quicksearch form in topdrop sends POST
	//something doesnt work about this... need to redo the topdrop search later when GET + HASH navi is implemented in search!

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

		var sort_by = $(this).data('value');

		
		if (ovault_cache.sort_by!=sort_by) {
			ovault_cache.sort_by_direction = 'desc'; //so it can be reset below
		}
		
		if (ovault_cache.sort_by_direction=='asc') 
			ovault_cache.sort_by_direction = 'desc'; else
			ovault_cache.sort_by_direction = 'asc';

		ovault_cache.sort_by = sort_by;
		
		s = ovault_cache.current_searchstring+'&sort_by='+ovault_cache.sort_by+'&sort_by_direction='+ovault_cache.sort_by_direction; //newpage overrides page
		ovault_cache.current_page = $(this).data('page');
		doSearch(s);		
		return false;
		
	});
</script>
