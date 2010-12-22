<?php
session_start();

define('PATH', str_replace('\\', '/', getcwd().'/../../Applications/BevoMedia/Common/'));

require_once(PATH.'AbsoluteIncludeHelper.include.php');
require_once(PATH.'Legacy.Abstraction.class.php');

//require_once(PATH.'/')
require_once(PATH.'/classes/clsMarketLogins.php');

global $intLoginID, $intProviderID;

function LoadProvider() {
	global $intLoginID, $intProviderID;
	$objLogin = new MarketLogins();
	$objLogin->ID = $intLoginID;
	$objLogin->GetDetails();

	
	$intProviderID = $objLogin->providerId;
}
?>