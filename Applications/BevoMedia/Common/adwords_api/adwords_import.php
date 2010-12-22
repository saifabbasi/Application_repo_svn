<?php

$Temp = (realpath(substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' ));
require_once($Temp . DIRECTORY_SEPARATOR . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');

$_GET['Debug'] = true;
global $time;
$time = microtime(true);
$apiCount = 0;
function debugTime($s = false, $t = true)
{
	if(!isset($_GET['Debug']))
		return;
	if($t === false)
		return;
	global $time, $apiCount;
	$s = str_replace(array("\t", "\n"), '', $s);
	echo "\n" . number_format(microtime(true) - $time, 6) . "\t" . $s . "\t" . '>> API COUNT: ' . $apiCount;
	@flush();
	@ob_flush();
}

function update($user__id, $AccountID, $JobID = false, $startDate = false, $endDate = false)
{
	global $apiCount;
	$apiCount = 0;
	if(!$startDate)
	    $startDate = strtotime('YESTERDAY');
	if(!$endDate)
	    $endDate = $startDate;
	$dateRange = ($startDate == $endDate) ? $startDate : $startDate . ' - ' . $endDate;
	$StatDates = array();
	$DateDiff = floor(($endDate - $startDate) / 86400);
	for($i = 0; $i <= $DateDiff; $i++)
	{
	    $date = date('Ymd', $startDate + 86400*$i);
	    $StatDates[strtotime($date)] = $date;
	}
	
	$AdwordsAccount = new Accounts_Adwords($user__id);
	$AdwordsAccount->setQueueJobId($JobID);
	$AdwordsAccount->getInfo($AccountID);
	
	$updateQueueId = $AdwordsAccount->startQueuedJobLog("[ $dateRange ] Updating account", 'in-progress');
	
	require_once(PATH . 'User.class.php');
	$User = new User($user__id);
	$Balance = $User->apiCalls;
	
	if($Balance <= 1000)
	{
		$AdwordsAccount->finishQueuedJobLog($updateQueueId, 'message', 'Not enough API credit.');
		return false;
	}
	$DeletedCombinationsSQ = array();
	
	$debug = false;
	if(isset($_GET['Debug']))
	{
		print '<pre style="text-align:left; background-color:#ffffff;">';
		$debug = true;
	}
	
	if(!$AdwordsAccount->VerifyAccountAPI())
	{
		$AdwordsAccount->finishQueuedJobLog($updateQueueId, 'message', 'Account not enabled');
		return false;
	}
	
	$campaigns = $AdwordsAccount->GetCampaignsAPI();
	$apiCount+=sizeof($campaigns);
	$output = '';
	
	debugTime('BEGIN CAMPAIGN LOOP');
	debugTime('COUNT: ' . sizeOf($campaigns));
	foreach ($campaigns as $campaign)
	{
		$CampaignName = (string)$campaign->name;
		$CampaignName = mysql_real_escape_string($CampaignName);
		$Status = (string)$campaign->status;
		$Status = mysql_real_escape_string(strtoupper($Status));
		
		$Sql = "SELECT ID FROM bevomedia_ppc_campaigns WHERE (user__id = {$user__id}) AND (accountId = {$AccountID}) AND (name = '{$CampaignName}') ";
		
		debugTime('START CAMPAIGN SQL');
		$CampaignID = mysql_query($Sql);
		if (mysql_num_rows($CampaignID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_campaigns (user__id, ProviderType, AccountID, Name, Updated) VALUES ({$user__id}, 1, {$AccountID}, '{$CampaignName}', 0 ); ";
			mysql_query($Sql);
			$CampaignID = mysql_insert_id();
			$campaignQueueId = $AdwordsAccount->startQueuedJobLog("Importing new campaign '$CampaignName'", 'success');
		} else
		{
			$CampaignID = mysql_fetch_assoc($CampaignID);
			$CampaignID = $CampaignID['ID'];
			
		}
		$output .= $Sql . "\n";
		debugTime($Sql);
		
		$Sql = "UPDATE bevomedia_ppc_campaigns SET Status = '{$Status}', apiCampaignId={$campaign->id} WHERE ID = {$CampaignID} LIMIT 1";
		debugTime($Sql);
		mysql_query($Sql);
		
		
		if($campaign->status == 'PAUSED' || $campaign->status == 'DELETED')
		{
		    $campaignQueueId = $AdwordsAccount->startQueuedJobLog("'$CampaignName' set to ". $campaign->status , 'success');
			//debugTime($campaign->name . ' IS ' . $campaign->status);
			//debugTime('END CAMPAIGN SQL');
			// if the campaign status is PAUSED or DELETED then
			// continue to the next campaign
			//continue;
		}
		debugTime('END CAMPAIGN SQL');
		
		debugTime('GET ACTIVE ADGROUPS');
		$adgroups = $AdwordsAccount->getAdGroupsAPI($campaign->id);
		$apiCount+=sizeof($adgroups);
		
	
		debugTime('BEGIN ADGROUP LOOP');
		debugTime("\tCOUNT:" . sizeOf($adgroups));
		if(sizeOf($adgroups)>0)
		foreach($adgroups as $adgroup)
		{
			$AdGroupName = (string)$adgroup->name;
			$AdGroupName = mysql_real_escape_string($AdGroupName);
			
			debugTime('START ADGROUP SQL');
			$Sql = "SELECT id FROM bevomedia_ppc_adgroups WHERE (campaignId = {$CampaignID} ) AND (name = '{$AdGroupName}') ; ";
			$AdGroupID = mysql_query($Sql);
			if (mysql_num_rows($AdGroupID)==0)
			{
				$Sql = "INSERT INTO bevomedia_ppc_adgroups (campaignId, name, updated) VALUES ({$CampaignID}, '{$AdGroupName}', 0)  ";
				mysql_query($Sql);
				$AdGroupID = mysql_insert_id();
				$AdwordsAccount->startQueuedJobLog("Creating new adgroup '$AdGroupName'", 'success');
			} else {
				$AdGroupID = mysql_fetch_assoc($AdGroupID);
				$AdGroupID = $AdGroupID['id'];
				$AdwordsAccount->startQueuedJobLog("Found existing adgroup '$AdGroupName'", 'success');
			}
			debugTime($Sql, false);
			$output .= "\t" . $Sql . "\n";
			debugTime('END ADGROUP SQL');
	
			
			debugTime('GET ADS FOR ADGROUP');
			$ads = $AdwordsAccount->GetAdsForAdGroupIdAPI($adgroup->id);
			$apiCount+=sizeof($ads);

			debugTime('DATE: [$startDate:' . date('Y-m-d', $startDate ) . '] -TO- [$endDate' . date('Y-m-d', $endDate) .']');
			
			$StatDate = $startDate;
			$DeletedCombinations = array();
			
			debugTime('BEGIN AD LOOP');
			foreach($ads as $ad)
			{
				if($ad->AdType != 'TextAd')
					continue;
					
				$apiAdId = (float)$ad->id;
				$Updated = 0;
				$Status = 1;
				
				$title = mysql_real_escape_string($ad->headline);
				$url = mysql_real_escape_string($ad->destinationUrl);
				$displayUrl = mysql_real_escape_string($ad->displayUrl);
				$shortDescription = mysql_real_escape_string($ad->description1 . ' ' . $ad->description2);
				
				$Sql = "SELECT ID FROM bevomedia_ppc_advariations WHERE apiAdId = {$apiAdId} AND Title = '[Bevo Temporary Ad Variation]' ";
				$BevoTempAd = mysql_query($Sql);
				if(mysql_num_rows($BevoTempAd)>0)
				{
					$BevoTempAd = mysql_fetch_assoc($BevoTempAd);
					$Sql = "UPDATE bevomedia_ppc_advariations SET AdGroupID = '$AdGroupID' WHERE ID = $BevoTempAd[ID] LIMIT 1";
					mysql_query($Sql);
					$AdwordsAccount->startQueuedJobLog("Found title for existing ad variation '$title'", 'success');
					
				}
				
				
				$Sql = "SELECT ID FROM bevomedia_ppc_advariations WHERE (AdGroupID = {$AdGroupID}) AND (apiAdId = {$apiAdId})";
				$PPCAdVarID = mysql_query($Sql);
				
				if (mysql_num_rows($PPCAdVarID)==0)
				{
					$Sql = "INSERT INTO bevomedia_ppc_advariations (AdGroupID, apiAdId, Updated, Status)
											  VALUES ({$AdGroupID}, {$apiAdId}, {$Updated}, {$Status}); ";
					mysql_query($Sql);
					$PPCAdVarID = mysql_insert_id();
					$AdwordsAccount->startQueuedJobLog("Creating new ad variation '$title'", 'success');
				} else
				{
					$PPCAdVarID = mysql_fetch_assoc($PPCAdVarID);
					$PPCAdVarID = $PPCAdVarID['ID'];
					
					$Sql = "UPDATE bevomedia_ppc_advariations SET Updated = 1, Status = {$Status} WHERE ID = {$PPCAdVarID} ";
					mysql_query($Sql);
				}
				debugTime($Sql);
				
				$Sql = "UPDATE bevomedia_ppc_advariations SET title = '{$title}', url = '{$url}', displayUrl = '{$displayUrl}', description = '{$shortDescription}' WHERE ID = {$PPCAdVarID}";
				mysql_query($Sql);
				//bevomedia_ppc_advariations
				debugTime($Sql);
			}
			debugTime('END AD LOOP');

			
			debugTime('GET CRITERIA FOR ADGROUP');
			$keywords = $AdwordsAccount->getKeywordsAPI($adgroup->id);
			debugTime('BEGIN KEYWORD LOOP');
			foreach($keywords as $keyword)
			{
				$Keyword = strtolower((string)$keyword->text);
				$Keyword = mysql_real_escape_string($Keyword);
				
				debugTime('BEGIN KEYWORD SQL');
				$Sql = "SELECT id FROM bevomedia_keyword_tracker_keywords WHERE (Keyword = '{$Keyword}'); ";
				$KeywordID = mysql_query($Sql);
				if (mysql_num_rows($KeywordID)==0)
				{
					$Sql = "INSERT INTO bevomedia_keyword_tracker_keywords (Keyword) VALUES ('{$Keyword}'); ";
					mysql_query($Sql);
					$KeywordID = mysql_insert_id();
				} else
				{
					$KeywordID = mysql_fetch_assoc($KeywordID);
					$KeywordID = $KeywordID['id'];
				}
				debugTime($Sql, false);
				$output .= "\t\t" . $Sql . "\n";
				debugTime('END KEYWORD SQL    ');
				

				$MatchType = (string)$keyword->type;
				if ($MatchType=="Phrase") $MatchType = 1; else
				if ($MatchType=="Exact") $MatchType = 2; else
				$MatchType = 0;
				
				$Status = (string)$keyword->status;
				if ($Status=="Active") $Status = 1; else
				if ($Status=="Paused") $Status = 2; else
				$Status = 0;
				
				$MaxCPC = 0;
				if(isset($keyword->bids->maxCpc->amount->microAmount))
				{
					$MaxCPC = ((string)$keyword->bids->maxCpc->amount->microAmount)/1000000;
				}
				$DestURL = (string)$keyword->destinationUrl;
				$Updated = 0;
				
				if($MaxCPC == 0)
					$MaxCPC = $adgroup->bids->keywordMaxCpc->amount->microAmount/1000000;
								
				if ($DestURL=='default URL') $DestURL = '';
				$APIKeywordID = floatval($keyword->id);

								
				debugTime('BEGIN KEYWORD SQL2');
				$Sql = "SELECT ID FROM bevomedia_ppc_keywords WHERE (AdGroupID = {$AdGroupID}) AND (KeywordID = {$KeywordID})  AND (MatchType = {$MatchType}) ";
				$PPCKeywordID = mysql_query($Sql);
				if (mysql_num_rows($PPCKeywordID)==0)
				{
					$Sql = "INSERT INTO bevomedia_ppc_keywords (AdGroupID, apiKeywordId, KeywordID, MatchType, Status, MaxCPC, DestURL, Updated)
											  VALUES ({$AdGroupID}, {$APIKeywordID}, {$KeywordID}, {$MatchType}, {$Status}, {$MaxCPC}, '{$DestURL}', {$Updated}); ";
											  
					mysql_query($Sql);
					$PPCKeywordID = mysql_insert_id();
				} else
				{
					$PPCKeywordID = mysql_fetch_assoc($PPCKeywordID);
					$PPCKeywordID = $PPCKeywordID['ID'];
					
					$Sql = "UPDATE bevomedia_ppc_keywords SET apiKeywordId = {$APIKeywordID}, MatchType = {$MatchType}, Status = {$Status}, MaxCPC = {$MaxCPC}, DestURL = '{$DestURL}', Updated = 1 WHERE ID = {$PPCKeywordID} ";
					mysql_query($Sql);
				}
				debugTime($Sql, false);
				$output .= "\t\t\t" . $Sql . "\n";
				debugTime('END KEYWORD SQL2');
				debugTime($PPCKeywordID . ' => ' . $Keyword . ' :MatchType[' . $MatchType .']', true);
			}
		  debugTime('END KEYWORD LOOP');
		}
	$AdwordsAccount->finishQueuedJobLog($updateQueueId, 'success');
	}
	
	if(isset($_GET['NoCharge']))
	{
		// DO NOT CHARGE USER FOR THIS UPDATE
	}else{
		
		if ($User->vaultID==0)
		{
			echo "Account not verified...";
			return;
		}
		
		if ($User->apiCalls<$apiCount*5)
		{
			echo 'Not enough API credit for adwords api. Adding more...';
			$User->AddUserAPICallsCharge();
		}
		$User->subtractApiCalls($apiCount*2, 'Adwords Update');
	}
	
	if ($User->vaultID==0)
	{
		echo "Account not verified...";
		return;
	}
	
	
	$User->getInfo($user__id);
	if($User->apiCalls <= 500)
	{
		echo 'Not enough API credit. Adding more...';
		$User->AddUserAPICallsCharge();
	}
	{
		$Queue = new QueueComponent();
		$JobID = $Queue->CreateJobID('Adwords Query Report', $user__id);
		$PATH = PATH;
		$envelope = <<<END
	<?php
		require_once('{$PATH}adwords_api/adwords_query_import.php');
		\$au = new apility_assist({$AccountID});
		\$report = \$au->getSearchQueryReport();
		query_update(\$report, {$AccountID});
	?>
END;
		$Queue->SendEnvelope($JobID, $envelope);
		
		
		$User->subtractApiCalls(500, 'Adwords Query Report');
	}
}

?>