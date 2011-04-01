<?php
require_once('apility.php');
require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');

class apility_assist
{
	private $api;
	public $enabled = true;
	
	public $error = false;
	public $disabled = false;
	
	function __construct($accountId = false)
	{
		if($accountId === false)
		{
			
		}else{
			$creds = $this->retrieveCreds($accountId);
			$disabled = $this->checkDisabled($accountId);
			if($creds['password'] == '' || $disabled)
				$this->notEnabled();
			else
				$this->connect($creds['username'], $creds['password']);
		}
	}
	
	function notEnabled()
	{
		$this->api = false;
		$this->enabled = false;
	}
	
	function checkDisabled($id)
	{
		if(isset($_GET['Confirm']))
			return false;
			
		$sql = 'SELECT * FROM bevomedia_accounts_adwords WHERE enabled = 0 AND id = ' . $id;
		$query = mysql_query($sql);
		$row = mysql_fetch_assoc($query);
		return (isset($row['ID']));
	}
	
	function getSearchQueryReport($reportName = false, $dateFrom = false, $dateTo = false)
	{
		if($reportName === false)
		{
			$reportName = 'Search Query Report ' . time();
		}
		if($dateFrom === false)
		{
			//$dateFrom = '2009-12-11';
			$dateFrom = date('Y-m-d', strtotime('TODAY -2 days'));
		}
		if($dateTo === false)
		{
			//$dateTo = '2009-02-28';
			$dateTo = date('Y-m-d');
		}
		
		$report = getSearchQueryXmlReport(
		  $reportName,
		  $dateFrom,
		  $dateTo,
		  array('CreativeId', 'Campaign', 'CampaignId', 'AdGroup', 'AdGroupId', 'Query', 'Impressions', 'Clicks', 'Cost', 'CPC', 'CTR', 'MatchType', 'AveragePosition'),
		  array('Creative', 'Daily'),
		  array(),
		  array(),
		  array(),
		  array(),
		  array(),
		  array(),
		  '',
		  '',
		  false,
		  array(),
		  false,
		  30,
		  true
		);
		
		return simplexml_load_string($report);
	}
	
	function retrieveCreds($id)
	{
		$sql = 'SELECT username, password FROM bevomedia_accounts_adwords WHERE ID = ' . $id;
		$row = mysql_fetch_assoc(mysql_query($sql));
		return array('username'=>$row['username'], 'password'=>$row['password']);
	}
	
	function getAccountInfo()
	{
		return $this->api->getAccountInfo();
	}
	
	function connect($username, $password, $api_key = '')
	{	
		$sba = '';
		//$sba = 'client_1+'; //client managers cannot have campaigns warning
		if($password == '')
		{
			$this->error = 'Unable to connect to account ' . $username . '.';
			$this->disabled = true;
			$this->notEnabled();
			return;
		}
		
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Google_Adwords_Developer_Token' ";
		$Row = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Row);
		$Google_Adwords_Developer_Token = $Row['value'];
		
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Google_Adwords_Application_Token' ";
		$Row = mysql_query($Sql);
		$Row = mysql_fetch_assoc($Row);		
		$Google_Adwords_Application_Token = $Row['value'];
		
		
		
		$this->api = new APIlityUser($username, $password, $sba.$username, ($api_key=='')?$Google_Adwords_Developer_Token:$api_key, $Google_Adwords_Application_Token);
		
 		$r = $this->api->getManagersClientAccounts();
	}
	
	function getCampaignFromAdGroup($id)
	{
		$adGroupObject = createAdGroupObject($id);
		$campaignObject = createCampaignObject($adGroupObject->belongsToCampaignId);
		return($campaignObject->getCampaignData());  
	}
	
	function getUnitCountForMethod($service, $method, $datefrom, $dateto)
	{
		$datefrom = date('Y-m-d', strtotime($datefrom));
		$dateto = date('Y-m-d', strtotime($dateto));
		return(
		  getUnitCountForMethod($service, $method, $datefrom, $dateto)
		);
	}
	
	function getUnitCount($day)
	{
		$yesterday = gmdate(
			  "Y-m-d",
			  mktime(
			    date("H"),
			    date("i"),
			    date("s"),
			    date("m"),
			    $day,
			    date("Y")
			  )
			);
			$dayBeforeYesterday = gmdate(
			  "Y-m-d",
			  mktime(
			    date("H"),
			    date("i"),
			    date("s"),
			    date("m"),
			    $day,
			    date("Y")
			  )
		);
		return getUnitCount($dayBeforeYesterday, $yesterday);  
	}
	
	function getUsageQuotaThisMonth()
	{
		return getUsageQuotaThisMonth();
	}
	
	function getCampaign($id)
	{
		$obj = createCampaignObject($id);
		return $obj->getCampaignData();
	}
	
	function addAdGroup($data)
	{
		$adGroupObject = addAdGroup($data['name'], $data['campaignId'], $data['status'], $data['maxCPC']);	
		
		if(isset($adGroupObject->id))
		{
			$this->addNegativeKeywords($adGroupObject->id, $data['negativeKeywords']);
			return floatval($adGroupObject->id);
		}
		
		return $adGroupObject;
	}
	
	function addNegativeKeywords($adGroupId, $negArr)
	{
		foreach($negArr as $neg)
		{
			$l = $this->addKeywordCriterion($adGroupId, $neg, 0, false, 'all', true);
		}
	}
	
	function addNegativeKeywordsToCampaign($campaignId, $negArr)
	{
		if(!sizeOf($negArr))
			return false;
		$campaignObject = createCampaignObject($campaignId);
		$negs = array();
		foreach($negArr as $neg)
		{
			$negs[] = array('text'=>$neg, 'type'=>"Broad");
		}
		$s = $campaignObject->setCampaignNegativeKeywordCriteria($negs);  
	}
	
	function createAdGroupArray($strAdGroupName, $apiCampaignId, $intAdGroupCPC, $Neg, $ContentBid = false)
	{
		$adGroupData = array();
		$adGroupData['name'] = $strAdGroupName;
		$adGroupData['campaignId'] = $apiCampaignId;
		$adGroupData['status'] = 'Enabled';
		$adGroupData['maxCPC'] = number_format($intAdGroupCPC,2);
		$adGroupData['negativeKeywords'] = $Neg;

		if(!$ContentBid)
		{
		}else{
			$adGroupData['keywordContentMaxCpc'] = number_format($ContentBid,2);
		}
		return $adGroupData;
	}
	
	function addCampaign($data, $neg = false)
	{
		$campaignObject = addCampaign(
		  $data['name'],
		  $data['status'],
		  $data['start'],
		  $data['end'],
		  $data['budgetAmount'],
		  $data['budgetPeriod'],
		  $data['networkTarget'],
		  $data['languages'],
		  $data['newGeoTargets']
		);
		
		if(isset($campaignObject->id))
		{
			if($neg !== false)
			{
				$this->addNegativeKeywordsToCampaign($campaignObject->id, $neg);
			}
			return floatval($campaignObject->id);
		}
		
		return $campaignObject;
	}
	
	function createCampaignArray($name, $budget = 50, $geo, $target)
	{
		if(isset($geo->countries) && sizeOf($geo->countries) > 1)
		{
			$geo = array('countryTargets'=>array('countries'=>$geo->countries));
		}else{
			$geo = array('countryTargets'=>array('countries'=>array('US')));
		}
		
		$campaignData = array();
		
		$campaignData['name'] = $name;
		$campaignData['status'] = 'Active';
		$campaignData['start'] = '';
		$campaignData['end'] = '';
		$campaignData['budgetAmount'] = $budget;
		$campaignData['budgetPeriod'] = 'Daily';
		$campaignData['networkTarget'] = array("GoogleSearch", "ContentNetwork");
		if($target == 'Content')
			$campaignData['networkTarget'] = array("ContentNetwork");
		
		if($target == 'Search')
			$campaignData['networkTarget'] = array("GoogleSearch");
		
		
		$campaignData['languages'] = array("en");
		$campaignData['newGeoTargets'] = $geo;
		
		return $campaignData;
	}
	
	function deleteCampaign($campaignid)
	{
		$campaign = createCampaignObject($campaignid);
		removeCampaign($campaign);
	}
	
	function deleteKeywords($adgroupid, $keywordid)
	{
		$criterionObject = createCriterionObject($adgroupid, $keywordid);
		removeCriterion($criterionObject);
	}
	
	function deleteAds($adgroupid, $adid)
	{
		$adObject = createAdObject($adgroupid, $adid);
		removeAd($adObject);
		//die($adgroupid . ' ' . $adid);
	}
	
	function addTextAd($data)
	{
		$adObject = addTextAd(
		  $data['adGroupId'],
		  $data['headline'],
		  $data['line1'],
		  $data['line2'],
		  $data['status'],
		  $data['displayUrl'],
		  $data['destUrl']
		);
		
		
		if(isset($adObject->id))
			return floatval($adObject->id);
		return $adObject;
	}
	
	function createAd($adGroupId, $title, $destUrl, $dispUrl, $description)
	{
		$destUrl = str_replace('<br/>', '', $destUrl);
		$data = array();
		$data['adGroupId'] = $adGroupId;
		$data['headline'] = $title;// "The headline";
		$data['line1'] = substr($description, 0, 34);// "The description1";
		$data['line2'] = substr($description, 34);// "The description2";
		$data['status'] = "Enabled";
		$data['displayUrl'] = $dispUrl;// "http://groups.google.com/group/adwords-api-php";
		$data['destUrl'] = $destUrl;// "http://groups.google.com/group/adwords-api-php";
		return $data;
	}
	
	function updateAdUrl($campaignId, $adgroupId, $newUrl)
	{
		$adObject = createAdObject($campaignId, $adgroupId);
		$adObject->setDestinationUrl($newUrl);  
	}
		
	function getCampaignIdUsingName($n)
	{
		$aC = $this->getAllCampaigns();
		$id = false;
		foreach($aC as $k=>$v)
		{
			if($v->name == $n)
			{
				$id = $v->id;
				break;
			}
		}
		
		return floatval($id);
	}
	
	function addKeywordCriterion($adGroupId, $keyword, $bid, $url, $lang = 'all', $isNegative = false)
	{
		print $adGroupId;
		die;
		$type = 'Broad';
		if(strpos($keyword, '"') !== false)
		{
			$type = 'Phrase';
			$keyword = str_replace('"', '', $keyword);
			$keyword = str_replace('\\', '', $keyword);
		}
		if(strpos($keyword, '[') !== false && strpos($keyword, ']'))
		{
			$type = 'Exact';
			$keyword = str_replace('[', '', str_replace(']', '', $keyword));
		}
		if(strpos($keyword, '-'))
		{
			$type = 'Negative';
			$keyword = str_replace('-', '', $keyword);
		}
		
		if($url == 'http://')
			$url = '';

		if($this->getCriterionIdUsingName($keyword, $type, $adGroupId) === false)
		{
			
		}else{
			return 'Keyword already exists.';
		}
		
		$criterionObject = addKeywordCriterion(
		  $keyword,
		  $adGroupId,
		  $type,
		  $isNegative,
		  $bid,
		  $lang,
		  $url
		);
		
		if(isset($criterionObject->id))
			return floatval($criterionObject->id);
		return $criterionObject;
	}
	
	function getAllKeywords($adGroupId)
	{
		return getAllCriteria($adGroupId);
	}
	
	function getCriterionIdUsingName($n, $t, $adGroupId)
	{
		$kws = $this->getAllKeywords($adGroupId);

		$id = false;
		if(!sizeOf($kws))
		{
			return false;
		}
		if(!$kws)
			return false;
			
		foreach($kws as $kw)
		{
			if($kw->text == $n && $kw->type == $t)
			{
				$id = $kw->id;
				break;
			}
		}
		
		return $id;
	}
	
	function getAdGroupIdUsingName($n, $campaignId)
	{
		$agrps = getAllAdGroups($campaignId);
		$id = false;
		if(!sizeOf($agrps))
			return false;

		if(!$agrps)
			return false;
			
		foreach($agrps as $k=>$v)
		{
			if($v->name == $n)
			{
				$id = $v->id;
				break;
			}
		}
		
		return floatval($id);
	}
	
	function getAdsForAdGroup($cId, $name)
	{
		$adGroup = createAdGroupObject($this->getAdGroupIdUsingName($cId,$name));
		return $adGroup->getAllAds();
	}
	
	function getAdsForAdGroupId($aId)
	{
		$adGroup = createAdGroupObject($aId);
		return $adGroup->getAllAds();
	}
	
	
	function getActiveCampaigns()
	{
		return $this->api->getActiveCampaigns();
	}
	
	function getAllCampaigns()
	{
		return $this->api->getAllCampaigns();
	}
	
	function getAllAdGroups($cId)
	{
		$adgroups = getAllAdGroups($cId);
		return $adgroups;
	}
	
	function getActiveAdGroups($cId)
	{
		$adgroups = getActiveAdGroups($cId);
		return $adgroups;	
	}
	
	function calcAdGroupStats($agId, $beginDate='1970-01-01', $endDate = false )
	{
		$beginDate = gmdate('Y-m-d', strtotime($beginDate));
		if($endDate == false)
			$endDate = gmdate('Y-m-d');
		else
			$endDate = gmdate('Y-m-d', strtotime($endDate));
			
		$adGroupObject = createAdGroupObject($agId);
		$adstats = $adGroupObject->getAdGroupStats($beginDate, $endDate); 
		
		$output = array();
		$output['netImpr'] = $adstats['impressions'];
		$output['netClicks'] = $adstats['clicks'];
		$output['netCtr'] = 0;
		if($adstats['impressions'] != 0)
			$output['netCtr'] = round($adstats['clicks'] / $adstats['impressions'], 2);
		$output['avgCPC'] = 0;//cost/clicks
		if($adstats['clicks'] != 0)
			$output['avgCPC'] = round($adstats['cost'] / $adstats['clicks'], 2);
		$output['netCost'] = $adstats['cost'];
		
		return $output;
	}
	
	function calcCampaignStats($cId, $beginDate='1970-01-01', $endDate = false )
	{
		$agrps = getActiveAdGroups($cId);
		
		$totals = array();
		$totals['netImpr'] = $totals['netClicks'] = $totals['netCtr'] = $totals['avgCPC'] = $totals['netCost'] = 0;
		
		foreach($agrps as $ag)
		{
			$agstats = $this->calcAdGroupStats($ag->id);
			$quickit = array('netImpr', 'netClicks', 'netCtr', 'avgCPC', 'netCost');
			
			foreach($quickit as $q)
				$totals[$q] += $agStats[$q];
		}
		$totals['netCtr'] = round($totals['netCtr'], 2);
		$totals['avgCPC'] = round($totals['avgCPC'], 2);
		
		return $totals;
	}

	function outputAdGroups($date=false, $aC=false)
	{
		if(!$date)
		{
			$beginDate = date('Y/m/d', strtotime('yesterday'));
			$endDate = date('Y/m/d', strtotime('today'));
		}else{
			$date = explode(' - ', $date);
			if(sizeOf($date)>1)
			{
				$beginDate = $date[0];
				$endDate = $date[1];
			}else{
				$beginDate = $date[0];
				$endDate = false;
			}
		}
		if(!$aC)
			return;
		else
			$aC = $this->getCampaign($aC);
		
		$output = '';
		if(isset($_GET['DateRange']))
			$gDateRange = $_GET[DateRange];
		else
			$gDateRange = date('Y/m/d', strtotime('today'));

		$aG = $this->getActiveAdGroups($aC['id']);
		
		foreach($aG as $campaign)
		{
			$stats = $this->calcAdGroupStats($campaign->id, $beginDate, $endDate);
			$stats['netCtr'] = round($stats['netCtr'] * 100, 2);
			$output .= <<<END_TOKEN
		  <tr (php if ($blnAltRow) { echo 'class="AltRow"'; } )>
		    <td class="border">&nbsp;</td>
		    <td><a href="publisher-adwords-adgroup.php?ID=$campaign->id&DateRange=$gDateRange">$campaign->name</a></td>
			<td style="text-align: center;">$stats[netImpr]</td>
			<td style="text-align: center;">$stats[netClicks]</td>
			<td style="text-align: center;">$stats[netCtr]%</td>
			<td style="text-align: center;">$$stats[avgCPC]</td>
			<td style="text-align: center;">$$stats[netCost]</td>
			<td class="tail">&nbsp;</td>
		  </tr>
END_TOKEN;
		}
		return $output;
	}
	
	function outputCampaigns($date=false, $aC=false)
	{
		if(!$date)
		{
			$beginDate = date('Y/m/d', strtotime('yesterday'));
			$endDate = date('Y/m/d', strtotime('today'));
		}else{
			$date = explode(' - ', $date);
			if(sizeOf($date)>1)
			{
				$beginDate = $date[0];
				$endDate = $date[1];
			}else{
				$beginDate = $date[0];
				$endDate = false;
			}
		}
		if(!$aC)
			$aC = $this->getActiveCampaigns();
			
		$output = '';
		if(isset($_GET['DateRange']))
			$gDateRange = $_GET[DateRange];
		else
			$gDateRange = date('Y/m/d', strtotime('today'));
			
		foreach($aC as $campaign)
		{
			$stats = $this->calcCampaignStats($campaign->id, $beginDate, $endDate);
			$stats['netCtr'] = round($stats['netCtr'] * 100, 2);
			$output .= <<<END_TOKEN
		  <tr (php if ($blnAltRow) { echo 'class="AltRow"'; } )>
		    <td class="border">&nbsp;</td>
		    <td><a href="publisher-adwords-campaign.php?ID=$campaign->id&DateRange=$gDateRange">$campaign->name</a></td>
			<td style="text-align: center;">$stats[netImpr]</td>
			<td style="text-align: center;">$stats[netClicks]</td>
			<td style="text-align: center;">$stats[netCtr]%</td>
			<td style="text-align: center;">$$stats[avgCPC]</td>
			<td style="text-align: center;">$$stats[netCost]</td>
			<td class="tail">&nbsp;</td>
		  </tr>
END_TOKEN;
		}
		return $output;
	}
}


?>