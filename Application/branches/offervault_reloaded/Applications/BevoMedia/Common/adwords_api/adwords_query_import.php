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

function getCampaignByName($name, $accountId)
{
	$sql = "SELECT ID FROM bevomedia_ppc_campaigns WHERE ProviderType = 1 AND AccountID = $accountId AND Name = '$name'";
	$query = mysql_query($sql);
	if(mysql_num_rows($query) < 1)
	{
		return false;
	}
	$row = mysql_fetch_assoc($query);
	return $row['ID'];
}
function getAdGroupByName($name, $campaignId)
{
	$sql = "SELECT ID FROM bevomedia_ppc_adgroups WHERE CampaignID = '$campaignId' AND Name = '$name'";
	$query = mysql_query($sql);
	if(mysql_num_rows($query) < 1)
	{
		return false;
	}
	$row = mysql_fetch_assoc($query);
	return $row['ID'];
}
function getKeywordsForAdGroup($adGroupId)
{
	$sql = "SELECT Keyword FROM bevomedia_ppc_keywords LEFT JOIN bevomedia_keyword_tracker_keywords ON bevomedia_ppc_keywords.KeywordID = bevomedia_keyword_tracker_keywords.ID WHERE bevomedia_ppc_keywords.AdGroupID = $adGroupId";
	$query = mysql_query($sql);
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

function query_update($report, $accountId)
{
	$DeletedCombinationsSQ = array();
	
	foreach($report->table->rows->row as $row)
	{
		if($row['Query'] == '1 other unique queries')
			continue;
			
		$cId = getCampaignByName($row['campaign'], $accountId);
		if($cId === false)
		{
			continue;
		}
		$aId = getAdGroupByName($row['adgroup'], $cId);
		if($aId === false)
		{
			continue;
		}
		
	
		if (!in_array(array($row['date'], $aId, $cId), $DeletedCombinationsSQ))
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
						(bevomedia_ppc_search_query.statdate = '$row[date]') AND
						(bevomedia_ppc_advariations.AdGroupID = {$aId}) AND
						(bevomedia_ppc_campaigns.ID = {$cId})
					"; 
			$DeleteResults = mysql_query($Sql);
		
			while ($DeleteRow = mysql_fetch_assoc($DeleteResults))
			{
				$Sql = "DELETE FROM bevomedia_ppc_search_query WHERE id = {$DeleteRow['ID']} ";
				mysql_query($Sql);
			}
			
			$DeletedCombinationsSQ[] = array($row['date'], $aId, $cId);
		}
		
		$kws = getKeywordsForAdGroup($aId);
		//print $row['campaign'] . "($cId) >> " . $row['adgroup'] . "($aId) :\t" . $row['Query'] . "\n";

		//print_r($row);
		$keyword = cmpQuery($row['Query'], $kws);
		//print "\t\t\t\t$keyword\n\n";
		
		$sql = "SELECT id FROM bevomedia_ppc_search_query WHERE ppcAdvariations_id = $row[creativeid] AND query = \"$row[Query]\" AND keyword = \"$keyword\" AND statdate = '$row[date]'";
		$query = mysql_query($sql);
		$count = mysql_num_rows($query);
		//print $count . "\n";
		if($count < 1)
		{
			$sql = "INSERT INTO bevomedia_ppc_search_query (ppcAdvariations_id, imps, clicks, ctr, cpc, cost, pos, query, keyword, statdate)
											VALUES ($row[creativeid], $row[imps], $row[clicks], $row[ctr], $row[cpc], $row[cost], $row[pos], '$row[Query]', '$keyword', '$row[date]')";
			
			//print $sql;
			$query = mysql_query($sql);
			$id = mysql_insert_id();
		}else{
			$rw = mysql_fetch_assoc($query);
			$id = $rw['id'];
			$sql = "UPDATE bevomedia_ppc_search_query SET imps = $row[imps], clicks = $row[clicks], ctr = $row[ctr], cpc = $row[cpc], cost = $row[cost], pos = $row[pos], query = '$row[Query]', keyword = '$keyword' WHERE id = $id";
			mysql_query($sql);
		}
	}
	
	$sql = "INSERT INTO bevomedia_adwords_api_usage (accountsAdwordsId, apiCalls) VALUES ('$accountId', 500)";
	mysql_query($sql);
	
}
?>