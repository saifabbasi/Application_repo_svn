<?php

class PPCAdGroups extends DBObject {

	function __construct() {
		parent::__construct('ppc_adgroups', 'ID', array('CampaignID', 'Name', 'Updated'));
	}
	
	function Exists($intInCampaignID, $strInName) {
		if (!is_numeric($intInCampaignID)) {
			return false;
		}
		
		if (strlen($strInName) < 1) {
			return false;
		}
		
		$strSQL = 'SELECT ID FROM ppc_adgroups WHERE (CampaignID = ' . $intInCampaignID . ' AND Name = \'' . $this->FixString($strInName) . '\')';
		$this->Select($strSQL);
	}
	
	function GetListByCampaign($intInCampaignID) {
		if (!is_numeric($intInCampaignID)) {
			return false;
		}
		
		$strSQL = 'SELECT ID, Name FROM ppc_adgroups WHERE (CampaignID = ' . $intInCampaignID . ')';
		$this->Select($strSQL);
	}
	
	function GetStatListByCampaign($intInCampaignID, $inStartDate, $inEndDate) {
		if (!is_numeric($intInCampaignID)) {
			return false;
		}
		
		if (!empty($inStartDate)) {
			$strWhere = 'StatDate >= \'' . date('Y-m-d', strtotime($inStartDate)) . '\'';
			
			if (!empty($inEndDate)) {
				$strWhere .= ' AND StatDate <= \'' . date('Y-m-d', strtotime($inEndDate)) . '\'';
			}
			
			if (strlen($strWhere) > 1) {
				$strWhere = ' (' . $strWhere . ') ';
			}
		}
		$strSQL = 'SELECT ppc_adgroups.ID, ppc_adgroups.Name, 
					FROM ((ppc_adgroups LEFT JOIN ppc_keywords ON ppc_adgroups.ID = ppc_keywords.AdGroupID)
						LEFT JOIN ppc_campaigns ON ppc_adgroups.CampaignID = ppc_campaigns.ID)
							LEFT JOIN (SELECT KeywordID, SUM(Impressions) AS NetImpr, SUM(Clicks) AS NetClicks, (SUM(Cost)/SUM(Clicks)) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS NetCost, AVG(Pos) AS AvgPos
										FROM ppc_keywords_stats WHERE ' . $strWhere . '
						LEFT JOIN ppc_keywords_stats ON ppc_keywords.ID = ppc_keywords_stats.KeywordID
						
						WHERE (CampaignID = ' . $intInCampaignID . ')';
		$strSQL .= $strWhere;
		$strSQL .= 'GROUP BY ID, Name';

		$this->Select($strSQL);
	}

}

?>