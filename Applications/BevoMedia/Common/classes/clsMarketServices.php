<?php

class MarketServices extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_services', 'id', array('name', 'description'));
	}

}

?>
