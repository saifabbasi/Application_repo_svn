<?php

class NetworkOfMonth extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_network_of_month', 'id', array('networkId', 'content', 'date'));
	}
	
	function GetList() {
		$strSQL = 'SELECT bevomedia_network_of_month.id, bevomedia_network_of_month.network__id, content, date, bevomedia_aff_network.title AS NetworkTitle, url, signupUrl
					FROM bevomedia_network_of_month LEFT JOIN bevomedia_aff_network ON bevomedia_network_of_month.network__id = bevomedia_aff_network.id';
		$this->Select($strSQL);
	}

}

?>
