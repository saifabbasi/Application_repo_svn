<?php

class TrackerStats extends DBObject {

	function __construct() {
		parent::__construct('adpalace_user_add_network_subid', 'ID', array('UserID', 'Name'));
	}
	
	function GetRevenue($intInUserID, $inStartDate = '', $inEndDate = '') {
		if (!is_numeric($intInUserID)) {
			return false;
		}
		if (!empty($inStartDate)) {
			$strWhere = 'Stat_Date >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND Stat_Date <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
			
			if (strlen($strWhere) > 1) {
				$strWhere = ' AND (' . $strWhere . ') ';
			}
		}
		
		$strSQL = 'SELECT SUM(REVENUE) AS NetRevenue
					FROM adpalace_user_aff_network_subid
					WHERE (UserID = ' . $intInUserID . ')';
		$strSQL .= $strWhere;
		$this->Select($strSQL);
	}
	
	function GetCost($intInUserID, $inStartDate = '', $inEndDate = '') {
		if (!is_numeric($intInUserID)) {
			return false;
		}
		if (!empty($inStartDate)) {
			$strWhere = 'ppc_keywords_stats.StatDate >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND ppc_keywords_stats.StatDate <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
			
			if (strlen($strWhere) > 1) {
				$strWhere = ' AND (' . $strWhere . ') ';
			}
		}
		
		$strSQL = 'SELECT SUM(Cost) AS NetCost FROM ((ppc_keywords_stats
					LEFT JOIN ppc_keywords ON ppc_keywords_stats.KeywordID = ppc_keywords.ID)
					LEFT JOIN ppc_adgroups ON ppc_keywords.AdGroupID = ppc_adgroups.ID)
					LEFT JOIN ppc_campaigns ON ppc_adgroups.CampaignID = ppc_campaigns.ID
					WHERE (ppc_campaigns.UserID = ' . $intInUserID . ') ';
		$strSQL .= $strWhere;
		$this->Select($strSQL);
	}

}

?>