<?php

class MarketProjectterms extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_project_terms', 'id', array('projectId', 'userId', 'providerId', 'deposit', 'terms', 'date'));
	}
	
	function GetListByprojectId($intInprojectId) {
		$strSQL = 'SELECT bevomedia_market_project_terms.id, bevomedia_market_project_terms.userId, providerId, deposit, terms, date, CONCAT(UserInfo.firstName, \' \', UserInfo.lastName) AS UserName, marketProviders.Name AS ProviderName FROM
					(bevomedia_market_project_terms LEFT JOIN bevomedia_user_info UserInfo ON bevomedia_market_project_terms.userId = UserInfo.id)
					LEFT JOIN bevomedia_market_providers marketProviders ON bevomedia_market_project_terms.providerId = marketProviders.id
					WHERE (projectId = ' . $intInprojectId . ')
					ORDER BY date ASC';
		$this->Select($strSQL);
	}

}

?>
