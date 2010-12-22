<?php

class MarketProviders extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_providers', 'ID', array('name', 'email', 'email2', 'payPal', 'description', 'priceRange', 'image', 'thumbImage', 'accountDate'));
	}
	
	function GetListByServiceID($intInServiceID) {
		if (!is_numeric($intInServiceID)) {
			return false;
		}
		$strSQL = 'SELECT bevomedia_market_providers.id, name, description, priceRange, image, thumbImage, accountDate 
					FROM bevomedia_market_providers
					INNER JOIN bevomedia_market_provider_services ON bevomedia_market_providers.id = bevomedia_market_provider_services.providerId
					WHERE (bevomedia_market_provider_services.serviceId = ' . $intInServiceID . ')';
		$this->Select($strSQL);
	}

}

?>
