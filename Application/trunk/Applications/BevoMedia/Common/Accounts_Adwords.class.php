<?php

/**
 * Creates and manages objects of the Accounts_Adwords table.
 *
 */


/**
 * Creates and manages objects of the Accounts_Adwords table.
 * 
 * Creates and manages objects of the Accounts_Adwords table and makes 
 * use of the APIlity Google Adwords API to create new Campaign, Ad Group, Keyword 
 * and Ad Variation objects.
 *  
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */

Class Accounts_Adwords Extends Accounts_PPC_Abstract {

  /**
   * Provider Type value used within local PPC Campaign tables.
   * Google Adwords = 1
   *
   * @var Integer $ProviderType
   */
  Protected $providerType = 1;
  
  Protected $API = false;

  /**
   * @var String $_table_name
   */
  Protected $_table_name = 'bevomedia_accounts_adwords';


	/**
	 * Return APIlity Google Adwords API.
	 *
	 * @return AdWordsUser
	 */
	Public Function GetAPI()
	{
		require_once (PATH . 'adwords_api/Google/Api/Ads/AdWords/Lib/AdWordsUser.php');
		$path = dirname(__FILE__) . '/../adwords_api/Google';
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		
		//require_once(PATH . 'adwords_api/apility_assist.php');
		if($this->API !== false)
			return $this->API;
		
		if(!isset($this->id))
			return false;
		
		$this->getInfo();
		
		try{	
			$this->API = new AdWordsUser(NULL, $this->username, $this->password, ($this->api_key!='')?$this->api_key:null);
			return $this->API;
		}catch(Exception $e)
		{
			throw new Exception ('Login rejected by provider');
			return false;
		}
	}
	
	/**
	 * Returns stats for the provided $AdID within the date range $StartDate and $EndDate
	 * If the $StartDate and $EndDate values are not specified, 'YESTERDAY' will be used
	 * The date range is inclusive
	 * 
	 * @param float $AdID
	 * @return object
	 */
	Public Function GetAdStatsAPI($AdID, $StartDate = false, $EndDate = false)
	{
		if($StartDate === false)
		{
			$StartDate = date('Ymd', strtotime('YESTERDAY'));
		}
		if($EndDate === false)
		{
			$EndDate = $StartDate;
		}
		$API = $this->GetAPI();
		
		$adGroupAdService = $API->GetAdGroupAdService('v200909');
		
		$selector = new AdGroupAdSelector();
		$selector->adIds = array($AdID);
		
		$statsDate = new DateRange();
		$statsDate->min = $StartDate;
		$statsDate->max = $EndDate;
		
		$statsSelect = new StatsSelector();
		$statsSelect->dateRange = $statsDate;
		
		
		$selector->statsSelector = $statsSelect;
		
		$page = $adGroupAdService->get($selector);
		return $page->entries[0]->stats;
	}

	/**
	 * Return all ads for the specified $AdGroupID
	 * 
	 * @param float $AdGroupID
	 * @return array
	 */
	Public Function GetAdsForAdGroupIdAPI($AdGroupID)
	{
		$API = $this->GetAPI();
		
		$adGroupAdService = $API->GetAdGroupAdService('v200909');
		
		$selector = new AdGroupAdSelector();
		$selector->adGroupIds = array($AdGroupID);
		
		// Get all ads.
		$page = $adGroupAdService->get($selector);
				
		$output = array();
		if(isset($page->entries))
			foreach($page->entries as $entry)
				$output[] = $entry->ad;
		
		return $output;
	}

	/**
	 * Returns stats for the provided $CriterionID in $AdGroupID within the date range $StartDate and $EndDate
	 * If the $StartDate and $EndDate values are not specified, 'YESTERDAY' will be used
	 * The date range is inclusive
	 * 
	 * @param float $AdID
	 * @return object
	 */
	Public Function GetCriterionStatsAPI($AdGroupID, $CriterionID = false, $StartDate = false, $EndDate = false)
	{
		if($StartDate === false)
		{
			$StartDate = date('Ymd', strtotime('YESTERDAY'));
		}
		if($EndDate === false)
		{
			$EndDate = $StartDate;
		}
		
		$API = $this->GetAPI();
		
		$adGroupCriterionService = $API->GetAdGroupCriterionService('v200909');
		
		$selector = new AdGroupCriterionSelector();

		$idFilter = new AdGroupCriterionIdFilter();
		$idFilter->adGroupId = $AdGroupID;
		if($CriterionID)
			$idFilter->criterionId = $CriterionID;
		
		$statsDate = new DateRange();
		$statsDate->min = $StartDate;
		$statsDate->max = $EndDate;
		
		$statsSelect = new StatsSelector();
		$statsSelect->dateRange = $statsDate;
		
		$selector->idFilters = array($idFilter);
		$selector->statsSelector = $statsSelect;
		

		$page = $adGroupCriterionService->get($selector);
		$stats = array();
		if(isset($page->entries))
			foreach($page->entries as $entry)
				if(isset($entry->stats))
					$stats[] = $entry;
		return $stats;
	}
	
	/**
	 * Return an array of campaigns from the api
	 * 
	 * @return array
	 */
	Public Function GetCampaignsAPI()
	{
		$API = $this->GetAPI();
		$campaignService = $API->GetCampaignService('v200909');
		$selector = new CampaignSelector();
		$page = $campaignService->get($selector);
		return $page->entries;
	}
	
	/**
	 * Return if account is recognized on the API server.
	 * If account is recognized, updates the table with Verified equal to '1'.
	 * If account is not recognized, updates the table with Verified equal to '0'.
	 *
	 * @return boolean
	 */
	Public Function VerifyAccountAPI()
	{
		try{
			$Info = $this->GetAPI();
		}catch(Exception $e)
		{
			//print_r($e);
			$Info = false;
		}
		//$Info = $this->API->getAccountInfo();
		if($Info === false)
		{
			$this->Update(array("verified"=>0, "id"=>$this->id));
			return false;
		}else{
			$this->Update(array("verified"=>1, "id"=>$this->id));
			return true;
		}
	}

	/**
	 * Returns error generated by API.
	 *
	 * @return String
	 */
	Public Function GetErrorAPI()
	{
		$API = $this->GetAPI();
		
		if($API->error != false)
			return $API->error;
		
		return false;
	}

	/**
	 * Add a Campaign using the API.
	 */	
	Public Function AddCampaignAPI($Name, $Budget = 5000, $Description = false, $GeoTargets = false, $NegativeKeywords = false, $ContentTargets = false)
	{
		$API = $this->GetAPI();
		
		$campaignService = $API->GetCampaignService('v200909');

		$campaign = new Campaign();
		$campaign->name = $Name;
		$campaign->status = 'ACTIVE';
  		$campaign->biddingStrategy = new ManualCPC();
		
		$budget = new Budget();
		$budget->period = 'DAILY';
		$budget->amount = new Money($Budget * 1000000);
		$budget->deliveryMethod = 'STANDARD';
		$campaign->budget = $budget;
		
		$operation = new CampaignOperation();
		$operation->operand = $campaign;
		$operation->operator = 'ADD';
		
		$operations = array($operation);
		
		// Add campaign.
		$result = $campaignService->mutate($operations);
		
		if (isset($result->value)) {
			foreach ($result->value as $campaign) {
				$this->addCampaignGeoTargets($campaign->id, $GeoTargets);
				if(!empty($NegativeKeywords))
				  $this->addCampaignNegativeKeywords($campaign->id, $NegativeKeywords);
				return $campaign->id;
			}
		} else {
			return false;
		}
	}
	
	Public Function addCampaignNegativeKeywords($campaignId, $negativeKeywords)
	{
		if(empty($negativeKeywords))
		{
			return true;
		}
		$API = $this->GetAPI();
		$campaignCriterionService = $API->GetCampaignCriterionService('v200909');
				
		$operations = array();
		foreach($negativeKeywords as $negativeKeyword)
		{
			if(empty($negativeKeyword))
			  continue;
			$keyword = new Keyword();
			$keyword->text = $negativeKeyword;
			$keyword->matchType = 'BROAD';
			
			// Create negative campaign criterion.
			$negativeCampaignCriterion = new NegativeCampaignCriterion();
			$negativeCampaignCriterion->campaignId = $campaignId;
			$negativeCampaignCriterion->criterion = $keyword;
			
			$operation = new CampaignCriterionOperation();
			$operation->operand = $negativeCampaignCriterion;
			$operation->operator = 'ADD';
			$operations[] = $operation;
		}
		if(empty($operations))
		  return true;	  
		// Add campaign criteria.
		$result = $campaignCriterionService->mutate($operations);
		if(isset($result->value))
		{
			return true;
		}
		return false;
	}
	
	Private Function addCampaignGeoTargets($campaignId, $geoTargets)
	{
		$API = $this->GetAPI();
		$campaignTargetService = $API->GetCampaignTargetService('v200909');

		$geoTargetList = new GeoTargetList();
		$geoTargetList->campaignId = $campaignId;
  		$geoTargetList->targets = array();
  		$targets = array();
  		foreach($geoTargets->countries as $countryTarget)
  		{
  			$temp = new CountryTarget($countryTarget);
  			$temp->TargetType = 'CountryTarget';
  			print_r($temp);
  			$targets[] = $temp;
  		}
  		$geoTargetList->targets = $targets;
  		
		$geoTargetOperation = new CampaignTargetOperation();
		$geoTargetOperation->operand = $geoTargetList;
		$geoTargetOperation->operator = 'SET';
		
		$result = $campaignTargetService->mutate(array($geoTargetOperation));
		
		if(isset($result->value))
		{
			return true;
		}
		return false;
	}

	/*
	 * Add an Ad Group using the API.
	 */
	Public Function AddAdGroupAPI($Name, $CampaignID, $Bid = 1, $AdDistribution = 'Search', $NegativeKeywords = null, $ContentBid = 0)
	{
		$API = $this->GetAPI();
		$adGroupService = $API->GetAdGroupService('v200909');
		$adGroupCriterionService = $API->GetAdGroupCriterionService('v200909');
		
		// Create ad group.
		$adGroup = new AdGroup();
		$adGroup->name = $Name;
		$adGroup->status = 'ENABLED';
		$adGroup->campaignId = $CampaignID;
		
		// Create ad group bid.
		$adGroupBids = new ManualCPCAdGroupBids();
		$adGroupBids->keywordMaxCpc = new Bid(new Money($Bid * 1000000));
		$adGroup->bids = $adGroupBids;
		
		// Create operations.
		$operation = new AdGroupOperation();
		$operation->operand = $adGroup;
		$operation->operator = 'ADD';
		
		$operations = array($operation);
		
		// Add ad group.
		$result = $adGroupService->mutate($operations);
		if(isset($result->value))
		{
			$apiAdgroupId = floatval($result->value[0]->id);
			foreach($NegativeKeywords as $i=>$kw)
			{
			  $this->AddKeywordAPI('-'.$kw, 0, '', $apiAdgroupId, '');
			}	  
			return $apiAdgroupId;
		}
		return false;
	}

	/**
	 * Add an Ad Variation using the API.
	 */
	Public Function AddAdVariationAPI($Title, $DestinationURL, $DisplayURL, $Description, $AdGroupID)
	{
		$API = $this->GetAPI();
		$adGroupAdService = $API->GetAdGroupAdService('v200909');
		
		// Create text ad.
		$textAd = new TextAd();
		$textAd->headline = $Title;
		$textAd->description1 = substr($Description, 0, 34);// "The description1";
		$textAd->description2 = substr($Description, 34);// "The description2";
		$textAd->displayUrl = $DisplayURL;
		$textAd->url = $DestinationURL;
		
		// Create ad group ad.
		$textAdGroupAd = new AdGroupAd();
		$textAdGroupAd->adGroupId = $AdGroupID;
		$textAdGroupAd->ad = $textAd;
		
		// Create operations.
		$textAdGroupAdOperation = new AdGroupAdOperation();
		$textAdGroupAdOperation->operand = $textAdGroupAd;
		$textAdGroupAdOperation->operator = 'ADD';
		
		$operations = array($textAdGroupAdOperation);
		
		
		// Add ads.
		try{
			$result = $adGroupAdService->mutate($operations);
			if(isset($result->value))
			{
				return $result->value[0]->ad->id;
			}
		}catch(Exception $e)
		{
			echo $e;
			return false;
		}
		return true;
	}	

	/**
	 * Add Keyword using the API.
	 */
	Public Function AddKeywordAPI($Keyword, $Bid, $DestinationURL, $AdGroupID, $AdvMatch)
	{
		$API = $this->GetAPI();
		if(empty($Keyword))
		  return false;
		$type = 'BROAD';
		if(strpos($Keyword, '"') !== false)
		{
			$type = 'PHRASE';
			$Keyword = str_replace('"', '', $Keyword);
			$Keyword = str_replace('\\', '', $Keyword);
		}
		if(strpos($Keyword, '[') !== false && strpos($Keyword, ']') !== false)
		{
			$type = 'EXACT';
			$Keyword = str_replace('[', '', str_replace(']', '', $Keyword));
		}
		$negative = false;
		if($Keyword[0] == '-')
		{
			$negative = true;
			$Keyword = preg_replace('/^-/', '', $Keyword);
		}
		$adGroupCriteriaService = $API->GetAdGroupCriterionService('v200909');
		
		// Create keyword.
		$keyword = new Keyword();
		$keyword->text = $Keyword;
		$keyword->matchType = $type;

		// Create biddable ad group criterion.
		$keywordAdGroupCriterion = !$negative ? new BiddableAdGroupCriterion() : new NegativeAdGroupCriterion();
		$keywordAdGroupCriterion->adGroupId = $AdGroupID;
		$keywordAdGroupCriterion->criterion = $keyword;
		
		if(floatval($Bid) && $type != 'NEGATIVE')
		{
			// Create bid
			$bidCriterion = new ManualCPCAdGroupCriterionBids();
			$bidCriterion->maxCpc = new Bid(new Money($Bid*1000000));
			$keywordAdGroupCriterion->bids = $bidCriterion;	
		}

		// Create operations.
		$keywordAdGroupCriterionOperation = new AdGroupCriterionOperation();
		$keywordAdGroupCriterionOperation->operand = $keywordAdGroupCriterion;
		$keywordAdGroupCriterionOperation->operator = 'ADD';
		
		$operations = array($keywordAdGroupCriterionOperation);
		// Add ad group criteria.
		try{
			$result = $adGroupCriteriaService->mutate($operations);
			if(isset($result->value))
			{
				return floatval($result->value[0]->criterion->id);
			}
			return false;
		}catch(Exception $e)
		{
			echo $e;
			return false;
		}
		return true;
	}
	
	/**
	 * Return the Campaign ID from the remote server for the given Campaign $Name.
	 *
	 * @param String $Name
	 * @return Integer
	 */
	Public Function GetCampaignIDAPI($Name)
	{
	   return $this->GetCampaignByNameAPI($Name)->id;
	}

	/**
	 * Return the Ad Group ID from the remote server for the given Ad Group $Name within Campaign $CampaignID.
	 *
	 * @param String $Name
	 * @param Integer $CampaignID
	 * @return Integer
	 */
	Public Function GetAdGroupIDAPI($Name, $CampaignID)
	{
	  $adgroups = $this->GetAdGroupsAPI($CampaignID);
	  foreach($adgroups as $adgroup)
		if($adgroup->name == $Name)
		  return $adgroup->id;
	  return false;
	}
          
          
	/**
	 * Return the Campaign Object from the remote server for the given Campaign $Name.
	 *
	 * @param String $Name
	 * @return Integer
	 */
	Public Function GetCampaignByNameAPI($Name)
	{
		$campaigns = $this->GetCampaignsAPI();
		foreach($campaigns as $campaign)
		{
			if($campaign->name == $Name)
			{
				return $campaign;
			}
		}
		return false;
	}

	/**
	 * Gets all adgroups for a campaign by ID
	 *
	 * @param Integer $CampaignID
	 * @return Integer
	 */
	Public Function GetAdGroupsAPI($CampaignID)
	{
		$API = $this->GetAPI();
		$adGroupService = $API->GetAdGroupService('v200909');

		// Create selector.
		$selector = new AdGroupSelector();
		$selector->campaignId = $CampaignID;
		
		// Get all ad groups.
		$page = $adGroupService->get($selector);

		return is_array($page->entries) ? $page->entries : array();
	}
	
	/**
	 * Gets all keywords for an Ad Group by ID
	 *
	 * @param Integer $AdGroupID
	 * @return Integer
	 */
	Public Function GetKeywordsAPI($AdGroupID)
	{
		$API = $this->GetAPI();
		$adGroupCriteriaService = $API->GetAdGroupCriterionService('v200909');

		// Create selector.
		$selector = new AdGroupCriterionSelector();
		$selector->userStatuses = array('ACTIVE');
		$selector->criterionUse = 'BIDDABLE';
		
		// Create id filter.
		$idFilter = new AdGroupCriterionIdFilter();
		$idFilter->adGroupId = $AdGroupID;

		$selector->idFilters = array($idFilter);
		
		$page = $adGroupCriteriaService->get($selector);
		$output = array();
		if(isset($page->entries))
			foreach($page->entries as $entry)
				$output[] = $entry->criterion;
		return $output;
	}

	/**
	 * Gets negative keywords for an Ad Group by ID
	 *
	 * @param Integer $AdGroupID
	 * @return Integer
	 */
	Public Function GetAdGroupNegativeKeywordsAPI($AdGroupID)
	{
		$API = $this->GetAPI();
		$adGroupCriteriaService = $API->GetAdGroupCriterionService('v200909');

		// Create selector.
		$selector = new AdGroupCriterionSelector();
		$selector->criterionUse = 'NEGATIVE';
		
		// Create id filter.
		$idFilter = new AdGroupCriterionIdFilter();
		$idFilter->adGroupId = $AdGroupID;

		$selector->idFilters = array($idFilter);
		
		$page = $adGroupCriteriaService->get($selector);
		return $page->entries;
	}

  /**
   * Returns standardized campaign object using input $CampaignID and 
   * $API_Campaign values to populate data
   *
   * @param Integer $CampaignID
   * @param Mixed $API_Campaign
   * @return PPCCampaignObject
   */
  Public Function homogenizeCampaign($CampaignID, $API_Campaign) {
    $Output = new PPCCampaignObject();
    $Output->providerType = 1;
    $Output->campaignId = $CampaignID;
    $Output->apiCampaignId = $API_Campaign->id;
    $Output->user__id = $this->user__id;
    $Output->name = $API_Campaign->name;
    $Output->budget = $API_Campaign->budget->amount->microAmount/1000000;
    $Output->status = strtoupper($API_Campaign->status);
    $Output->negativeKeywords = array();
    return $Output;
  }

  /**
   * Returns standardized ad group object using input $Campaign and 
   * $API_AdGroup values to populate data
   *
   * @param PPCCampaignObject $Campaign
   * @param Mixed $API_AdGroup
   * @return PPCAdGroupObject
   */
  Public Function homogenizeAdGroup($Campaign, $API_AdGroup) {
  var_dump($API_AdGroup);
    $Output = new PPCAdGroupObject();
    $Output->providerType = $Campaign->providerType;
    $Output->campaignId = $Campaign->campaignId;
    $Output->apiCampaignId = $Campaign->apiCampaignId;
    $Output->apiAdgroupId = $API_AdGroup->id;
    $Output->user__id = $Campaign->user__id;
    $Output->name = $API_AdGroup->name;
    $Output->bid = $API_AdGroup->keywordMaxCpc;
    $Output->contentBid = $API_AdGroup->keywordContentMaxCpc;
    $Output->status = strtoupper($API_AdGroup->status);
    if($Output->status == 'ENABLED')
      $Output->status = 'ACTIVE';
    return $Output;
  }
  /**
   * Returns standardized ad group object using input $AdGroup and 
   * $API_Keyword values to populate data
   *
   * @param PPCAdGroupObject $AdGroup
   * @param Mixed $API_Keyword
   * @return PPCKeywordObject
   */
  Public Function homogenizeKeyword($AdGroup, $API_Keyword) 
    {
      $Output = new PPCKeywordObject();
      $Output->providerType = $AdGroup->providerType;
      $Output->campaignId = $AdGroup->campaignId;
      $Output->apiCampaignId = $AdGroup->apiCampaignId;
      $Output->apiAdgroupId = $AdGroup->apiAdgroupId;
      $Output->apiKeywordId = $API_Keyword->id;
      $Output->user__id = $AdGroup->user__id;
      $Output->keyword = $API_Keyword->text;
      $Output->bid = $API_Keyword->maxCpc;
      return $Output;
    }
  /**
   * Return a Campaign Object from the remote server for the given 
   * $CampaignId
   *
   * @param Integer $Id
   * @return Integer
   */
  Public Function GetCampaignByIdAPI($Id)
    {
      	$API = $this->GetAPI();
		$campaignService = $API->GetCampaignService('v200909');
		$selector = new CampaignSelector();
		$selector->ids = array($Id);
		$page = $campaignService->get($selector);
		return $page->entries[0];
    }
}
?>