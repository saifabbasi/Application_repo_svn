<?php /* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="MostSeenPopups.html">Most Seen Popups<span></span></a></li>
			<li><a href='MostSeenOffers.html'>Most Seen Offers<span></span></a></li>
			<li><a href='MostSeenNiches.html'>Most Seen Niches<span></span></a></li>
			<li><a href='SearchbyDate.html'>Search by Date<span></span></a></li>
			<li><a class="active" href='SearchbyKeyword.html'>Search by Keyword<span></span></a></li>
			<li><a href='ViewAllbyOffer.html'>"View All" by Offer<span></span></a></li>
			<li><a href='ViewAllbyNiche.html'>"View All" by Niche<span></span></a></li>
			<li><a href='SuggestATarget.html'>Suggest A Target<span></span></a></li>
		</ul>
		
		
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
	?>



<?php

$title='Search By Keyword';

//your api key
$key='237f3f76g2f487gfiwvbbwwf7g34';

//search parameters
$keyword=$_GET['q'];
$search_type=$_GET['search_type'];
$currentpage=$_GET['currentpage'];
$query_url_string="q=".$keyword."&search_type=".$search_type;

if ((strlen($keyword)>3)){
//load the xml file
$xml = simplexml_load_file("http://distantsunmedia.com/api/keyword-search.php?q=".$keyword."&search_type=".$search_type."&key=".$key."&currentpage=".$currentpage);

//get summary details
$total_results=$xml->summary->results;
$offset=$xml->summary->offset;
$currentpage=$xml->summary->current_page;
$totalpages=$xml->summary->total_pages;
$rowsperpage=$xml->summary->rows_per_page;


//build results array
foreach ($xml->pops->pop_up as $pop){
$popped_urls[]=$pop->popped_url;
$target_urls[]=$pop->target_url;
$screengrab_locations[]=$pop->screengrab_location;
$offers[]=$pop->offer;
$offer_ids[]=$pop->offer_id;
$niches[]=$pop->niche;
$niche_ids[]=$pop->niche_id;
$dates_seen[]=$pop->date_seen;
$short_popped_urls[]=$pop->short_popped_url;
$tn_screengrab_locations[]=$pop->tn_screengrab_location;
}
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
<td WIDTH="350">View All Pops For This: <a href=Similar.html?search_for=pops&search_by=popped_url&url='.$short_popped_urls[$a].'&offer_id='.$offer_ids[$a].'&niche_id='.$niche_ids[$a].'>Popped URL</a> | <a href=Similar.html?search_for=pops&search_by=niche&url='.$short_popped_urls[$a].'&offer_id='.$offer_ids[$a].'&niche_id='.$niche_ids[$a].'>Niche</a> | <a href=Similar.html?search_for=pops&search_by=offer&url='.$short_popped_urls[$a].'&offer_id='.$offer_ids[$a].'&niche_id='.$niche_ids[$a].'>Offer</a></td>
<td WIDTH="350">View All Pops For This: <a href=Similar.html?search_for=pops&search_by=popped_url&url='.$short_popped_urls[$b].'&offer_id='.$offer_ids[$b].'&niche_id='.$niche_ids[$b].'>Popped URL</a> | <a href=Similar.html?search_for=pops&search_by=niche&url='.$short_popped_urls[$b].'&offer_id='.$offer_ids[$b].'&niche_id='.$niche_ids[$b].'>Niche</a> | <a href=Similar.html?search_for=pops&search_by=offer&url='.$short_popped_urls[$b].'&offer_id='.$offer_ids[$b].'&niche_id='.$niche_ids[$b].'>Offer</a></td>
<td WIDTH="350">View All Pops For This: <a href=Similar.html?search_for=pops&search_by=popped_url&url='.$short_popped_urls[$c].'&offer_id='.$offer_ids[$c].'&niche_id='.$niche_ids[$c].'>Popped URL</a> | <a href=Similar.html?search_for=pops&search_by=niche&url='.$short_popped_urls[$c].'&offer_id='.$offer_ids[$c].'&niche_id='.$niche_ids[$c].'>Niche</a> | <a href=Similar.html?search_for=pops&search_by=offer&url='.$short_popped_urls[$c].'&offer_id='.$offer_ids[$c].'&niche_id='.$niche_ids[$c].'>Offer</a></td>
</tr>

<tr>
<td WIDTH="350">View All Targets For This: <a href=Similar.html?search_for=targets&search_by=popped_url&url='.$short_popped_urls[$a].'&offer_id='.$offer_ids[$a].'&niche_id='.$niche_ids[$a].'>Popped URL</a> | <a href=Similar.html?search_for=targets&search_by=niche&url='.$short_popped_urls[$a].'&offer_id='.$offer_ids[$a].'&niche_id='.$niche_ids[$a].'>Niche</a> | <a href=Similar.html?search_for=targets&search_by=offer&url='.$short_popped_urls[$a].'&offer_id='.$offer_ids[$a].'&niche_id='.$niche_ids[$a].'>Offer</a></td>
<td WIDTH="350">View All Targets For This: <a href=Similar.html?search_for=targets&search_by=popped_url&url='.$short_popped_urls[$b].'&offer_id='.$offer_ids[$b].'&niche_id='.$niche_ids[$b].'>Popped URL</a> | <a href=Similar.html?search_for=targets&search_by=niche&url='.$short_popped_urls[$b].'&offer_id='.$offer_ids[$b].'&niche_id='.$niche_ids[$b].'>Niche</a> | <a href=Similar.html?search_for=targets&search_by=offer&url='.$short_popped_urls[$b].'&offer_id='.$offer_ids[$b].'&niche_id='.$niche_ids[$b].'>Offer</a></td>
<td WIDTH="350">View All Targets For This: <a href=Similar.html?search_for=targets&search_by=popped_url&url='.$short_popped_urls[$c].'&offer_id='.$offer_ids[$c].'&niche_id='.$niche_ids[$c].'>Popped URL</a> | <a href=Similar.html?search_for=targets&search_by=niche&url='.$short_popped_urls[$c].'&offer_id='.$offer_ids[$c].'&niche_id='.$niche_ids[$c].'>Niche</a> | <a href=Similar.html?search_for=targets&search_by=offer&url='.$short_popped_urls[$c].'&offer_id='.$offer_ids[$c].'&niche_id='.$niche_ids[$c].'>Offer</a></td>
</tr>


<tr>
<td WIDTH="350"></td>
<td WIDTH="350"></td>
<td WIDTH="350"></td>

</tr>
</p></table>';
$a=$c+1;
}







$form='<p><form method="get" action="SearchbyKeyword.html">

<input type="text" name="q" value="'.$keyword.'">

Search In:
<select name="search_type">
<option ';
if ($search_type=='3'){$form.= 'selected';}
$form.=' value="3">Target &amp; Popped URLs</option>
<option ';
if ($search_type=='2'){$form.='selected';}
$form.=' value="2">Popped URLs Only</option>
<option ';
if ($search_type=='1'){$form.= 'selected';}
$form.='value="1">Target URLs Only</option>
</select>



<input type="submit" />

</form></p>';



/******  build the pagination links ******/
if ($total_results>0){
$current_page_info=($offset + 1).' - '.($rowsperpage * $currentpage).' of '.$total_results.' Results</br>';



// range of num links to show
$range = 10;

// if not on page 1, don't show back links
if ($currentpage > 1) {
   // show << link to go back to page 1
$current_page_info.= " <a href='SearchbyKeyword.html?currentpage=1&$query_url_string'><<</a> ";
   // get previous page num
   $prevpage = $currentpage - 1;
   // show < link to go back to 1 page
$current_page_info.= " <a href='SearchbyKeyword.html?currentpage=$prevpage&$query_url_string'><</a> ";
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
$current_page_info.= " <a href='SearchbyKeyword.html?currentpage=$x&$query_url_string'>$x</a> ";
      } // end else
   } // end if 
} // end for
                
                 
// if not on last page, show forward and last page links        
if ($currentpage != $totalpages) {
   // get next page
   $nextpage = $currentpage + 1;
    // echo forward link for next page 
$current_page_info.= " <a href='SearchbyKeyword.html?currentpage=$nextpage&$query_url_string'>></a> ";
   // echo forward link for lastpage
$current_page_info.= " <a href='SearchbyKeyword.html?currentpage=$totalpages&$query_url_string'>>></a> ";
} // end if
}
/****** end build pagination links ******/
else{
if ((strlen($keyword)>3)){
$results_table= '<p>Sorry, there are no results for this query. Would you like to <a href="suggest-target.php">suggest a target</a> to be checked for pop ups?</p>';
}
}



//display the form to submit a query
echo $form;

echo "<br />";

//display the page info (will only display after a submission)
echo $current_page_info;


//display the results of the search (will only display after a submission)
echo $results_table;



?>

