<?php /* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="MostSeenPopups.html">Most Seen Popups<span></span></a></li>
			<li><a href='MostSeenOffers.html'>Most Seen Offers<span></span></a></li>
			<li><a href='MostSeenNiches.html'>Most Seen Niches<span></span></a></li>
			<li><a href='SearchbyDate.html'>Search by Date<span></span></a></li>
			<li><a href='SearchbyKeyword.html'>Search by Keyword<span></span></a></li>
			<li><a class="active" href='ViewAllbyOffer.html'>"View All" by Offer<span></span></a></li>
			<li><a href='ViewAllbyNiche.html'>"View All" by Niche<span></span></a></li>
			<li><a href='SuggestATarget.html'>Suggest A Target<span></span></a></li>
		</ul>
		
		
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
	?>


<?php

$title='"View All" By Offer';

//your api key
$key='237f3f76g2f487gfiwvbbwwf7g34';



//load the xml file
$xml = simplexml_load_file("http://distantsunmedia.com/api/get-offers.php?key=".$key);



//build results array
foreach ($xml->pops->pop_up as $pop){
$offers[]=$pop->offer;
$offer_ids[]=$pop->offer_id;

}



$form='<p><form method="get" action="Similar.html">

Offer:
<select name="offer_id">';

$x=0;
while ($x<(COUNT($offers))){
$form.= '<option value="'.$offer_ids[$x].'">'.$offers[$x].'</option>';
$x++;
}

$form.='</select>

View All:
<select name="search_for">
<option value="pops">Pops</option>
<option value="targets">Targets</option>
</select>

<input type="hidden" value="offer" name="search_by" />

<input type="submit" />

</form></p>';

echo $form;


?>