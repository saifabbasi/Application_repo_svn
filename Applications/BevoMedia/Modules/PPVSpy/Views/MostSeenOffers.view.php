<?php /* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="MostSeenPopups.html">Most Seen Popups<span></span></a></li>
			<li><a class="active" href='MostSeenOffers.html'>Most Seen Offers<span></span></a></li>
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


<div align="center">

<?php

$title='Most Seen Offers';

//your api key
$key='237f3f76g2f487gfiwvbbwwf7g34';




//load the xml file
$xml = simplexml_load_file("http://distantsunmedia.com/api/most-seen.php?search_for=offers&key=".$key);

//build results array
foreach ($xml->pops->pop_up as $pop){
$offers[]=$pop->offer;
$niches[]=$pop->niche;
$offer_ids[]=$pop->offer_id;
$niche_ids[]=$pop->niche_id;
}

$results_table=$results_table.'<p><table cellpadding="2" width="100%"><tr style="text-align: left;">
<th>Offer</th>
<th>Niche</th>
<th width="100">View All Pops For This:</th>
<th width="100">View All Targets For This:</th>
</tr>';

//build html to display results
$num_ads=COUNT($offers);
$a=0;

while ($a<$num_ads){

$b=$a+1;

$results_table.='<tr>
<td>'.$b.') '.htmlentities($offers[$a]).'</td>
<td>'.htmlentities($niches[$a]).'</td>
<td><a href="Similar.html?search_for=pops&amp;search_by=offer&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Offer</a> | <a href="Similar.html?search_for=pops&amp;search_by=niche&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Niche</a></td>
<td><a href="Similar.html?search_for=targets&amp;search_by=offer&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Offer</a> | <a href="Similar.html?search_for=targets&amp;search_by=niche&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Niche</a></td>
</tr>';


$a++;
}


$results_table.='</table></p>';

echo $results_table;

?>

</div>
