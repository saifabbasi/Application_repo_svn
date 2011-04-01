<?php
function yahoo_api_import_debug($str, $sc = true)
{
	if($sc === false)
		return;
	print $str . "\n";
}


function YahooImportUploadReport($url, $accountID, $JobID)
{
	global $userId, $intAccountID;
	$intAccountID = $accountID;
	$FileName = $url;
	/*
	$FileName = $_FILES['report']['tmp_name'];
	
	// Extract Zip File or Read Upload Contents
	if (strtolower(substr($_FILES['report']['name'], strlen($_FILES['report']['name'])-3, 3)) == 'zip')
	{
		$strReport = ExtractReport($_FILES['report']['tmp_name']);
		$FileName = str_replace("\\", "/", sys_get_temp_dir())."/".md5(time().date("u"));
		file_put_contents($FileName, $strReport);
	}
	else
	{
		$strReport = file_get_contents($_FILES['report']['tmp_name']);
	}*/
	
	global $DateImported;
	//file_put_contents(str_replace("\\", "/", sys_get_temp_dir())."/YahooImport_".md5(time().date("u"), file_get_contents(urlencode($FileName))));
	$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description) VALUES
	(
		(select id from bevomedia_queue where jobId='$JobID'),
 		now(), now(), 'YAHOO', 'success', 'Importing report');
	)";
	mysql_query($msgQuery);
	try {
	    $DateImported = ImportYahooAdwords($FileName, $intAccountID, $JobID);
	} catch (Exception $e) {
	    $msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description, output) VALUES
    		(
    			(select id from bevomedia_queue where jobId='".$JobID."'),
   		 		now(), now(), 'YAHOO', 'error', 'Error importing report request, mysql_real_escape_string(print_r($e, true));
    		)";
    		mysql_query($msgQuery);
	}
	
	return true;
}

function ExtractReport($strInFile)
{
	//yahoo_api_import_debug('>>ExtractReport: ' . $strInFile);
	
	$objZip = zip_open($strInFile);
	
	if (!$objZip)
	{
		return false;
	}
	
	$strContents = '';
	
	while ($objEntry = zip_read($objZip))
	{
		$strName =  zip_entry_name($objEntry);
		
		// Look for the Keyword Performance XML
		if (strpos($strName, 'Keyword-Performance') !== false)
		{
			// Ignore Non XML Reports
			if (substr($strName, strlen($strName)-3, 3) != 'xml')
			{
				continue;
			}
			
			if (zip_entry_open($objZip, $objEntry))
			{
				$strContents = zip_entry_read($objEntry, zip_entry_filesize($objEntry));
				zip_entry_close($objEntry);
			}
		}
	}
	
	zip_close($objZip);
	return $strContents;
}

function CheckDateRange($strInXML) {
	if (strlen($strInXML) < 1) {
		return false;
	}
	
	$objXML = simplexml_load_string($strInXML);
		
	if (!$objXML) {
		return false;
	}
		
	// Get Report Date, Assume 1 Day and use StartDate as StatDate
	$arrReportAtts = $objXML->attributes();
		
	$strDateStart = (string) $arrReportAtts['dateStart'];
	$strDateEnd = (string) $arrReportAtts['dateEnd'];
	
	if (date('Y-m-d', strtotime($strDateStart)) != date('Y-m-d', strtotime($strDateEnd))) {
		return false;
	}
	else {
		return true;
	}
}

function ImportYahooAdwords($FileName, $AccountID, $JobID)
{
	yahoo_api_import_debug('Updating Stats for Yahoo:');

	$Sql = "SELECT ID, user__id, username, password, masterAccountId FROM bevomedia_accounts_yahoo WHERE ID = '{$AccountID}'; ";
	$userId = mysql_query($Sql);
	if (mysql_num_rows($userId)==0)
	{
		echo ("The e-mail is not registered.");
		return false;
	} else {
		$userId = mysql_fetch_assoc($userId);
		$AccountID = $userId['ID'];
		$YahooEmail = $userId['username'];
		$yahoo_api = new yahoo_api($userId['username'], $userId['password'], $userId['masterAccountId']);
		$userId = $userId['user__id'];
		$emailSql = "SELECT email FROM bevomedia_user WHERE ID = {$userId}";
		$emailQuery = mysql_query($emailSql);
		$emailRow = mysql_fetch_assoc($emailQuery);
		$email = $emailRow['email'];
	}
	
	
	yahoo_api_import_debug('Bevo User: ' . $email . ' (ID: ' . $userId . ')');
	yahoo_api_import_debug('Yahoo User: ' . $YahooEmail);
	
	yahoo_api_import_debug('');
	
	yahoo_api_import_debug('Updating Campaign status:');
	$Sql = "SELECT id, Name FROM bevomedia_ppc_campaigns WHERE (user__id = {$userId}) AND (AccountID = {$AccountID}) AND (ProviderType = 2) ";
	$Query = mysql_query($Sql);
	while($Row = mysql_fetch_assoc($Query))
	{
		$c = $yahoo_api->getCampaignUsingNameAll($Row['Name']);
		$Status = mysql_escape_string(strtoupper($c->status));
		if($Status == strtoupper('ON'))
		{
			$Status = 'ACTIVE';
		}
		if($Status == strtoupper('OFF'))
		{
			$Status = 'PAUSED';
		}
		if(!$Status || $Status == strtoupper('DELETED'))
		{
			$Status = 'DELETED';
		}
		yahoo_api_import_debug("\t" . 'Setting "' . $Row['Name'] . '" status to... ' . $Status);
		$Sql = "UPDATE bevomedia_ppc_campaigns SET Status = '{$Status}' WHERE ID = {$Row['id']} LIMIT 1";
		//print $Sql;
		mysql_query($Sql);
	}
	yahoo_api_import_debug('...completed.');
	
	
	yahoo_api_import_debug('');
	yahoo_api_import_debug('Loading stats file "' . $FileName . '"...');
	$XML = simplexml_load_file($FileName);

	$StatDate = date("Y-m-d", strtotime((string)$XML->attributes()->dateStart));
	yahoo_api_import_debug('Processing stats for ' . $StatDate . ':');
		
	$DeletedCombinations = array();
	$DeletedCombinationsSQ = array();
	foreach ($XML->row as $Row)
	{
		$Analytics = $Row->analytics;
		
		//campaign
		$CampaignName = (string)$Row->attributes()->cmpgnName;
		$CampaignName = mysql_real_escape_string($CampaignName);
		
		$Sql = "SELECT ID FROM bevomedia_ppc_campaigns WHERE (user__id = {$userId}) AND (Name = '{$CampaignName}') AND (ProviderType = 2) ";
		$CampaignID = mysql_query($Sql);
		if (mysql_num_rows($CampaignID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_campaigns (user__id, ProviderType, AccountID, Name, Updated) VALUES ({$userId}, 2, {$AccountID}, '{$CampaignName}', 0 ); ";
			mysql_query($Sql);
			yahoo_api_import_debug("\t" . 'Adding campaign "' . $CampaignName . '".');
			$CampaignID = mysql_insert_id();
		} else
		{
			$CampaignID = mysql_fetch_assoc($CampaignID);
			$CampaignID = $CampaignID['ID'];
		}
		//yahoo_api_import_debug($Sql);
		//campaign

		//adgroup
		$AdGroupName = (string)$Row->attributes()->adGrpName;
		$AdGroupName = mysql_real_escape_string($AdGroupName);
		
		$Sql = "SELECT ID FROM bevomedia_ppc_adgroups WHERE (CampaignID = {$CampaignID} ) AND (Name = '{$AdGroupName}') ; ";
		$AdGroupID = mysql_query($Sql);
		if (mysql_num_rows($AdGroupID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_adgroups (CampaignID, Name, Updated) VALUES ({$CampaignID}, '{$AdGroupName}', 0)  ";
			mysql_query($Sql);
			yahoo_api_import_debug("\t" . 'Adding AdGroup "' . $AdGroupName . '" to Campaign "' . $CampaignName . '".');
			$AdGroupID = mysql_insert_id();
		} else
		{
			$AdGroupID = mysql_fetch_assoc($AdGroupID);
			$AdGroupID = $AdGroupID['ID'];
		}
		//yahoo_api_import_debug($Sql);
		//adgroup
		
		
		//keyword
		$Keyword = strtolower((string)$Row->attributes()->keywordName);
		$Keyword = mysql_real_escape_string($Keyword);
		
		$Sql = "SELECT ID FROM bevomedia_keyword_tracker_keywords WHERE (Keyword = '{$Keyword}'); ";
		$KeywordID = mysql_query($Sql);
		if (mysql_num_rows($KeywordID)==0)
		{
			$Sql = "INSERT INTO bevomedia_keyword_tracker_keywords (Keyword) VALUES ('{$Keyword}'); ";
			mysql_query($Sql);
			$KeywordID = mysql_insert_id();
		} else
		{
			$KeywordID = mysql_fetch_assoc($KeywordID);
			$KeywordID = $KeywordID['ID'];
		}
		//yahoo_api_import_debug($Sql);
		//keyword
		
		
		
		//bevomedia_ppc_keywords
		$MaxCPC = 0;
		$DestURL = (string)$Row->attributes()->url;
		$Updated = 0;
		$Status = 1;
		
		$APIKeywordID = floatval($Row->attributes()->keywordID);
		
		if ($DestURL=='default URL') $DestURL = '';
		
		$Sql = "SELECT id FROM bevomedia_ppc_keywords WHERE (AdGroupID = {$AdGroupID}) AND (KeywordID = {$KeywordID})";
		$PPCKeywordID = mysql_query($Sql);
		if (mysql_num_rows($PPCKeywordID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_keywords (apikeywordid, AdGroupID, KeywordID, MaxCPC, DestURL, Updated, Status)
									  VALUES ({$APIKeywordID}, {$AdGroupID}, {$KeywordID}, {$MaxCPC}, '{$DestURL}', {$Updated}, {$Status}) ";
			mysql_query($Sql);
			yahoo_api_import_debug("\t" . 'Adding Keyword "' . $Keyword. '" to AdGroup "' . $AdGroupName. '".');
			
			$PPCKeywordID = mysql_insert_id();
		} else
		{
			$PPCKeywordID = mysql_fetch_assoc($PPCKeywordID);
			$PPCKeywordID = $PPCKeywordID['id'];
			
			$Sql = "UPDATE bevomedia_ppc_keywords SET apikeywordid = {$APIKeywordID}, MaxCPC = {$MaxCPC}, DestURL = '{$DestURL}', Updated = 1, Status = {$Status} WHERE ID = {$PPCKeywordID} ";
			mysql_query($Sql);
		}
		yahoo_api_import_debug($Sql);
		//bevomedia_ppc_keywords

		
		
		//bevomedia_ppc_keywords_stats
		if (!in_array(array($StatDate, $AdGroupID, $CampaignID), $DeletedCombinations))
		{
			$Sql = "SELECT
						bevomedia_ppc_keywords_stats.ID
					FROM
						`bevomedia_ppc_keywords_stats`,
						`bevomedia_ppc_keywords`,
						`bevomedia_ppc_campaigns`,
						`bevomedia_ppc_adgroups`,
						`bevomedia_keyword_tracker_keywords`
					WHERE
						(bevomedia_ppc_keywords.ID = bevomedia_ppc_keywords_stats.KeywordID) AND
						(bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.ID) AND
						(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
						(bevomedia_ppc_keywords.KeywordID = bevomedia_keyword_tracker_keywords.ID) AND
						(bevomedia_ppc_keywords_stats.StatDate = '$StatDate') AND
						(bevomedia_ppc_keywords.AdGroupID = {$AdGroupID}) AND
						
						(bevomedia_ppc_campaigns.ID = {$CampaignID})
					";
			$DeleteResults = mysql_query($Sql);
			while ($DeleteRow = mysql_fetch_assoc($DeleteResults))
			{
				$Sql = "DELETE FROM bevomedia_ppc_keywords_stats WHERE ID = {$DeleteRow['ID']} ";
				mysql_query($Sql);
			}
			
			$DeletedCombinations[] = array($StatDate, $AdGroupID, $CampaignID);
		}
		
		
		$Impressions = (string)$Analytics->attributes()->numImpr;
		$Clicks = (int)$Analytics->attributes()->numClick;
		$CPC = (float)$Analytics->attributes()->cpc;
		$CTR = (float)$Analytics->attributes()->ctr;
		$CPM = 0;
		$Cost = (float)$Analytics->attributes()->cost;
		$Pos = (float)$Analytics->attributes()->averagePosition;
		
		
		$Sql = "SELECT id, cost FROM bevomedia_ppc_keywords_stats WHERE KeywordID = {$PPCKeywordID} AND StatDate = '{$StatDate}'";
		$PPCStateKeywordID = mysql_query($Sql);
		if (mysql_num_rows($PPCStateKeywordID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_keywords_stats (KeywordID, Impressions, Clicks, CPC, CPM, Cost, Pos, StatDate)
									  VALUES ({$PPCKeywordID}, {$Impressions}, {$Clicks}, {$CPC}, {$CPM}, {$Cost}, {$Pos}, '{$StatDate}'); ";
			mysql_query($Sql);
			yahoo_api_import_debug("\t\t" . 'Adding Keyword Stats (Impressions: ' . $Impressions . ', Clicks: ' . $Clicks . ', Cost: ' . $Cost . ') to Keyword "' . $Keyword . '".');
			$PPCStateKeywordID = mysql_insert_id();
		} else
		{
			$PPCStateKeywordID = mysql_fetch_assoc($PPCStateKeywordID);
			
			$Cost += $PPCStateKeywordID['cost'];
			$PPCStateKeywordID = $PPCStateKeywordID['id'];
			
			$Sql = "UPDATE bevomedia_ppc_keywords_stats SET KeywordID = {$PPCKeywordID}, Impressions = {$Impressions}, Clicks = {$Clicks},
					CPC = {$CPC}, CPM = {$CPM}, Cost = {$Cost}, Pos = {$Pos}, StatDate = '{$StatDate}'
					WHERE ID = {$PPCStateKeywordID} ";
			mysql_query($Sql);
		}
		yahoo_api_import_debug($Sql);
		//bevomedia_ppc_keywords_stats
		
		
		
		//search_query_stats
		$AdID = (string)$Row->attributes()->adID;
		$KeywordName = (string)$Row->attributes()->keywordName;
		
		if (!in_array(array($StatDate, $AdGroupID, $CampaignID), $DeletedCombinationsSQ))
		{
			$Sql = "SELECT
						bevomedia_ppc_search_query.id AS ID
					FROM
						`bevomedia_ppc_search_query`,
						`bevomedia_ppc_advariations`,
						`bevomedia_ppc_campaigns`,
						`bevomedia_ppc_adgroups`
					WHERE
						(bevomedia_ppc_search_query.ppcAdvariations_id = bevomedia_ppc_advariations.apiAdId) AND
						(bevomedia_ppc_advariations.AdGroupID = bevomedia_ppc_adgroups.ID) AND
						(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
						(bevomedia_ppc_search_query.statdate = '$StatDate') AND
						(bevomedia_ppc_advariations.AdGroupID = {$AdGroupID}) AND
						(bevomedia_ppc_campaigns.ID = {$CampaignID})
					";
			$DeleteResults = mysql_query($Sql);

			while ($DeleteRow = mysql_fetch_assoc($DeleteResults))
			{
				$Sql = "DELETE FROM bevomedia_ppc_search_query WHERE id = {$DeleteRow['ID']} ";
				mysql_query($Sql);
			}
			
			$DeletedCombinationsSQ[] = array($StatDate, $AdGroupID, $CampaignID);
		}
		
		$Sql = "SELECT bevomedia_ppc_search_query.id AS id FROM bevomedia_ppc_search_query LEFT JOIN bevomedia_ppc_advariations ON bevomedia_ppc_advariations.apiAdId = bevomedia_ppc_search_query.ppcAdvariations_id WHERE AdGroupID = {$AdGroupID} AND query = \"{$KeywordName}\" AND keyword = \"{$Keyword}\" AND statdate = '{$StatDate}'";
		$query = mysql_query($Sql);
		$count = mysql_num_rows($query);
		if($count < 1)
		{
			$Sql = "INSERT INTO bevomedia_ppc_search_query (ppcAdvariations_id, imps, clicks, ctr, cpc, cost, pos, query, keyword, statdate) VALUES ($AdID, $Impressions, $Clicks, $CTR, $CPC, $Cost, $Pos, '$KeywordName', '$Keyword', '$StatDate')";
			$query = mysql_query($Sql);
			$id = mysql_insert_id();
		}else{
			$rw = mysql_fetch_assoc($query);
			$id = $rw['id'];
			$Sql = "UPDATE bevomedia_ppc_search_query SET imps = ($Impressions+imps), clicks = ($Clicks+clicks), ctr = $CTR, cpc = $CPC, cost = $Cost, pos = $Pos, query = '$KeywordName', keyword = '$Keyword' WHERE id = $id";
			mysql_query($Sql);
		}
		//yahoo_api_import_debug($Sql);
		//search_query_stats
	}
	
	yahoo_api_import_debug('...completed.');
	return $StatDate;
}


function ListReportDates($AccountID)
{
	$Sql = "SELECT
				DISTINCT(bevomedia_ppc_keywords_stats.StatDate) AS `Date`
			FROM
				adwords_accounts,
				bevomedia_ppc_campaigns,
				bevomedia_ppc_adgroups,
				bevomedia_ppc_keywords,
				bevomedia_ppc_keywords_stats
			WHERE
				(bevomedia_ppc_campaigns.user__id = $_SESSION[userId]) AND
				(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
				(bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.ID) AND
				(bevomedia_ppc_keywords_stats.KeywordID = bevomedia_ppc_keywords.ID) AND
				(bevomedia_ppc_campaigns.ProviderType = 2) AND
				(bevomedia_ppc_campaigns.AccountID = $AccountID)
			";
	$Rows = mysql_query($Sql);
	$Dates = array();
	
	if (mysql_num_rows($Rows))
	{
		while ($Row = mysql_fetch_assoc($Rows))
			$Dates[] = date("Y/m/d", strtotime($Row['Date']));
	}
	
	return $Dates;
}

?>