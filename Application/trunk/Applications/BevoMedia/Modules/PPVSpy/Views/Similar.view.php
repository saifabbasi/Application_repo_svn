<?php /* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="MostSeenPopups.html">Most Seen Popups<span></span></a></li>
			<li><a href='MostSeenOffers.html'>Most Seen Offers<span></span></a></li>
			<li><a href='MostSeenNiches.html'>Most Seen Niches<span></span></a></li>
			<li><a href='SearchbyDate.html'>Search by Date<span></span></a></li>
			<li><a href='SearchbyKeyword.html'>Search by Keyword<span></span></a></li>
			<li><a href='ViewAllbyOffer.html'>"View All" by Offer<span></span></a></li>
			<li><a href='ViewAllbyNiche.html'>"View All" by Niche<span></span></a></li>
			<li><a href='SuggestATarget.html'>Suggest A Target<span></span></a></li>
		</ul>
		
		
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
	?>



<?php
			
			//your api key
$key='237f3f76g2f487gfiwvbbwwf7g34';
			
			//get variables
$short_popped_url=$_GET['url'];
$offer_id=$_GET['offer_id'];
$niche_id=$_GET['niche_id'];
$search_by=$_GET['search_by'];
$search_for=$_GET['search_for'];
$target=$_GET['target'];
$currentpage=$_GET['currentpage'];
$query_url_string="search_for=".$search_for."&amp;search_by=".$search_by."&amp;url=".$short_popped_url."&amp;offer_id=".$offer_id."&amp;niche_id=".$niche_id."&amp;target=".$target;

//load the xml file
$xml = simplexml_load_file("http://distantsunmedia.com/api/similar.php?search_for=".$search_for."&amp;search_by=".$search_by."&amp;url=".$short_popped_url."&amp;offer_id=".$offer_id."&amp;niche_id=".$niche_id."&amp;target=".$target."&amp;key=".$key."&amp;currentpage=".$currentpage);

//get summary details
$total_results=$xml->summary->results;
$offset=$xml->summary->offset;
$currentpage=$xml->summary->current_page;
$totalpages=$xml->summary->total_pages;
$rowsperpage=$xml->summary->rows_per_page;
$offer=$xml->summary->offer;
$niche=$xml->summary->niche;

$title="All ".ucfirst($search_for);
if ($search_by=='popped_url') {
$title.=' Seen For '.$short_popped_url;
}
elseif ($search_by=='offer') {
$title.=' For The "'.$offer.'" Offer';
}
elseif ($search_by=='niche') {
$title.=' For The "'.$niche.'" Niche';
}
elseif ($search_by=='target') {
$title.=" That Popped On ".$target;
}







if ($search_for=='pops'){
//build results array
foreach ($xml->pops->pop_up as $pop){
$popped_urls[]=$pop->popped_url;
$target_urls[]=$pop->target_url;
$screengrab_locations[]=$pop->screengrab_location;
$dateDisplay = substr($pop->date_seen, 0, 4).'-'.substr($pop->date_seen, 4, 2).'-'.substr($pop->date_seen, 6, 2);
$dates_seen[]=$dateDisplay;
$offers[]=$pop->offer;
$niches[]=$pop->niche;
$offer_ids[]=$pop->offer_id;
$niche_ids[]=$pop->niche_id;
$short_popped_urls[]=$pop->short_popped_url;
$tn_screengrab_locations[]=$pop->tn_screengrab_location;
}

//build html to display ads
$num_ads=COUNT($dates_seen);
$a=0;

while ($a<$num_ads){
$b=$a+1;
$c=$b+1;
$results_table=$results_table.'<p><table cellpadding="2" style="text-align: center;">

<tr>
<td WIDTH="350"><a rel="shadowbox" href="'.$screengrab_locations[$a].'"><img src="'.$tn_screengrab_locations[$a].'" width="200" height="100" /></a></td>
<td WIDTH="350"><a rel="shadowbox" href="'.$screengrab_locations[$b].'"><img src="'.$tn_screengrab_locations[$b].'" width="200" height="100" /></a></td>
<td WIDTH="350"><a rel="shadowbox" href="'.$screengrab_locations[$c].'"><img src="'.$tn_screengrab_locations[$c].'" width="200" height="100" /></a></td>
</tr>

<tr>
<td WIDTH="350">Pop:<input type="text" value="'.$popped_urls[$a].'"/></td>
<td WIDTH="350">Pop:<input type="text" value="'.$popped_urls[$b].'"/></td>
<td WIDTH="350">Pop:<input type="text" value="'.$popped_urls[$c].'"/></td>
</tr>

<tr>
<td WIDTH="350">Target:<input type="text" value="'.$target_urls[$a].'"/></td>
<td WIDTH="350">Target:<input type="text" value="'.$target_urls[$b].'"/></td>
<td WIDTH="350">Target:<input type="text" value="'.$target_urls[$c].'"/></td>
</tr>
<tr>
<td WIDTH="350">Offer: '.$offers[$a].'</td>
<td WIDTH="350">Offer: '.$offers[$b].'</td>
<td WIDTH="350">Offer: '.$offers[$c].'</td>
</tr>

<tr>
<td WIDTH="350">Niche: '.$niches[$a].'</td>
<td WIDTH="350">Niche: '.$niches[$b].'</td>
<td WIDTH="350">Niche: '.$niches[$c].'</td>
</tr>

<tr>
<td WIDTH="350">View All Pops For This: <a href=Similar.html?search_for=pops&amp;search_by=popped_url&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'>Popped URL</a> | <a href=Similar.html?search_for=pops&amp;search_by=niche&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'>Niche</a> | <a href=Similar.html?search_for=pops&amp;search_by=offer&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'>Offer</a></td>
<td WIDTH="350">View All Pops For This: <a href=Similar.html?search_for=pops&amp;search_by=popped_url&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'>Popped URL</a> | <a href=Similar.html?search_for=pops&amp;search_by=niche&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'>Niche</a> | <a href=Similar.html?search_for=pops&amp;search_by=offer&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'>Offer</a></td>
<td WIDTH="350">View All Pops For This: <a href=Similar.html?search_for=pops&amp;search_by=popped_url&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'>Popped URL</a> | <a href=Similar.html?search_for=pops&amp;search_by=niche&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'>Niche</a> | <a href=Similar.html?search_for=pops&amp;search_by=offer&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'>Offer</a></td>
</tr>

<tr>
<td WIDTH="350">View All Targets For This: <a href=Similar.html?search_for=targets&amp;search_by=popped_url&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'>Popped URL</a> | <a href=Similar.html?search_for=targets&amp;search_by=niche&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'>Niche</a> | <a href=Similar.html?search_for=targets&amp;search_by=offer&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'>Offer</a></td>
<td WIDTH="350">View All Targets For This: <a href=Similar.html?search_for=targets&amp;search_by=popped_url&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'>Popped URL</a> | <a href=Similar.html?search_for=targets&amp;search_by=niche&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'>Niche</a> | <a href=Similar.html?search_for=targets&amp;search_by=offer&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'>Offer</a></td>
<td WIDTH="350">View All Targets For This: <a href=Similar.html?search_for=targets&amp;search_by=popped_url&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'>Popped URL</a> | <a href=Similar.html?search_for=targets&amp;search_by=niche&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'>Niche</a> | <a href=Similar.html?search_for=targets&amp;search_by=offer&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'>Offer</a></td>
</tr>



<tr>
<td WIDTH="350"></td>
<td WIDTH="350"></td>
<td WIDTH="350"></td>

</tr>
</p></table>';
$a=$c+1;
}
}


elseif ($search_for=='targets'){
//build results array
foreach ($xml->pops->pop_up as $pop){
$popped_urls[]=$pop->popped_url;
$target_urls[]=$pop->target_url;
$screengrab_locations[]=$pop->screengrab_location;
$dates_seen[]=$pop->date_seen;
}
$target_urls=array_unique($target_urls);
$rows=COUNT($target_urls)+4;
$results_table='<textarea rows="'.$rows.'" cols="80">';

//display targets
foreach ($target_urls as $target_url){
$results_table.=$target_url.'
';
}
$results_table.='</textarea>';

}


if ($search_for=='pops'){
/******  build the pagination links ******/
$current_page_info=($offset + 1).' - '.($rowsperpage * $currentpage).' of '.$total_results.' Results</br>';


// range of num links to show
$range = 10;

// if not on page 1, don't show back links
if ($currentpage > 1) {
   // show << link to go back to page 1
$current_page_info.= " <a href='Similar.html?currentpage=1&amp;$query_url_string'><<</a> ";
   // get previous page num
   $prevpage = $currentpage - 1;
   // show < link to go back to 1 page
$current_page_info.= " <a href='Similar.html?currentpage=$prevpage&amp;$query_url_string'><</a> ";
} // end if 

// loop to show links to range of pages around current page
for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
   // if it's a valid page number...
   if (($x > 0) && ($x <= $totalpages)) {
      // if we're on current page...
      if ($x == $currentpage) {
         // 'highlight' it but don't make a link
$current_page_info.= " [<b>$x</b>] ";
      // if not current page...
      } else {
         // make it a link
$current_page_info.= " <a href='Similar.html?currentpage=$x&amp;$query_url_string'>$x</a> ";
      } // end else
   } // end if 
} // end for
                
                 
// if not on last page, show forward and last page links        
if ($currentpage != $totalpages) {
   // get next page
   $nextpage = $currentpage + 1;
    // echo forward link for next page 
$current_page_info.= " <a href='Similar.html?currentpage=$nextpage&amp;$query_url_string'>></a> ";
   // echo forward link for lastpage
$current_page_info.= " <a href='Similar.html?currentpage=$totalpages&amp;$query_url_string'>>></a> ";
} // end if
/****** end build pagination links ******/
}


//display the page info (will only display after a submission)
echo $current_page_info;


//display the results of the search (will only display after a submission)
echo $results_table;


?>



			
				