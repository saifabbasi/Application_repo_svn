<?php
		
Class Network_Stats {
	Protected $_db = false;
	
	Public $User_ID;
	Public $Total;
	
	Public Function __construct()
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		$this->Total = $this->GetEmptyStats();
	}
	
	Public Function LoadPPCStats($NetworkID)
	{
		$Temp = $this->GetAllPPCStats($NetworkID);
		$this->AddToTotal($Temp);
	}
	
	Public Function AddToTotal($Stats)
	{
		foreach($this->Total as $Key=>$Value)
		{
			if(isset($Stats->{$Key}))
				$this->Total->{$Key} += $Stats->{$Key};
		}
	}
	
	Public Function GetPPCStats($Provider, $ID)
	{
		$Sql = 'SELECT bevomedia_ppc_campaigns.accountId, SUM(Impressions) AS Impressions, SUM(Clicks) AS Clicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS Cost, AVG(Pos) AS AvgPos
				FROM ((bevomedia_ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON bevomedia_ppc_keywords.id = bevomedia_ppc_keywords_stats.keywordId)
				LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.adGroupId = bevomedia_ppc_adgroups.id)
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.campaignId = bevomedia_ppc_campaigns.id
				WHERE (bevomedia_ppc_campaigns.providerType = ' . $Provider . ' AND bevomedia_ppc_campaigns.accountId = ' . $ID . ') GROUP BY accountId';
		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			return $this->GetEmptyStats();
		return $Output;
	}
	
	Public Function GetAllPPCStats($Provider, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		
		$Sql = 'SELECT bevomedia_ppc_campaigns.AccountID, SUM(Impressions) AS Impressions, SUM(Clicks) AS Clicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS Cost, AVG(Pos) AS AvgPos
				FROM ((bevomedia_ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON bevomedia_ppc_keywords.id = bevomedia_ppc_keywords_stats.KeywordID)
				LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.id)
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.id
				WHERE (bevomedia_ppc_campaigns.ProviderType = ' . $Provider . ') ' . $DateAppendSql . ' GROUP BY ProviderType';
		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			return $this->GetEmptyStats();

		return $Output;
	}
	
	Public Function GetMonthToDateCostForUser($UserID)
	{
		$StartDate = date('Y-m-01');
		$EndDate = date('Y-m-d');
		
		$Output = 0;
		for($i=1; $i<4; $i++)
		{
			$Temp = $this->GetAllPPcStatsForUser($i, $UserID, $StartDate, $EndDate);
			$Output += $Temp->cost;
		}
		
		return $Output;
	}
	
	Public Function GetAllPPCAccountStatsForUser($Provider, $UserID, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		
		$Sql = 'SELECT ppc_campaigns.AccountID, SUM(Impressions) AS Impressions, SUM(Clicks) AS Clicks, AVG(CPC) AS AvgCPC, AVG(CPM) AS AvgCPM, SUM(Cost) AS Cost, AVG(Pos) AS AvgPos
				FROM ((bevomedia_ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON bevomedia_ppc_keywords.id = bevomedia_ppc_keywords_stats.KeywordID)
					LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.id)
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = ppc_campaigns.id
				WHERE (bevomedia_ppc_campaigns.ProviderType = ' . $Provider . ' AND bevomedia_ppc_campaigns.UserID = ' . $UserID . ') ' . $DateAppendSql . ' GROUP BY ProviderType';
		$Output = $this->_db->fetchAll($Sql);
		if(!$Output)
			return array();
		
			
		return $Output;
	}
	
	
	Public Function GetAllPPCStatsForUser($Provider, $UserID, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		
		$Sql = 'SELECT bevomedia_ppc_campaigns.AccountID, SUM(Impressions) AS impressions, SUM(Clicks) AS clicks, AVG(CPC) AS avgCpc, AVG(CPM) AS avgCpm, SUM(Cost) AS cost, AVG(Pos) AS avgPos
				FROM ((bevomedia_ppc_keywords LEFT JOIN bevomedia_ppc_keywords_stats ON bevomedia_ppc_keywords.id = bevomedia_ppc_keywords_stats.KeywordID)
					LEFT JOIN bevomedia_ppc_adgroups on bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.id)
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.id
				WHERE (bevomedia_ppc_campaigns.ProviderType = ' . $Provider . ' AND bevomedia_ppc_campaigns.user__id = ' . $UserID . ') ' . $DateAppendSql . ' GROUP BY ProviderType';
		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			return $this->GetEmptyStats();

		return $Output;
	}
	
	Public Function GetAllAdGroupsForCampaign($CampaignID)
	{
		$Sql = "SELECT 
					AdGroups.id as ID, 
					AdGroups.name AS Name 
				FROM 
					`bevomedia_ppc_adgroups` AdGroups 
				WHERE 
					AdGroups.campaignId = $CampaignID ";
		
		$Output = $this->_db->fetchAll($Sql);

		if(!$Output)
			return array();

		return $Output;
	}
	
	Public Function GetContentMatchStats($AdGroupID, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		$Sql = "SELECT 
					SUM(impressions) AS Impressions,
					SUM(clicks) AS Clicks,
					AVG(cpc) AS AvgCPC,
					SUM(cost) as Cost,
					AVG(pos) as Pos
				FROM bevomedia_ppc_contentmatch_stats
				WHERE adgroupId = $AdGroupID
				$DateAppendSql ";
				
		$Output = $this->_db->fetchRow($Sql);
		
		if(!$Output || !$Output->Clicks)
			return false;
		return $Output;
		
	}
		
	Public Function GetAllKeywordsForAdGroup($AdGroupID)
	{
		$Sql = "SELECT 
					Keywords.id AS id, 
					Keyword.keyword AS name,
					Keywords.matchType AS MatchType
				FROM
					`bevomedia_ppc_keywords` Keywords
				LEFT JOIN
					bevomedia_keyword_tracker_keywords Keyword ON Keyword.id = Keywords.keywordId
				WHERE 
					Keywords.adGroupId = $AdGroupID ";
		
		$Output = $this->_db->fetchAll($Sql);
		foreach($Output as $Key=>$Row)
		{
			$Temp = $Row->name;
			if($Row->MatchType == 2)
				$Temp = '[' . $Temp . ']';
			if($Row->MatchType == 1)
				$Temp = '"' . $Temp . '"';
				
			$Output[$Key]->formattedName = $Temp;
		}

		if(!$Output)
			return array();

		return $Output;
	}
	
	Public Function GetSearchQueryStats($AdGroupID, $StartDate = false, $EndDate = false)
	{
		$Output = array();
		$Keywords = $this->GetAllKeywordsForAdGroup($AdGroupID);
		foreach($Keywords as $Keyword)
		{
			$Temp = new stdClass();
			$Temp->Keyword = $Keyword;
			$Temp->Stats = $this->GetSearchQueryStatsForKeyword($AdGroupID, $Keyword->name, $StartDate, $EndDate);
			$Output[] = $Temp;
		}
		return $Output;
	}
	
	Public Function GetSearchQueryStatsForKeyword($AdGroupID, $Keyword, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		
		$Sql = "SELECT 
					Stats.id as id, 
					Stats.query AS name, 
					Stats.keyword AS keyword,
					SUM(Stats.clicks) AS clicks, 
					SUM(Stats.impressions) AS impressions, 
					AVG(Stats.ctr) as ctr,
					AVG(Stats.cpc) AS avgCpc,
					SUM(Stats.cost) AS cost,
					Stats.statdate as date,
					Stats.ppcAdvariations_id as creativeId
				FROM 
					bevomedia_ppc_advariations AdVars 
				INNER JOIN 
					bevomedia_ppc_search_query Stats ON Stats.ppcAdvariations_id = AdVars.apiAdId 
				WHERE 
					AdVars.AdGroupID = $AdGroupID AND Stats.keyword = \"$Keyword\"
					$DateAppendSql
				GROUP BY Stats.query, Stats.keyword
				ORDER BY Stats.keyword";
		$Output = array();
		$Rows = $this->_db->fetchAll($Sql);
		
		foreach($Rows as $Row)
		{
			$Row->adVarPreview = $this->GenerateAdVarPreview($Row->creativeId);
			$Output[] = $Row;
		}
		//if(!$Output)
			//$Output = array(); //array($this->GetEmptyStats());
		return $Output;
	}
	
	Public Function GetAdVariation($ID)
	{
		$Sql = "SELECT * FROM bevomedia_ppc_advariations WHERE apiAdId = $ID";
		return $this->_db->fetchRow($Sql);
	}
	
	Public Function GenerateAdVarPreview($AdVarID)
	{
		$Ad = $this->GetAdVariation($AdVarID);
		if(!$Ad)
			return false;
		$Output = "
				<a style='text-decoration: underline; font-size: 123.1%; color: rgb(0, 0, 222);' href='$Ad->url'>$Ad->title</a>
				<br/>
				<span>$Ad->description</span><br>
				<span style='color: rgb(0, 128, 0);'><span style='font-weight: bolder;'>$Ad->displayUrl</span></span>
				";
		return $Output;
	}
	
	Public Function GetAllKeywordStatsForAdGroup($AdGroupID, $StartDate = false, $EndDate = false)
	{
		$Output = array();
		$AdGroups = $this->GetAllKeywordsForAdGroup($AdGroupID);
		
		foreach($AdGroups as $AdGroup)
		{
			$OutputRow = $this->GetKeywordStats($AdGroup->id, $StartDate, $EndDate);
			$OutputRow->id = $AdGroup->id;
			$OutputRow->name = $AdGroup->name;
			$OutputRow->formattedName = $AdGroup->formattedName;
			$Output[] = $OutputRow;
		}

		if(!$Output)
			return array();

		return $Output;
	}
	
	Public Function GetAllAdVariationStatsForAdGroup($AdGroupID, $StartDate = false, $EndDate = false)
	{
		$Output = array();
		$AdVars = $this->GetAllAdVariationsForAdGroup($AdGroupID);
		
		foreach($AdVars as $AdVar)
		{
			$OutputRow = $this->GetAdVariationStats($AdVar->id, $StartDate, $EndDate);
			$OutputRow->ID = $AdVar->id;
			$OutputRow->API_ID = $AdVar->api_ad_id;
			$OutputRow->title = $AdVar->title;
			$OutputRow->destinationUrl = $AdVar->destinationUrl;
			$OutputRow->displayUrl = $AdVar->displayUrl;
			$OutputRow->description = $AdVar->description;
			$Output[] = $OutputRow;
		}

		if(!$Output)
			return array();

		return $Output;
	}
		
	Public Function GetAllAdVariationsForAdGroup($AdGroupID)
	{
		$Sql = "SELECT 
					AdVars.id AS id, 
					AdVars.apiAdId AS api_ad_id, 
					AdVars.adGroupId AS adGroupId,
					AdVars.title AS title,
					AdVars.url as destinationUrl,
					AdVars.displayUrl as displayUrl,
					AdVars.description as description,
					AdVars.status as status
				FROM
					`bevomedia_ppc_advariations` AdVars
				WHERE 
					AdVars.adGroupId = $AdGroupID ";
		
		$Output = $this->_db->fetchAll($Sql);

		if(!$Output)
			return array();

		return $Output;
	}

	Public Function GetAdVariationStats($AdVariationID, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		
		$Sql = "SELECT 
					AdVars.id as ID, 
					SUM(Stats.clicks) AS clicks, 
					SUM(Stats.impressions) AS impressions, 
					(SUM(Stats.clicks) / SUM(Stats.impressions)) as ctr,
					(SUM(Stats.cost) / SUM(Stats.clicks)) AS avgCpc,
					SUM(Stats.cost) AS cost,
					AVG(Stats.pos) as pos
				FROM 
					`bevomedia_ppc_advariations` AdVars
				LEFT JOIN 
					bevomedia_ppc_advariations_stats Stats ON Stats.advariationsId = AdVars.id 
				WHERE 
					AdVars.id = $AdVariationID
					$DateAppendSql
				GROUP BY AdVars.id";

		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			$Output = $this->GetEmptyStats();
			
		if(!$Output->ctr)
			$Output->ctr = 0;
			
		return $Output;
	}
	
	
	Public Function GetAllAdGroupStatsForCampaign($CampaignID, $StartDate = false, $EndDate = false)
	{
		$Output = array();
		$AdGroups = $this->GetAllAdGroupsForCampaign($CampaignID);
		
		foreach($AdGroups as $AdGroup)
		{
			$OutputRow = $this->GetAdGroupStats($AdGroup->ID, $StartDate, $EndDate);
			$OutputRow->id = $AdGroup->ID;
			$OutputRow->name = $AdGroup->Name;
			$Output[] = $OutputRow;
		}

		if(!$Output)
			return array();

		return $Output;
	}
	
	Public Function GetAllCampaignsForAccount($AccountID, $ProviderType)
	{
		$Sql = "SELECT 
					Campaigns.id as ID, 
					Campaigns.name AS Name,
					Campaigns.status AS Status
				FROM 
					`bevomedia_ppc_campaigns` Campaigns
				WHERE 
					Campaigns.accountId = $AccountID 
				AND
					Campaigns.providerType = $ProviderType";
		
		$Output = $this->_db->fetchAll($Sql);

		if(!$Output)
			return array();
			
		return $Output;
	}
	
	Public Function GetAllCampaignStatsForAccount($AccountID, $ProviderType, $StartDate = false, $EndDate = false)
	{
		$Output = array();
		$Campaigns = $this->GetAllCampaignsForAccount($AccountID, $ProviderType);

		foreach($Campaigns as $Campaign)
		{
			$OutputRow = $this->GetCampaignStats($Campaign->ID, $StartDate, $EndDate);
			$OutputRow->id = $Campaign->ID;
			$OutputRow->name = $Campaign->Name;
			$OutputRow->status= $Campaign->Status;
			$Output[] = $OutputRow;
		}

		if(!$Output)
			return array();

		return $Output;
	}
	
	Public Function GetAdGroupStats($AdGroupID, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		
		$Sql = "SELECT 
					AdGroups.id as id, 
					AdGroups.name AS name, 
					SUM(Stats.clicks) AS clicks, 
					SUM(Stats.impressions) AS impressions, 
					(SUM(Stats.clicks) / SUM(Stats.impressions)) as ctr,
					(SUM(Stats.cost) / SUM(Stats.clicks)) AS avgCpc,
					SUM(Stats.cost) AS cost
				FROM 
					`bevomedia_ppc_adgroups` AdGroups 
				LEFT JOIN 
					bevomedia_ppc_keywords Keywords ON AdGroups.id = Keywords.adGroupId
				LEFT JOIN 
					bevomedia_ppc_keywords_stats Stats ON Stats.keywordId = Keywords.id 
				WHERE 
					AdGroups.id = $AdGroupID
					$DateAppendSql
				GROUP BY AdGroups.id";

		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			$Output = $this->GetEmptyStats();
		return $Output;
	}
	
	Public Function GetKeywordStats($KeywordID, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		
		$Sql = "SELECT 
					Keywords.id as id, 
					SUM(Stats.clicks) AS clicks, 
					SUM(Stats.impressions) AS impressions, 
					(SUM(Stats.clicks) / SUM(Stats.impressions)) as ctr,
					(SUM(Stats.cost) / SUM(Stats.clicks)) AS avgCpc,
					SUM(Stats.cost) AS cost
				FROM 
					`bevomedia_ppc_keywords` Keywords
				LEFT JOIN 
					bevomedia_ppc_keywords_stats Stats ON Stats.keywordId = Keywords.id 
				WHERE 
					Keywords.id = $KeywordID
					$DateAppendSql
				GROUP BY Keywords.id";
		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			$Output = $this->GetEmptyStats();
		return $Output;
	}
	
	Public Function GetCampaignStats($CampaignID, $StartDate = false, $EndDate = false)
	{
		$DateAppendSql = '';
		if($StartDate !== false)
		{
			$DateAppendSql = ' AND statDate >= "' . date('Y-m-d', strtotime($StartDate)) . '"';
			if($EndDate !== false)
			{
				$DateAppendSql .= ' AND statDate <= "' . date('Y-m-d', strtotime($EndDate)) . '"';
			}
		}
		
		$Sql = "SELECT 
					Campaigns.id as id, 
					Campaigns.name AS name, 
					SUM(Stats.clicks) AS clicks, 
					SUM(Stats.impressions) AS impressions, 
					(SUM(Stats.clicks) / SUM(Stats.impressions)) as ctr,
					(SUM(Stats.cost) / SUM(Stats.clicks)) AS avgCpc,
					SUM(Stats.cost) AS cost
				FROM
					bevomedia_ppc_campaigns Campaigns
				LEFT JOIN 
					bevomedia_ppc_adgroups AdGroups ON Campaigns.id = AdGroups.campaignId
				LEFT JOIN 
					bevomedia_ppc_keywords Keywords ON AdGroups.id = Keywords.adGroupId
				LEFT JOIN 
					bevomedia_ppc_keywords_stats Stats ON Stats.keywordId = Keywords.id 
				WHERE 
					Campaigns.id = $CampaignID
					$DateAppendSql
				GROUP BY Campaigns.id";

		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			$Output = $this->GetEmptyStats();
		return $Output;
	}
	
	Public Function GetProviderTypeFromAdGroupID($AdGroupID)
	{
		$Sql = "SELECT 
					Campaign.providerType AS ProviderType
				FROM `bevomedia_ppc_adgroups` AdGroups 
				LEFT JOIN 
					bevomedia_ppc_campaigns Campaign ON AdGroups.campaignID = Campaign.id
				WHERE 
					AdGroups.id = $AdGroupID
				";
		$Row = $this->_db->fetchRow($Sql);
		return $Row->ProviderType;
	}
	
	Public Function GetProviderTypeFromCampaignID($CampaignID)
	{
		$Sql = "SELECT 
					Campaign.providerType AS ProviderType
				FROM
					bevomedia_ppc_campaigns Campaign
				WHERE 
					Campaign.id = $CampaignID
				";
		$Row = $this->_db->fetchRow($Sql);
		if(!$Row)
			return false;
		return $Row->ProviderType;
	}
	
	Public Function GetParentsForAdGroup($AdGroupID)
	{
		$Sql = "SELECT 
					Campaign.name AS CampaignName,
					Campaign.id AS CampaignID,
					Campaign.providerType AS ProviderType,
					Campaign.accountID as AccountID
				FROM
					bevomedia_ppc_adgroups AdGroup
				LEFT JOIN
					bevomedia_ppc_campaigns Campaign ON Campaign.id = AdGroup.CampaignID
				WHERE 
					AdGroup.id = $AdGroupID
				";
		$Row = $this->_db->fetchRow($Sql);
		if(!$Row)
			return false;
		return $Row;
	}
	
	Public Function GetParentsForCampaign($CampaignID)
	{
		$Sql = "SELECT 
					Campaign.name AS CampaignName,
					Campaign.id AS CampaignID,
					Campaign.providerType AS ProviderType,
					Campaign.accountId as AccountID
				FROM
					bevomedia_ppc_campaigns Campaign 
				WHERE 
					Campaign.id = $CampaignID
				";
		$Row = $this->_db->fetchRow($Sql);
		if(!$Row)
			return false;
		return $Row;
	}
	
	Public Function GetAccountName($AccountID, $ProviderType)
	{
		$ProviderArr = array(1=>'accounts_adwords', 2=>'accounts_yahoo', 3=>'accounts_msnadcenter');
		$Sql = "SELECT 
					Account.username AS Name
				FROM
					bevomedia_".($ProviderArr[$ProviderType])." as Account
				WHERE 
					Account.id = $AccountID
				";
		$Row = $this->_db->fetchRow($Sql);
		if(!$Row)
			return false;
		return $Row->Name;
	}
	
	Public Function GetCampaignName($CampaignID)
	{
		$Sql = "SELECT 
					Campaign.name AS Name
				FROM
					bevomedia_ppc_campaigns Campaign
				WHERE 
					Campaign.id = $CampaignID
				";
		$Row = $this->_db->fetchRow($Sql);
		if(!$Row)
			return false;
		return $Row->Name;
	}
		
	Public Function GetAdGroupName($AdGroupID)
	{
		$Sql = "SELECT 
					AdGroup.name AS Name
				FROM
					bevomedia_ppc_adgroups AdGroup
				WHERE 
					AdGroup.id = $AdGroupID
				";
		$Row = $this->_db->fetchRow($Sql);
		return $Row->Name;
	}
	
	
	Public Function GetEmptyStats($Pos = false)
	{
		$Output = new stdClass();
		
		$Output->impressions = 0;
		$Output->clicks = 0;
		$Output->cost = 0;
		$Output->ctr = 0;
		$Output->avgCpc = 0;
		if($Pos === true)
			$Output->pos = 0;
		
		return $Output;
	}
	
	Public Function GetTopBarChartCosts($UserID, $DateStart = null, $DateEnd = null)
	{
		if ($DateStart==null)
		{
			$DateStart = date('Y').'-'.date('m').'-1';
			$DateStart = date('Y-m-d', strtotime($DateStart));
			$DateEnd = date('Y-m-d');
		}
		
		$Sql = "SELECT statDate, bevomedia_ppc_campaigns.AccountID, SUM( Impressions ) AS impressions, SUM( Clicks ) AS clicks, AVG( CPC ) AS avgCpc, AVG( CPM ) AS avgCpm, SUM( Cost ) AS cost, AVG( Pos ) AS avgPos
				FROM (
				(
				bevomedia_ppc_keywords
				LEFT JOIN bevomedia_ppc_keywords_stats ON bevomedia_ppc_keywords.id = bevomedia_ppc_keywords_stats.KeywordID
				)
				LEFT JOIN bevomedia_ppc_adgroups ON bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.id
				)
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.id
				WHERE (
				bevomedia_ppc_campaigns.user__id =?
				)
				AND statDate >=  ?
				AND statDate <=  ?
				GROUP BY statDate
				";
		$Costs = $this->_db->fetchAll($Sql, array($UserID, $DateStart, $DateEnd));
		$ReturnArray = array();
		
		foreach ($Costs as $Cost) 
		{
			$ReturnArray[$Cost->statDate] = $Cost;
		}
		
		return $ReturnArray;
	}
	
	Public Function GetTopBarChartEarning($UserID, $DateStart = null, $DateEnd = null)
	{
		if ($DateStart==null)
		{
			$DateStart = date('Y').'-'.date('m').'-1';
			$DateStart = date('Y-m-d', strtotime($DateStart));
			$DateEnd = date('Y-m-d');
		}
		
		$Sql = "SELECT
						S.id,
						SUM( S.clicks ) AS CLICKS,
						SUM( S.conversions ) AS CONVERSIONS,
						SUM(S.revenue) AS REVENUE,
						(SUM(S.REVENUE)*1000) AS ECPM,
						S.statDate
					FROM bevomedia_user_aff_network_subid S
					
					WHERE S.user__id = ?
					AND S.statDate >= ?
					AND S.statDate <= ?					
					GROUP BY S.statDate
				";
		
		$Earnings = $this->_db->fetchAll($Sql, array($UserID, $DateStart, $DateEnd));
		$ReturnArray = array();
		
		foreach ($Earnings as $Earning) 
		{
			$ReturnArray[$Earning->statDate] = $Earning;
		}
		
		return $ReturnArray;
	}
	
}
?>