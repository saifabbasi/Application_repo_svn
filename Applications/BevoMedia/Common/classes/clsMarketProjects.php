<?php

class MarketProjects extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_projects', 'ID', array('user__id', 'providerId', 'name', 'date', 'lastPost', 'acceptedId', 'orderId', 'userComplete', 'providerComplete', 'paid'));
	}
	
	function GetList() {
		$strSQL = 'SELECT bevomedia_market_projects.ID, bevomedia_market_projects.providerId, bevomedia_market_projects.user__id, bevomedia_market_projects.name, bevomedia_market_projects.date, lastPost, acceptedId, orderId, userComplete, providerComplete, paid, bevomedia_market_providers.name AS Providername, CONCAT(bevomedia_user_info.Firstname, \' \', bevomedia_user_info.Lastname) AS Username, bevomedia_market_project_terms.Deposit, bevomedia_market_project_terms.Terms FROM
					((bevomedia_market_projects LEFT JOIN bevomedia_market_providers ON bevomedia_market_projects.providerId = bevomedia_market_providers.ID)
					LEFT JOIN bevomedia_user_info ON bevomedia_market_projects.user__id = bevomedia_user_info.ID)
					LEFT JOIN bevomedia_market_project_terms ON bevomedia_market_projects.acceptedId = bevomedia_market_project_terms.ID
					ORDER BY bevomedia_market_projects.lastPost DESC';
		$this->Select($strSQL);
	}
	
	function GetListByUserID($intInuser__id) {
		$strSQL = 'SELECT bevomedia_market_projects.ID, bevomedia_market_projects.providerId, bevomedia_market_projects.name, bevomedia_market_projects.date, lastPost, acceptedId, orderId, userComplete, providerComplete, paid, bevomedia_market_providers.name AS Providername, bevomedia_market_project_terms.Deposit, bevomedia_market_project_terms.Terms FROM
					(bevomedia_market_projects LEFT JOIN bevomedia_market_providers ON bevomedia_market_projects.providerId = bevomedia_market_providers.ID)
					LEFT JOIN bevomedia_market_project_terms ON bevomedia_market_projects.acceptedId = bevomedia_market_project_terms.ID
					WHERE (bevomedia_market_projects.user__id = ' . $intInuser__id . ')
					ORDER BY bevomedia_market_projects.lastPost DESC';
		$this->Select($strSQL);
	}
	
	function GetListByProviderId($intInproviderId) {
		$strSQL = 'SELECT bevomedia_market_projects.ID, bevomedia_market_projects.user__id, name, bevomedia_market_projects.date, lastPost, acceptedId, orderId, userComplete, providerComplete, paid, CONCAT(bevomedia_user_info.Firstname, \' \', bevomedia_user_info.Lastname) AS Username, bevomedia_market_project_terms.Deposit, bevomedia_market_project_terms.Terms FROM
					(bevomedia_market_projects LEFT JOIN bevomedia_user_info ON bevomedia_market_projects.user__id = bevomedia_user_info.ID)
					LEFT JOIN bevomedia_market_project_terms ON bevomedia_market_projects.acceptedId = bevomedia_market_project_terms.ID
					WHERE (bevomedia_market_projects.providerId = ' . $intInproviderId . ')
					ORDER BY bevomedia_market_projects.lastPost DESC';
		$this->Select($strSQL);
	}
	
	function GetUnpaidList() {
		$strSQL = 'SELECT bevomedia_market_projects.ID, bevomedia_market_projects.providerId, bevomedia_market_projects.user__id, bevomedia_market_projects.name, bevomedia_market_projects.date, lastPost, acceptedId, orderId, userComplete, providerComplete, paid, provider.name AS Providername, CONCAT(bevomedia_user_info.Firstname, \' \', bevomedia_user_info.Lastname) AS Username, bevomedia_market_project_terms.Deposit, bevomedia_market_project_terms.Terms FROM
					((bevomedia_market_projects LEFT JOIN bevomedia_market_providers ON bevomedia_market_projects.providerId = bevomedia_market_providers.ID)
					LEFT JOIN bevomedia_user_info ON bevomedia_market_projects.user__id = bevomedia_user_info.ID)
					LEFT JOIN bevomedia_market_project_terms ON bevomedia_market_projects.acceptedId = bevomedia_market_project_terms.ID
					WHERE (bevomedia_market_projects.userComplete = 1 AND bevomedia_market_projects.providerComplete = 1 AND bevomedia_market_projects.paid = 0)
					ORDER BY bevomedia_market_projects.lastPost DESC';
		$this->Select($strSQL);
	}
	
	function GetUnpaidListGroupByProvider() {
		$strSQL = 'SELECT bevomedia_market_projects.providerId, bevomedia_market_providers.name AS Providername, bevomedia_market_providers.PayPal, SUM(bevomedia_market_project_terms.Deposit) AS NetPayment FROM
					(bevomedia_market_projects LEFT JOIN bevomedia_market_providers ON bevomedia_market_projects.providerId = bevomedia_market_providers.ID)
					LEFT JOIN bevomedia_market_project_terms ON bevomedia_market_projects.acceptedId = bevomedia_market_project_terms.ID
					WHERE (bevomedia_market_projects.userComplete = 1 AND bevomedia_market_projects.providerComplete = 1 AND bevomedia_market_projects.paid = 0)
					GROUP BY bevomedia_market_projects.providerId, Providername';
		$this->Select($strSQL);
	}
	
	function GetPendingListGroupByProvider() {
		$strSQL = 'SELECT bevomedia_market_projects.providerId, bevomedia_market_providers.name AS Providername, bevomedia_market_providers.PayPal, SUM(bevomedia_market_project_terms.Deposit) AS NetPayment FROM
					(bevomedia_market_projects LEFT JOIN bevomedia_market_providers ON bevomedia_market_projects.providerId = bevomedia_market_providers.ID)
					LEFT JOIN bevomedia_market_project_terms ON bevomedia_market_projects.acceptedId = bevomedia_market_project_terms.ID
					WHERE ((bevomedia_market_projects.userComplete = 0 OR bevomedia_market_projects.providerComplete = 0) AND bevomedia_market_projects.paid = 0 AND bevomedia_market_projects.orderId > 0)
					GROUP BY bevomedia_market_projects.providerId, Providername';
		$this->Select($strSQL);
	}
	
	function Markpaid($intInproviderId) {
		$strSQL = 'UPdate bevomedia_market_projects SET paid = 1 WHERE (providerId = ' . $intInproviderId . ') AND (userComplete = 1 AND providerComplete = 1 AND paid = 0)';
		$this->Query($strSQL);
	}

}

?>
