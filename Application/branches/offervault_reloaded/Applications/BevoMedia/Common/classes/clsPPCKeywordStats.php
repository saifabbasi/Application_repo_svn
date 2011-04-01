<?php

class PPCKeywordStats extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_ppc_keywords_stats', 'ID', array('KeywordID', 'Impressions', 'Clicks', 'CPC', 'CPM', 'Cost', 'Pos', 'StatDate'));
	}
	
	function DateStatExists($strInStatDate, $intInKeywordID) {
		if ($strInStatDate == '' || !is_numeric($intInKeywordID)) {
			return false;
		}
		$strSQL = 'SELECT ID FROM bevomedia_ppc_keywords_stats WHERE (StatDate = \'' . $strInStatDate . '\' AND KeywordID = ' . $intInKeywordID . ')';
		$this->Select($strSQL);
	}
	
	function GetKeywordsStats($strInKeywordIDs, $inStartDate, $inEndDate) {
		$strWhere = '';
		if (strlen($strInKeywordIDs) > 1) {
			$strWhere = ' (KeywordID IN (' . $strInKeywordIDs . ')) AND ';
		}
		if (!empty($inStartDate)) {
			$strWhere .= 'StatDate >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND StatDate <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
		}
		$strSQL = 'SELECT KeywordID, SUM(Impressions) AS NetImpr, SUM(Clicks) AS NetClicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS NetCost, AVG(Pos) AS AvgPos
					FROM bevomedia_ppc_keywords_stats WHERE ' . $strWhere . ' 
					GROUP BY KeywordID';
		$this->Select($strSQL);
	}
	
	function GetStatsByAdGroupIDs($strInIDs, $inStartDate, $inEndDate) {
		if (!empty($inStartDate)) {
			$strWhere = 'StatDate >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND StatDate <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
			
			if (strlen($strWhere) > 1) {
				$strWhere = ' AND (' . $strWhere . ') ';
			}
		}
		$strSQL = 'SELECT bevomedia_ppc_keywords.adGroupId, SUM(Impressions) AS NetImpr, SUM(Clicks) AS NetClicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS NetCost, AVG(Pos) AS AvgPos
					FROM ((ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON ppc_keywords.ID = bevomedia_ppc_keywords_stats.KeywordID)
						LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.adGroupId = bevomedia_ppc_adgroups.ID)
						LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
						WHERE (AdGroupID IN (' . $strInIDs . '))';
		$strSQL .= $strWhere;
		$strSQL .= 'GROUP BY AdGroupID';
		$this->Select($strSQL);
	}
	
	function GetStatsByAdGroups($intInUserID, $inStartDate, $inEndDate) {
		if (!empty($inStartDate)) {
			$strWhere = 'StatDate >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND StatDate <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
			
			if (strlen($strWhere) > 1) {
				$strWhere = ' AND (' . $strWhere . ') ';
			}
		}
		$strSQL = 'SELECT bevomedia_ppc_keywords.adGroupId, SUM(Impressions) AS NetImpr, SUM(Clicks) AS NetClicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS NetCost, AVG(Pos) AS AvgPos
					FROM ((ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON ppc_keywords.ID = bevomedia_ppc_keywords_stats.KeywordID)
						LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.adGroupId = bevomedia_ppc_adgroups.ID)
						LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
						WHERE (bevomedia_ppc_campaigns.user__id = ' . $intInUserID . ')';
		$strSQL .= $strWhere;
		$strSQL .= 'GROUP BY AdGroupID';
		$this->Select($strSQL);
	}
	
	function GetStatsByCampaign($intInCampID, $inStartDate, $inEndDate) {
		if (!is_numeric($intInCampID)) {
			return false;
		}
		$strWhere = '';
		
		if (!empty($inStartDate)) {
			$strWhere = 'StatDate >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND StatDate <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
			
			if (strlen($strWhere) > 1) {
				$strWhere = ' AND (' . $strWhere . ') ';
			}
		}
		$strSQL = 'SELECT bevomedia_ppc_keywords.adGroupId, SUM(Impressions) AS NetImpr, SUM(Clicks) AS NetClicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS NetCost, AVG(Pos) AS AvgPos
					FROM ((ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON ppc_keywords.ID = bevomedia_ppc_keywords_stats.KeywordID)
						LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.adGroupId = bevomedia_ppc_adgroups.ID)
						LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
						WHERE (CampaignID = ' . $intInCampID . ')';
		$strSQL .= $strWhere;
		$strSQL .= 'GROUP BY AdGroupID';
		$this->Select($strSQL);
	}
	
	function GetStatsByAccount($intInUserID, $intInProvider = 0, $intInaccountId = 0, $inStartDate, $inEndDate) {
		if (!is_numeric($intInaccountId)) {
			return false;
		}
		
		$strWhere = '';
		
		if (!empty($inStartDate)) {
			$strWhere = 'StatDate >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND StatDate <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
			
			if (strlen($strWhere) > 1) {
				$strWhere = ' AND (' . $strWhere . ') ';
			}
		}
		
		if ($intInProvider != 0) {
			$strWhere2 = ' AND bevomedia_ppc_campaigns.ProviderType = ' . $intInProvider . ' ';
		}
		
		if ($intInaccountId != 0) {
			$strWhere2 .= ' AND bevomedia_ppc_campaigns.accountId = ' . $intInaccountId . ' ';
		}
		
		$strSQL = 'SELECT bevomedia_ppc_campaigns.ID, SUM(Impressions) AS NetImpr, SUM(Clicks) AS NetClicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS NetCost, AVG(Pos) AS AvgPos
					FROM ((ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON ppc_keywords.ID = bevomedia_ppc_keywords_stats.KeywordID)
						LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.adGroupId = bevomedia_ppc_adgroups.ID)
						LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
						WHERE (bevomedia_ppc_campaigns.user__id = ' . $intInUserID . ' ' . $strWhere2 . ')'; 
		$strSQL .= $strWhere;
		$strSQL .= 'GROUP BY ID'; 
		$this->Select($strSQL);
	}
	
	function GetStatsByProvider($intInUserID, $intInProvider, $inStartDate, $inEndDate) {
		if (!is_numeric($intInUserID)) {
			return false;
		}
		
				
		$strWhere = '';
		
		if (!empty($inStartDate)) {
			$strWhere = 'StatDate >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND StatDate <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
			
			if (strlen($strWhere) > 1) {
				$strWhere = ' AND (' . $strWhere . ') ';
			}
		}
		$strSQL = 'SELECT bevomedia_ppc_campaigns.accountId, SUM(Impressions) AS NetImpr, SUM(Clicks) AS NetClicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS NetCost, AVG(Pos) AS AvgPos
					FROM ((bevomedia_ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON bevomedia_ppc_keywords.ID = bevomedia_ppc_keywords_stats.KeywordID)
						LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.adGroupId = bevomedia_ppc_adgroups.ID)
						LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
						WHERE (bevomedia_ppc_campaigns.user__id = ' . $intInUserID . ' AND bevomedia_ppc_campaigns.ProviderType = ' . $intInProvider . ')';
		$strSQL .= $strWhere;
		$strSQL .= 'GROUP BY accountId';
		
		$this->Select($strSQL);
	}

}

?>