<?php

class MarketLeads extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_leads', 'id', array('user__id', 'providerId', 'productId', 'createDate', 'lastUpdate'));
	}
	
	function GetListByproviderId($intInproviderId) {
		$strSQL = 'SELECT bevomedia_market_leads.id, bevomedia_market_leads.user__id, productId, createDate, lastUpdate, CONCAT(bevomedia_user_info.firstName, \' \', bevomedia_user_info.lastName) AS UserName, bevomedia_market_products.name AS ProductName
					FROM (bevomedia_market_leads LEFT JOIN bevomedia_user_info ON bevomedia_market_leads.user__id = bevomedia_user_info.user__id)
					LEFT JOIN bevomedia_market_products ON bevomedia_market_leads.productId = bevomedia_market_products.id
					WHERE (bevomedia_market_leads.providerId = ' . $intInproviderId . ')';
		$this->Select($strSQL);
	}
	
	function GetOpenList($intInproviderId) {
		$strSQL = 'SELECT bevomedia_market_leads.id, bevomedia_market_leads.user__id, bevomedia_market_leads.productId, createDate, lastUpdate, CONCAT(bevomedia_user_info.firstName, \' \', bevomedia_user_info.lastName) AS UserName, bevomedia_market_products.name AS ProductName
					FROM ((bevomedia_market_leads LEFT JOIN bevomedia_user_info ON bevomedia_market_leads.user__id = bevomedia_user_info.user__id)
					LEFT JOIN bevomedia_market_products ON bevomedia_market_leads.productId = bevomedia_market_products.id)
					LEFT JOIN bevomedia_market_provider_services ON bevomedia_market_products.serviceId = bevomedia_market_provider_services.serviceId
					WHERE (bevomedia_market_leads.providerId = 0 AND bevomedia_market_provider_services.providerId = ' . $intInproviderId . ')';
		$this->Select($strSQL);
	}
	
	function GetListByuser__id($intInuser__id) {
		$strSQL = 'SELECT bevomedia_market_leads.id, bevomedia_market_leads.providerId, productId, createDate, lastUpdate, bevomedia_market_products.name AS ProductName, bevomedia_market_providers.name AS ProviderName
					FROM (bevomedia_market_leads LEFT JOIN bevomedia_market_providers ON bevomedia_market_leads.providerId = bevomedia_market_providers.id)
					LEFT JOIN bevomedia_market_products ON bevomedia_market_leads.productId = bevomedia_market_products.id
					WHERE (bevomedia_market_leads.user__id = ' . $intInuser__id . ')';
		$this->Select($strSQL);
	}

}

?>
