<?php

class MarketProviderServices extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_provider_services', 'id', array('providerId', 'serviceId'));
	}
	
	function GetListByProviderID($intInProviderID) {
		$strSQL = 'SELECT id, serviceId FROM bevomedia_market_provider_services WHERE (providerId = ' . $intInProviderID . ')';
		$this->Select($strSQL);
	}
	
	function DeleteByProviderID($intInProviderID) {
		$strSQL = 'DELETE FROM bevomedia_market_provider_services WHERE (providerId = ' . $intInProviderID . ')';
		$this->Query($strSQL);
	}

}

?>
