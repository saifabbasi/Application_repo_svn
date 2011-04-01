<?php

class MarketProjects extends DBObject {

	function __construct() {
		parent::__construct('market_projects', 'ID', array('UserID', 'ProviderID', 'Name', 'Date', 'LastPost', 'AcceptedID', 'OrderID', 'UserComplete', 'ProviderComplete', 'Paid'));
	}
	
	function GetList() {
		$strSQL = 'SELECT market_projects.ID, market_projects.ProviderID, market_projects.UserID, market_projects.Name, market_projects.Date, LastPost, AcceptedID, OrderID, UserComplete, ProviderComplete, Paid, market_providers.Name AS ProviderName, CONCAT(ADPALACE_USERINFO.First_Name, \' \', ADPALACE_USERINFO.Last_Name) AS UserName, market_project_terms.Deposit, market_project_terms.Terms FROM
					((market_projects LEFT JOIN market_providers ON market_projects.ProviderID = market_providers.ID)
					LEFT JOIN ADPALACE_USERINFO ON market_projects.UserID = ADPALACE_USERINFO.USERID)
					LEFT JOIN market_project_terms ON market_projects.AcceptedID = market_project_terms.ID
					ORDER BY market_projects.LastPost DESC';
		$this->Select($strSQL);
	}
	
	function GetListByUserID($intInUserID) {
		$strSQL = 'SELECT market_projects.ID, market_projects.ProviderID, market_projects.Name, market_projects.Date, LastPost, AcceptedID, OrderID, UserComplete, ProviderComplete, Paid, market_providers.Name AS ProviderName, market_project_terms.Deposit, market_project_terms.Terms FROM
					(market_projects LEFT JOIN market_providers ON market_projects.ProviderID = market_providers.ID)
					LEFT JOIN market_project_terms ON market_projects.AcceptedID = market_project_terms.ID
					WHERE (market_projects.UserID = ' . $intInUserID . ')
					ORDER BY market_projects.LastPost DESC';
		$this->Select($strSQL);
	}
	
	function GetListByProviderID($intInProviderID) {
		$strSQL = 'SELECT market_projects.ID, market_projects.UserID, Name, market_projects.Date, LastPost, AcceptedID, OrderID, UserComplete, ProviderComplete, Paid, CONCAT(ADPALACE_USERINFO.First_Name, \' \', ADPALACE_USERINFO.Last_Name) AS UserName, market_project_terms.Deposit, market_project_terms.Terms FROM
					(market_projects LEFT JOIN ADPALACE_USERINFO ON market_projects.UserID = ADPALACE_USERINFO.USERID)
					LEFT JOIN market_project_terms ON market_projects.AcceptedID = market_project_terms.ID
					WHERE (market_projects.ProviderID = ' . $intInProviderID . ')
					ORDER BY market_projects.LastPost DESC';
		$this->Select($strSQL);
	}
	
	function GetUnpaidList() {
		$strSQL = 'SELECT market_projects.ID, market_projects.ProviderID, market_projects.UserID, market_projects.Name, market_projects.Date, LastPost, AcceptedID, OrderID, UserComplete, ProviderComplete, Paid, provider.Name AS ProviderName, CONCAT(ADPALACE_USERINFO.First_Name, \' \', ADPALACE_USERINFO.Last_Name) AS UserName, market_project_terms.Deposit, market_project_terms.Terms FROM
					((market_projects LEFT JOIN market_providers ON market_projects.ProviderID = market_providers.ID)
					LEFT JOIN ADPALACE_USERINFO ON market_projects.UserID = ADPALACE_USERINFO.USERID)
					LEFT JOIN market_project_terms ON market_projects.AcceptedID = market_project_terms.ID
					WHERE (market_projects.UserComplete = 1 AND market_projects.ProviderComplete = 1 AND market_projects.Paid = 0)
					ORDER BY market_projects.LastPost DESC';
		$this->Select($strSQL);
	}
	
	function GetUnpaidListGroupByProvider() {
		$strSQL = 'SELECT market_projects.ProviderID, market_providers.Name AS ProviderName, market_providers.PayPal, SUM(market_project_terms.Deposit) AS NetPayment FROM
					(market_projects LEFT JOIN market_providers ON market_projects.ProviderID = market_providers.ID)
					LEFT JOIN market_project_terms ON market_projects.AcceptedID = market_project_terms.ID
					WHERE (market_projects.UserComplete = 1 AND market_projects.ProviderComplete = 1 AND market_projects.Paid = 0)
					GROUP BY market_projects.ProviderID, ProviderName';
		$this->Select($strSQL);
	}
	
	function GetPendingListGroupByProvider() {
		$strSQL = 'SELECT market_projects.ProviderID, market_providers.Name AS ProviderName, market_providers.PayPal, SUM(market_project_terms.Deposit) AS NetPayment FROM
					(market_projects LEFT JOIN market_providers ON market_projects.ProviderID = market_providers.ID)
					LEFT JOIN market_project_terms ON market_projects.AcceptedID = market_project_terms.ID
					WHERE ((market_projects.UserComplete = 0 OR market_projects.ProviderComplete = 0) AND market_projects.Paid = 0 AND market_projects.OrderID > 0)
					GROUP BY market_projects.ProviderID, ProviderName';
		$this->Select($strSQL);
	}
	
	function MarkPaid($intInProviderID) {
		$strSQL = 'UPDATE market_projects SET Paid = 1 WHERE (ProviderID = ' . $intInProviderID . ') AND (UserComplete = 1 AND ProviderComplete = 1 AND Paid = 0)';
		$this->Query($strSQL);
	}

}

?>