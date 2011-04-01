<?php

/**
 * User_Stats Class
 */

/**
 * User_Stats Class
 * 
 * User_Stats Class
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */

Class User_Stats {
	
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $user__id
	 */
	Public $user__id;
	
	/**
	 * @var Array $Total
	 */
	Public $Total;
	
	/**
	 * Constructor
	 *
	 * @param Integer $ID
	 */
	Public Function __construct($ID = false)
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		
		$this->Total = $this->GetEmptyStats();

		if($ID !== false)
		{
			$this->user__id = $ID;
			$this->GetAggregateStats();
		}
	}
	
	/**
	 * Populates this object with statistic information from Adwords, Yahoo and MSN PPC Publishers.
	 */
	Public Function GetAggregateStats()
	{
		$Temp = $this->GetAllAdwordsStats();
		$this->AdwordsTotal = $Temp;
		$this->AddToTotal($Temp);
		
		$Temp = $this->GetAllYahooStats();
		$this->YahooTotal = $Temp;
		$this->AddToTotal($Temp);
		
		$Temp = $this->GetAllMSNStats();
		$this->MSNTotal = $Temp;
		$this->AddToTotal($Temp);
	}
	
	/**
	 * Adds values from the provided $Stats array to this $Total.
	 *
	 * @param Array $Stats
	 */
	Public Function AddToTotal($Stats)
	{
		foreach($this->Total as $Key=>$Value)
		{
			$this->Total->{$Key} += $Stats->{$Key};
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	Public Function GetAllAdwordsStats()
	{
		return $this->GetAllPPCStats(1);
	}
		
	Public Function GetAllYahooStats()
	{
		return $this->GetAllPPCStats(2);
	}
		
	Public Function GetAllMSNStats()
	{
		return $this->GetAllPPCStats(3);
	}
	
	Public Function GetAdwordsStats($ID)
	{
		return $this->GetPPCStats(1, $ID);
	}
	
	Public Function GetYahooStats($ID)
	{
		return $this->GetPPCStats(2, $ID);
	}
	
	Public Function GetMSNStats($ID)
	{
		return $this->GetPPCStats(3, $ID);
	}
	
	Public Function GetPPCStats($Provider, $ID)
	{
		$Sql = 'SELECT bevomedia_ppc_campaigns.accountId, SUM(Impressions) AS Impressions, SUM(Clicks) AS Clicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS Cost, AVG(Pos) AS AvgPos
				FROM ((bevomedia_ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON bevomedia_ppc_keywords.id = bevomedia_ppc_keywords_stats.KeywordID)
				LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.adGroupId = bevomedia_ppc_adgroups.id)
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.campaignId = bevomedia_ppc_campaigns.id
				WHERE (bevomedia_ppc_campaigns.user__id = ' . $this->user__id . ' AND bevomedia_ppc_campaigns.providerType = ' . $Provider . ' AND bevomedia_ppc_campaigns.accountId = ' . $ID . ') GROUP BY AccountID';
		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			return $this->GetEmptyStats();
		return $Output;
	}
	
	Public Function GetAllPPCStats($Provider)
	{
		$Sql = 'SELECT bevomedia_ppc_campaigns.accountId, SUM(Impressions) AS Impressions, SUM(Clicks) AS Clicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS Cost, AVG(Pos) AS AvgPos
				FROM ((bevomedia_ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON bevomedia_ppc_keywords.id = bevomedia_ppc_keywords_stats.KeywordID)
				LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.adGroupId = bevomedia_ppc_adgroups.id)
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.campaignId = bevomedia_ppc_campaigns.id
				WHERE (bevomedia_ppc_campaigns.user__id = ' . $this->user__id . ' AND bevomedia_ppc_campaigns.providerType = ' . $Provider . ') GROUP BY AccountID';
		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			return $this->GetEmptyStats();

		return $Output;
	}
	
	Public Function GetEmptyStats()
	{
		$Output = new stdClass();
		$Output->Impressions = 0;
		$Output->Clicks = 0;
		$Output->Cost = 0;
		
		return $Output;
	}
}
?>