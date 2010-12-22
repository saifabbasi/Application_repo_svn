<?php

date_default_timezone_set('America/New_York');

//UploadReport('https://ews21.marketing.ews.yahooapis.com:443/report/V5/ReportOutput?t=MSWozdpsH9M7bqLuwYe57SMTcqtlBTEIAindsRWcXIYm69AVZnwHvbkAQs35S.T6wmQzg70M9gonbjKr1_1dfcBATBiB_UqDgXEW87g-', 14);

// if (!is_numeric($intAccountID)) {
	// $intAccountID = 0;
// }

// if (strtoupper($strAction) == 'UPLOAD') {
	// UploadReport();
// }

function yahoo_api_import_ads_debug($str)
{
	print $str . "\n";
}

function YahooImportUploadReportAds($url, $accountID = 4)
{
	global $userId, $intAccountID;
	$intAccountID = $accountID;
	$FileName = $url;
	
	global $DateImported;
	$DateImported = ImportYahooAdwordsAds($FileName, $intAccountID);
	
	return true;
}

function ImportYahooAdwordsAds($FileName, $AccountID)
{
	yahoo_api_import_ads_debug('Updating Stats for Yahoo Ad Variations:');
	
	$Sql = "SELECT * FROM bevomedia_accounts_yahoo WHERE id = '{$AccountID}'; ";
	$userId = mysql_query($Sql);
	$apiCount = 0;
	if (mysql_num_rows($userId)==0)
	{
		echo ("The e-mail is not registered.");
		return false;
	} else
	{
		$userId = mysql_fetch_assoc($userId);
		$AccountID = $userId['id'];
		
		$username = $userId['username'];
		$password = $userId['password'];
		$Yahooemail = $username;
		$masterAccountId = $userId['masterAccountId'];
		$acId = $userId['id'];
		
		$userId = $userId['user__id'];
		$emailSql = "SELECT email FROM bevomedia_user WHERE id = {$userId}";
		$emailQuery = mysql_query($emailSql);
		$emailRow = mysql_fetch_assoc($emailQuery);
		$email = $emailRow['email'];
	}
	$user = new User($userId);	
	yahoo_api_import_ads_debug('Bevo User: ' . $email . ' (ID: ' . $userId . ')');
	yahoo_api_import_ads_debug('Yahoo User: ' . $Yahooemail);
	yahoo_api_import_ads_debug('');
	
	require_once(PATH . 'yahoo_api/yahoo_api.php');
	$yahoo_api = new yahoo_api($username, $password, $masterAccountId, $acId);

	yahoo_api_import_ads_debug('Loading stats file "' . $FileName . '"...');
	$XML = simplexml_load_file($FileName);
	
	$StatDate = date("Y-m-d", strtotime((string)$XML->attributes()->dateStart));
	yahoo_api_import_ads_debug('Processing ad variation stats for ' . $StatDate . ':');
	
	$DeletedCombinations = array();
	
	foreach ($XML->row as $Row)
	{
		$apiCount += 10;
		$Analytics = $Row->analytics;
		
		//campaign
		$CampaignName = (string)$Row->attributes()->cmpgnName;
		$CampaignName = mysql_real_escape_string($CampaignName);
		
		$Sql = "SELECT id FROM bevomedia_ppc_campaigns WHERE (user__id = {$userId}) AND (Name = '{$CampaignName}') AND (ProviderType = 2) ";
		$CampaignID = mysql_query($Sql);
		if (mysql_num_rows($CampaignID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_campaigns (user__id, ProviderType, AccountID, Name, Updated) VALUES ({$userId}, 2, {$AccountID}, '{$CampaignName}', 0 ); ";
			yahoo_api_import_ads_debug("\t" . 'Adding campaign "' . $CampaignName . '".');
			mysql_query($Sql);
			$CampaignID = mysql_insert_id();
		} else
		{
			$CampaignID = mysql_fetch_assoc($CampaignID);
			$CampaignID = $CampaignID['id'];
		}
		//yahoo_api_import_ads_debug($Sql);
		//campaign
		

		//adgroup
		$AdGroupName = (string)$Row->attributes()->adGrpName;
		$AdGroupName = mysql_real_escape_string($AdGroupName);
		
		$Sql = "SELECT id FROM bevomedia_ppc_adgroups WHERE (CampaignID = {$CampaignID} ) AND (Name = '{$AdGroupName}') ; ";
		$AdGroupID = mysql_query($Sql);
		if (mysql_num_rows($AdGroupID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_adgroups (CampaignID, Name, Updated) VALUES ({$CampaignID}, '{$AdGroupName}', 0)  ";
			mysql_query($Sql);
			yahoo_api_import_ads_debug("\t" . 'Adding AdGroup "' . $AdGroupName . '" to Campaign "' . $CampaignName . '".');
			$AdGroupID = mysql_insert_id();
		} else
		{
			$AdGroupID = mysql_fetch_assoc($AdGroupID);
			$AdGroupID = $AdGroupID['id'];
		}
		//yahoo_api_import_ads_debug($Sql);
		//adgroup
		//keyword
		$apiAdId= (float)$Row->attributes()->adID;
		
		//bevomedia_ppc_advariations
		$MaxCPC = 0;
		$DestURL = (string)$Row->attributes()->url;
		$Updated = 0;
		$Status = 1;
		
		if ($DestURL=='default URL') $DestURL = '';
		
		
		$Sql = "SELECT id FROM bevomedia_ppc_advariations WHERE apiAdId= {$apiAdId} AND Title = '[Bevo Temporary Ad Variation]' ";
		$BevoTempAd = mysql_query($Sql);
		if(mysql_num_rows($BevoTempAd)>0)
		{
			$BevoTempAd = mysql_fetch_assoc($BevoTempAd);
			$Sql = "UPDATE bevomedia_ppc_advariations SET AdGroupID = '$AdGroupID' WHERE ID = $BevoTempAd[id] LIMIT 1";
			yahoo_api_import_ads_debug("\t" . 'Ad Variation identified... [Bevo Temporary Ad Variation] ('. $apiAdId.') belongs to "' . $AdGroupName . '".');
			mysql_query($Sql);
		}

		
		$Sql = "SELECT id FROM bevomedia_ppc_advariations WHERE (AdGroupID = {$AdGroupID}) AND (apiAdId= {$apiAdId})";
		$PPCAdVarID = mysql_query($Sql);
		
		if (mysql_num_rows($PPCAdVarID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_advariations (AdGroupID, apiAdId, Updated, Status)
									  VALUES ({$AdGroupID}, {$apiAdId}, {$Updated}, {$Status}); ";
			mysql_query($Sql);
			yahoo_api_import_ads_debug("\t" . 'Adding Ad Variation (' . $apiAdId. ') to "' . $AdGroupName . '".');
			$PPCAdVarID = mysql_insert_id();
		} else
		{
			$PPCAdVarID = mysql_fetch_assoc($PPCAdVarID);
			$PPCAdVarID = $PPCAdVarID['id'];
			
			$Sql = "UPDATE bevomedia_ppc_advariations SET Updated = 1, Status = {$Status} WHERE ID = {$PPCAdVarID} ";
			mysql_query($Sql);
		}
		
		
		$ad = $yahoo_api->getAdByAdId($apiAdId);
		
		$title = mysql_real_escape_string($ad->title);
		$url = mysql_real_escape_string($ad->url);
		$displayUrl = mysql_real_escape_string($ad->displayUrl);
		$shortDescription = mysql_real_escape_string($ad->shortDescription);
		
		$Sql = "UPDATE bevomedia_ppc_advariations SET title = '{$title}', url = '{$url}', displayUrl = '{$displayUrl}', description = '{$shortDescription}' WHERE ID = {$PPCAdVarID}";
		mysql_query($Sql);
		yahoo_api_import_ads_debug("\t\t" . 'Updating Ad Variation (' . $apiAdId. ') setting Title: "' . $title . '", Description: "' . $shortDescription . '", Display URL: "' . $displayUrl . '".');
		//bevomedia_ppc_advariations
		
		
		
		//bevomedia_ppc_advariations_stats
		if (!in_array(array($StatDate, $AdGroupID, $CampaignID), $DeletedCombinations))
		{
			$Sql = "SELECT
						bevomedia_ppc_advariations_stats.id
					FROM
						`bevomedia_ppc_advariations_stats`,
						`bevomedia_ppc_advariations`,
						`bevomedia_ppc_campaigns`,
						`bevomedia_ppc_adgroups`
					WHERE
						(bevomedia_ppc_advariations.ID = bevomedia_ppc_advariations_stats.advariationsId) AND
						(bevomedia_ppc_advariations.AdGroupID = bevomedia_ppc_adgroups.ID) AND
						(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
						(bevomedia_ppc_advariations_stats.StatDate = '$StatDate') AND
						(bevomedia_ppc_advariations.AdGroupID = {$AdGroupID}) AND
						(bevomedia_ppc_campaigns.ID = {$CampaignID})
					";
			$DeleteResults = mysql_query($Sql);
			while ($DeleteRow = mysql_fetch_assoc($DeleteResults))
			{
				$Sql = "DELETE FROM bevomedia_ppc_advariations_stats WHERE ID = {$DeleteRow['id']} ";
				mysql_query($Sql);
			}
			
			$DeletedCombinations[] = array($StatDate, $AdGroupID, $CampaignID);
		}

		
		
		
		$Impressions = (string)$Analytics->attributes()->numImpr;
		$Clicks = (int)$Analytics->attributes()->numClick;
		$CPC = (float)$Analytics->attributes()->cpc;
		$Cost = (float)$Analytics->attributes()->cost;
		$Pos = (float)$Analytics->attributes()->averagePosition;
		//$CPM = 0;
		//$CTR = (float)$Analytics->attributes()->ctr;
		
		
		$Sql = "SELECT id FROM bevomedia_ppc_advariations_stats WHERE advariationsId = {$PPCAdVarID} AND statdate = '{$StatDate}'";
		$PPCStateAdVarID = mysql_query($Sql);
		if (mysql_num_rows($PPCStateAdVarID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_advariations_stats (advariationsId, Impressions, Clicks, CPC, Cost, Pos, StatDate)
									  VALUES ({$PPCAdVarID}, {$Impressions}, {$Clicks}, {$CPC}, {$Cost}, {$Pos}, '{$StatDate}'); ";
			mysql_query($Sql);
			yahoo_api_import_ads_debug("\t\t" . 'Adding Ad Variation Stats (Impressions: ' . $Impressions . ', Clicks: ' . $Clicks . ', Cost: ' . $Cost . ') to Ad "' . $title . '".');
			$PPCStateKeywordID = mysql_insert_id();
		} else
		{
			$PPCStateAdVarID = mysql_fetch_assoc($PPCStateAdVarID);
			$PPCStateAdVarID = $PPCStateAdVarID['id'];
			
			$Sql = "UPDATE bevomedia_ppc_advariations_stats SET advariationsId = {$PPCAdVarID}, impressions = {$Impressions}, clicks = {$Clicks},
											cpc = {$CPC}, cost = {$Cost}, pos = {$Pos}
										WHERE id = {$PPCStateAdVarID} AND  statdate = '{$StatDate}'";
			mysql_query($Sql);
		}
		yahoo_api_import_ads_debug($Sql);
		//bevomedia_ppc_advariations_stats
		
		//bevomedia_ppc_contentmatch_stats
		if((int)$Row->attributes()->tacticID == 24)
		{
			$Sql = "SELECT id FROM bevomedia_ppc_contentmatch_stats WHERE adgroup_id = {$AdGroupID} AND statdate = '{$StatDate}' ";
			$PPCContentMatch = mysql_query($Sql);
			if (mysql_num_rows($PPCContentMatch)==0)
			{
				$Sql = "INSERT INTO bevomedia_ppc_contentmatch_stats (adgroup_id, Impressions, Clicks, CPC, Cost, Pos, StatDate)
										  VALUES ({$AdGroupID}, {$Impressions}, {$Clicks}, {$CPC}, {$Cost}, {$Pos}, '{$StatDate}'); ";
				mysql_query($Sql);
				$PPCContentMatch = mysql_insert_id();
			} else {
				$PPCContentMatch = mysql_fetch_assoc($PPCStateAdVarID);
				$PPCContentMatch = $PPCStateAdVarID['id'];
				
				$Sql = "UPDATE bevomedia_ppc_contentmatch_stats SET impressions = {$Impressions}, clicks = {$Clicks},
												cpc = {$CPC}, cost = {$Cost}, pos = {$Pos}, statdate = '{$StatDate}'
											WHERE ID = {$PPCContentMatch} ";
				mysql_query($Sql);
			}
		}
		//yahoo_api_import_ads_debug($Sql);
		//bevomedia_ppc_contentmatch_stats
	}
	
	yahoo_api_import_ads_debug('...completed.');
	return $StatDate;
//	$user->subtractApiCalls($apiCalls, 'Yahoo API update');
}

?>