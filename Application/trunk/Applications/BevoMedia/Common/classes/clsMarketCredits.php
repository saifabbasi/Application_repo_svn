<?php

class Marketcredits extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_credits', 'id', array('user__id', 'orderId', 'productId', 'credits'));
	}
	
	function GetListByuser__id($intInuser__id) {
		$strSQL = 'SELECT id, orderId, productId, credits FROM bevomedia_market_credits WHERE (user__id = ' . $intInuser__id . ')';
		$this->Select($strSQL);
	}
	
	function GetListByuser__idByproductId($intInuser__id, $intInproductId) {
		$strSQL = 'SELECT id, credits FROM bevomedia_market_credits WHERE (user__id = ' . $intInuser__id . ' AND productId = ' . $intInproductId . ') ORDER BY id ASC';
		$this->Select($strSQL);
	}
	
	function GetListSumByuser__id($intInuser__id) {
		$strSQL = 'SELECT productId, SUM(credits) AS Productcredits FROM bevomedia_market_credits WHERE (user__id = ' . $intInuser__id . ') GROUP BY productId';
		$this->Select($strSQL);
	}
	
	function GetByorderId($intInorderId) {
		$strSQL = 'SELECT id, productId, credits FROM bevomedia_market_credits WHERE (orderId = ' . $intInorderId . ')';
		$this->Select($strSQL);
	}
	
	function DeleteZerocredits() {
		$strSQL = 'DELETE FROM bevomedia_market_credits WHERE (credits = 0)';
		$this->Query($strSQL);
	}

}

?>
