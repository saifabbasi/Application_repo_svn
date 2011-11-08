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
			<li><a class="active" href='SuggestATarget.html'>Suggest A Target<span></span></a></li>
		</ul>
		
		
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
	?>



<?php

$title="Suggest A Target";

//your api key
$key='237f3f76g2f487gfiwvbbwwf7g34';

//search parameters
$target=$_POST['target'];

if ((strlen($target)>2)){
//load the xml file
$xml = simplexml_load_file("http://distantsunmedia.com/api/suggest-target.php?target=".$target."&key=".$key);

$form= '<p>Thank you. Your target has been added to the queue. Please check back in 24 to 48 hours.</p>';
}
else{
$form= '<p>Suggest a target below to be checked for pop ups.</p><p><form method="post" action="SuggestATarget.html">
<input type="text" name="target" value="" />

<input type="submit" />

</form></p>';

}

echo $form;

?>