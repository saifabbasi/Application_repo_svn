<?php

class MarketProducts extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_products', 'id', array('serviceId', 'name', 'description', 'price'));
	}

	function GetList() {
		$strSQL = 'SELECT bevomedia_market_products.id, bevomedia_market_products.name, description, price, bevomedia_market_services.name AS Servicename FROM
					bevomedia_market_products LEFT JOIN bevomedia_market_services ON bevomedia_market_products.serviceId = bevomedia_market_services.id
					ORDER BY serviceId';
		$this->Select($strSQL);
	}
}

?>
