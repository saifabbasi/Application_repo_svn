<?php

class PPCKeywords extends DBObject {

	function __construct() {
		parent::__construct('ppc_keywords', 'ID', array('AdGroupID', 'KeywordID', 'MatchType', 'Status', 'MaxCPC', 'DestURL', 'Updated'));
	}
	
	function GetListByAdGroup($intInAdGroupID) {
		if (!is_numeric($intInAdGroupID)) {
			return false;
		}
		
		$strSQL = 'SELECT ppc_keywords.ID, ppc_keywords.KeywordID, MatchType, Status, MaxCPC, DestURL, keyword_tracker_keywords.Keyword FROM
					ppc_keywords LEFT JOIN keyword_tracker_keywords ON ppc_keywords.KeywordID = keyword_tracker_keywords.ID
					WHERE (AdGroupID = ' . $intInAdGroupID . ')
					ORDER BY Status DESC';
		$this->Select($strSQL);
	}
	
	function Exists($intInAdGroupID, $intInKeywordID, $intInMatchType) {
		if (!is_numeric($intInAdGroupID) || !is_numeric($intInKeywordID) || !is_numeric($intInMatchType)) {
			return false;
		}
		
		$strSQL = 'SELECT ID FROM ppc_keywords WHERE (AdGroupID = ' . $intInAdGroupID . ' AND KeywordID = ' . $intInKeywordID . ' AND MatchType = ' . $intInMatchType . ')';
		$this->Select($strSQL);
	}

}

?>