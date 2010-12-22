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

function msn_api_import_ads_debug($str)
{
        echo $str;
}

ini_set('memory_limit', '512M');

function UploadReportAds($userId, $intAccountID, $reportFile) {	
	if (!is_numeric($userId) || !is_numeric($intAccountID)) {
		return false;
	}

	$reportFile = sys_get_temp_dir() . '/' . $reportFile;
	// Only Accept Text/XML Uploads
	
	
	// Extract Zip File or Read Upload Contents
	if (strtolower(substr($reportFile, strlen($reportFile)-3, 3)) == 'zip') 
	{
		$strReport = ExtractReportAds($reportFile);
	}
	else
	{
		$strReport = file_get_contents($reportFile);
	}
	
	$FileName = str_replace("\\", "/", sys_get_temp_dir())."/".md5(time().date("u"));
	file_put_contents($FileName, $strReport);



	if (strlen($strReport) < 1) 
	{
		return false;
	}
	

	global $DateImported;
	$DateImported = ImportMicrosoftAdCenterAds($FileName, $intAccountID);

	//@unlink($reportFile);
	return true;
	//ParseReport($arrUser, $strReport);
}


function ExtractReportAds($strInFile) {
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


function ImportMicrosoftAdCenterAds($FileName, $AccountID)
{	
	$disabledAdGroups = array();
	
	$Sql = "SELECT ID, user__id, username, password FROM bevomedia_accounts_msnadcenter WHERE ID = '{$AccountID}'; ";
	$userId = mysql_query($Sql);
	if (mysql_num_rows($userId)==0)
	{
		die ("The e-mail is not registered.");
	} else
	{
		$userId = mysql_fetch_assoc($userId);
		$row = $userId;
		$AccountID = $userId['ID'];
		$userId = $userId['user__id'];		
	}
	
	$msn = new msn_api($row['username'], $row['password']);
	$msn->enableErrorSuppression();

	$DeletedCombinations = array();
	
	$Header = array();

	$Started = false;
	echo file_get_contents($FileName);
	$handle = fopen($FileName, "r");
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
	{
		if(sizeOf($data) < 10)
			continue;
			
		//print $data[0] . "\n";
		
		if ($data[0]=="Month" || $data[0]=="GregorianDate")
		{
			$Started = true;
			$Header = array_flip($data);
			continue;
		}
		
		
		if ($Started==false) continue;
		
		if ( ($Started==true) && ($data[0]=="") ) break;
		
		foreach ($data as $key => $value)
		{
			$data[$key] = mysql_real_escape_string(trim($value));
		}
		
		
//		$Status = 1;
//		$StatDate = $data[0];
//		$AccountName = $data[1];
//		$CampaignName = $data[2];	
//		$AdGroupName = $data[3];
//		$apiAdId = $data[4];
//		$MaxCPC = $data[5];
//		$Impressions = $data[6];
//		$Clicks = $data[7];
//		$CPC = $data[8];
//		$Cost = $CPC * $Clicks;
//		//$Cost = $data[9];
//		$Pos = $data[10];
//		$api_adgroupid = $data[14];

		
//		$Status = 1;
//		$StatDate = $data[0];
//		$AccountName = $data[1];
//		$CampaignName = $data[2];
//		$AdGroupName = $data[3];
//		$apiAdId = $data[4];
//		$MaxCPC = 0;
//		$Impressions = $data[5];
//		$Clicks = $data[6];
//		$CPC = $data[7];
//		$Cost = $CPC * $Clicks;
//		//$Cost = $data[9];
//		$Pos = $data[8];
//		$api_adgroupid = $data[10];

		
		$Status = 1;
		$StatDate = $data[$Header['GregorianDate']];
		$AccountName = $data[$Header['AccountName']];
		$CampaignName = $data[$Header['CampaignName']];
		$AdGroupName = $data[$Header['AdGroupName']];
		$apiAdId = $data[$Header['AdId']];
		$MaxCPC = 0;
		$Impressions = $data[$Header['Impressions']];
		$Clicks = $data[$Header['Clicks']];
		$CPC = $data[$Header['AverageCpc']];
		$Cost = $CPC * $Clicks;
		//$Cost = $data[9];
		$Pos = $data[$Header['AveragePosition']];
		$api_adgroupid = $data[$Header['AdGroupId']];
		
		
		if($Pos == '')
			$Pos = 0;
		if($Cost == '')
			$Cost = 0;
		
		$StatDate = date("Y-m-d", strtotime($StatDate));
		
		if ($StatDate=="1969-12-31")
		{
			print_r($Row);
			die("Wrong date");
		}
		
		
		//campaign
		$Sql = "SELECT ID FROM bevomedia_ppc_campaigns WHERE (user__id = {$userId}) AND (Name = '{$CampaignName}') AND (AccountID = {$AccountID}) AND (ProviderType = 3) ";
		echo $Sql . "\n";
		$CampaignID = mysql_query($Sql);
		if (mysql_num_rows($CampaignID)==0)
		{		
			$Sql = "INSERT INTO bevomedia_ppc_campaigns (user__id, ProviderType, AccountID, Name, Updated) VALUES ({$userId}, 3, {$AccountID}, '{$CampaignName}', 0 ); ";
			echo $Sql . "\n";
			if (!mysql_query($Sql)) die("170");
			$CampaignID = mysql_insert_id();
		} else
		{
			$CampaignID = mysql_fetch_assoc($CampaignID);
			$CampaignID = $CampaignID['ID'];
		}
		//campaign

		
		//adgroup
		$Sql = "SELECT ID FROM bevomedia_ppc_adgroups WHERE (CampaignID = {$CampaignID} ) AND (Name = '{$AdGroupName}') ; ";
		echo $Sql . "\n";
		$AdGroupID = mysql_query($Sql);
		if (mysql_num_rows($AdGroupID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_adgroups (CampaignID, Name, Updated) VALUES ({$CampaignID}, '{$AdGroupName}', 0)  "; 
			echo $Sql . "\n";
			if (!mysql_query($Sql)) die("189"); 
			$AdGroupID = mysql_insert_id();
		} else
		{
			$AdGroupID = mysql_fetch_assoc($AdGroupID);
			$AdGroupID = $AdGroupID['ID'];
		}
		//adgroup
		
		
		
		/*if($ad = $msn->getAd($api_adgroupid, $apiAdId))
		{
			print_r($ad);
			die;
		}*/
		
		//$skip = true;
		//if(!in_array($api_adgroupid, $disabledAdGroups))
			$skip = false;
		
		
		if($skip)
		{
		
		}else{
			echo '$api_adgroupid: '.$api_adgroupid."\n";
			echo '$apiAdId: '.$apiAdId."\n\n";
			$ad = $msn->getAd($api_adgroupid, $apiAdId);
			print_r($ad);
			if($ad === false)
			{
				$disabledAdGroups[] = $api_adgroupid;
			}else{
				//bevomedia_ppc_advariations
				$MaxCPC = 0;
				//$DestURL = (string)$Row->attributes()->url;
				$Updated = 0;
				$Status = 1;
				
				$Sql = "SELECT ID FROM bevomedia_ppc_advariations WHERE apiAdId = '{$apiAdId}' AND Title = '[Bevo Temporary Ad Variation]' ";
				echo $Sql . "\n";
				$BevoTempAd = mysql_query($Sql);
				if(mysql_num_rows($BevoTempAd)>0)
				{
					$BevoTempAd = mysql_fetch_assoc($BevoTempAd);
					$Sql = "UPDATE bevomedia_ppc_advariations SET AdGroupID = '$AdGroupID' WHERE ID = $BevoTempAd[ID] LIMIT 1";
					echo $Sql . "\n";
					mysql_query($Sql);
				}
				
				if(!isset($DestURL))
					$DestURL = '';
				if ($DestURL=='default URL') $DestURL = '';
				
				$Sql = "SELECT ID FROM bevomedia_ppc_advariations WHERE (AdGroupID = {$AdGroupID}) AND (apiAdId = {$apiAdId})"; 
				echo $Sql . "\n";
				$PPCAdVarID = mysql_query($Sql);
				
				if (mysql_num_rows($PPCAdVarID)==0)
				{
					$Sql = "INSERT INTO bevomedia_ppc_advariations (AdGroupID, apiAdId, Updated, Status) 
											  VALUES ({$AdGroupID}, {$apiAdId}, {$Updated}, {$Status}); ";
					echo $Sql . "\n";
					mysql_query($Sql);
					$PPCAdVarID = mysql_insert_id();
				} else
				{
					$PPCAdVarID = mysql_fetch_assoc($PPCAdVarID);
					$PPCAdVarID = $PPCAdVarID['ID'];
					
					$Sql = "UPDATE bevomedia_ppc_advariations SET Updated = 1, Status = {$Status} WHERE ID = {$PPCAdVarID} ";
					echo $Sql . "\n";
					mysql_query($Sql);
				}
				
				
				//$ad = $msn->getAdByAdId($apiAdId);
				
				$title = mysql_real_escape_string($ad->Title);
				$url = mysql_real_escape_string($ad->DestinationUrl);
				$displayUrl = mysql_real_escape_string($ad->DisplayUrl);
				$shortDescription = mysql_real_escape_string($ad->Text);
				
				$Sql = "UPDATE bevomedia_ppc_advariations SET title = '{$title}', url = '{$url}', displayUrl = '{$displayUrl}', description = '{$shortDescription}' WHERE ID = {$PPCAdVarID}";
				mysql_query($Sql);
				//bevomedia_ppc_advariations
				
				
				
				//bevomedia_ppc_advariations_stats
				if (!in_array(array($StatDate, $AdGroupID, $CampaignID), $DeletedCombinations))
				{
					$Sql = "SELECT 
								bevomedia_ppc_advariations_stats.ID
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
					$DeleteResults = mysql_query($Sql) or die(mysql_error());
					echo $Sql . "\n";
					while ($DeleteRow = mysql_fetch_assoc($DeleteResults))
					{
						$Sql = "DELETE FROM bevomedia_ppc_advariations_stats WHERE ID = {$DeleteRow['ID']} ";
						echo $Sql . "\n";
						mysql_query($Sql) or die (mysql_error());
					}
					
					$DeletedCombinations[] = array($StatDate, $AdGroupID, $CampaignID);
				}
		
				
				/*$Impressions = (string)$Analytics->attributes()->numImpr;
				$Clicks = (int)$Analytics->attributes()->numClick;
				$CPC = (float)$Analytics->attributes()->cpc;
				$Cost = (float)$Analytics->attributes()->cost;
				$Pos = (float)$Analytics->attributes()->averagePosition;
				*///$CPM = 0;
				//$CTR = (float)$Analytics->attributes()->ctr;
				
				
				$Sql = "SELECT ID FROM bevomedia_ppc_advariations_stats WHERE advariationsId = {$PPCAdVarID} ";
				echo $Sql . "\n";
				$PPCStateAdVarID = mysql_query($Sql);
				if (mysql_num_rows($PPCStateAdVarID)==0)
				{
					$Sql = "INSERT INTO bevomedia_ppc_advariations_stats (advariationsId, Impressions, Clicks, CPC, Cost, Pos, StatDate) 
											  VALUES ({$PPCAdVarID}, {$Impressions}, {$Clicks}, {$CPC}, {$Cost}, {$Pos}, '{$StatDate}'); ";
					echo $Sql . "\n";
					mysql_query($Sql);
					$PPCStateKeywordID = mysql_insert_id();
				} else
				{
					$PPCStateAdVarID = mysql_fetch_assoc($PPCStateAdVarID);
					$PPCStateAdVarID = $PPCStateAdVarID['ID'];
					
					$Sql = "UPDATE bevomedia_ppc_advariations_stats SET advariationsId = {$PPCAdVarID}, impressions = {$Impressions}, clicks = {$Clicks}, 
													cpc = {$CPC}, cost = {$Cost}, pos = {$Pos}, statdate = '{$StatDate}'
												WHERE ID = {$PPCStateAdVarID} ";
					echo $Sql . "\n";
					mysql_query($Sql);
				}
				//bevomedia_ppc_advariations_stats
			}
		}
	}
	
	fclose($handle);

	if(!isset($StatDate))
	{
		echo 'NOSTATDATE' . "\n";
		return 0;
	}
		
	return $StatDate;
}
	
?>