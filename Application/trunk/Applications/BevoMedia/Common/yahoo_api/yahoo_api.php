<?php

include(ABSPATH . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');


function yahoo_api_debug($msg)
{
	return;
}
	
class yahoo_api
{
	private $internal_account_id;
	
	private $campaignService;
	private $adGroupService;
	private $adService;
	private $keywordService;
	private $accountService;
	private $basicReportService;
	private $excludedWordsService;
	private $targetingService;
	private $budgetingService;
	private $geographicalDictionaryService;
	
	private $accountID;
	private $username;
	private $password;
	private $masteraccountid;
	
	private $EWS_HEADERS;
	private $SAMPLE_DATA;
	private $ACCOUNT_LOCATION_CACHE;
	private $CACHE_FILE_NAME;
	
	public $disabled = false;
	public $error = false;
	
	/* CONSTRUCT */
	
	public function __construct($username = false, $password = false, $masterAccountId = false, $accountId = false, $debug = false)
	{
		if(!defined("EWS_LOCATION_SERVICE_ENDPOINT"))
		{
			//define("EWS_LOCATION_SERVICE_ENDPOINT", "sandbox.marketing.ews.yahooapis.com");
			define("EWS_LOCATION_SERVICE_ENDPOINT", "global.marketing.ews.yahooapis.com");
			define("EWS_ACCESS_HTTP_PROTOCOL", "https");
			define("EWS_DEBUG", $debug);
			define("EWS_VERSION", "V6");
			define("EWS_NAMESPACE", "http://marketing.ews.yahooapis.com/".EWS_VERSION);
			define("TEMP_FILES_DIR", sys_get_temp_dir() );
			define("MARKET", "US");
			define("SAMPLE_DATA_DIR", ".");
			define("SAMPLE_DATA_FILENAME", "sample_data_");
		}
		
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Username' ";
		$Row = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Row);
		$Yahoo_PPC_Username = $Row['value'];
		
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Password' ";
		$Row = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Row);
		$Yahoo_PPC_Password = $Row['value'];
		
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Master_Account_ID' ";
		$Row = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Row);
		$Yahoo_PPC_Master_Account_ID = $Row['value'];
		
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Account_ID' ";
		$Row = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Row);
		$Yahoo_PPC_Account_ID = $Row['value'];
		
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_License' ";
		$Row = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Row);
		$Yahoo_PPC_License = $Row['value'];
		
		
		$this->EWS_HEADERS = array(
		    "username"          => $Yahoo_PPC_Username,
		    "password"          => $Yahoo_PPC_Password,
		    "masterAccountID"   => $Yahoo_PPC_Master_Account_ID,
		    "accountID"         => $Yahoo_PPC_Account_ID,
		    "license"		=> $Yahoo_PPC_License
		);
		
		
		
		if(EWS_LOCATION_SERVICE_ENDPOINT == "sandbox.marketing.ews.yahooapis.com")
		{
			$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Sandbox_Username' ";
			$Row = mysql_query($Sql);
			$Row = mysql_fetch_assoc($Row);
			$Yahoo_PPC_Sandbox_Username = $Row['value'];
			
			$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Sandbox_Password' ";
			$Row = mysql_query($Sql);
			$Row = mysql_fetch_assoc($Row);
			$Yahoo_PPC_Sandbox_Password = $Row['value'];
			
			$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Sandbox_Master_Account_ID' ";
			$Row = mysql_query($Sql);
			$Row = mysql_fetch_assoc($Row);
			$Yahoo_PPC_Sandbox_Master_Account_ID = $Row['value'];
			
			$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Sandbox_Account_ID' ";
			$Row = mysql_query($Sql);
			$Row = mysql_fetch_assoc($Row);
			$Yahoo_PPC_Sandbox_Account_ID = $Row['value'];
			
			$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Yahoo_PPC_Sandbox_License' ";
			$Row = mysql_query($Sql);
			$Row = mysql_fetch_assoc($Row);
			$Yahoo_PPC_Sandbox_License = $Row['value'];
			
			
			$this->EWS_HEADERS = array(
			    "username"          => $Yahoo_PPC_Sandbox_Username,
			    "password"          => $Yahoo_PPC_Sandbox_Password,
			    "masterAccountID"   => $Yahoo_PPC_Sandbox_Master_Account_ID,
			    "accountID"         => $Yahoo_PPC_Sandbox_Account_ID,
			    "license"		=> $Yahoo_PPC_Sandbox_License
			);
		}
		
		if($accountId != false)
			$this->internal_account_id = $accountId;
		if($username != false)
			$this->EWS_HEADERS['onBehalfOfUsername'] = $username;
		if($password != false)
			$this->EWS_HEADERS['onBehalfOfPassword'] = $password;

		if($username != false && $password != false)
		{
			$this->EWS_HEADERS['masterAccountID'] = $masterAccountId;
			$this->EWS_HEADERS['accountID'] = '';
		}
		
		$this->SAMPLE_DATA = array();
		$this->ACCOUNT_LOCATION_CACHE = array();
		$this->loadLocationCache();
		$this->accountID = $this->EWS_HEADERS['accountID'];
		
		$this->createAccountService();
		
		$this->username = $this->EWS_HEADERS['username'];
		$this->password = $this->EWS_HEADERS['password'];
		$this->masteraccountid = $this->EWS_HEADERS['masterAccountID'];
	
		if($this->accountService == NULL)
		{
			$this->error = 'Unable to connect to account ' . $this->username . '.';
			$this->disabled = true;
			return;
		}
		
		if($this->EWS_HEADERS['accountID'] == '')
		{
			$this->EWS_HEADERS['accountID'] = $this->getAccountID();
			$this->accountID = $this->EWS_HEADERS['accountID'];
			$this->username = $this->EWS_HEADERS['onBehalfOfUsername'];
			$this->password = $this->EWS_HEADERS['onBehalfOfPassword'];
		}
		
		$this->createBasicReportService();
		$this->createExcludedWordsService();
		$this->createBudgetingService();
		$this->createTargetingService();
		$this->createCampaignService();
		$this->createAdGroupService();
		$this->createKeywordService();
		$this->createAdService();
	}
	
	/* PUBLIC */

	
	public function getCampaignUsingNameAll($name)
	{
		$campaigns = $this->getAllCampaigns();
		
		foreach($campaigns as $c)
		{
			if($c->name == $name)
				return $c;
		}
	}
	
	public function getCampaignIdUsingName($name)
	{
		$campaigns = $this->getActiveCampaigns();
		
		foreach($campaigns as $c)
		{
			if($c->name == $name)
				return floatval($c->ID);
		}
	}
	
	public function getCampaign($CampaignID)
	{
		$retObj = $this->execute(
				$this->campaignService,
				'getCampaign',
				array(
					'campaignID'=>floatval($CampaignID)
				)
			);
		
		return $retObj->out;
	}

	public function getAllCampaigns()
	{
		if(!isset($this->campaignService))
			$this->createCampaignService();
		
		$campaigns = $this->campaignService->getCampaignsByAccountID(array('accountID'=>$this->EWS_HEADERS['accountID'], 'includeDeleted'=>true));
		if(!isset($campaigns->out->Campaign))
			return array();
		if(!is_array($campaigns->out->Campaign))
			return array($campaigns->out->Campaign);
		return $campaigns->out->Campaign;
	}
	
	public function getActiveCampaigns()
	{
		if(!isset($this->campaignService))
			$this->createCampaignService();
		
		$campaigns = $this->campaignService->getCampaignsByAccountID(array('accountID'=>$this->EWS_HEADERS['accountID'], 'includeDeleted'=>false));
		if(!isset($campaigns->out->Campaign))
			return array();
		if(!is_array($campaigns->out->Campaign))
			return array($campaigns->out->Campaign);
		return $campaigns->out->Campaign;
	}
	
	public function getActiveAdGroups($cID)
	{
		$adGroups = $this->adGroupService->getAdGroupsByCampaignID(array('campaignID'=>floatval($cID), 'includeDeleted'=>false, 'startElement'=>0, 'numElements'=>1000));
		if(!isset($adGroups->out->AdGroup))
			return array();
		if(!is_array($adGroups->out->AdGroup))
			return array($adGroups->out->AdGroup);
		return $adGroups->out->AdGroup;
	}
	
	public function getAdGroupIdUsingName($name, $cID)
	{
		$adGroups = $this->getActiveAdGroups($cID);
		
		foreach($adGroups as $c)
		{
			if($c->name == $name)
				return floatval($c->ID);
		}
	}
	
	public function getAdsByAdGroupID($aID)
	{
		$ads = $this->adService->getAdsByAdGroupID(array('adGroupID'=>floatval($aID), 'includeDeleted'=>true));
		if(!isset($ads->out->Ad))
			return array();
		if(!is_array($ads->out->Ad))
			return array($ads->out->Ad);
		return $ads->out->Ad;
	}
	
	public function getKeywordsByAdGroupID($aID)
	{
		$kws = $this->keywordService->getKeywordsByAdGroupID(array('adGroupID'=>floatval($aID), 'includeDeleted'=>true, 'startElement'=>0, 'numElements'=>1000));
		if(!isset($kws->out->Keyword))
			return array();
		if(!is_array($kws->out->Keyword))
			return array($kws->out->Keyword);
		return $kws->out->Keyword;
	}
	
	public function getAdByAdId($aID)
	{
		$ad = $this->adService->getAd(array('adID'=>floatval($aID)));
		return $ad->out;
	}
	
	public function getReportList($onlyCompleted = false)
	{
		if(!isset($this->basicReportService))
			$this->createBasicReportService();
			
		$params = array('onlyCompleted'=>$onlyCompleted);
		$output = $this->basicReportService->getReportList($params);
		return $output;
	}
	
	public function getAccounts()
	{
		$c = $this->accountService->getAccounts();
		return $c->out;
	}
	
	private function getAccountID()
	{
		$c = $this->accountService->getAccounts();
		$out = $c->out;
		$account = is_array($out->Account) ? $out->Account[0] : $out->Account;
		$id = $account->ID;
		return $id;
	}
	
	public function addReport($reportName, $reportType, $dateStart = false, $dateEnd = false)
	{
		if(!isset($this->basicReportService))
			$this->createBasicReportService();
		
		
		if(!$dateStart)
		    $dateStart = date('c', strtotime('yesterday'));
		if(!$dateEnd)
		    $dateEnd = $dateStart;
		
			
		$reportRequestParams = array('reportName'=>$reportName, 'reportType'=>$reportType, 'startDate'=>$dateStart, 'endDate'=>$dateEnd);

		$formatParams = array('zipped'=>false, 'fileOutputType'=>'XML');
		$params = array('accountID'=>$this->accountID, 'reportRequest'=>$reportRequestParams, 'fileOutputFormat' => $formatParams);
		$output = new stdClass();
		$output = $this->basicReportService->addReportRequest($params);
		return $output->out;
	}
		
	public function getReport($id)
	{
		if(!isset($this->basicReportService))
			$this->createBasicReportService();
		
		$reportParams = array('reportID'=>$id);
		$output = $this->basicReportService->getReportDownloadUrl($reportParams);
		
		return $output->out;
	}
	
	public function addAdGroup($name, $cm_bid, $ss_bid, $campaign_id, $NegativeKeywords, $adDst)
	{
		if(!isset($this->adGroupService))
			$this->createAdGroupService();

		$contSrch = $sponSrch = true;
		
		if($adDst == '')
		{
			//leave default
		}
		
		if($cm_bid == 0)
		{
			$cm_bid = 0.01;
			$adDst = 'Search';
		}
		if($adDst == 'Search')
		{
			$contSrch = false;
			$sponSrch = true;
		}
		if($adDst == 'Content')
		{
			$contSrch = true;
			$sponSrch = false;
		}
		
		$accountID = $this->accountID;
		$adGroup = $this->createAdGroup(
			NULL,                               /* ID                      */
			$accountID,                         /* account                 */
			$name,						        /* name                    */
			false,                              /* auto optimization       */
			false,                              /* advanced match          */
			floatval($campaign_id),                       /* campaign                */
			$cm_bid,							/* content match maxbid    */
			$contSrch,                               /* content match           */
			$ss_bid, 							/* sponsored search maxbid */
			$sponSrch,                               /* sponsored search        */
			'On',                               /* status                  */
			false);                             /* watch                   */
		
		yahoo_api_debug("Calling addAdGroup");
		
		$retObj = $this->execute(
		        $this->adGroupService,
		        'addAdGroup',
		        array( 'adGroup' => $adGroup )
		        );

		$out = $this->checkResponse($retObj);

		if($out === false)
		{
			$this->addExcludedWords($retObj->out->adGroup->ID, $NegativeKeywords);
			return floatval($retObj->out->adGroup->ID);
		}
		return $out;
	}
		
	public function getTargetingProfileForCampaign($CampaignID)
	{
		$retObj = $this->execute(
				$this->targetingService,
				'getTargetingProfileForCampaign',
				array(
					'campaignID'=>floatval($CampaignID)
				)
			);
		return $retObj;
	}
	
	
	public function updateCampaignDailySpendLimit($CampaignID, $Budget)
	{
		$retObj = $this->execute(
				$this->budgetingService,
				'updateCampaignDailySpendLimit',
				array(
					'campaignID'=>floatval($CampaignID),
					'spendLimit'=>array('limit'=>floatval($Budget))
				)
			);
		
		return $retObj;
	}

	public function getCampaignDailySpendLimit($CampaignID)
	{
		$retObj = $this->execute(
				$this->budgetingService,
				'getCampaignDailySpendLimit',
				array(
					'campaignID'=>floatval($CampaignID)
				)
			);
		
		return $retObj;
	}
	
	public function updateTargetingProfileForCampaign($CampaignID, $Profile)
	{
		$retObj = $this->execute(
				$this->targetingService,
				'updateTargetingProfileForCampaign',
				array(
					'campaignID'=>floatval($CampaignID),
					'targetingProfile'=>$Profile,
					'updateAll'=>false
				)
			);
		return $retObj;
	}
	
	public function createTargetingProfileForCountries($Countries)
	{
		
		if(!isset($this->targetingService))
			$this->createTargetingService();
			
		if(!isset($this->geographicalDictionaryService))
			$this->createGeographicalDictionaryService();
		
		$geoTargets = array();
		
		foreach($Countries->countries as $Country)
		{
			$loc = $this->execute(
						$this->geographicalDictionaryService,
						'getGeoLocationsByString',
						array('geoString'=>$Country)
					);
			
					
			if(!isset($loc->out->GeoLocationProbability))
				continue;
				
			if(sizeOf($loc->out->GeoLocationProbability)==1)
				$loc->out->GeoLocationProbability = array($loc->out->GeoLocationProbability);
			
			foreach($loc->out->GeoLocationProbability as $locP)
			{
				if(!isset($locP->geoLocation))
					continue;
					
				if($locP->geoLocation->placeType->value == 'Country')
				{
					$geoTargets[] = array('geoLocation'=>$locP->geoLocation);
					continue;
				}
					
				if($locP->probability < 0.4)
					continue;
				//$geoTargets[] = $locP->geoLocation;
			}
		}
		/*
		$retObj = $this->execute(
				$this->targetingService,
				'getTargetingAttributesForCampaign',
				array(
					'campaignID'=>57157001
					)
			);
			
		
		 * $retObj = $this->execute(
				$this->targetingService,
				'addTargetingAttributesForCampaign',
				array(
					'campaignID'=>57157001,
					'targetingAttributes'=>array(
							'targetingAttributeDescriptor'=>array(
									'targetingType'=>'Country')
					)
				)
			);
		 */
		
		if(!sizeOf($geoTargets))
			return false;
			
		return array('geoTargets'=>$geoTargets);
				
	}
    public function getExcludedWords($adGroupId)
    {
      if(!isset($this->excludedWordsService))
        $this->createExcludedWordsService();
      yahoo_api_debug("Calling getExcludedWords($adGroupId)");
      $params = array('adGroupID' => $adGroupId);
      return $this->execute($this->excludedWordsService, 'getExcludedWordsByAdGroupID', $params);
    }
	public function addExcludedWords($adGroupId, $ExWords)
	{
		if(!isset($this->excludedWordsService))
			$this->createExcludedWordsService();

		yahoo_api_debug("Calling addExcludedWords");
		
		foreach($ExWords as $ExWord)
		{
			$excludedWordObject = array(
				'accountID'	=>		$this->accountID,
				'adGroupID'	=>		floatval($adGroupId),
				'text'		=>		$ExWord
			);
			
		
			$retObj = $this->execute(
								$this->excludedWordsService,
								'addExcludedWordToAdGroup',
								array('excludedWord'=>$excludedWordObject)
									);
		}
	}
	
	public function deleteCampaign($campaignID)
	{
		if(!isset($this->campaignService))
			$this->createCampaignService();
			
		$retObj = $this->execute($this->campaignService,
					'deleteCampaign',
					array( 'campaignID' => floatval($campaignID) )
					);
		
		$out = $this->checkResponse($retObj);

		if($out === false)
		{
			return false;
		}
		return floatval($out);
	}
	
	public function addCampaign($name, $description, $countries, $budget)
	{
		if(!isset($this->campaignService))
			$this->createCampaignService();
		$profile = $this->createTargetingProfileForCountries($countries);
		
		$now = time();
		
		$campaign = $this->createCampaign(
			 NULL,                                  /* ID */
			$name,          						/* name */
			$description,   						/* description */
			$this->accountID,						/* accountID */
			'On',                                   /* status */
			true,                                   /* sponsored search */
			true,                                   /* advanced match */
			false,                                  /* campaign optimization */
			true,                                   /* content match */
			date( "c", $now),   					/* start date - now */
			date( "c", $now + 60*60*24*365) 		/* end date - one year from now */
			);
		
		yahoo_api_debug("Calling addCampaign");
		
		if($profile === false && sizeOf($countries)>0)
		{
			return 'Geotargeting Error: No targets found for the locations specified. Please be sure that targets are located within your account scope (US based accounts can only request US-based targets).';
		}

		$retObj = $this->execute($this->campaignService,
					'addCampaign',
					array( 'campaign' => $campaign )
					);
		
		$out = $this->checkResponse($retObj);
		if($out === false)
		{
			$this->updateTargetingProfileForCampaign($retObj->out->campaign->ID, $profile);
			$this->updateCampaignDailySpendLimit($retObj->out->campaign->ID, $budget);
			return floatval($retObj->out->campaign->ID);
		}
		return $out;
	}
	
	public function addKeywords($adGroupID, $keywordText, $keywordMaxBid, $keywordURL, $advmatch = false)
	{
		if($advmatch == '1')
		{
			$advmatch = true;
		}else{
			$advmatch = false;
		}
		if($keywordMaxBid <= 0 || $keywordMaxBid == '')
			$keywordMaxBid = 0.01;
			
		if($keywordURL == '')
			$keywordURL = NULL;
			
		$keyword = $this->createKeyword(
			NULL,                               /* ID */
			floatval($adGroupID),
			$advmatch,                          /* advanced match */
			NULL,                               /* alt text */
			$keywordMaxBid, 					/* ss max bid */
			'On',                               /* status */
			$keywordText,       				/* text */
			$keywordURL,						/* URL */
			NULL                                /* watch */
		);
		
		yahoo_api_debug("Calling addKeywords");
		
		$keywordsParam = array( 'keywords' =>
			array($keyword)
			);

		$retObj = $this->execute($this->keywordService, 'addKeywords', $keywordsParam);
		
		$out = $this->checkResponse($retObj->out->KeywordResponse);
		if($out === false)
		{
			return floatval($retObj->out->KeywordResponse->keyword->ID);
		}
		return $out;
	}
	
	public function addAd($adGroupID, $title, $url, $displayURL, $description)
	{
		$title = @iconv("UTF-8", "UTF-8", $title);
		$description = @iconv("UTF-8", "UTF-8", $description);
		$name = $title;
		$ad = $this->createAd(
					NULL,                       		/* ID */
					floatval($adGroupID),							/* adgroup id */
					$description,  				   		/* description */
					$displayURL,						/* display URL */
					$name,						       	/* name */
					$description, 						/* short description */
					'On',                           	/* status */
					$title,   				    		/* title */
					$url					       		/* URL */
					);
		yahoo_api_debug("Calling addAds");
		
		$adsParam = array( 'ads' => array($ad) );
		
		$retObj = $this->execute($this->adService, 'addAds', $adsParam);

		
		
		$out = $this->checkResponse($retObj->out->AdResponse);
		if($out === false)
		{
			//$out = $this->checkForEditorialReason($this->adService, $retObj->out->AdResponse->ad);
		}
		if($out === false)
		{
			return floatval($retObj->out->AdResponse->ad->ID);
		}
		return $out;
	}
	
	public function deleteKeywords($adGroupID, $keywordId)
	{
		$kwsParam = array( 'keywordID' => $keywordId );
		$retObj = $this->execute($this->keywordService, 'deleteKeyword', $kwsParam);
	}
	
	public function deleteAds($adGroupID, $adId)
	{
		$adsParam = array( 'adID' => $adId );
		$retObj = $this->execute($this->adService, 'deleteAd', $adsParam);
	}
	
	public function rcsQueueOutput($id, $JobID = false, $user_id = false)
	{
	    $user_id = intval($user_id);
		$username = $this->username;
		$password = $this->password;
		$masteraccountid = $this->masteraccountid;
		$accountID = $this->internal_account_id;
		$time = time();
		
		$PATH = PATH;
		
		$output = <<<END
<?php
	require_once('{$PATH}AbsoluteIncludeHelper.include.php');
	
	\$yahoo_api = new yahoo_api('$username', '$password', '$masteraccountid', $accountID);
	\$report = \$yahoo_api->getReport($id);
	if(\$report->reportStatus == 'Pending')
	{
		\$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description) VALUES
		(
			(select id from bevomedia_queue where jobId='$JobID'),
	 		now(), now(), 'YAHOO', 'message', 'Still waiting for report request #$id'
		)";
		echo \$msgQuery;
		mysql_query(\$msgQuery);
		
   		\$Queue = new QueueComponent();
		\$NEWJOBID =  \$Queue->CreateJobID('Yahoo: $username REQUEUED', $user_id);
    	echo 'REQUEUED';
		\$envelope = \$yahoo_api->rcsQueueOutput($id, '$JobID', $user_id);
		\$Queue->SendEnvelope(\$NEWJOBID, \$envelope);
	}else{
		require_once('{$PATH}yahoo_api/yahoo_api_import.php');
		\$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description) VALUES
		(
			(select id from bevomedia_queue where jobId='$JobID'),
	 		now(), now(), 'YAHOO', 'success', 'Recieved report for request #$id'
		)";
		mysql_query(\$msgQuery);
		YahooImportUploadReport(\$report->downloadUrl, $accountID, '$JobID');
		\$reportId = \$yahoo_api->addReport('AdSummary-$user_id-$time', 'AdSummary');
		\$Queue = new QueueComponent();
		\$NEWJOBID =  \$Queue->CreateJobID('Yahoo AV: $username', $user_id);
		\$envelope = \$yahoo_api->rcsQueueAdVarsOutput(\$reportId, \$NEWJOBID);
		\$Queue->SendEnvelope(\$NEWJOBID, \$envelope);
		require_once('{$PATH}Accounts_Yahoo.class.php');
\$account = new Accounts_Yahoo();
\$account->GetInfo($accountID);
\$account->setQueueJobId('$JobID');
//echo "Updated " . \$account->UpdateCampaignsFromAPI() . " campaigns";
	}
?>
END;
		return $output;
	}
	
	public function rcsQueueAdVarsOutput($id, $JobID, $user_id = false)
	{
	    $user_id = intval($user_id);
		$username = $this->username;
		$password = $this->password;
		$masteraccountid = $this->masteraccountid;
		$accountID = 1;
		$accountID = $this->internal_account_id;

		$PATH = PATH;
		
		$output = <<<END
<?php
	require_once('{$PATH}AbsoluteIncludeHelper.include.php');
	
	\$yahoo_api = new yahoo_api('$username', '$password', '$masteraccountid', $accountID);
	\$report = \$yahoo_api->getReport($id);
	if(\$report->reportStatus == 'Pending')
	{
		sleep(60);
   		\$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description) VALUES
		(
			(select id from bevomedia_queue where jobId='$JobID'),
	 		now(), now(), 'YAHOO', 'message', 'Still waiting for report request #$id'
		)";
		echo \$msgQuery;
		mysql_query(\$msgQuery);
		
   		\$Queue = new QueueComponent();
		\$NEWJOBID =  \$Queue->CreateJobID('Yahoo AV: $username REQUEUED', $user_id);
    	echo 'REQUEUED';
		\$envelope = \$yahoo_api->rcsQueueAdVarsOutput($id, '$JobID', $user_id);
		\$Queue->SendEnvelope(\$NEWJOBID, \$envelope);
	}else{
		require_once('{$PATH}yahoo_api/yahoo_api_import_ads.php');
		\$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description) VALUES
		(
			(select id from bevomedia_queue where jobId='$JobID'),
	 		now(), now(), 'YAHOO', 'success', 'Recieved report for request #$id'
		)";
		mysql_query(\$msgQuery);
		YahooImportUploadReportAds(\$report->downloadUrl, $accountID);
	}
?>
END;
		return $output;
	}
	
	/* PRIVATE */
	
	private function createExcludedWordsService()
	{
		$this->excludedWordsService = $this->createClient(EWS_VERSION."/ExcludedWordsService", $this->accountID);
	}

	private function createTargetingService()
	{
		$this->targetingService = $this->createClient(EWS_VERSION."/TargetingService", $this->accountID);
	}

	private function createBudgetingService()
	{
		$this->budgetingService = $this->createClient(EWS_VERSION."/BudgetingService", $this->accountID);
	}
	
	private function createBasicReportService()
	{
		$this->basicReportService = $this->createClient(EWS_VERSION."/BasicReportService", $this->accountID);
	}
	
	private function createCampaignService()
	{
		$this->campaignService = $this->createClient(EWS_VERSION."/CampaignService", $this->accountID);
	}
	
	private function createAdGroupService()
	{
		$this->adGroupService = $this->createClient(EWS_VERSION."/AdGroupService", $this->accountID);
	}
	
	private function createKeywordService()
	{
		$this->keywordService = $this->createClient(EWS_VERSION."/KeywordService", $this->accountID);
	}
	
	private function createAccountService()
	{
		$this->accountService = $this->createClient(EWS_VERSION."/AccountService", $this->accountID);
	}
	
	private function createAdService()
	{
		$this->adService = $this->createClient(EWS_VERSION."/AdService", $this->accountID);
	}
	
	private function createGeographicalDictionaryService()
	{
		$this->geographicalDictionaryService = $this->createClient(EWS_VERSION."/GeographicalDictionaryService", $this->accountID);
	}
	
	private function createCampaign($ID, $name,$description,$accountID,$status,$sponsoredSearchON, $advancedMatchON, $campaignOptimizationON, $contentMatchON, $startDate, $endDate)
	{
		$campaign = array(
			'ID'                 => $ID,
			'name'               => $name,
			'description'        => $description,
			'accountID'          => $accountID,
			'status'             => $status,
			'sponsoredSearchON'  => $sponsoredSearchON,
			'advancedMatchON'    => $advancedMatchON,
			'campaignOptimizationON' => $campaignOptimizationON,
			'contentMatchON'     => $contentMatchON,
			'startDate'          => $startDate,
			'endDate'            => $endDate
			);
		
		return $campaign;
	}
	
	private function createAdGroup($ID,$accountID,$name,$adAutoOptimizationON,$advancedMatchON,$campaignID,$contentMatchMaxBid,
	$contentMatchON,$sponsoredSearchMaxBid,$sponsoredSearchON,$status,$watchON)
	{
		$adGroup = array (
			'ID'                    => $ID,
			'accountID'             => $accountID,
			'name'                  => $name,
			'adAutoOptimizationON'  => $adAutoOptimizationON,
			'advancedMatchON'       => $advancedMatchON,
			'campaignID'            => $campaignID,
			'contentMatchMaxBid'    => $contentMatchMaxBid,
			'contentMatchON'        => $contentMatchON,
			'sponsoredSearchMaxBid' => $sponsoredSearchMaxBid,
			'sponsoredSearchON'     => $sponsoredSearchON,
			'status'                => $status,
			'watchON'               => $watchON
		);
		
		return $adGroup;
	}
	
	private function createKeyword($ID,$adGroupID,$advancedMatchON,$alternateText,$sponsoredSearchMaxBid,$status,$text,$url,$watchON)
	{
		$keyword = array (
			'ID'                    => $ID,
			'adGroupID'             => $adGroupID,
			'advancedMatchON'       => $advancedMatchON,
			'alternateText'         => $alternateText,
			'sponsoredSearchMaxBid' => $sponsoredSearchMaxBid,
			'status'                => $status,
			'text'                  => $text,
			'url'                   => $url,
			'watchON'               => $watchON
		);
		
		return $keyword;
	}
	
	private function createAd($ID,$adGroupID,$description,$displayUrl,$name,$shortDescription,$status,$title,$url)
	{
		$ad = array (
				'ID'               => $ID,
				'adGroupID'        => $adGroupID,
				'description'      => $description,
				'displayUrl'       => $displayUrl,
				'name'             => $name,
				'shortDescription' => $shortDescription,
				'status'           => $status,
				'title'            => $title,
				'url'              => $url
		);
		
		return $ad;
	}
	
	private function checkResponse($retObj)
	{
		if( isset($retObj->out->operationSucceeded) && !$retObj->out->operationSucceeded )
		{
			$fault = $retObj->out->errors;
			if(is_array($fault->Error))
				return $fault->Error[0]->message;
			return $fault->Error->message;
			
			trigger_error("SOAP Fault: (faultcode: {$fault->Error->code}, faultstring: {$fault->Error->message})",E_USER_ERROR);
		}
		else if(isset($retObj->operationSucceeded) && !$retObj->operationSucceeded)
		{
			$fault = $retObj->errors;
			if(is_array($fault->Error))
				return $fault->Error[0]->message;
			return $fault->Error->message;
				
			$this->checkForEditorialReason(null,$retObj,$fault->Error->code);
			trigger_error("SOAP Fault: (faultcode: {$fault->Error->code}, faultstring: {$fault->Error->message})",E_USER_ERROR);
		}
		
		return false;
	}
	
	private function checkForEditorialReason($adService,$ad,$errorcode=NULL)
	{
		$editorialReasons = NULL;
		
		if(isset($ad->editorialStatus) && $ad->editorialStatus == "Pending")
		{
			return "Ad is in pending state.";
			//yahoo_api_debug(" Ad in pending state.");
			if($adService)
			{
				$retObj = $this->execute($this->adService,"getEditorialReasonsForAd",array("adID" => $ad->ID));
				$editorialReasons = $retObj->out;
			}
		}
		else if(isset($errorcode) && $errorcode == "E2014")
		{
			//return 'Ad rejected';
			//yahoo_api_debug(" Ad rejected.");
			$editorialReasons = $ad->editorialReasons;
		}
		
		  //There might not be any editorial reasons yet for a Pending OMO.
		if(isset($editorialReasons->adEditorialReasons))
		{
			return $editorialReasons->adEditorialReasons;
			$this->printEditorialReason(" Editorial Reason Code - Ad           : ", $editorialReasons->adEditorialReasons);
			$this->printEditorialReason(" Editorial Reason Code - decription   : ", $editorialReasons->descriptionEditorialReasons);
			$this->printEditorialReason(" Editorial Reason Code - display Url  : ", $editorialReasons->displayUrlEditorialReasons);
			$this->printEditorialReason(" Editorial Reason Code - short desc   : ", $editorialReasons->shortDescriptionEditorialReason);
			$this->printEditorialReason(" Editorial Reason Code - title        : ", $editorialReasons->titleEditorialReasons);
			$this->printEditorialReason(" Editorial Reason Code - url content  : ", $editorialReasons->urlContentEditorialReasons);
			$this->printEditorialReason(" Editorial Reason Code - url          : ", $editorialReasons->urlEditorialReasons);
			$this->printEditorialReason(" Editorial Reason Code - url string   : ", $editorialReasons->urlStringEditorialReasons);
		}
		
		return false;
	}
	
	private function printEditorialReason($msg,$reason)
	{
		if(isset($reason->int) && count($reason->int)==1)
		{
			yahoo_api_debug($msg.$reason->int);
		}
		else if(isset($reason->int) && count($reason->int)>1)
		{
			yahoo_api_debug($msg.implode(",",$reason->int));
		}
	}
	
	private function loadLocationCache()
	{
		$cacheFile = $this->getLocationCacheFileName();
		
		if(!file_exists($cacheFile) || !is_file($cacheFile)) return;
		
		$lines = file($cacheFile);
		
		$this->ACCOUNT_LOCATION_CACHE = array();
		
		foreach($lines as $line_num => $line)
		{
			$line = trim($line);
			if(!$line) continue;
			if(strpos($line,'#')===0) continue;
			
			list($accountID, $location) = explode('=',$line);
			
			$accountID = trim($accountID);
			$location  = trim($location);
			
			yahoo_api_debug("$accountID, $location;");
			
			if($accountID && $location)
			{
				$this->ACCOUNT_LOCATION_CACHE[$accountID] = $location;
			}
		}
		
		yahoo_api_debug(count($this->ACCOUNT_LOCATION_CACHE)." account location read from cache.");
		if(EWS_DEBUG) print_r($this->ACCOUNT_LOCATION_CACHE);
	}
	
	private function getLocationCacheFileName()
	{
	    if(!isset($this->CACHE_FILE_NAME))
	    {
	        $this->CACHE_FILE_NAME = "tmptest_ews_cache_";
	        $location = EWS_LOCATION_SERVICE_ENDPOINT;
	
	        for($i=0;$i<strlen(EWS_LOCATION_SERVICE_ENDPOINT);$i++)
	        {
	            $achar = $location[$i];
	            if(ord($achar)>=ord('A') && ord($achar)<=ord('z')) $this->CACHE_FILE_NAME .= $achar;
	        }
	
	        yahoo_api_debug("CACHE_FILE_NAME: $this->CACHE_FILE_NAME");
	    }
	
	    return TEMP_FILES_DIR."/".$this->CACHE_FILE_NAME;
	}
	
	private function createClient($service, $accountID, $useLocationService=true)
	{
		if($useLocationService) $wsdlEndPointURL = $this->getEndPointFromLocationService($service, $accountID);
		else $wsdlEndPointURL = EWS_ACCESS_HTTP_PROTOCOL."://".EWS_LOCATION_SERVICE_ENDPOINT."/services/".$service;
		
		if($wsdlEndPointURL == NULL)
			return NULL;
			
		yahoo_api_debug("Creating $service client");
		$client = new SoapClient(
				"$wsdlEndPointURL?wsdl",
				array(	'trace'      => true,
						'exceptions' => true,
						'location'   => $wsdlEndPointURL,
						'uri'        => EWS_NAMESPACE,
						'connection_timeout'=>10)
		);
		$headers = $this->createHeaders();
		
		yahoo_api_debug("Setting header");
		
		$client->__setSoapHeaders( $headers );
		
		return $client;
	}
	
	private function createHeaders()
	{
		$headers = array();
		
		foreach($this->EWS_HEADERS as $aHeaderName => $aHeaderValue)
		{
			yahoo_api_debug("Creating $aHeaderName header");
			
			$aHeader = new SoapHeader(
						EWS_NAMESPACE,
						$aHeaderName,
						$aHeaderValue
						);

			array_push($headers,$aHeader);
		}
		
		
		return $headers;
	}
	
	private function getEndPointFromLocationService($serviceName, $accountID)
	{
		$cachedAccountLocation = array_key_exists($accountID,$this->ACCOUNT_LOCATION_CACHE) ? $this->ACCOUNT_LOCATION_CACHE[$accountID] : NULL;
		
		if(!$cachedAccountLocation)
		{
			$client = $this->createClient(EWS_VERSION."/LocationService",NULL,false);
			
			if($client)
			{
				$response = $this->execute($client,"getMasterAccountLocation",NULL);
				if($response)
				{
					$cachedAccountLocation = $response->out;
					$this->persistAccountLocationCache($accountID,$cachedAccountLocation);
				}
			}
		}
		
		if(!$cachedAccountLocation)
		{
			return NULL;
		    trigger_error("Service Error: Failed to get account location from server: EWS_LOCATION_SERVICE_ENDPOINT",E_USER_ERROR);
		}
		
		return $cachedAccountLocation."/".$serviceName;
	}
	
	private function persistAccountLocationCache($accountID, $location)
	{
		global $ACCOUNT_LOCATION_CACHE, $CACHE_FILE_NAME;
		
		//Step 1: Store in the internal data structure
		$this->ACCOUNT_LOCATION_CACHE[$accountID] = $location;
		
		//Step 2: Store in a persistent store
		$cacheFile = $this->getLocationCacheFileName();
		
		if(!$handle = fopen($cacheFile, 'a'))
		{
			echo "Cannot open file ($cacheFile)";
			exit;
		}
		
		$accountLocationEntry = "\n$accountID = $location";
		
		// Write $somecontent to our opened file.
		if (fwrite($handle, $accountLocationEntry) === FALSE)
		{
			echo "Cannot write to file ($cacheFile)";
			exit;
		}
		@chmod($cacheFile, 0777);
		
		yahoo_api_debug("Success, wrote ($accountLocationEntry) to file ($cacheFile)" );
		
		fclose($handle);
	}
	
	private function execute($soapClient, $operation, $params)
	{
		try
		{
			if($params)
			{
				$result = $soapClient->__soapCall(
							$operation,
							array(
								$params
							)
						);
			}
			else
			{
				$result = $soapClient->__soapCall($operation,array());
			}
		}
		catch (SoapFault $fault)
		{
			return NULL;
			return 'API Error: <b>'.$fault->faultstring.'</b>';
			/*die();
			header('location: /publisher-mynetworks.php?yahoo_searchmarketing=1&error=1&message='.$fault->faultstring);
			die;
			trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})",E_USER_ERROR);
			*/
		}
		
		//check for faults in result
		if (isset($result) && is_soap_fault($result))
		{
			trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})",E_USER_ERROR);
		}
		else if(isset($result))
		{
			return $result;
		}
		else
		{
			return NULL;
		}
	}
	
}

?>