<?php

if(isset($_SERVER['argc']) && $_SERVER['argc']>0)
{
	$tmp = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], 'Applications'));
	include($tmp . '/Applications/BevoMedia/' . 'Common/AbsoluteIncludeHelper.include.php');
}
else
{
	$tmp = substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], 'www'));
	include($tmp . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');
}

if(!function_exists('msn_api_import_debug'))
{
	function msn_api_import_debug($str)
	{
		echo $str;
	}
}

function UploadReport($userId, $intAccountid, $reportFile) {	
	if (!is_numeric($userId) || !is_numeric($intAccountid)) {
		return false;
	}
	
	$reportFile = sys_get_temp_dir() . '/' . $reportFile;
	
	//print $reportFile;
	// Only Accept Text/XML Uploads
	$arrAcceptedTypes = array('text/csv', 'text/plain', 'application/zip', 'text/tsv', '');
	/*if (!in_array($_FILES['report']['type'], $arrAcceptedTypes)) 
	{
	//	return false;
	}
	if ($_FILES['report']['size'] > 50000) {
	//	return false;
	}*/
	
	// Extract Zip File or Read Upload Contents
	if (strtolower(substr($reportFile, strlen($reportFile)-3, 3)) == 'zip') 
	{
		$strReport = ExtractReport($reportFile);
	}else{
		$strReport = file_get_contents($reportFile);
	}
	
	$FileName = str_replace("\\", "/", sys_get_temp_dir())."/".md5(time().date("u"));
	file_put_contents($FileName, $strReport);
	
	if (strlen($strReport) < 1) 
	{
		return false;
	}
	
	global $DateImported;
	$DateImported = ImportMicrosoftAdCenter($FileName, $intAccountid);
	
	@unlink($reportFile);
	return true;
	//ParseReport($arrUser, $strReport);
}

function ExtractReport($strInFile) {
	msn_api_import_debug('>>REPORTFILE: ' . $strInFile);
	$objZip = zip_open($strInFile);
	
	if (!$objZip) {
		return false;
	}
	
	$strContents = '';
	
	while ($objEntry = zip_read($objZip)) {
		$strName =  zip_entry_name($objEntry);
		
		if (substr($strName, strlen($strName)-3, 3) != 'csv') 
		{
			continue;
		}
		
		if (zip_entry_open($objZip, $objEntry)) {
			$strContents = zip_entry_read($objEntry, zip_entry_filesize($objEntry));
			zip_entry_close($objEntry);
		}
		
	}
	
	zip_close($objZip);
	return $strContents;
}

function addMSNAccountToQueueImport($id)
{
	global $db;
	$sql = "SELECT * FROM bevomedia_accounts_msnadcenter WHERE id = " . $id;
	   $row = mysql_fetch_assoc(mysql_query($sql, $db));
	
	   $username = $row['username'];
	   $password = $row['password'];
	   $user_id = $row['user__id'];
	   $msn_account_id = $row['id'];
	
	$reportType = 'DailyReport';
	$reportName = $reportType . '-' . time();
	
	$msn = new msn_api($username, $password);
	$api_account_id = $msn->getAccountid();
		
	if(isset($reportid))
		$reportid = $reportid;
	else
		$reportid = $msn->addReport($reportName);
		
	$report = $msn->getReportFile($reportid);
	if($report == 'PENDING')
	{
		$Queue = new QueueComponent();
		$Jobid =  $Queue->CreateJobid();
		$envelope = $msn->rcsQueueOutput($reportid, $user_id, $msn_account_id);
		$Queue->SendEnvelope($Jobid, $envelope);
	}else{
		echo "\n" . 'PROCESSED WITHOUT QUEUE' . "\n";
		require(ABSPATH . 'Applications/BevoMedia/Common/msn_api/msn_api_import.php');
		echo (int)UploadReport($user_id, $msn_account_id, $report);
	}
}


function ImportMicrosoftAdCenter($FileName, $AccountId)
{
	msn_api_import_debug(">>ImportMicrosoftAdCenter\n\tFilename: $FileName\n\tAccountid: $AccountId");
	$Sql = "SELECT id, user__id, Username, Password FROM bevomedia_accounts_msnadcenter WHERE id = '{$AccountId}'; ";
	$userId = mysql_query($Sql);
	if (mysql_num_rows($userId)==0)
	{
		//die ("The e-mail is not registered.");
	} else
	{
		$userId = mysql_fetch_assoc($userId);
		$AccountId = $userId['id'];
		$Password = $userId['Password'];
		$Username = $userId['Username'];
		$userId = $userId['user__id'];		
	}

	msn_api_import_debug("user__id: $userId");
	
	$DeletedCombinations = array();

	$Started = false;
	$handle = fopen($FileName, "r");
	$updatedCampaigns = array();
	
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
	{
		if(sizeOf($data) < 10)
			continue;
			
		//print $data[0] . "\n";
		
		if ($data[0]=="Month" || $data[0]=="GregorianDate" || $data[0] == 'Week')
		{
			$Started = true;
			continue;
		}
		
		if ($Started==false) continue;
		
		if ( ($Started==true) && ($data[0]=="") ) break;
		
		foreach ($data as $key => $value)
		{
			$data[$key] = mysql_real_escape_string(trim($value));
		}
		
		
		$status = 1;
		$StatDate = $data[0];
		$AccountName = $data[1];
		$CampaignName = $data[2];	
		$AdGroupName = $data[3];
		$Keyword = $data[4];
		$MaxCPC = $data[5];
		$Impressions = $data[6];
		$Clicks = $data[7];
		$CPC = $data[8];
		$Cost = $CPC * $Clicks;
		//$Cost = $data[9];
		$Pos = $data[10];
		$APIKeywordid = $data[13];
		
		if($Pos == '')
			$Pos = 0;
		if($Cost == '')
			$Cost = 0;
			
		$StatDate = date("Y-m-d", strtotime($StatDate));
		
		msn_api_import_debug("StatDate: $StatDate");
		msn_api_import_debug(">>Stats\n\tCampaign Name: $CampaignName\n\tAd Group Name:$AdGroupName\n\tKeyword: $Keyword\n\tImpressions: $Impressions\n\tClicks: $Clicks\n\tCost: $Cost");
	
		if ($StatDate=="1969-12-31")
		{
			print_r($Row);
			die("Wrong date");
		}
		
		
		//campaign
		$Sql = "SELECT id FROM bevomedia_ppc_campaigns WHERE (user__id = {$userId}) AND (Name = '{$CampaignName}') AND (Accountid = {$AccountId}) AND (ProviderType = 3) ";
		$Campaignid = mysql_query($Sql);
		if (mysql_num_rows($Campaignid)==0)
		{		
			$Sql = "INSERT INTO bevomedia_ppc_campaigns (user__id, ProviderType, Accountid, Name, Updated) VALUES ({$userId}, 3, {$AccountId}, '{$CampaignName}', 0 ); ";
			if (!mysql_query($Sql)) die("170");
			$Campaignid = mysql_insert_id();
		} else
		{
			$Campaignid = mysql_fetch_assoc($Campaignid);
			$Campaignid = $Campaignid['id'];
		}
		//campaign

		
		//adgroup
		$Sql = "SELECT id FROM bevomedia_ppc_adgroups WHERE (Campaignid = {$Campaignid} ) AND (Name = '{$AdGroupName}') ; ";
		$AdGroupid = mysql_query($Sql);
		if (mysql_num_rows($AdGroupid)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_adgroups (Campaignid, Name, Updated) VALUES ({$Campaignid}, '{$AdGroupName}', 0)  "; 
			if (!mysql_query($Sql)) die("189"); 
			$AdGroupid = mysql_insert_id();
		} else
		{
			$AdGroupid = mysql_fetch_assoc($AdGroupid);
			$AdGroupid = $AdGroupid['id'];
		}
		//adgroup
		
		
		//keyword
		$Sql = "SELECT id FROM bevomedia_keyword_tracker_keywords WHERE (Keyword = '{$Keyword}'); ";
		$Keywordid = mysql_query($Sql);
		if (mysql_num_rows($Keywordid)==0)
		{
			$Sql = "INSERT INTO bevomedia_keyword_tracker_keywords (Keyword) VALUES ('{$Keyword}'); "; 
			if (!mysql_query($Sql)) die("207");
			$Keywordid = mysql_insert_id();
		} else
		{
			$Keywordid = mysql_fetch_assoc($Keywordid);
			$Keywordid = $Keywordid['id'];
		}
		//keyword
		
		
	}
	fclose($handle);
	
	if(!isset($StatDate))
	{
		echo 'NOSTATDATE' . "\n";
		return 0;
	}
	
	$msn_api = new msn_api($Username, $Password);
	$Sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE (user__id = {$userId}) AND (Accountid = {$AccountId}) AND (ProviderType = 3) ";
	$Query = mysql_query($Sql);
	while($Row = mysql_fetch_assoc($Query))
	{
		$c = $msn_api->getCampaignUsingName($Row['name']);
		$status = false;
		if($c)
		{
			$status = mysql_escape_string(strtoupper($c->Status));
			if($status == strtoupper('BudgetAndManualPaused') || $status == strtoupper('BudgetPaused'))
			{
				$status = 'SUSPENDED';
			}
		}
		if(!$status)
		{
			$status = 'DELETED';
		}
		$Sql = "UPDATE bevomedia_ppc_campaigns SET status = '{$status}' WHERE id = {$Row['id']} LIMIT 1";
		print $Sql;
		mysql_query($Sql);
	}
	
	msn_api_import_debug('>>STATDATE: ' . $StatDate);
	msn_api_import_debug('>>YESTERDAY:' . date('Y-m-d', strtotime('yesterday')));
	if($StatDate != date('Y-m-d', strtotime('yesterday')))
	{
		require_once(PATH . 'User.class.php');
		$user = new User($row['user__id']);
		
		if ( ($user->vaultID==0) && (!$user->IsSubscribed(User::PRODUCT_INSTALL_NETWORKS)) && ($user->membershipType!='premium') )
		{
			echo "Account not verified...";
			continue;
		}
		
		
//		if($user->apiCalls < 1000)
//		{
//			echo('Not enough API credit. Buying more...');
//			$user->AddUserAPICallsCharge();
//		}
		
//		if($user->apiCalls < 1000)
//		{
//			msn_api_import_ads_debug('Not enough API credit.');
//		}else
		{
			addMSNAccountToQueueImport($AccountId);
//			$user->subtractApiCalls(1000, 'MSN Queue Import');
			msn_api_import_debug('!REQUEUED!');
		}
	}
	return $StatDate;
}

?>