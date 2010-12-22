<?php

if(isset($_SERVER['argc']) && $_SERVER['argc']>0)
{
	$tmp = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], 'Applications'));
	require_once($tmp . '/Applications/BevoMedia/' . 'Common/AbsoluteIncludeHelper.include.php');
}
else
{
	$tmp = substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], 'www'));
	require_once($tmp . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');
}

if(!function_exists("cmpQuery"))
{
	function getCampaignByName($name, $accountId)
	{
		$Sql = "SELECT ID FROM bevomedia_ppc_campaigns WHERE ProviderType = 3 AND AccountID = $accountId AND Name = '$name'";
		$query = mysql_query($Sql);
		if(mysql_num_rows($query) < 1)
		{
			return false;
		}
		$row = mysql_fetch_assoc($query);
		return $row['ID'];
	}
	function getAdGroupByName($name, $campaignId)
	{
		$Sql = "SELECT ID FROM bevomedia_ppc_adgroups WHERE CampaignID = '$campaignId' AND Name = '$name'";
		$query = mysql_query($Sql);
		if(mysql_num_rows($query) < 1)
		{
			return false;
		}
		$row = mysql_fetch_assoc($query);
		return $row['ID'];
	}
	function getKeywordsForAdGroup($adGroupId)
	{
		$Sql = "SELECT Keyword FROM bevomedia_ppc_keywords LEFT JOIN bevomedia_keyword_tracker_keywords ON bevomedia_ppc_keywords.KeywordID = bevomedia_keyword_tracker_keywords.ID WHERE bevomedia_ppc_keywords.AdGroupID = $adGroupId";
		echo $Sql."\n";
		$query = mysql_query($Sql);
		if(mysql_num_rows($query) < 1)
		{
			return false;
		}
		$rows = array();
		while($row = mysql_fetch_assoc($query))
			$rows[] = $row['Keyword'];
		return $rows;
	}
	function cmpQuery($query, $kwList)
	{
		$cmp = array();
		foreach($kwList as $kw)
		{
			$lVal = levenshtein($query, $kw);
			$sVal = similar_text($kw, $query);
			$temp = array('lev'=>$lVal, 'sim'=>$sVal, 'keyword'=>$kw);
			$cmp[] = $temp;
		}
		$simVal = getSimVal($cmp);
		$cmp = removeSims($cmp, $simVal);
		$match = getLowestLevKW($cmp);
		return $match;
	}
	function removeSims($arr, $val)
	{
		$output = array();
		foreach($arr as $k=>$v)
		{
			if($v['sim'] == $val)
			{
				$output[] = $v;
			}
		}
		return $output;
	}
	function getSimVal($arr)
	{
		$high = -1;
		foreach($arr as $itm)
		{
			if($itm['sim'] > $high)
			{
				$high = $itm['sim'];
			}
		}
		return $high;
	}
	function getLowestLevKW($arr)
	{
		$low = 10000;
		$kw = $arr[0]['keyword'];
		foreach($arr as $itm)
		{
			if($itm['lev'] <= $low)
			{
				$low = $itm['lev'];
				$kw = $itm['keyword'];
			}
		}
		return $kw;
	}
}



function UploadReportQuery($userId, $intAccountID, $reportFile) {	
	if (!is_numeric($userId) || !is_numeric($intAccountID)) {
		return false;
	}

	$reportFile = sys_get_temp_dir() . '/' . $reportFile;
	
	// Only Accept Text/XML Uploads
	$arrAcceptedTypes = array('text/csv', 'text/plain', 'application/zip', 'text/tsv', '');

	// Extract Zip File or Read Upload Contents
	if (strtolower(substr($reportFile, strlen($reportFile)-3, 3)) == 'zip') 
	{
		$strReport = ExtractReportQuery($reportFile);
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
	$DateImported = ImportMicrosoftAdCenterQuery($FileName, $intAccountID);

	//@unlink($reportFile);
	return true;
	//ParseReport($arrUser, $strReport);
}

function ExtractReportQuery($strInFile) {
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


function ImportMicrosoftAdCenterQuery($report, $accountId)
{
	$DeletedCombinationsSQ = array();
	
	$handle = fopen($report, "r");
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
	{	
		if(sizeOf($data) < 10)
			continue;
			
		print_r($data);
		print "\n";
		//print $data[0] . "\n";
		
		if ($data[0]=="Month" || $data[0]=="GregorianDate")
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
		
		$Status = 1;
		$StatDate = $data[0];
		$AccountName = $data[1];
		$CampaignName = $data[2];	
		$AdGroupName = $data[3];
		$apiAdId = $data[4];
		$Impressions = $data[5];
		$Clicks = $data[6];
		$CPC = $data[7];
		$Pos = $data[8];
		$AdType = $data[9];
		$AdGroupID = $data[10];
		$SearchQuery = $data[11];
		$Cost = $data[12];
		
		$StDate = date('Y-m-d', strtotime($StatDate));
		
		if($SearchQuery == '1 other unique queries')
			continue;
			
		$cId = getCampaignByName($CampaignName, $accountId);
		if($cId === false)
		{
			continue;
		}
		$aId = getAdGroupByName($AdGroupName, $cId);
		if($aId === false)
		{
			continue;
		}
		
	
		if (!in_array(array($StDate, $aId, $cId), $DeletedCombinationsSQ))
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
						(bevomedia_ppc_search_query.statdate = '$StDate') AND
						(bevomedia_ppc_advariations.AdGroupID = {$aId}) AND
						(bevomedia_ppc_campaigns.ID = {$cId})
					"; 
			print $Sql . "\n";
			$DeleteResults = mysql_query($Sql);
		
			while ($DeleteRow = mysql_fetch_assoc($DeleteResults))
			{
				$Sql = "DELETE FROM bevomedia_ppc_search_query WHERE id = {$DeleteRow['ID']} ";
				print $Sql . "\n";
				mysql_query($Sql);
			}
			
			$DeletedCombinationsSQ[] = array($StDate, $aId, $cId);
		}
		
		$kws = getKeywordsForAdGroup($aId);
		//print $row['campaign'] . "($cId) >> " . $row['adgroup'] . "($aId) :\t" . $row['Query'] . "\n";

		//print_r($row);
		$keyword = cmpQuery($SearchQuery, $kws);
		//print "\t\t\t\t$keyword\n\n";
		
		$Sql = "SELECT id FROM bevomedia_ppc_search_query WHERE ppcAdvariations_id = $apiAdId AND query = \"$SearchQuery\" AND keyword = \"$keyword\" AND statdate = '$StDate'";
		print $Sql . "\n";
		$query = mysql_query($Sql);
		$count = mysql_num_rows($query);
		//print $count . "\n";
		if($count < 1)
		{
			$Sql = "INSERT INTO bevomedia_ppc_search_query	(ppcAdvariations_id, imps, clicks, cpc, cost, pos, query, keyword, statdate)
											VALUES	($apiAdId, $Impressions, $Clicks, $CPC, $Cost, $Pos, '$SearchQuery', '$keyword', '$StDate')";
			print $Sql . "\n";
			$query = mysql_query($Sql);
			$id = mysql_insert_id();
		}else{
			$rw = mysql_fetch_assoc($query);
			$id = $rw['id'];
			$Sql = "UPDATE bevomedia_ppc_search_query SET imps = $Impressions, clicks = $Clicks, cpc = $CPC, cost = $Cost, pos = $Pos, query = '$SearchQuery', keyword = '$keyword' WHERE id = $id";
			print $Sql . "\n";
			mysql_query($Sql);
		}
	}
	
}
?>