<?php

require('include.php');
require('auth.php');

LegacyAbstraction::doAction('Update', 'UpdateOffers');

function UpdateOffers() {
	$arrOffersVal = $_POST['Offer'];
	
	if (empty($arrOffersVal)) {
		return false;
	}
	
	$strOfferXML = '<?xml version="1.0" ?><ticker>';
	
	foreach ($arrOffersVal as $strThisOffer) {
		$strOfferXML .= '<tickeritem debut="1" exit="2">' . stripslashes($strThisOffer) . '</tickeritem>';
	}
	
	$strOfferXML .= '</ticker>';
	
	$file = fopen(PATH . '../../../www/Themes/BevoMedia/ticker_text.xml', 'w');
	
	fwrite($file, $strOfferXML, strlen($strOfferXML));
	fclose($file);

	header('Location: topoffers.php');
}

function ListOffers() {
	$strOffers = file_get_contents(PATH . '../../../www/Themes/BevoMedia/ticker_text.xml');
	
	$objXML = simplexml_load_string($strOffers);
	
	foreach ($objXML->tickeritem as $objThisTicker) {
//		$arrAtts = $objThisTicker->attributes();
//		$strDateStart = (string) $arrAtts[''];

		$strThisTicker = (string) $objThisTicker;
		
?>
  <tr>
    <td><textarea name="Offer[]" cols="60" rows="3"><?php echo htmlspecialchars($strThisTicker); ?></textarea></td>
	<td><a href="#NewOffer" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);" /><img src="/Themes/BevoMedia/img/delete.png" border="0"/></a></td>
  </tr>
<?php
	}
}

$strPageTitle = 'Network Top Offers';
include('templates/topoffers.php');

?>