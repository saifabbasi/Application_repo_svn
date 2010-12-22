<?php

include(ABSPATH . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');
define('MSN_DEBUG', false);

class msn_api
{
	private $sandbox = false;
	private $suppressErrors = false;
	private $username, $password, $accountId, $devToken, $appToken;

	private $xmlns, $headerNamespace, $negativeKeywords;
	private $URI;
	private $customerProxy, $campaignProxy, $reportProxy, $adminProxy;
	
	public $responseHeaderTrackingId;
	
	public $error = false;
	public $disabled = false;
	
	public $customerId = false;
	
	/***** PUBLIC *****/
	public function __construct($username = false, $password = false, $devToken = '', $appToken = false, $accountId = false)
	{
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'MSN_Dev_Token' ";
		$Row = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Row);
		$devToken = $Row['value'];
		
		$quickAssign = array('username', 'password', 'accountId', 'devToken', 'appToken');
		foreach($quickAssign as $q)
			if($$q !== false)
				$this->{$q} = $$q;
		
		$this->init();
	}
	
	public function enableErrorSuppression()
	{
		$this->suppressErrors = true;
	}
	public function disableErrorSuppression()
	{
		$this->suppressErrors = false;
	}
	
	public function getAssignedQuota()
	{
		$client = $this->adminClient();
		$output = $this->soapCall($client, 'GetAssignedQuota', false);
		return $output;
	}
	
	public function getRemainingQuota()
	{
		$client = $this->adminClient();
		$output = $this->soapCall($client, 'GetRemainingQuota', false);
		return $output;
	}
	
	public function getAccounts()
	{
		$client = $this->customerClient();
		$output = $this->soapCall($client, 'GetAccounts', false, $this->createHeader('ApiUserAuthHeader'), false);
		return $output;
	}
	
	public function getReportFile($id = false)
	{
		if($id === false)
			return $id;
		$report = $this->getReport($id);
		
		if($report == 'PENDING')
		{
			return 'PENDING';
		}else{
		    if(!$report)
		        throw new Exception("Report not found: $id");
			return $this->downloadReport($report);
		}
	}
	
	public function downloadReport($url)
	{
		$dir = sys_get_temp_dir() . '/';
		$fileName = md5(time().$url) . '.zip';
		$handleIn = fopen($url,"r");
		$handleOut = fopen($dir . $fileName, "w+");
		while (!feof($handleIn))
		{
		  $content = fread($handleIn, 8192);
		  fwrite($handleOut,$content);
		}
		fclose($handleIn);
		fclose($handleOut);
		return $fileName;
	}
	
	public function addReport($name)
	{
		//$agg = 'Weekly'; $predef = 'LastWeek';
		$agg = 'Daily'; $predef = 'Yesterday';
		
		$request = new KeywordPerformanceReportRequest();
		$request->Format = 'Csv';
		$request->Language = 'English';
		$request->ReportName = $name;
		$request->ReturnOnlyCompleteData = false;
		$request->Aggregation = $agg;
		$request->Time = array('PredefinedTime'=>$predef);
		
		$request->Columns = array('TimePeriod',
								'AccountName',
		                        'CampaignName',
								'AdGroupName',
		                        'Keyword',
		                        'CurrentMaxCpc',
		                        'Impressions',
		                        'Clicks',
								'AverageCpc',
								'CostPerConversion',
								'AveragePosition',
								'AdType',
								'AdDistribution',
								'KeywordId'
								);
		
		$request->Filter = array('AdDistribution'=>'Search Content');
		
		$request->Scope = array('AccountIds'=>array($this->accountId));
		
		$soapstruct = new SoapVar($request,
		                          SOAP_ENC_OBJECT,
		                          "KeywordPerformanceReportRequest",
		                          $this->xmlns);
		
		$params=array('ReportRequest'=>$soapstruct);
		
		$client = $this->reportClient();
		$output = $this->soapCall($client, 'SubmitGenerateReport', $params);
		
//		print '<pre>';
//		print_r($output);
		
		if(is_string($output))
			return 'FAILED';
		return $output->ReportRequestId;
	}
	
	public function addAdReport($name)
	{
		//$agg = 'Monthly'; $predef = 'LastSixMonths';
		$agg = 'Daily'; $predef = 'Yesterday';
		
		$request = new AdPerformanceReportRequest();
		$request->Format = 'Csv';
		$request->Language = 'English';
		$request->ReportName = $name;
		$request->ReturnOnlyCompleteData = false;
		$request->Aggregation = $agg;
		$request->Time = array('PredefinedTime'=>$predef);
		
		$request->Columns = array('TimePeriod',
								'AccountName',
		                        'CampaignName',
								'AdGroupName',
		                        'AdId',
		                        'AdDescription',
		                        'Impressions',
		                        'Clicks',
								'AverageCpc',
								'CostPerConversion',
								'AveragePosition',
								'AdType',
								'AdDistribution',
								'AdTitle',
								'AdGroupId'
								);
		
		$request->Filter = array('AdDistribution'=>'Search Content');
		
		$request->Scope = array('AccountIds'=>array($this->accountId));
		
		$soapstruct = new SoapVar($request,
		                          SOAP_ENC_OBJECT,
		                          "AdPerformanceReportRequest",
		                          $this->xmlns);
		
		$params=array('ReportRequest'=>$soapstruct);
		
		$client = $this->reportClient();
		$output = $this->soapCall($client, 'SubmitGenerateReport', $params);
		
		return $output->ReportRequestId;
	}
	
	public function addSearchQueryReport($name)
	{
		$agg = 'Daily'; $predef = 'Yesterday';
		
		$request = new SearchQueryPerformanceReportRequest();
		$request->Format = 'Csv';
		$request->Language = 'English';
		$request->ReportName = $name;
		$request->ReturnOnlyCompleteData = false;
		$request->Aggregation = $agg;
		$request->Time = array('PredefinedTime'=>$predef);
		
		$request->Columns = array('TimePeriod',
								'AccountName',
		                        'CampaignName',
								'AdGroupName',
		                        'AdId',
		                        'Impressions',
		                        'Clicks',
								'AverageCpc',
								'AveragePosition',
								'AdType',
								'AdGroupId',
								'SearchQuery',
								'Spend'
								);
		
		$request->Filter = array('AdDistribution'=>'Search Content');
		
		$request->Scope = array('AccountIds'=>array($this->accountId));
		
		$soapstruct = new SoapVar($request,
		                          SOAP_ENC_OBJECT,
		                          "SearchQueryPerformanceReportRequest",
		                          $this->xmlns);
		
		$params = array('ReportRequest'=>$soapstruct);
		
		$client = $this->reportClient();
		$output = $this->soapCall($client, 'SubmitGenerateReport', $params);
		
		return $output->ReportRequestId;
	}
	
	public function getReport($reportId)
	{
		$client = $this->reportClient();
		$params=array('ReportRequestId'=>$reportId);
		$output = $this->soapCall($client, 'PollGenerateReport', $params);
        if(is_string($output))
            return false;
		if($output->ReportRequestStatus->Status == 'Success')
			return $output->ReportRequestStatus->ReportDownloadUrl;
		else
			return 'PENDING';
	}
	
	public function getCampaigns()
	{
		$client = $this->campaignClient();
		$params = array('AccountId' => $this->accountId);
		$output = $this->soapCall($client, 'GetCampaignsByAccountId', $params);
		$campaigns = $output->Campaigns->Campaign;
		if(!is_array($campaigns))
			$campaigns = array($campaigns);
		
		return $campaigns;
	}
	
	public function getCampaignUsingName($name)
	{
		$name = trim($name);
		$campaigns = $this->getCampaigns();
		foreach($campaigns as $campaign)
		{
			if($campaign->Name == $name)
				return $campaign;
		}
		return false;
	}
	
	public function getCampaignIdUsingName($name)
	{
		$name = trim($name);
		$campaigns = $this->getCampaigns();
		foreach($campaigns as $campaign)
		{
			if($campaign->Name == $name)
				return floatval($campaign->Id);
		}
		return false;
	}
	
	public function getAdGroups($campaignId)
	{
		$client = $this->campaignClient();
		$params = array('CampaignId' => $campaignId);
		$output = $this->soapCall($client, 'GetAdGroupsByCampaignId', $params);
		
		if(!isset($output->AdGroups))
			return array();
		if(!isset($output->AdGroups->AdGroup))
			return array();
			
		$adGroups = $output->AdGroups->AdGroup;
		if(!is_array($adGroups))
			$adGroups = array($adGroups);
		
		return $adGroups;
	}
	
	public function getAdGroupIdUsingName($name, $campaignId)
	{
		$name = trim($name);
		$adGroups = $this->getAdGroups($campaignId);
		if(!sizeOf($adGroups))
			return false;
			
		foreach($adGroups as $adGroup)
		{
			if($adGroup->Name == $name)
				return floatval($adGroup->Id);
		}
		return false;
	}
	
	public function getAds($adGroupId)
	{
		$client = $this->campaignClient();
		$params = array('AdGroupId' => $adGroupId);
		$output = $this->soapCall($client, 'GetAdsByAdGroupId', $params);
		$ads = $output->Ads->Ad;
		
		if(!is_array($ads))
			$ads = array($ads);
		
		return $ads;
	}
	
	public function getAd($adGroupId, $adId)
	{
		$client = $this->campaignClient();
		$params = array('AdGroupId' => $adGroupId, 'AdIds'=>array($adId));
		$output = $this->soapCall($client, 'GetAdsByIds', $params);
		echo 'getAd Soap Call Output: '; print_r($output); echo "\n\n";
		if($output === false)
			return false;
		if(sizeOf($output->Ads->Ad) == 0)
			return false;
			
		$ads = $output->Ads->Ad;
		
		return $ads;
	}
	
	public function getKeywords($adGroupId)
	{
		$client = $this->campaignClient();
		$params = array('AdGroupId' => $adGroupId);
		$output = $this->soapCall($client, 'GetKeywordsByAdGroupId', $params);
		$kws = $output->Keywords->Keyword;
		if(!is_array($kws))
			$kws = array($kws);
		
		return $kws;
	}
	
	public function addKeywords($keywords, $adGroupId)
	{
		$client = $this->campaignClient();
		$params = array('AdGroupId'=>$adGroupId, 'Keywords'=>array('Keyword'=>$keywords));
		$output = $this->soapCall($client, 'AddKeywords', $params);
		if(isset($output->KeywordIds->long))
			return floatval($output->KeywordIds->long);
		return $output;
	}
	
	public function createKeywordArray($text, $bid = null, $param1 = null)
	{
		$base =
		array(
			"Text"				=> $text,
    	    "BroadMatchBid"		=> array('Amount' => 0),
    	    "PhraseMatchBid"	=> array('Amount' => 0),
    	    "ExactMatchBid"		=> array('Amount' => 0),
			"Param1"			=> $param1,
    	    "NegativeKeywords"	=> $this->negativeKeywords
		);
		if(stripos($text, '[') !== false)
		{
			$base['ExactMatchBid'] = array('Amount' => $bid);
		}else if(stripos($text, '"') !== false){
			$base['PhraseMatchBid'] = array('Amount' => $bid);
		}else{
			$base['BroadMatchBid'] = array('Amount' => $bid);
		}
		return $base;
	}
	
	public function addAdGroups($adGroups, $campaignId)
	{
		$client = $this->campaignClient();
		$params = array('CampaignId'=>$campaignId, 'AdGroups'=>array('AdGroup'=>$adGroups));
		$output = $this->soapCall($client, 'AddAdGroups', $params);
		if(isset($output->AdGroupIds->long))
			return floatval($output->AdGroupIds->long);
		return $output;
	}
	
	public function createAdGroupArray($name, $bid, $adDst = 'Search', $neg = null, $contentBid = 0, $endYear = '2012', $endMonth = '12', $endDay = '31', $biddingModel = 'Keyword', $pricingModel = 'Cpc', $startDate = null, $lang = 'EnglishUnitedStates')
	{
		if($contentBid == 0)
			$contentBid = null;
			
		if($adDst == '')
			$adDst = 'Search';
		if($adDst == 'SearchContent')
		{
			$adDst = array('Search', 'Content');
		}
		$adDst = 'Search';
		//$this->negativeKeywords = $neg;
		
		return
		array(
			'Name'=> $name,
			'AdDistribution' => $adDst,
			'BiddingModel' => $biddingModel,
			'PricingModel' => $pricingModel,
			'ExactMatchBid' => array('Amount' => $bid),
			'ContentMatchBid' => array('Amount' => $contentBid),
			'StartDate' => $startDate,
			'EndDate' => array('Day'=>$endDay, 'Month'=>$endMonth, 'Year'=>$endYear),
			'LanguageAndRegion'=>$lang,
			'NegativeKeywords' => $neg,
			'Status'=>'Active'
		);
	}
	
	public function updateAdDestinationUrl($adGroupId, $adId, $url)
	{
		$client = $this->campaignClient();
		$ads = array($this->createUpdateUrlAd($adId, $url));
		$params = array('AdGroupId'=>$adGroupId, 'Ads'=>array('Ad'=>$ads));
		$output = $this->soapCall($client, 'UpdateAds', $params);
		return $output;
	}
	
	public function deleteAdGroups($campaignId, $adGroupIds)
	{
		if(!is_array($adGroupIds))
		{
			$adGroupIds = array($adGroupIds);
		}
		$client = $this->campaignClient();
		$params = array('CampaignId'=>$campaignId, 'AdGroupIds'=>$adGroupIds);
		$output = $this->soapCall($client, 'DeleteAdGroups', $params);
		if(isset($output->AdIds->long))
			return floatval($output->AdIds->long);
		return $output;
	}
	
	public function deleteKeywords($adGroupId, $keywordIds)
	{
		if(!is_array($keywordIds))
		{
			$keywordIds = array($keywordIds);
		}
		$client = $this->campaignClient();
		$params = array('AdGroupId'=>$adGroupId, 'KeywordIds'=>$keywordIds);
		$output = $this->soapCall($client, 'DeleteKeywords', $params);
		//print_r($output);
		//print_r($this->responseHeaderTrackingId);
		if(isset($output->KeywordIds->long))
			return floatval($output->KeywordIds->long);
		return $output;
	}
	
	public function deleteAds($adGroupId, $adIds)
	{
		if(!is_array($adIds))
		{
			$adIds = array($adIds);
		}
		$client = $this->campaignClient();
		$params = array('AdGroupId'=>$adGroupId, 'AdIds'=>$adIds);
		$output = $this->soapCall($client, 'DeleteAds', $params);
		//print_r($output);
		//print_r($this->responseHeaderTrackingId);
		if(isset($output->AdIds->long))
			return floatval($output->AdIds->long);
		return $output;
	}
	
	public function addAds($ads, $adGroupId)
	{
		$client = $this->campaignClient();
		$params = array('AdGroupId'=>$adGroupId, 'Ads'=>array('Ad'=>$ads));
		$output = $this->soapCall($client, 'AddAds', $params);
		if(isset($output->AdIds->long))
			return floatval($output->AdIds->long);
		return $output;
	}
	
	public function createUpdateUrlAd($id, $destUrl, $type='TextAd')
	{
		$output = array('Id'=>$id, 'DestinationUrl'=>$destUrl);
		return new SoapVar($output, SOAP_ENC_OBJECT, $type, $this->xmlns);
	}
	
	public function createAd($title, $destUrl, $displayUrl, $text, $type='TextAd')
	{
		$output = array('Title'=>$title, 'DestinationUrl'=>$destUrl, 'DisplayUrl'=>$displayUrl, 'Text'=>$text);
		return new SoapVar($output, SOAP_ENC_OBJECT, $type, $this->xmlns);
	}
	
	public function addCampaigns($campaigns)
	{
		$client = $this->campaignClient();
		$params = array('AccountId'=>$this->accountId, 'Campaigns'=>array('Campaign'=>$campaigns));
		$output = $this->soapCall($client, 'AddCampaigns', $params);
		
		if(isset($output->CampaignIds->long))
			return floatval($output->CampaignIds->long);
		else
			return $output;
	}
	
	public function getTargetsByCampaignId($campaignId)
	{
		$client = $this->campaignClient();
		$params = array('CampaignIds' => array($campaignId));
		$output = $this->soapCall($client, 'GetTargetsByCampaignIds', $params);
		$kws = $output->Keywords->Keyword;
		if(!is_array($kws))
			$kws = array($kws);
		
		return $kws;
	}
	
	public function addCountryTargetsToLibrary($Countries)
	{
		$Countries = $Countries->countries;
		$targets = array();
		$targets[0] = new Target();
		$targets[0]->Name = 'Test Target Country Region Targetting #1';
		$countryTargetBids = array();
		foreach($Countries as $Country)
		{
			$temp = new CountryTargetBid();
			$temp->CountryAndRegion = $Country;
			$temp->IncrementalBid = 'ZeroPercent';
			$countryTargetBids[] = $temp;
			$temp = false;
		}
		$targets[0]->Location = new LocationTarget();
		$targets[0]->Location->CountryTarget = new MSN_CountryTarget();
		$targets[0]->Location->CountryTarget->Bids = $countryTargetBids;
		$targets[0]->Location->TargetAllLocations = 'false';
		$client = $this->campaignClient();
		$params = array('Targets' => array('Target' => $targets));
		$output = $this->soapCall($client, 'AddTargetsToLibrary', $params);
		if(isset($output->TargetIds->long))
			return intval($output->TargetIds->long);
		return $output;
	}
	
	public function createCampaignArray($name, $description, $dailybudget = 100, $neg = null, $budgetType = 'MonthlyBudgetSpendUntilDepleted', $dst = 'true', $timezone = 'PacificTimeUSCanadaTijuana')
	{
		return
		array(
			'BudgetType' => $budgetType,
			'ConversionTrackingEnabled' => 'false',
			'DaylightSaving' => $dst,
			'Description' => $description,
			'MonthlyBudget' => $dailybudget * 30,
			'Name' => $name,
			'NegativeKeywords' => $neg,
			'TimeZone' => $timezone
		);
	}
	
	public function getAccountId()
	{
		return $this->accountId;
	}
	
	
	/***** RCS QUEUE *****/
	public function rcsQueueOutput($report_id, $jobId, $user_id, $msn_account_id)
	{
		$username = $this->username;
		$password = $this->password;
		$time = time();
		
		$PATH = PATH;
		$output = <<<END
<?php
	require_once('{$PATH}AbsoluteIncludeHelper.include.php');
	
	\$msn = new msn_api('$username', '$password');
	\$report = \$msn->getReportFile($report_id);
	if(\$report == 'PENDING')
	{
		global \$QueueComponentJobID;
		\$Queue = new QueueComponent();
		\$NEWJOBID =  \$Queue->CreateJobID();
		\$myQuery = "update bevomedia_queue set started = '0000-00-00 00:00:00', completed='0000-00-00 00:00:00'
      					where JobID = '" . \$QueueComponentJobID. "'" ;
    	mysql_query(\$myQuery);
    	\$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description) VALUES
    	(
    		(select id from bevomedia_queue where jobId='".\$QueueComponentJobID."'),
    		now(), now(), 'MSN', 'message', 'Waiting for report... Requeued'
    	)";
    	mysql_query(\$msgQuery);
	}else{
		require_once('{$PATH}Accounts_MSNAdCenter.class.php');
		\$account = new Accounts_MSNAdCenter();
		\$account->setQueueJobId('$jobId');
		\$account->GetInfo($msn_account_id);
		echo "Updated " . \$account->UpdateCampaignsFromAPI() . " campaigns";
		require_once('{$PATH}msn_api/msn_api_import.php');
		echo (int)UploadReport($user_id, $msn_account_id, \$report);
		\$reportId = \$msn->addAdReport('AdReport-$user_id--$time');
		\$Queue = new QueueComponent();
		\$JobID =  \$Queue->CreateJobID('MSN Ad Report', $user_id);
		\$envelope = \$msn->rcsQueueOutputAdVars(\$reportId, \$JobID, $user_id, $msn_account_id);
		\$Queue->SendEnvelope(\$JobID, \$envelope);

	}
?>
END;
		return $output;
	}
	
	public function rcsQueueOutputAdVars($report_id, $jobId, $user_id, $msn_account_id)
	{
		$username = $this->username;
		$password = $this->password;
		$time = time();
		
		$PATH = PATH;
		$output = <<<END
<?php
	require_once('{$PATH}AbsoluteIncludeHelper.include.php');
	
	\$msn = new msn_api('$username', '$password');
	\$report = \$msn->getReportFile($report_id);
	if(\$report == 'PENDING')
	{
   		global \$QueueComponentJobID;
		\$myQuery = "update bevomedia_queue set started = '0000-00-00 00:00:00', completed='0000-00-00 00:00:00'
      					where JobID = '" . \$QueueComponentJobID. "'" ;
    	mysql_query(\$myQuery);
    	\$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description) VALUES
    	(
    		(select id from bevomedia_queue where jobId='".\$QueueComponentJobID."'),
    		now(), now(), 'MSN', 'message', 'Waiting for report... Requeued'
    	)";
    	mysql_query(\$msgQuery);
	}else{
		require_once('{$PATH}msn_api/msn_api_import_ads.php');
		echo (int)UploadReportAds($user_id, $msn_account_id, \$report);
		\$Queue = new QueueComponent();
		\$JobID =  \$Queue->CreateJobID('MSN SQ Report', $user_id);
		\$reportId = \$msn->addSearchQueryReport('SQReport-$user_id-$time');
		\$envelope = \$msn->rcsQueueOutputSearchQuery(\$reportId, \$JobID, $user_id, $msn_account_id);
		\$Queue->SendEnvelope(\$JobID, \$envelope);
	}
?>
END;
		return $output;
	}
	
	public function rcsQueueOutputSearchQuery($report_id, $jobId, $user_id, $msn_account_id)
	{
		$username = $this->username;
		$password = $this->password;
		$time = time();
		
		$PATH = PATH;
		$output = <<<END
<?php
	require_once('{$PATH}AbsoluteIncludeHelper.include.php');
	
	\$msn = new msn_api('$username', '$password');
	\$report = \$msn->getReportFile($report_id);
	if(\$report == 'PENDING')
	{
   		global \$QueueComponentJobID;
		\$myQuery = "update bevomedia_queue set started = '0000-00-00 00:00:00', completed='0000-00-00 00:00:00'
      					where JobID = '" . \$QueueComponentJobID. "'" ;
    	mysql_query(\$myQuery);
    	\$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description) VALUES
    	(
    		(select id from bevomedia_queue where jobId='".\$QueueComponentJobID."'),
    		now(), now(), 'MSN', 'message', 'Waiting for report... Requeued'
    	)";
    	mysql_query(\$msgQuery);
	}else{
		require_once('{$PATH}msn_api/msn_api_query_import.php');
		echo (int)UploadReportQuery($user_id, $msn_account_id, \$report);
	}
?>
END;
		return $output;
	}
	
	
	/***** PRIVATE *****/
	private function customerClient()
	{
		return $this->createClient($this->customerProxy);
	}
	
	private function campaignClient()
	{
		return $this->createClient($this->campaignProxy);
	}
	
	private function adminClient()
	{
		return $this->createClient($this->adminProxy);
	}
	
	private function reportClient()
	{
		return $this->createClient($this->reportProxy);
	}
	
	private function forceAccountId()
	{
		$accounts = $this->getAccounts();
		if(!isset($accounts->GetAccountsResult))
		{
			$this->error = 'Unable to connect to account ' . $this->username . '.';
			$this->disabled = true;
			return;
		}
		$accounts = $accounts->GetAccountsResult;
		if(isset($accounts->AdCenterAccount))
		{
			if(is_array($accounts->AdCenterAccount))
			{
				$accountArr = $accounts->AdCenterAccount;
			}else{
				$accountArr = $accounts;
			}
		}
		
		if(sizeOf($accountArr) == 0)
		{
			//echo ('ERROR: NO ACCOUNTS');
			return false;
		}
		
		foreach($accountArr as $account)
		{
			if($account->Status == 'Active')
				$this->accountId = $account->AccountId;
		}
	}
	
	private function createClient($proxy)
	{
		$opts = array('trace'=>true);
		$client = new SOAPClient($proxy, $opts);
		return $client;
	}
	
	private function createHeader($type = false)
	{
		$header = array();
		
		if($type === false)
		{
			if(isset($this->appToken))
				$header[] = new SoapHeader($this->xmlns, 'ApplicationToken', $this->appToken, false);
			if(isset($this->devToken))
				$header[] = new SoapHeader($this->xmlns, 'DeveloperToken', $this->devToken, false);
			if(isset($this->username))
				$header[] = new SoapHeader($this->xmlns, 'UserName', $this->username, false);
			if(isset($this->password))
				$header[] = new SoapHeader($this->xmlns, 'Password', $this->password, false);
			if(isset($this->accountId))
				$header[] = new SoapHeader($this->xmlns, 'CustomerAccountId', $this->accountId, false);

			$header[] = new SoapHeader($this->xmlns, 'CustomerId', $this->customerId, false);
		}
		
		if($type == 'ApiUserAuthHeader')
		{
			$header['UserName'] = $this->username;
			$header['Password'] = $this->password;
			$header['UserAccessKey'] = $this->devToken;
			$header = new SoapHeader($this->headerNamespace, 'ApiUserAuthHeader', $header, false);
		}
		
		return $header;
	}
	
	private function init()
	{
		if($this->sandbox)
		{
			$this->URI = 'https://sandboxapi.adcenter.microsoft.com/Api/Advertiser/v6/';
			$this->devToken = '38AQ1766Y47W';
		}else
			$this->URI = 'https://adcenterapi.microsoft.com/api/advertiser/v6/';
		
		$this->negativeKeywords = null;
		$this->headerNamespace = 'http://adcenter.microsoft.com/syncapis';
		$this->xmlns = 'https://adcenter.microsoft.com/v6';
		
		$this->customerProxy = $this->URI . '/CustomerManagement/CustomerManagement.asmx?wsdl';
		$this->campaignProxy = $this->URI . '/CampaignManagement/CampaignManagementService.svc?wsdl';
		$this->reportProxy = $this->URI . 'Reporting/ReportingService.svc?wsdl';
		$this->adminProxy = $this->URI . 'Administration/AdministrationService.svc?wsdl';
		
		if(!isset($this->accountId))
			$this->forceAccountId();
	}
	
	private function soapCall($client, $action, $params = false, $inputHeaders = false, $outputHeaders = true)
	{
		if($inputHeaders === false)
			$inputHeaders = $this->createHeader();
			
		if($params === false)
			$params = array('APIFlags' => 0);
		else
			$params = array($action.'Request'=>$params);
		
		try{
			if($outputHeaders === false)
				$result = $client->__soapCall($action, $params, null, $inputHeaders);
			else
				$result = $client->__soapCall($action, $params, null, $inputHeaders, $outputHeaders);
		}catch(Exception $e){
			//print_r($e);
			$msg = $this->processError($e);
			return $msg;
		}
		
		if($outputHeaders)
			$this->responseHeaderTrackingId = $outputHeaders['TrackingId'];
			
		return $result;
	}
	
	private function processError($e)
	{
		if($this->suppressErrors == true)
			return false;
			
			//ERROR REPO
			//*/
		
		if(isset($e->detail->ApiFaultDetail->BatchErrors->BatchError->Message))
			$error = $e->detail->ApiFaultDetail->BatchErrors->BatchError->Message;
		else if(isset($e->detail->ApiFaultDetail->BatchErrors->BatchError->Message))
			$error = $e->detail->ApiFaultDetail->BatchErrors->BatchError->Message;
		else if(isset($e->detail->AdApiFaultDetail->Errors->AdApiError->Message))
			$error = $e->detail->AdApiFaultDetail->Errors->AdApiError->Message;
		else if(isset($e->detail->ApiFaultDetail->BatchErrors->BatchError[0]->Message))
			$error = $e->detail->ApiFaultDetail->BatchErrors->BatchError[0]->Message;
		else if(isset($e->detail->EditorialApiFaultDetail->BatchErrors->BatchError->Message))
			$error = $e->detail->EditorialApiFaultDetail->BatchErrors->BatchError->Message;
		else if(isset($e->detail->EditorialApiFaultDetail->BatchErrors->BatchError[0]->Message))
			$error = $e->detail->EditorialApiFaultDetail->BatchErrors->BatchError[0]->Message;
		else if(isset($e->detail->EditorialApiFaultDetail->EditorialErrors->EditorialError->Message))
			$error = $e->detail->EditorialApiFaultDetail->EditorialErrors->EditorialError->Message;
		else if(isset($e->detail->EditorialApiFaultDetail->EditorialErrors->EditorialError[0]->Message))
			$error = $e->detail->EditorialApiFaultDetail->EditorialErrors->EditorialError[0]->Message;
		else if(isset($e->detail->ExceptionDetail->InnerException->Message))
			$error = $e->detail->ExceptionDetail->InnerException->Message;
		else
			$error = 'An uncaught API related error has occurred.';
			
		return $error;
	}
}

class AdPerformanceReportRequest
{
	public $Format;
	public $Language;
	public $ReportName;
	public $ReturnOnlyCompleteData;
	public $Aggregation;
	public $Columns;
	public $Filter;
	public $Scope;
	public $Time;
}

class SearchQueryPerformanceReportRequest
{
	public $Format;
	public $Language;
	public $ReportName;
	public $ReturnOnlyCompleteData;
	public $Aggregation;
	public $Columns;
	public $Filter;
	public $Scope;
	public $Time;
}

class KeywordPerformanceReportRequest
{
	public $Format;
	public $Language;
	public $ReportName;
	public $ReturnOnlyCompleteData;
	public $Aggregation;
	public $Columns;
	public $Filter;
	public $Scope;
	public $Time;
}

class Target
{
    public $Age;
    public $Day;
    public $Gender;
    public $Hour;
    public $Id;
    public $Location;
}

class LocationTarget
{
    public $BusinessTarget;
    public $CityTarget;
    public $CountryTarget;
    public $MetroAreaTarget;
    public $RadiusTarget;
    public $StateTarget;
    public $TargetAllLocations;
}

class MSN_CountryTarget
{
    public $Bids;
}

class CountryTargetBid
{
    public $CountryAndRegion;
    public $IncrementalBid;
}


?>