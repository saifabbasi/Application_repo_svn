<?php

/**
 * Abstract Accounts_PPC_Abstract Class
 */


/**
 * Abstract class that will be reimplemented for use with specific PPC providers.
 * Provider types are as follow:
 * <pre>
 *  1. Google AdWords
 *  2. Yahoo Search Marketing
 *  3. MSN Ad Center
 * </pre>
 *
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
Abstract Class Accounts_PPC_Abstract Extends Accounts_Abstract {
	
	/**
	 * @var Integer $providerType
	 */
	Protected $providerType;
	
	/**
	 * @var Mixed $API
	 */
	Protected $api = false;
	
	Protected $queueJobId = false;
	
	public function setQueueJobId($id)
	{
	    $this->queueJobId = $id;
	}
	
	public function startQueuedJobLog($description, $status = 'message', $finishWithOutput = false)
	{
	    $ID = 0;
	    if($this->queueJobId)
		{
		    $ProviderNames = array('YAHOO', 'ADWORDS', 'MSN');
		    $desc = $this->_db->quote($description);
		    $provider = @$ProviderNames[$this->providerType];
		    $sql = "INSERT INTO bevomedia_queue_log (queueId, started, provider, status, description) VALUES ((SELECT id FROM bevomedia_queue WHERE jobId='{$this->queueJobId}'), NOW(), '$provider', '$status', $desc)";
            $this->_db->exec($sql);
            $ID = $this->_db->lastInsertId();
            if($finishWithOutput !== false)
                $this->finishQueuedJobLog($ID, false, $finishWithOutput);
		}
		return $ID;
	}
	public function finishQueuedJobLog($LogId, $status = false, $output = '')
	{
	    if($LogId)
		{
		    $out = $this->_db->quote($output);
		    $STATUS_STRING = '';
		    if($status)
		        $STATUS_STRING = ", status='$status'";
		    $sql = "UPDATE bevomedia_queue_log SET completed=NOW(), output=$out $STATUS_STRING WHERE id={$LogId}";
            $this->_db->exec($sql);
		}
	}
	
	/**
	 * Add Campaign to local table and return table insert ID.
	 *
	 * @param String $name
	 * @param Float $apiCampaignId
	 * @param Float $budget
	 * @param String $SearchContent
	 * @return Integer
	 */
	Public Function addCampaign($name, $apiCampaignId = 0, $budget = 0, $SearchContent = '')
	{
		//TABLE STRUCTURE FOR `ppc_campaigns`
		//{ID (int PK), apiCampaignId (biguint20), user__id (int), providerType (smallint), accountId (int), name (varchar50), budget (float), searchTarget (varchar13), updated (tinyint)}
		$Insert = array();
		$Insert['user__id'] = $this->user__id;
		$Insert['providerType'] = $this->providerType;
		$Insert['accountId'] = $this->id;
		$Insert['name'] = $name;
		$Insert['apiCampaignId'] = $apiCampaignId;
		$Insert['budget'] = $budget;
		$Insert['SearchContent'] = $SearchContent;
		
		if($this->getCampaignId($name) !== false)
			return $this->getCampaignId($name);
		
		foreach($Insert as $k=>$v)
			$Insert[$k] = $this->_db->quote($v);
			
		$Sql = "INSERT INTO bevomedia_ppc_campaigns (user__id, providerType, accountId, name, apiCampaignId, budget, searchTarget) VALUES ($Insert[user__id], $Insert[providerType], $Insert[accountId], $Insert[name], $Insert[apiCampaignId], $Insert[budget], $Insert[SearchContent])";

		$this->_db->exec($Sql);
		return floatval($this->_db->lastInsertId());
	}
	
	/**
	 * Get Campaign from local table matching $ID and return data populated object.
	 * The returned PPCCampaignObject will match a homogenized Campaign Object.
	 *
	 * @param Integer $ID
	 * @return PPCCampaignObject
	 */
	Public Function getCampaign($ID)
	{
		//ID 	apiCampaignId 	user__id 	providerType 	accountId 	name 	budget 	searchTarget 	updated 	status
		$Output = new PPCCampaignObject();
		$Data = $this->_db->fetchRow("SELECT id, apiCampaignId, user__id, providerType, accountId, name, budget, searchTarget, updated, status FROM bevomedia_ppc_campaigns WHERE id = {$ID}");
		$Output->providerType = $Data->providerType;
		$Output->campaignId = $Data->id;
        $Output->accountId = $Data->accountId;
		$Output->apiCampaignId = $Data->apiCampaignId;
		$Output->user__id = $Data->user__id;
		$Output->name = $Data->name;
		$Output->budget = $Data->budget;
		$Output->searchTarget = $Data->searchTarget;
		$Output->status = $Data->status;
		$Output->updated = $Data->updated;
		$Output->negativeKeywords = $this->getCampaignNegativeKeywords($ID);
		$Output->geotargetCountries = $this->getCampaignGeotargetCountries($ID);
		return $Output;
	}
	
	
	/**
	 * Get AdGroup from local table matching $ID and return data populated object.
	 * The returned PPCAdGroupObject will match a homogenized AdGroup Object.
	 *
	 * @param Integer $ID
	 * @return PPCAdGroupObject
	 */
	Public Function getAdGroup($ID)
	{
		//ID 	apiadGroupId 	campaignId 	name 	bid 	contentBid 	adDistribution 	updated 	status
		$Output = new PPCAdGroupObject();
		$Data = $this->_db->fetchRow("SELECT id, apiAdgroupId, campaignId, name, bid, contentBid, adDistribution, status FROM bevomedia_ppc_adgroups WHERE ID = {$ID}");
		$Campaign = $this->getCampaign($Data->campaignId);
		$Output->adGroupId = $Data->id;
		$Output->apiAdgroupId = $Data->apiAdgroupId;
		$Output->campaignId = $Campaign->campaignId;
		$Output->providerType = $Campaign->providerType;
		$Output->user__id = $Campaign->user__id;
		$Output->name = $Data->name;
        $Output->bid = $Data->bid;
        $Output->contentBid = $Data->contentBid;
        $Output->adDistribution = $Data->adDistribution;
        $Output->status = $Data->status;
        $Output->negativeKeywords = $this->getAdGroupNegativeKeywords($ID);
		return $Output;
	}
    /**
	 * Get array of Negative Keywords for matching $AdGroupID.
	 *
	 * @param Integer $AdGroupID
	 * @return Array
	 */
	Public Function getAdGroupNegativeKeywords($AdGroupID)
	{
		$Output = array();
		$Data = $this->_db->fetchAll("SELECT negativeKeyword FROM bevomedia_ppc_adgroups_negativekeywords WHERE adGroupId = {$AdGroupID}");
		foreach($Data as $Datum)
		{
			$Output[] = $Datum->negativeKeyword;
		}
		return $Output;
	}

	/**
	 * Get array of Negative Keywords for matching $campaignId.
	 *
	 * @param Integer $campaignId
	 * @return Array
	 */
	Public Function getCampaignNegativeKeywords($campaignId)
	{
		$Output = array();
		$Data = $this->_db->fetchAll("SELECT negativeKeyword FROM bevomedia_ppc_campaigns_negative_keywords WHERE campaignId = {$campaignId}");
		foreach($Data as $Datum)
		{
			$Output[] = $Datum->negativeKeyword;
		}
		return $Output;
	}

	/**
	 * Get array of geotarget countries for matching $campaignId.
	 *
	 * @param Integer $campaignId
	 * @return Array
	 */
	Public Function getCampaignGeotargetCountries($campaignId)
	{
		$Output = array();
		$Data = $this->_db->fetchAll("SELECT country FROM bevomedia_ppc_campaigns_geotargets_countries WHERE campaignId = {$campaignId}");
		foreach($Data as $Datum)
		{
			$Output[] = $Datum->country;
		}
		return $Output;
	}

	/**
	 * Updates campaign stored in local table.
	 *
	 * @param Integer $ID
	 * @param Float $AdGroup
	 * @param Float $Keyword_homogenized
	 * @return Keyword
	 */
	Public Function updateKeyword($AdGroup, $Keyword_h)
	{
		$Update = array();
        $Update['adGroupId'] = $AdGroup->id;
		$Update['apiKeywordId'] = $Keyword_h->apiKeywordId;
		$Update['keywordId'] = 0;
		$Update['matchType'] = $Keyword_h->matchType;
		$Update['status'] = $Keyword_h->status;
		$Update['maxCPC'] = $Keyword_h->maxCPC;
		$Update['destURL'] = $Keyword_h->destURL;
		
		foreach($Update as $k=>$v)
			$Update[$k] = $this->_db->quote($v);
			
		$Sql = "UPDATE bevomedia_ppc_campaigns SET apiCampaignId = $Update[apiCampaignId], keywordId = $Update[keywordId], matchType = $Update[matchType], status = $Update[status], maxCPC = $Update[maxCPC], destURL = $Update[destURL], updated = NOW() WHERE id = $ID";
		$this->_db->exec($Sql);
		
		return $this->getCampaign($ID);
	}
	
	/**
	 * Updates campaign stored in local table.
	 *
	 * @param Integer $ID
	 * @param Float $apiCampaignId
	 * @param Float $budget
	 * @param String $searchTarget
	 * @return Accounts_PPC_Abstract
	 */
	Public Function updateCampaign($ID,$Campaign_h)
	{
		$Update = array();
		$Update['apiCampaignId'] = $Campaign_h->apiCampaignId;
		$Update['budget'] = $Campaign_h->budget;
		$Update['searchTarget'] = $Campaign_h->searchTarget;
		
		foreach($Update as $k=>$v)
			$Update[$k] = $this->_db->quote($v);
			
		$Sql = "UPDATE bevomedia_ppc_campaigns SET apiCampaignId = $Update[apiCampaignId], budget = $Update[budget], searchTarget = $Update[searchTarget], updated = NOW() WHERE id = $ID";
		$this->_db->exec($Sql);
		
		return $this->getCampaign($ID);
	}
	
	/**
	 * Updates campaign from API
	 *
	 * @param Integer $ID
	 * @return Accounts_PPC_Abstract
	 */
	Public Function updateCampaignFromAPI($ID, $replace_negative_keywords = false, $deep_update = false, $force_update = false)
        {
          $campaign = $this->GetCampaign($ID);
          // Try to get the API ID, if we don't have it
          if($campaign->apiCampaignId == 0)
            $campaign->apiCampaignId = $this->getCampaignIDAPI($campaign->name);
          // Still no API ID? Campaign must be deleted
          if($campaign->apiCampaignId == 0)
          {
            $this->_db->exec("UPDATE bevomedia_ppc_campaigns SET status='DELETED' WHERE ID=$ID");
            echo "Campaign data is missing from API; marking as deleted.";
            return false;
          }
          //Failsafe - Will not update if we've updated in the last 30 minutes
          //unless $force_update is true
          if($force_update || strtotime($campaign->updated) < (strtotime("NOW") - 60*30))
          {
            $campaign_h = $this->homogenizeCampaign($ID, $this->getCampaignByIdAPI($campaign->apiCampaignId));
            //TODO -- get negative keywords from API
            if(false && $replace_negative_keywords)
              $this->replaceCampaignNegativeKeywords($ID, $campaign_h->negativeKeywords);
            $campaign = $this->updateCampaign($ID, $campaign_h);
            if($deep_update)
            {
              $this->updateCampaignAdGroupsFromAPI($campaign, $replace_negative_keywords, $deep_update);
            }
          }
          return $campaign;
	}
    /**
     * Update all campaigns for the current account from the API
     * @return Integer
     */
    Public Function updateCampaignsFromAPI()
            {
              $campaigns = $this->getCampaigns();
              $i = 0;
              $AllLogId = $this->startQueuedJobLog("Updating all campaigns from API", 'in-progress');
              foreach($campaigns as $c)
              {
                //Skip the deleted ones
                if($c->status == 'DELETED')
                {
                  $this->startQueuedJobLog("Skipping deleted campaign {$c->id}", 'message', '');
                  continue;
                }
                $LogId = 0;
                try{
                  $LogId = $this->startQueuedJobLog("Updating campaign {$c->id} ... ", 'in-progress');
                  if($this->updateCampaignFromAPI($c->id, true, true, true))
                    $i += 1;
                  $this->finishQueuedJobLog($LogId, 'success');
                } catch (Exception $e) {
                  if($LogId)
                      $this->finishQueuedJobLog($LogId, 'error', $e->getMessage() );
                  else
                      $this->startQueuedJobLog("Error updating campaign {$c->id}", 'error', $e->getMessage());
                }
              }
              $this->finishQueuedJobLog($AllLogId, 'success');
              return $i;
            }

	/**
	 * Updates AdGroup stored in local table.
	 *
	 * @param Integer $ID
	 * @param Float $api_adgroup_id
	 * @param Float $Budget
	 * @param String $SearchTarget
	 * @return AdGroup
	 */
	Public Function updateAdGroup($ID, $AdGroup)
	{
		$Update = array();
		$Update['apiAdgroupId'] = $AdGroup->apiAdgroupId;
		$Update['bid'] = $AdGroup->bid;
		$Update['contentBid'] = $AdGroup->contentBid;
		$Update['adDistribution'] = $AdGroup->adDistribution;
		$Update['status'] = $AdGroup->status;
		
		foreach($Update as $k=>$v)
			$Update[$k] = $this->_db->quote($v);
			
        $Sql = "UPDATE bevomedia_ppc_adgroups SET apiAdgroupId = $Update[apiAdgroupId], bid = $Update[bid], contentBid = $Update[contentBid], adDistribution = $Update[adDistribution], status = $Update[status], updated = NOW() WHERE id = $ID";
		$this->_db->exec($Sql);
		return $this->getAdGroup($ID);
	}
	
	/**
	 * Updates adgroup from API
	 *
	 * @param Integer $Campaign
	 * @return AdGroup
	 */
	Public Function updateCampaignAdGroupsFromAPI($Campaign, $replace_negative_keywords = false, $deep_update = false)
        {
            $adgroups_api = $this->getAdGroupsAPI($Campaign->apiCampaignId);
            $adgroups_local = $this->getAdGroups($Campaign->campaignId);
            $adgroups_final = array();
            // Delete local adgroups that are not present in API results
            foreach($adgroups_local as $adgroup_l)
            {
              $delete = true;
              foreach($adgroups_api as $adgroup_a)
              {
                $adgroup_h = $this->homogenizeAdGroup($Campaign, $adgroup_a);
                if($adgroup_h->apiAdgroupId == $adgroup_l->apiAdgroupId)
                  $delete = false;
              }
              if($delete)
              {
                $this->_db->exec("UPDATE bevomedia_ppc_adgroups SET status='DELETED', updated=NOW() WHERE id={$adgroup_l->id}");
              }
            }
            // Update local adgroups, creating ones we don't already have
            foreach($adgroups_api as $adgroup_a)
            {
              $create = true;
              $adgroup_h = $this->homogenizeAdGroup($Campaign, $adgroup_a);
              foreach($adgroups_local as $adgroup_l)
              {
                if($adgroup_l->apiAdgroupId == $adgroup_h->apiAdgroupId)
                {
                  $create = false;
                  $adgroup = $this->updateAdGroup($adgroup_l->id, $adgroup_h);
                }
              }
              if($create)
              {
                $id = $this->addAdGroup($adgroup_h->name, $Campaign->campaignId);
                $adgroup = $this->updateAdGroup($id, $adgroup_h);
              }
              if($replace_negative_keywords)
                $kw = $this->replaceAdGroupNegativeKeywords($adgroup->adGroupId, $this->getAdGroupNegativeKeywordsAPI($adgroup));
              $adgroups_final[] = $adgroup;
            }
            return $adgroups_final;
	}
	/**
	 * Replaces negative keywords for the locally stored adgroup.
	 * This will remove all existing negative keywords and replace them with the provided $NegativeKeywords
	 *
	 * @param Integer $ID
	 * @param Array $NegativeKeywords
	 * @return Accounts_PPC_Abstract
	 */
	Public Function replaceAdGroupNegativeKeywords($ID, $NegativeKeywords)
	{
		$this->removeAdGroupNegativeKeywords($ID);
		
		foreach($NegativeKeywords as $NegativeKeyword)
		{
			$Insert = array();
			$Insert['adGroupId'] = $ID;
			$Insert['negativeKeyword'] = $this->_db->quote($NegativeKeyword);
			
			$Sql = "INSERT INTO bevomedia_ppc_adgroups_negativekeywords (adGroupId, negativeKeyword) VALUES ($Insert[adGroupId], $Insert[negativeKeyword])";
			$this->_db->exec($Sql);
		}
		
		return $ID;
	}
	
	
	/**
	 * Removes the negative keywords for the specified AdGroup $ID.
	 *
	 * @param Integer $ID
	 * @return Accounts_PPC_Abstract
	 */
	Public Function removeAdGroupNegativeKeywords($ID)
	{
		$Sql = "DELETE FROM bevomedia_ppc_adgroups_negativekeywords WHERE adGroupId = $ID";
		$this->_db->exec($Sql);
		return $this;
	}
	/**
	 * Replaces negative keywords for the locally stored campaign.
	 * This will remove all existing negative keywords and replace them with the provided $negativeKeywords
	 *
	 * @param Integer $ID
	 * @param Array $negativeKeywords
	 * @return Accounts_PPC_Abstract
	 */
	Public Function replaceCampaignNegativeKeywords($ID, $negativeKeywords)
	{
		$this->removeCampaignNegativeKeywords($ID);
		
		foreach($negativeKeywords as $negativeKeyword)
		{
			$Insert = array();
			$Insert['campaignId'] = $ID;
			$Insert['negativeKeyword'] = $this->_db->quote($negativeKeyword);
			
			$Sql = "INSERT INTO bevomedia_ppc_campaigns_negative_keywords (campaignId, negativeKeyword) VALUES ($Insert[campaignId], $Insert[negativeKeyword])";
			$this->_db->exec($Sql);
		}
		
		return $ID;
	}
	
	
	/**
	 * Removes the negative keywords for the specified Campaign $ID.
	 *
	 * @param Integer $ID
	 * @return Accounts_PPC_Abstract
	 */
	Public Function removeCampaignNegativeKeywords($ID)
	{
		$Sql = "DELETE FROM bevomedia_ppc_campaigns_negative_keywords WHERE campaignId = $ID";
		$this->_db->exec($Sql);
		return $this;
	}
	/**
	 * Replaces geotarget countries for the locally stored campaign.
	 * This will remove all existing geotarget countries and replace them with the provided $countries
	 *
	 * @param Integer $ID
	 * @param Array $countries
	 * @return Accounts_PPC_Abstract
	 */
	Public Function replaceCampaignGeotargetCountries($ID, $countries)
	{
		$this->removeCampaignGeotargetCountries($ID);
		
		foreach($countries as $country)
		{
			$Insert = array();
			$Insert['campaignId'] = $ID;
			$Insert['countries'] = $this->_db->quote($country);
			
			$Sql = "INSERT INTO bevomedia_ppc_campaigns_geotargets_countries (campaignId, country) VALUES ($Insert[campaignId], $Insert[countries])";
			$this->_db->exec($Sql);
		}
		
		return $ID;
	}
	
	
	/**
	 * Removes the geotarget countries for the specified Campaign $ID.
	 *
	 * @param Integer $ID
	 * @return Accounts_PPC_Abstract
	 */
	Public Function removeCampaignGeotargetCountries($ID)
	{
		$Sql = "DELETE FROM bevomedia_ppc_campaigns_geotargets_countries WHERE campaignId = $ID";
		$this->_db->exec($Sql);
		return $this;
	}

	/**
	 * Insert Ad Group into local table within Campaign $campaignId and return table insert ID.
	 *
	 * @param String $name
	 * @param Integer $campaignId
	 * @return Integer
	 */
	Public Function addAdGroup($name, $campaignId, $apiAdgroupId = '', $bid = 0, $contentBid = 0, $adDistribution = 'SearchContent')
	{
		//TABLE STRUCTURE FOR `ppc_adgroups`
		//{ID (int PK), campaignId (int), name (varchar50), updated (tinyint)}
		$Insert = array();
		$Insert['campaignId'] = $campaignId;
		$Insert['name'] = $name;
		$Insert['apiAdgroupId'] = $apiAdgroupId;
		$Insert['bid'] = $bid;
		$Insert['contentBid'] = $contentBid;
		$Insert['adDistribution'] = $adDistribution;
		
		//TODO: Add Ad Group Functionality
		if($this->getAdGroupId($name, $campaignId) !== false)
			return $this->getAdGroupId($name, $campaignId);
			
		foreach($Insert as $k=>$v)
			$Insert[$k] = $this->_db->quote($v);
			
		$sql = "INSERT INTO bevomedia_ppc_adgroups (name, campaignId, apiAdgroupId, bid, contentBid, adDistribution, updated) VALUES ($Insert[name], $Insert[campaignId], $Insert[apiAdgroupId], $Insert[bid], $Insert[contentBid], $Insert[adDistribution], now())";

		$this->_db->exec($sql);
		return floatval($this->_db->lastInsertId());
	}
	
	/**
	 * Insert Ad Variation into local table after finding the Campaign and Ad Group container and return table insert id.
	 *
	 * @param String $Campaignname
	 * @param String $AdGroupname
	 * @param String $Title
	 * @param String $DestinationURL
	 * @param String $DisplayURL
	 * @param String $Description
	 * @return Integer
	 */
	Public Function addAdVariationTo($Campaignname, $AdGroupname, $Title, $DestinationURL, $DisplayURL, $Description, $API_AD_ID = false)
	{
		$campaignId = $this->getCampaignId($Campaignname);
		$adGroupId = $this->getAdGroupId($AdGroupname, $campaignId);
		return $this->addAdVariation($adGroupId, $Title, $DestinationURL, $DisplayURL, $Description, $API_AD_ID);
	}
	
	/**
	 * Insert Ad Variation into local table within Ad Group $adGroupId and return table insert id.
	 *
	 * @param Integer $adGroupId
	 * @param String $Title
	 * @param String $DestinationURL
	 * @param String $DisplayURL
	 * @param String $Description
	 * @return Integer
	 */
	Public Function addAdVariation($adGroupId, $Title, $DestinationURL, $DisplayURL, $Description, $API_AD_ID = false)
	{
		//TABLE STRUCTURE FOR `ppc_advariations`
		//{ID (int PK), adGroupId (int), api_ad_id (bigint), title (tinytext),
		//	url (varchar1024), displayUrl (tinytext), description (tinytext),
		//	status (char1), updated (char1)}
		$Insert = array();
		$Insert['adGroupId'] = $adGroupId;
		$Insert['title'] = $Title;
		$Insert['url'] = $DestinationURL;
		$Insert['displayUrl'] = $DisplayURL;
		$Insert['description'] = $Description;
		if($API_AD_ID !== false)
			$Insert['api_ad_id'] = $API_AD_ID; // TBI
		
		foreach($Insert as $k=>$v)
			$Insert[$k] = $this->_db->quote($v);
			
		if($API_AD_ID === false)
		{
			$sql = "INSERT INTO bevomedia_ppc_advariations (adGroupId, title, url, displayUrl, description)
				VALUES ($Insert[adGroupId], $Insert[title], $Insert[url], $Insert[displayUrl], $Insert[description])";
		}else{
			$sql = "INSERT INTO bevomedia_ppc_advariations (adGroupId, title, url, displayUrl, description, apiAdId)
				VALUES ($Insert[adGroupId], $Insert[title], $Insert[url], $Insert[displayUrl], $Insert[description], $Insert[api_ad_id])";
		}
		$this->_db->exec($sql);
		return floatval($this->_db->lastInsertId());
	}
	
	/**
	 * Remove Ad Variation from local table that matches ID of $ID.
	 *
	 * @param Integer $ID
	 * @return Integer
	 */
	Public Function removeAdVariation($ID)
	{
		$sql = "DELETE FROM bevomedia_ppc_advariations WHERE id = $ID";
		$this->_db->exec($sql);
	}
	
	/**
	 * Remove Keyword from local table that matches ID of $ID.
	 *
	 * @param Integer $ID
	 * @return Integer
	 */
	Public Function removeKeyword($ID)
	{
		$sql = "DELETE FROM bevomedia_ppc_keywords WHERE id = $ID";
		$this->_db->exec($sql);
	}
	
	
	/**
	 * Insert a Keyword into the local table within Ad Group $adGroupId and return the table insert ID.
	 *
	 * @param Integer $adGroupId
	 * @param String $Keyword
	 * @param Float $bid
	 * @param String $DestinationURL
	 * @param Float $apiKeywordId
	 * @return Float
	 */
	Public Function addKeyword($adGroupId, $Keyword, $bid, $DestinationURL, $apiKeywordId = false)
	{
		//TABLE STRUCTURE FOR `ppc_keywords`
		//{ID (int PK), adGroupId (int), keywordId (int), MatchType (smallint),
		//	status (smallint6), maxCPC (decimal4,2), destUrl(varchar255), updated (tinyint4)}
		$Insert = array();
		$Insert['adGroupId'] = $adGroupId;
		$Insert['keywordId'] = $this->getKeywordId($Keyword);
		//$Insert['MatchType'] = $MatchType; // TBI
		$Insert['maxCPC'] = $bid;
		$Insert['destUrl'] = $DestinationURL;
		$Insert['status'] = 1;
		if($apiKeywordId !== false)
			$Insert['apiKeywordId'] = $apiKeywordId; // TBI
		
		foreach($Insert as $k=>$v)
			$Insert[$k] = $this->_db->quote($v);
			
			
		if($apiKeywordId === false)
		{
			$sql = "INSERT INTO bevomedia_ppc_keywords (adGroupId, keywordId, maxCPC, destUrl, status)
				VALUES ($Insert[adGroupId], $Insert[keywordId], $Insert[maxCPC], $Insert[destUrl], $Insert[status])";
		}else{
			$sql = "INSERT INTO bevomedia_ppc_keywords (adGroupId, keywordId, maxCPC, destUrl, status, apiKeywordId)
				VALUES ($Insert[adGroupId], $Insert[keywordId], $Insert[maxCPC], $Insert[destUrl], $Insert[status], $Insert[apiKeywordId])";
		}
		$this->_db->exec($sql);
		return floatval($this->_db->lastInsertId());
	}
	
	
	/**
	 * Insert a Keyword into the local table after finding the Campaign and Ad Group container and return the table insert ID.
	 *
	 * @param String $Campaignname
	 * @param String $AdGroupname
	 * @param String $Keyword
	 * @param Float $bid
	 * @param String $DestinationURL
	 * @return Integer
	 */
	Public Function addKeywordTo($Campaignname, $AdGroupname, $Keyword, $bid, $DestinationURL, $apiKeywordId = false)
	{
		$campaignId = $this->getCampaignId($Campaignname);
		$adGroupId = $this->getAdGroupId($AdGroupname, $campaignId);
		return $this->addKeyword($adGroupId, $Keyword, $bid, $DestinationURL, $apiKeywordId);
	}
	
	
	/**
	 * Insert a Keyword into local table and return the table insert ID.
	 *
	 * @param String $Keyword
	 * @return Integer
	 */
	Protected Function insertKeyword($Keyword)
	{
		//TABLE STRUCTURE FOR `keyword_tracker_keywords`
		//{ID (int PK), Keyword (varchar50)}
		$Keyword = $this->_db->quote($Keyword);
			
		$sql = "INSERT INTO bevomedia_keyword_tracker_keywords (keyword) VALUES ($Keyword)";

		$this->_db->exec($sql);
		return $this->_db->lastInsertId();
	}
	
	/**
	 * Get Keyword ID from local table matching $Keyword.
	 * If no keyword is found then a keyword will be inserted and that ID will be returned.
	 *
	 * @param String $Keyword
	 * @return Integer
	 */
	Public Function getKeywordId($Keyword)
	{
		$quoted_Keyword = $this->_db->quote($Keyword);
		$KeywordRow = $this->_db->fetchRow('SELECT id FROM bevomedia_keyword_tracker_keywords WHERE keyword = "' . $quoted_Keyword . '"');
		if(!$KeywordRow)
			return $this->InsertKeyword($Keyword);
			
		return $KeywordRow->id;
	}
	
	/**
	 * Return Campaign ID for Campaign matching $name.
	 *
	 * @param String $name
	 * @return Integer
	 */
	Public Function getCampaignId($name)
	{
		$name = trim($name);
		$Campaigns = $this->getCampaigns();
		foreach($Campaigns as $Campaign)
		{
			if(trim($Campaign->name) == $name)
			{
				return floatval($Campaign->id);
			}
		}
		
		return false;
	}
	
	
	/**
	 * Return local Campaign name matching $ID.
	 *
	 * @param Integer $ID
	 * @return String
	 */
	Public Function getCampaignName($ID)
	{
		$Campaigns = $this->getCampaigns();
		foreach($Campaigns as $Campaign)
		{
			if($Campaign->id == $ID)
				return $Campaign->name;
		}
		
		return false;
	}
	
	/**
	 * Return local Ad Group ID matching $name within Campaign $campaignId.
	 *
	 * @param String $name
	 * @param Integer $campaignId
	 * @return Integer
	 */
	Public Function getAdGroupId($name, $campaignId = false)
	{
		$name = trim($name);
		if(!$campaignId)
			return false;
		$AdGroups = $this->getAdGroups($campaignId);
		foreach($AdGroups as $AdGroup)
		{
			if($AdGroup->name == $name)
				return floatval($AdGroup->id);
		}
		
		return false;
	}
	
	/**
	 * Return local Ad Group name matching $ID within Campaign $campaignId.
	 *
	 * @param Integer $ID
	 * @param Integer $campaignId
	 * @return String
	 */
	Public Function getAdGroupName($ID, $campaignId)
	{
		$AdGroups = $this->getAdGroups($campaignId);
		foreach($AdGroups as $AdGroup)
		{
			if($AdGroup->id == $ID)
				return $AdGroup->name;
		}
		
		return false;
	}
	
	/**
	 * Return local Ad Group matching $ID
	 *
	 * @param int $ID
	 * @return Object
	 */
	Public Function getAdGroupById($ID)
	{
		return $this->_db->fetchRow('SELECT * FROM bevomedia_ppc_adgroups WHERE id = ' . $ID);
	}
	
	/**
	 * Return all Campaigns from local tables matching this Account $ID and this $providerType.
	 *
	 * @return Array
	 */
	Public Function getCampaigns()
	{
		return $this->_db->fetchAll('SELECT * FROM bevomedia_ppc_campaigns WHERE accountId = ' . $this->id . ' AND providerType = ' . $this->providerType);
	}
	
	/**
	 * Return all Ad Groups from local tables matching the $campaignId.
	 *
	 * @param Integer $campaignId
	 * @return Array
	 */
	Public Function getAdGroups($campaignId)
	{
	  if(is_numeric($campaignId))
		return $this->_db->fetchAll('SELECT * FROM bevomedia_ppc_adgroups WHERE campaignId = ' . $campaignId);
	  return array();
	}
	
	/**
	 * Return all Ad Variations from local tables within $adGroupId.
	 *
	 * @param Integer $adGroupId
	 * @return Array
	 */
	Public Function getAdVariations($adGroupId)
	{
	  if(is_numeric($adGroupId))
		return $this->_db->fetchAll('SELECT * FROM bevomedia_ppc_advariations WHERE adGroupId = ' . $adGroupId);
	  return array();
	}
	
	/**
	 * Return all Keywords from local tables within $adGroupId.
	 *
	 * @param Integer $adGroupId
	 * @return Array
	 */
	Public Function getKeywords($adGroupId)
	{
		return $this->_db->fetchAll('SELECT bevomedia_ppc_keywords.destURL AS destURL, bevomedia_ppc_keywords.matchType as matchType, bevomedia_ppc_keywords.id AS id, bevomedia_ppc_keywords.adGroupId AS adGroupId, bevomedia_ppc_keywords.maxCPC AS maxCPC, bevomedia_ppc_keywords.apiKeywordId AS apiKeywordId, bevomedia_keyword_tracker_keywords.keyword AS Keyword FROM bevomedia_ppc_keywords LEFT JOIN bevomedia_keyword_tracker_keywords ON bevomedia_keyword_tracker_keywords.ID = bevomedia_ppc_keywords.keywordId WHERE adGroupId = ' . $adGroupId);
	}
	
	/**
	 * Return the API associated with this PPC Account.
	 */
	Public Abstract Function getAPI();
	
	/**
	 * Return an error generated by this $API.
	 */
	Public Abstract Function getErrorAPI();
	
	/**
	 * Add a Campaign using this $API.
	 *
	 * @param String $name
	 * @param Float $budget
	 * @param String $Description
	 * @param Mixed $GeoTargets
	 * @param Mixed $negativeKeywords
	 */
	Public Abstract Function addCampaignAPI($name, $budget = 5000, $Description = false, $GeoTargets = false, $negativeKeywords = false, $ContentTargets = false);

	/**
	 * Add an Ad Group using this $API.
	 *
	 * @param String $name
	 * @param Integer $campaignId
	 * @param Float $bid
	 * @param String $adDistribution
	 * @param Mixed $negativeKeywords
	 */
	Public Abstract Function addAdGroupAPI($name, $campaignId, $bid = 1, $adDistribution = 'Search', $negativeKeywords = null, $contentBid = 0);

	/**
	 * Add an Ad Variation using this $API.
	 *
	 * @param String $Title
	 * @param String $DestinationURL
	 * @param String $DisplayURL
	 * @param String $Description
	 * @param Integer $adGroupId
	 */
	Public Abstract Function addAdVariationAPI($Title, $DestinationURL, $DisplayURL, $Description, $adGroupId);
	
	/**
	 * Add a Keyword using this $API.
	 *
	 * @param String $Keyword
	 * @param Float $bid
	 * @param String $DestinationURL
	 * @param Integer $adGroupId
	 * @param Mixed $AdvMatch
	 */
	Public Abstract Function addKeywordAPI($Keyword, $bid, $DestinationURL, $adGroupId, $AdvMatch);
	
	/**
	 * Return a Campaign from the remote server for the Campaign $Id.
	 *
	 * @param integer $Id
	 * @return Campaign
	 */
	Public Abstract Function getCampaignByIdAPI($Id);

	/**
	 * Return the Campaign ID from the remote server for the Campaign $name.
	 *
	 * @param String $name
	 * @return Integer
	 */
	Public Abstract Function getCampaignIdAPI($name);
	
	/**
	 * Return the Ad Group ID from the remote server for the Ad Group $name contained within the Campaign matching $campaignId.
	 *
	 * @param String $name
	 * @param Integer $campaignId
	 * @return Integer
	 */
	Public Abstract Function getAdGroupIdAPI($name, $campaignId);
  
    /**
	 * Return the Ad Groups for campaign from the remote server
	 *
	 * @param String $Name
	 * @param Integer $CampaignID
	 * @return Integer
	 */
	Public Abstract Function getAdGroupsAPI($CampaignID);
	

    /**
     * Returns standardized campaign object using input $campaignId and
     * $InputCampaign values to populate data
     *
     * @param Integer $campaignId
     * @param Mixed $InputCampaign
     * @return PPCCampaignObject
     */
    Public Abstract Function homogenizeCampaign($campaignId, $InputCampaign);
	
    /**
     * Returns standardized ad group object using input $Campaign and
     * $InputAdGroup values to populate data
     *
     * @param PPCCampaignObject $Campaign
     * @param Mixed $InputAdGroup
     * @return PPCAdGroupObject
     */
    Public Abstract Function homogenizeAdGroup($Campaign, $InputAdGroup);

    /**
     * Returns standardized ad group object using input $AdGroup and
     * $InputKeyword values to populate data
     *
     * @param PPCAdGroupObject $AdGroup
     * @param Mixed $InputKeyword
     * @return PPCKeywordObject
     */
    Public Abstract Function homogenizeKeyword($AdGroup, $InputKeyword);

	/**
	 * Return if account is recognized on the API server.
	 * If account is recognized, updates the table with Verified equal to '1'.
	 * If account is not recognized, updates the table with Verified equal to '0'.
	 *
	 * @return boolean
	 */
	Public Abstract Function VerifyAccountAPI();
}

/**
 * Standardized Campaign Object for use with PPC Accounts.
 */
Class PPCCampaignObject {
	Public $providerType = 0;
	Public $user__id = 0;
	Public $campaignId = 0;
	Public $apiCampaignId = 0;
	Public $name = '';
	Public $budget = 0;
	Public $GeoTargets = array();
	Public $searchTarget = 'SearchContent';
	Public $negativeKeywords = array();
	Public $updated = '0000-00-00 00:00:00';
	Public $status = 'Active';
}

/**
 * Standardized AdGroup Object for use with PPC Accounts.
 */
Class PPCAdGroupObject {
	Public $providerType = 0;
	Public $user__id = 0;
	Public $campaignId = 0;
	Public $adGroupId = 0;
	Public $apiCampaignId = 0;
	Public $apiAdgroupId = 0;
	Public $name = '';
	Public $bid = 0;
	Public $contentBid = 0;
	Public $adDistribution = 'SearchContent';
	Public $updated = '0000-00-00 00:00:00';
	Public $status = 'Active';
}

/**
 * Standardized Keyword Object for use with PPC Accounts.
 */
Class PPCKeywordObject {
	Public $providerType = 0;
	Public $user__id = 0;
	Public $campaignId = 0;
	Public $adGroupId = 0;
	Public $keywordId = 0;
	Public $apiCampaignId = 0;
	Public $apiAdgroupId = 0;
	Public $apiKeywordId = 0;
	Public $keyword = '';
	Public $bid = 0;
	Public $destinationURL = '';
	Public $AdvancedMatch = array();
	Public $updated = '0000-00-00 00:00:00';
	Public $status = 'Active';
}


// NOT IN USE YET
/**
 * Standardized AdVariation Object for use with PPC Accounts.
 */
Class PPCAdVariationObject {
	Public $providerType = 0;
	Public $user__id = 0;
	Public $campaignId = 0;
	Public $adGroupId = 0;
	Public $adVariationId = 0;
	Public $apiCampaignId = 0;
	Public $apiAdgroupId = 0;
	Public $apiAdVariationId = 0;
	Public $keyword = '';
	Public $bid = 0;
	Public $destinationURL = '';
	Public $AdvancedMatch = array();
	Public $updated = '0000-00-00 00:00:00';
	Public $status = 'Active';
}
// NOT IN USE YET


?>