<?php

class MarketPackages extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_packages', 'id', array('name', 'price'));
	}
	
	
}

?>
