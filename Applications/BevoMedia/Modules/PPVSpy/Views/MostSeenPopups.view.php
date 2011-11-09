<?php /* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/PPVTools/PageSniper.html">PPV Research<span></span></a></li>
			<li><a class="active" href="/BevoMedia/PPVSpy/Index.html">PPV Spy<span></span></a></li>
			<li><a class="active" href="MostSeenPopups.html">Most Seen Popups<span></span></a></li>
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

$title='Most Seen Pops';

//your api key
$key='237f3f76g2f487gfiwvbbwwf7g34';

//load the xml file
$xml = simplexml_load_file("http://distantsunmedia.com/api/most-seen.php?search_for=pops&key=".$key);


//build results array
foreach ($xml->pops->pop_up as $pop){
$short_popped_urls[]=$pop->short_popped_url;
$screengrab_locations[]=$pop->screengrab_location;
$tn_screengrab_locations[]=$pop->tn_screengrab_location;
$offers[]=$pop->offer;
$niches[]=$pop->niche;
$offer_ids[]=$pop->offer_id;
$niche_ids[]=$pop->niche_id;
}


//build html to display ads
$num_ads=COUNT($short_popped_urls);
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
<td WIDTH="350">Pop: '.htmlentities($short_popped_urls[$a]).'...</td>
<td WIDTH="350">Pop: '.htmlentities($short_popped_urls[$b]).'...</td>
<td WIDTH="350">Pop: '.htmlentities($short_popped_urls[$c]).'...</td>
</tr>

<tr>
<td WIDTH="350">Offer: '.htmlentities($offers[$a]).'</td>
<td WIDTH="350">Offer: '.htmlentities($offers[$b]).'</td>
<td WIDTH="350">Offer: '.htmlentities($offers[$c]).'</td>
</tr>

<tr>
<td WIDTH="350">Niche: '.htmlentities($niches[$a]).'</td>
<td WIDTH="350">Niche: '.htmlentities($niches[$b]).'</td>
<td WIDTH="350">Niche: '.htmlentities($niches[$c]).'</td>
</tr>

<tr>
<td WIDTH="350">View All Pops For This: <a href="Similar.html?search_for=pops&amp;search_by=popped_url&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Popped URL</a> | <a href="Similar.html?search_for=pops&amp;search_by=niche&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Niche</a> | <a href="Similar.html?search_for=pops&amp;search_by=offer&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Offer</a></td>
<td WIDTH="350">View All Pops For This: <a href="Similar.html?search_for=pops&amp;search_by=popped_url&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'">Popped URL</a> | <a href="Similar.html?search_for=pops&amp;search_by=niche&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'">Niche</a> | <a href="Similar.html?search_for=pops&amp;search_by=offer&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'">Offer</a></td>
<td WIDTH="350">View All Pops For This: <a href="Similar.html?search_for=pops&amp;search_by=popped_url&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'">Popped URL</a> | <a href="Similar.html?search_for=pops&amp;search_by=niche&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'">Niche</a> | <a href="Similar.html?search_for=pops&amp;search_by=offer&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'">Offer</a></td>
</tr>

<tr>
<td WIDTH="350">View All Targets For This: <a href="Similar.html?search_for=targets&amp;search_by=popped_url&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Popped URL</a> | <a href="Similar.html?search_for=targets&amp;search_by=niche&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Niche</a> | <a href="Similar.html?search_for=targets&amp;search_by=offer&amp;url='.$short_popped_urls[$a].'&amp;offer_id='.$offer_ids[$a].'&amp;niche_id='.$niche_ids[$a].'">Offer</a></td>
<td WIDTH="350">View All Targets For This: <a href="Similar.html?search_for=targets&amp;search_by=popped_url&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'">Popped URL</a> | <a href="Similar.html?search_for=targets&amp;search_by=niche&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'">Niche</a> | <a href="Similar.html?search_for=targets&amp;search_by=offer&amp;url='.$short_popped_urls[$b].'&amp;offer_id='.$offer_ids[$b].'&amp;niche_id='.$niche_ids[$b].'">Offer</a></td>
<td WIDTH="350">View All Targets For This: <a href="Similar.html?search_for=targets&amp;search_by=popped_url&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'">Popped URL</a> | <a href="Similar.html?search_for=targets&amp;search_by=niche&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'">Niche</a> | <a href="Similar.html?search_for=targets&amp;search_by=offer&amp;url='.$short_popped_urls[$c].'&amp;offer_id='.$offer_ids[$c].'&amp;niche_id='.$niche_ids[$c].'">Offer</a></td>
</tr>

<tr>
<td WIDTH="350"></td>
<td WIDTH="350"></td>
<td WIDTH="350"></td>
</tr>

</table></p>';
$a=$c+1;
}


echo $results_table;



?>


