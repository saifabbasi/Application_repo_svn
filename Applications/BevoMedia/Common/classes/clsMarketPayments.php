<?php

class MarketPayments extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_payments', 'id', array('providerId', 'amount', 'date'));
	}

	function GetList() {
		$strSQL = 'SELECT bevomedia_market_payments.id, providerId, amount, date, bevomedia_market_providers.name AS Providername FROM
					bevomedia_market_payments LEFT JOIN bevomedia_market_providers ON bevomedia_market_payments.providerId = bevomedia_market_providers.id
					ORDER BY date DESC';
		$this->Select($strSQL);
	}
	
	function GetListByproviderId($intInproviderId, $strInStartdate = '', $strInEnddate = '') {
		$strWhere = '';
		if ($strInStartdate != '') {
			$strWhere = ' AND date >= \'' . $strInStartdate . '\' ';
			
			if ($strInEnddate != '') {
				$strWhere .= ' AND date <= \'' . $strInEnddate . '\' ';
			}
		}
		$strSQL = 'SELECT bevomedia_market_payments.id, amount, providerId, date, bevomedia_market_providers.name AS Providername FROM
					bevomedia_market_payments LEFT JOIN bevomedia_market_providers ON bevomedia_market_payments.providerId = bevomedia_market_providers.id
					WHERE (providerId = ' . $intInproviderId . ') ';
		$strSQL .= $strWhere;
		$strSQL .= 'ORDER BY date DESC';
		$this->Select($strSQL);
	}
		
}

?>
