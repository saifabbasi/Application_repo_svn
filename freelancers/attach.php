<?php

require('include.php');
require('../classes/clsMarketLeadAttach.php');
require('auth.php');

$intAttachID = $_GET['ID'];

if (!is_numeric($intAttachID)) {
	$intAttachID = 0;
}

LoadAttachment();

function LoadAttachment() {
	global $intAttachID;

	$objAttach = new MarketLeadAttachments();
	$objAttach->ID = $intAttachID;
	$objAttach->GetDetails();
	
	$strFilename = $objAttach->Filename;
	
	header('Location: https://www.bevomedia.com/attachments/' . $intAttachID . $strFilename);
}

?>