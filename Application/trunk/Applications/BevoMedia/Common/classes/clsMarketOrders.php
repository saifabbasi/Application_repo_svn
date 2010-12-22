<?php

class MarketOrders extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_orders', 'id', array('user__id', 'orderDate', 'txn'));
	}

	function TxnExists($strInTxn) {
		$strSQL = 'SELECT id FROM bevomedia_market_orders WHERE (txn = \'' . $this->FixString($strInTxn) . '\')';
		$this->Select($strSQL);
	}

}

?>
