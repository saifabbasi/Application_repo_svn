<?php

/**
 * Creates and manages objects of the Accounts_Yahoo table.
 *
 */


/**
 * Creates and manages objects of the Accounts_Yahoo table.
 * 
 * Creates and manages objects of the Accounts_Yahoo table and makes 
 * use of the Yahoo API to create new Campaign, Ad Group, Keyword 
 * and Ad Variation objects.
 *  
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */

Class Accounts_Yahoo Extends Accounts_PPC_Abstract {
	
	/**
	 * Provider Type value used within local PPC Campaign tables.
	 * Yahoo Search Marketing = 2
	 *
	 * @var Integer $ProviderType
	 */
	Protected $providerType = 2;
	
	Protected $API = false;
	
	/**
	 * @var String $_table_name
	 */
	Protected $_table_name = 'bevomedia_accounts_yahoo';
	
	/**
	 * @var Integer $masterAccountId
	 */
	Public $masterAccountId;
	
	
	/**
	 * Inserts a new row into the table using the values provided by $Data.
	 *
	 * Example:
	 * <code>
	 * // Create a new associative array of data to be inserted.
	 * $Data = array();
	 * $Data['User_ID'] = 1;
	 * $Data['Username'] = 'JohnDoe';
	 * $Data['Password'] = 'Password12';
	 * $Data['MasterAccountID'] = '12345678';
	 * 	
	 * // Create a new instance of Accounts_Yahoo.
	 * $Yahoo = new Accounts_Yahoo();
	 * 
	 * // Insert the data and return the id of the inserted row.
	 * $InsertID = $Yahoo->Insert($Data);
	 * </code>
	 * 
	 * @param Array $Data	The values to be inserted into the table.
	 * @return Integer		The id of the inserted row.
	 */
	Public Function Insert($data)
	{
		$Insert = array();
		$Insert['User_ID'] = $data['User_ID'];
		$Insert['Username'] = $data['username'];
		$Insert['Password'] = $data['password'];
		$Insert['MasterAccountID'] = $data['masterAccountId'];
		
		foreach($Insert as $k=>$v)
			$Insert[$k] = $this->_db->quote($v);
			
		$sql = "Insert INTO bevomedia_accounts_yahoo (user__id, username, password, masterAccountId) VALUES ($Insert[User_ID], $Insert[Username], $Insert[Password], $Insert[MasterAccountID])";

		$this->_db->exec($sql);
		$this->id = $this->_db->lastInsertId();
		
		$this->getInfo();
		
		return $this->id;
	}
	
	/**
	 * Return Yahoo Search Marketing API.
	 *
	 * @return yahoo_api
	 */
	Public Function GetAPI()
	{
		require_once(PATH . 'yahoo_api/yahoo_api.php');
		if($this->API !== false)
			return $this->API;
			
		if(!isset($this->id))
			return false;
			
		$this->getInfo();
		
		$this->API = new yahoo_api($this->username, $this->password, $this->masterAccountId);
		return $this->API;
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
	 * Return if account is recognized on the API server.
	 * If account is recognized, updates the table with Verified equal to '1'.
	 * If account is not recognized, updates the table with Verified equal to '0'.
	 *
	 * @return boolean
	 */
	Public Function VerifyAccountAPI()
	{
		$this->GetAPI();
		$Disabled = $this->API->disabled;
		if($Disabled == '1')
		{
			$this->Update(array("verified"=>0, "id"=>$this->id));
			return false;
		}else{
			$this->Update(array("verified"=>1, "id"=>$this->id));
			return true;
		}
	}
	
	/**
	 * Add a Campaign using the API.
	 */	
	Public Function AddCampaignAPI($Name, $Budget = 5000, $Description = false, $GeoTargets = false, $NegativeKeywords = false, $ContentTargets = false)
	{
		$API = $this->GetAPI();
		
		if($Description === false)
		{
			$Description = $Name;
		}
		
		$Campaign = $API->addCampaign($Name, $Description, $GeoTargets, $Budget);
		return $Campaign;
	}
	
	/*
	 * Add an Ad Group using the API.
	 */
	Public Function AddAdGroupAPI($Name, $CampaignID, $Bid = 1, $AdDistribution = 'Search', $NegativeKeywords = null, $ContentBid = 0)
	{
		$API = $this->GetAPI();
			
		$AdGroup = $API->addAdGroup($Name, $ContentBid, $Bid, $CampaignID, $NegativeKeywords, $AdDistribution, $ContentBid);
		return $AdGroup;
	}
	
	/**
	 * Add an Ad Variation using the API.
	 */
	Public Function AddAdVariationAPI($Title, $DestinationURL, $DisplayURL, $Description, $AdGroupID)
	{
		$API = $this->GetAPI();
		
		$AdVariation = $API->addAd($AdGroupID, $Title, $DestinationURL, $DisplayURL, $Description);
		return $AdVariation;
	}
	
	/**
	 * Removes Ad Variations using the API.
	 */
	Public Function RemoveAdVariationsAPI($AdGroupID, $AdVariationIDs)
	{
		$API = $this->GetAPI();
		
		return $API->deleteAds($AdGroupID, $AdVariationIDs);
	}
	
	/**
	 * Add Keyword using the API.
	 */
	Public Function AddKeywordAPI($Keyword, $Bid, $DestinationURL, $AdGroupID, $AdvMatch)
	{
		$API = $this->GetAPI();
		
		$Keyword = $API->addKeywords($AdGroupID, $Keyword, $Bid, $DestinationURL, $AdvMatch);
		return $Keyword;
	}
	
	/**
	 * Return the Campaign Object from the remote server for the given Campaign $Name.
	 *
	 * @param String $Name
	 * @return Integer
	 */
	Public Function GetCampaignByNameAPI($Name)
	{
		$Output = $this->GetAPI()->getCampaignUsingNameAll($Name);
		return $Output;
	}
		
	/**
	 * Return the Campaign ID from the remote server for the given Campaign $Name.
	 *
	 * @param String $Name
	 * @return Integer
	 */
	Public Function GetCampaignIDAPI($Name)
	{
		return $this->GetAPI()->getCampaignIdUsingName($Name);
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
		return $this->GetAPI()->getAdGroupIdUsingName($Name, $CampaignID);
	}

  /**
   * Gets all keywords for an Ad Group by ID
   *
   * @param Integer $AdGroupID
   * @return Integer
   */
    Public Function GetAdGroupsAPI($CampaignID)
        {
          return $this->GetAPI()->getActiveAdGroups($CampaignID);
        }    

  /**
   * Return an array of Keywords from the remote server for the given AdGroup $AdGroupID.
   *
   * @param String $Name
   * @param Integer $AdGroupID
   * @return Integer
   */
  Public Function GetKeywordsAPI($AdGroupID)
                {
                  return $this->GetAPI()->getKeywordsByAdGroupID($AdGroupID);
                }

  /**
   * Gets negative keywords for an Ad Group by ID
   *
   * @param Integer $AdGroupID
   * @return Integer
   */
  Public Function GetAdGroupNegativeKeywordsAPI($AdGroup)
  {
    $Output = array();
    $excluded = $this->GetAPI()->getExcludedWords($AdGroup->apiAdgroupId)->out;
    if($excluded && count($excluded) > 0)
      foreach($excluded->ExcludedWord as $kw)
        $Output[] = $kw->phraseSearchText;
    return $Output;
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
          $Output->providerType = 2;
          $Output->campaignIc = $CampaignID;
          $Output->apiCampaignId = $API_Campaign->ID;
          $Output->user__id = $this->user__id;
          $Output->name = $API_Campaign->name;
          $Output->budget = $this->GetAPI()->getCampaignDailySpendLimit($API_Campaign->ID)->out->limit;
          if(isset($API_Campaign->NegativeKeywords->string))
            $Ouput->negativeKeywords = $API_Campaign->NegativeKeywords->string;
          $Output->status = strtoupper($API_Campaign->status);
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
      $Output = new PPCAdGroupObject();
      $Output->providerType = $Campaign->providerType;
      $Output->campaignId = $Campaign->campaignId;
      $Output->apiCampaigndD = $Campaign->apiCampaignId;
      $Output->apiAdgroupId = $API_AdGroup->ID;
      $Output->user__id = $Campaign->user__id;
      $Output->name = $API_AdGroup->name;
      $Output->bid = $API_AdGroup->sponsoredSearchMaxBid;
      $Output->contentBid = $API_AdGroup->contentMatchMaxBid;
      $Output->status = strtoupper($API_AdGroup->status);
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
    Public Function homogenizeKeyword($AdGroup, $API_Keyword) {
    }
	
	/**
	 * Return a Campaign from the remote server for the Campaign $Id.
	 *
	 * @param integer $Id
	 * @return Campaign
	 */
    Public Function GetCampaignByIdAPI($Id) {
      return $this->GetAPI()->getCampaign($Id);
    }
}
?>