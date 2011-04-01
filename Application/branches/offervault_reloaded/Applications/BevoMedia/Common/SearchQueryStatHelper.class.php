<?php
class SearchQueryStatHelper
{
	
	public function __construct()
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
	}
	
	private function CalcCostTotal($Rows)
	{
		$Output = 0;
		foreach($Rows as $Row)
		{
			if(!isset($Row->sumCost))
			{
				$Row->sumCost = 0;
			}
			$Output += $Row->sumCost;
		}
		return $Output;
	}
	
	private function CalcRevenueTotal($Rows)
	{
		$Output = 0;
		foreach($Rows as $Row)
		{
			$Output += $Row->sumRevenue;
		}
		return $Output;
	}
	
	private function CalcClickTotal($Rows)
	{
		$Output = 0;
		foreach($Rows as $Row)
		{
			$Output += $Row->sumClick;
		}
		return $Output;
	}
	
	private function CompareRawToQuery(&$RawRow, &$SearchQueryList)
	{
		$RawKeyword = $RawRow->RawKeyword;
		$Compare = array();
		foreach($SearchQueryList as $SearchQueryKey=>$SearchQuery)
		{
			if($RawRow->sumClick == 0)
				continue;
			if($SearchQuery->sumClick == 0)
				continue;
				
			$Query = $SearchQuery->query;
			$lVal = levenshtein($RawKeyword, $Query);
			$sVal = similar_text($Query, $RawKeyword);
			$temp = array('lev'=>$lVal, 'sim'=>$sVal, 'RawKeyword'=>$RawKeyword, 'SearchQuery'=>$SearchQuery, 'SearchQueryKey'=>$SearchQueryKey);
			$Compare[] = $temp;
		}
		
		$High = -1;
		foreach($Compare as $Item)
		{
			if($Item['sim'] > $High)
			{
				$High = $Item['sim'];
			}
		}
		
		$SlimmedCompare = array();
		foreach($Compare as $Item)
		{
			if($Item['sim'] == $High)
			{
				$SlimmedCompare[] = $Item;
			}
		}

		$Low = 10000;
		$Match = "";
		foreach($SlimmedCompare as $Item)
		{
			if($Item['lev'] <= $Low)
			{
				$Low = $Item['lev'];
				$Match = $Item['RawKeyword'];
				$ItemMatch = $Item;
			}
		}
		
		if(!isset($ItemMatch))
		{
			return false;
		}
		
		$RawRow->sumCost += $ItemMatch['SearchQuery']->sumCost;
		$SearchQueryList[$ItemMatch['SearchQueryKey']]->sumClick -= $ItemMatch['SearchQuery']->sumClick;
		return $Match;
	}
	
	private function PopTrackerRows($TrackerRows, $SearchQueryRows)
	{
		foreach($TrackerRows as $TrackerRow)
		{
			$this->CompareRawToQuery($TrackerRow, $SearchQueryRows);
		}
		return $TrackerRows;
	}
	
	private function PopSearchQueryRows($SearchQueryRows)
	{
		$Output = array();
		foreach($SearchQueryRows as $SearchRow)
		{
			if($SearchRow->sumClick > 0)
			{
				$Output[] = $SearchRow;
			}
		}
		return $Output;
	}
	
	private function AddSearchToTracker($SearchQueryRows, $TrackerRows)
	{
		foreach($SearchQueryRows as $SearchQueryRow)
		{
			$SearchQueryRow->referrer_url = $SearchQueryRow->orig_page_url = $SearchQueryRow->click_time = '';
			$SearchQueryRow->BidKeyword = $SearchQueryRow->keyword;
			$SearchQueryRow->RawKeyword = $SearchQueryRow->query;
			$SearchQueryRow->sumConv = 0;
			$SearchQueryRow->bidKeywordId = $SearchQueryRow->rawKeywordId = $SearchQueryRow->click_id  = $SearchQueryRow->creativeId = '-1';
			$TrackerRows[] = $SearchQueryRow;
		}
		return $TrackerRows;
	}
	
	private function CompareAgainstStatRows($TrackerRows, $StatRows)
	{
		$Output = array();
		$TempClickArray = array();
		$TotalClickCount = $TotalTrackerClickCount = 0;
		foreach($StatRows as $StatRow)
		{
			$TotalClickCount += $StatRow->sumClick;
			$TempClickArray[$StatRow->keyword] = $StatRow->sumClick;
		}

		foreach($TrackerRows as $TrackerRow)
		{
			$TotalTrackerClickCount += $TrackerRow->sumClick;
		}
		
		$ClickDifference = $TotalTrackerClickCount - $TotalClickCount;
		if($ClickDifference < 0)
		{
			return $this->SubZeroClickDifference($TrackerRows, $StatRows, $ClickDifference);
		}
		if($ClickDifference == 0)
		{
			return $TrackerRows;
		}
		
		$ClicksRemoved = $CostDiscrepancy = 0;
		foreach($TrackerRows as $TrackerRow)
		{
			$Added = false;
			foreach($StatRows as $StatRow)
			{
				if($StatRow->keyword == $TrackerRow->BidKeyword)
				{
					if($ClicksRemoved >= $ClickDifference)
					{
						continue;
					}
					if($TempClickArray[$StatRow->keyword] <= 0)
					{
						continue;
					}
					$ClicksRemoved += $StatRow->sumClick;
					$TempClickArray[$StatRow->keyword] -= $StatRow->sumClick;
					$CostDiscrepancy += $StatRow->sumCost;
					$Added = true;
				}
			}
			if(!$Added)
			{
				$Output[] = $TrackerRow;
			}
		}
		
		$LargestClickKey = 0;
		foreach($Output as $OutputKey=>$OutputRow)
		{
			if($OutputRow->sumClick > $Output[$LargestClickKey]->sumClick)
			{
				$LargestClickKey = $OutputKey;
			}
		}
		$Output[$LargestClickKey]->sumCost += $CostDiscrepancy;
		return $Output;
	}
	
	private function SubZeroClickDifference($TrackerRows, $StatRows, $ClickDifference)
	{
		$TempClickArray = array();
		foreach($StatRows as $StatRow)
		{
			$TempClickArray[$StatRow->keyword] = isset($TempClickArray[$StatRow->keyword])?($TempClickArray[$StatRow->keyword]):0;
			$TempClickArray[$StatRow->keyword] += $StatRow->sumClick;
		}
		
		foreach($TrackerRows as $TrackerRow)
		{
			if($ClickDifference == 0)
			{
				continue;
			}
			if(!isset($TempClickArray[$TrackerRow->BidKeyword]))
			{
				continue;
			}
			if($TrackerRow->sumClick < $TempClickArray[$TrackerRow->BidKeyword])
			{
				$Amount = $TempClickArray[$TrackerRow->BidKeyword] - $TrackerRow->sumClick;
				if($Amount > $ClickDifference)
				{
					$Amount = $ClickDifference;
				}
				$TrackerRow->sumClick -= $Amount;
				$ClickDifference -= $Amount;
			}
		}
		return $TrackerRows;
	}
	
	private function SanitizeClickConversions($TrackerRows)
	{
		$Output = array();
		$AttachConv = array();
		foreach($TrackerRows as $TrackerRow)
		{
			if($TrackerRow->sumClick <= 0 && $TrackerRow->RawKeyword == '')
			{
				$AttachConv[] = $TrackerRow;
			}else{
				$Output[] = $TrackerRow;
			}
		}
				
		if(sizeof($AttachConv))
		{
			foreach($AttachConv as $AttachRow)
			{
				$LargestClickKey = $this->GetLargestClickKey($Output);
				@$Output[$LargestClickKey]->sumConv += $AttachRow->sumConv;
			}
		}
		return $Output;
	}
	
	private function DistributeCost($TrackerRows)
	{
		$NoCostClickCount = 0;
		if(!sizeof($TrackerRows))
		{
			return $TrackerRows;
		}
		$HighestCPCObject = $TrackerRows[0];
		foreach($TrackerRows as $TrackerRow)
		{
			if(!isset($TrackerRow->sumCost) || $TrackerRow->sumCost == 0)
			{
				$NoCostClickCount += @$TrackerRow->sumClick;
			}else{
				if($TrackerRow->sumCost / $TrackerRow->sumClick > $HighestCPCObject->sumCost / $HighestCPCObject->sumClick)
				{
					$HighestCPCObject = $TrackerRow;
				}
			}
		}
		$CPC = 0;
		if($NoCostClickCount != 0)
		{
			$CPC = $HighestCPCObject->sumCost / $NoCostClickCount;
		}
		$CPC *= 1;
		foreach($TrackerRows as $TrackerRow)
		{
			//$TrackerRow->RawKeyword .= ' [' . $TrackerRow->BidKeyword . ']';
			if(@$TrackerRow->sumClick <= 0)
			{
				continue;
			}
			$AmountCPC = $CPC * $TrackerRow->sumClick;
			if(!isset($TrackerRow->sumCost) || $TrackerRow->sumCost == 0 )
			{
				$HighestCPCObject->sumCost -= $AmountCPC;
				$TrackerRow->sumCost += $AmountCPC;
				//$TrackerRow->RawKeyword .= ' [' . $TrackerRow->BidKeyword . ']';
			}
		}
		
		return $TrackerRows;
	}
	
	private function DistributeCostPerBidKeyword($TrackerRows, $StatRows)
	{
		$TrackerBidKeywords = array();
		foreach($TrackerRows as $TrackerRow)
		{
			if(!in_array(@$TrackerRow->BidKeyword, $TrackerBidKeywords))
			{
				$TrackerBidKeywords[] = @$TrackerRow->BidKeyword;
			}
		}
		
		$Temp = array();
		$Extra = 0;
		foreach($StatRows as $StatRow)
		{
			if(in_array($StatRow->keyword, $TrackerBidKeywords))
			{
				$Temp[] = $StatRow;
			}else{
				$Extra += $StatRow->sumCost;
			}
		}
		$StatRows = $Temp;
		
		$DistribExtra = 0;
		if(sizeof($StatRows) > 0)
		{
			$DistribExtra = $Extra / sizeof($StatRows);
		}
		
		foreach($StatRows as $StatRow)
		{
			$StatRow->sumCost += $DistribExtra;
		}
		
		$StatCostTotal = $this->CalcCostTotal($StatRows);
		$TrackerCostTotal = $this->CalcCostTotal($TrackerRows);
		$Difference = $StatCostTotal - $TrackerCostTotal;
				
		if($Difference <= 0)
		{
			return $TrackerRows;
		}
		
		$StatClickTotal = $this->CalcClickTotal($StatRows);
		
		$ClickCostDistribution = array();
		foreach($StatRows as $StatRow)
		{
			$ClickCostDistribution[$StatRow->keyword] = isset($ClickCostDistribution[$StatRow->keyword])?$ClickCostDistribution[$StatRow->keyword]:0;
			$Amount = ($StatRow->sumClick / $StatClickTotal) * $Difference;
			$ClickCostDistribution[$StatRow->keyword] += $Amount;
		}
		
		$TrackerKeywordTotals = array();
		foreach($TrackerRows as $TrackerRow)
		{
			$TrackerKeywordTotals[$TrackerRow->BidKeyword] = isset($TrackerKeywordTotals[$TrackerRow->BidKeyword])?$TrackerKeywordTotals[$TrackerRow->BidKeyword]:0;
			$TrackerKeywordTotals[$TrackerRow->BidKeyword] += $TrackerRow->sumClick;
		}
		
		foreach($TrackerRows as $TrackerRow)
		{
			if(!isset($ClickCostDistribution[$TrackerRow->BidKeyword]))
			{
				$ClickCostDistribution[$TrackerRow->BidKeyword] = 0;
			}
			if($TrackerKeywordTotals[$TrackerRow->BidKeyword] == 0)
			{
				$Amnt = 0;
			}else{
				$Amnt = ($TrackerRow->sumClick / $TrackerKeywordTotals[$TrackerRow->BidKeyword]);
			}
			
			$Amount = $ClickCostDistribution[$TrackerRow->BidKeyword] * $Amnt;
			$TrackerRow->sumCost += $Amount;
		}
		
		$StatCostTotal = $this->CalcCostTotal($StatRows);
		$TrackerCostTotal = $this->CalcCostTotal($TrackerRows);
		
		return $TrackerRows;
	}
	
	private function RemoveExcessCost($TrackerRows, $StatRows)
	{
		$StatCostTotal = $this->CalcCostTotal($StatRows);
		$TrackerCostTotal = $this->CalcCostTotal($TrackerRows);
		$Difference = $StatCostTotal - $TrackerCostTotal;

		if($Difference >= 0)
		{
			return $TrackerRows;
		}
		
		$HighestTrackerRowKey = $this->GetLargestCostKey($TrackerRows);
		$HighestTrackerRow = $TrackerRows[$HighestTrackerRowKey];
		
		$HighestMinimumCost = $HighestTrackerRow->sumCost + $Difference;
		$KeySet = $this->GetHigherThanCostKeySet($TrackerRows, $HighestMinimumCost);
		$CostDistribution = $Difference / sizeof($KeySet);
		foreach($KeySet as $Key)
		{
			$TrackerRows[$Key]->sumCost += $CostDistribution;
		}
		return $TrackerRows;
	}
	
	private function GroupIdenticalRows($TrackerRows)
	{
		$Output = array();
		foreach($TrackerRows as $TrackerRow)
		{
			if(!isset($Output[@$TrackerRow->RawKeyword]))
			{
				$Output[@$TrackerRow->RawKeyword] = $TrackerRow;
			}else{
				$Temp = $Output[$TrackerRow->RawKeyword];
				$Temp->sumClick += $TrackerRow->sumClick;
				$Temp->sumCost += $TrackerRow->sumCost;
				$Temp->sumConv += $TrackerRow->sumConv;
				$Temp->sumRevenue += $TrackerRow->sumRevenue;
			}
		}
		return $Output;
	}
	
	private function GetHigherThanCostKeySet($TrackerRows, $Cost)
	{
		$Output = array();
		foreach($TrackerRows as $OutputKey=>$OutputRow)
		{
			if($OutputRow->sumCost >= $Cost)
			{
				$Output[] = $OutputKey;
			}
		}
		return $Output;
	}
	
	private function GetLargestCostKey($TrackerRows)
	{
		$LargestCostKey = 0;
		foreach($TrackerRows as $OutputKey=>$OutputRow)
		{
			if($OutputRow->sumCost > $TrackerRows[$LargestCostKey]->sumCost)
			{
				$LargestCostKey = $OutputKey;
			}
		}
		return $LargestCostKey;
	}
	
	private function GetLargestClickKey($TrackerRows)
	{
		$LargestClickKey = 0;
		foreach($TrackerRows as $OutputKey=>$OutputRow)
		{
			if(@$OutputRow->sumClick > @$TrackerRows[$LargestClickKey]->sumClick)
			{
				$LargestClickKey = $OutputKey;
			}
		}
		return $LargestClickKey;
	}
	
	private function GetLargestClickKeyWithEmptyKeyword($TrackerRows)
	{
		$Found = false;
		$LargestClickKey = 0;
		foreach($TrackerRows as $OutputKey=>$OutputRow)
		{
			if(@$OutputRow->RawKeyword != '')
				continue;
							
			if($OutputRow->sumClick >= $TrackerRows[$LargestClickKey]->sumClick)
			{
				$LargestClickKey = $OutputKey;
				$Found = true;
			}
		}
		if(!$Found)
		{
			return false;
		}
		return $LargestClickKey;
	}
	
	private function DailyTotalStruct()
	{
		$Output = new stdClass();
		$Output->Date = '';
		$Output->Revenue = 0;
		$Output->Expense = 0;
		$Output->Profit = 0;
		$Output->ROI = 0;
		return $Output;
	}
	
	public function GetDailyTotals($StartDate, $EndDate, $UserID, $ProviderType = false, $AccountID = false, $CampaignID = false)
	{
		$Output = array();
		
		$StartDate = strtotime($StartDate);
		$EndDate = strtotime($EndDate);
		
		while($StartDate <= $EndDate)
		{
			$StartDateSql = $EndDateSql = date('m/d/Y', $StartDate);
			$TrackerRows = $this->GetTrackerRows($StartDateSql, $EndDateSql, $UserID, $ProviderType, $AccountID, $CampaignID);
			$StatRows = $this->GetStatsRows($StartDateSql, $EndDateSql, $UserID, $ProviderType, $AccountID, $CampaignID);
			
			$Temp = $this->DailyTotalStruct();
			$Temp->Date = date('M j, Y', $StartDate);
			$Temp->Revenue = $this->CalcRevenueTotal($TrackerRows);
			$Temp->Expense = $this->CalcCostTotal($StatRows);
			$Temp->Profit = $Temp->Revenue - $Temp->Expense;
			if($Temp->Expense != 0)
			{
				$Temp->ROI = ($Temp->Profit / $Temp->Expense) * 100;
			}
			
			$Output[] = $Temp;

			$StartDate += 86400;
		}
		
		return $Output;
	}
	
	private function AssignUnknownClicksToConversions($TrackerRows)
	{
		$Output = array();
		$AttachClicks = array();
		foreach($TrackerRows as $TrackerRow)
		{
			if(@$TrackerRow->sumClick == 0 && @$TrackerRow->sumConv > 0)
			{
				$AttachClicks[] = $TrackerRow;
			}else{
				$Output[] = $TrackerRow;
			}
		}
		
		foreach($AttachClicks as $AttachClick)
		{
			$LargestKey = $this->GetLargestClickKeyWithEmptyKeyword($Output);
			@$Output[$LargestKey]->sumClick -= $AttachClick->sumConv;
			$AttachClick->sumClick += $AttachClick->sumConv;
			$Output[] = $AttachClick;
		}
		
		return $Output;
	}
	
	private function DistributeEmptyKeywords($TrackerRows)
	{
		$Output = array();
		$sumCost = 0;
		$sumClick = 0;
		foreach($TrackerRows as $TrackerRow)
		{
			if(@$TrackerRow->RawKeyword != '' && @$TrackerRow->BidKeyword != '')
			{
				$Output[] = $TrackerRow;
				continue;
			}
				
			$sumCost += @$TrackerRow->sumCost;
			$sumClick += @$TrackerRow->sumClick;
		}
		
		if($sumClick != 0)
		{
			$costPerClick = $sumCost / $sumClick;
		}
		$Key = $this->GetLargestClickKey($TrackerRows);
		/*foreach($Output as $OutputRow)
		{
			if($sumClick != 0)
			{
				$OutputRow->sumClick += 1;
				$sumClick -= 1;
				$OutputRow->sumCost += $costPerClick;
			}
		}*/
		if(isset($TrackerRows[$Key]))
		{
			$TrackerRows[$Key]->sumClick += $sumClick;
			$TrackerRows[$Key]->sumCost += $sumCost;
		}
		
		return $Output;
	}
	
	public function Process($StartDate, $EndDate, $UserID, $ProviderType = false, $AccountID = false, $CampaignID = false)
	{
		$TrackerRows = $this->GetTrackerRows($StartDate, $EndDate, $UserID, $ProviderType, $AccountID, $CampaignID);
		$StatRows = $this->GetStatsRows($StartDate, $EndDate, $UserID, $ProviderType, $AccountID, $CampaignID);
		$ModStatRows = $this->GetStatsRows($StartDate, $EndDate, $UserID, $ProviderType, $AccountID, $CampaignID);
		$SearchQueryRows = $this->GetSearchQueryRows($StartDate, $EndDate, $UserID, $ProviderType, $AccountID, $CampaignID);
		
		$TrackerRows = $this->PopTrackerRows($TrackerRows, $SearchQueryRows);
		$SearchQueryRows = $this->PopSearchQueryRows($SearchQueryRows);
		$TrackerRows = $this->AddSearchToTracker($SearchQueryRows, $TrackerRows);
		$TrackerRows = $this->CompareAgainstStatRows($TrackerRows, $StatRows);
		$TrackerRows = $this->SanitizeClickConversions($TrackerRows);
		$TrackerRows = $this->DistributeCost($TrackerRows);
		$TrackerRows = $this->DistributeCostPerBidKeyword($TrackerRows, $StatRows);
		$TrackerRows = $this->RemoveExcessCost($TrackerRows, $StatRows);
		$TrackerRows = $this->GroupIdenticalRows($TrackerRows);
		$TrackerRows = $this->AssignUnknownClicksToConversions($TrackerRows);
		$TrackerRows = $this->DistributeEmptyKeywords($TrackerRows);
		
		$Output = new stdClass();
		$Output->TrackerRows = $TrackerRows;
		$Output->StatRows = $StatRows;
		$Output->ModStatRows = $ModStatRows;
		$Output->SearchQueryRows = $SearchQueryRows;
		
		
		return $Output;
	}
	
	public function GetTrackerRows($StartDate, $EndDate, $UserID, $ProviderType = false, $AccountID = false, $CampaignID = false)
	{
		$StartDate = date('Y-m-d', strtotime($StartDate));
		$EndDate = date('Y-m-d', strtotime($EndDate));
		
		$AndSql = "";
		if($ProviderType == 'PPC')
		{
			$AndSql .= 'AND (pc.ProviderType = 1 OR pc.ProviderType = 2 OR pc.ProviderType = 3)';
		}else{
			$AndSql .= ($ProviderType !== false)?' AND pc.ProviderType = '.$ProviderType:'';
		}
		$AndSql .= ($AccountID !== false)?' AND pc.AccountID = '.$AccountID:'';
		$AndSql .= ($CampaignID !== false)?' AND pc.id = '.$CampaignID :'';
		
		$Sql = "SELECT
					tc.*,
					ktk_r.keyword as RawKeyword,
					ktk_b.keyword as BidKeyword,
					pc.*,
					pa.*,
					pc.Name as CampaignName,
					pa.Name as AdGroupName,
					sum(DISTINCT afs.CLICKS) as sumClick,
					sum(afs.CONVERSIONS) as sumConv,
					sum(afs.REVENUE) as sumRevenue
				FROM
					bevomedia_tracker_clicks tc
				JOIN
					bevomedia_keyword_tracker_keywords ktk_r
					ON ktk_r.id = tc.rawKeywordId
				JOIN
					bevomedia_keyword_tracker_keywords ktk_b
					ON ktk_b.id = tc.bidKeywordId
				JOIN
					bevomedia_ppc_advariations pav
					ON pav.apiAdId = tc.creativeId
				JOIN
					bevomedia_ppc_adgroups pa
					ON pa.id = pav.adGroupId
				JOIN
					bevomedia_ppc_campaigns pc
					ON (pc.user__id = {$UserID} AND pc.id = pa.campaignId)
				JOIN
					bevomedia_user_aff_network_subid afs
					ON (afs.user__id = {$UserID} AND afs.subId = tc.subId)
				WHERE
					afs.user__id = {$UserID}
					AND clickDate
						BETWEEN '{$StartDate}' AND '{$EndDate}'
					AND tc.creativeId != ''
					{$AndSql}
				GROUP BY
					tc.id
				ORDER BY
					ktk_b.keyword
				";
		return $this->_db->fetchAll($Sql);
	}
	
	public function GetStatsRows($StartDate, $EndDate, $UserID, $ProviderType = false, $AccountID = false, $CampaignID = false)
	{
		$StartDate = date('Y-m-d', strtotime($StartDate));
		$EndDate = date('Y-m-d', strtotime($EndDate));
		
		$AndSql = "";
		if($ProviderType == 'PPC')
		{
			$AndSql .= 'AND (pc.ProviderType = 1 OR pc.ProviderType = 2 OR pc.ProviderType = 3)';
		}else{
			$AndSql .= ($ProviderType !== false)?' AND pc.ProviderType = '.$ProviderType:'';
		}
		$AndSql .= ($AccountID !== false)?' AND pc.AccountID = '.$AccountID:'';
		$AndSql .= ($CampaignID !== false)?' AND pc.id = '.$CampaignID :'';
		
		$Sql = "SELECT
				*,
				pk.id as KeywordID,
				ktk_b.id as BidKeywordID,
				sum(pks.Clicks) as sumClick,
				sum(pks.Cost) as sumCost
			FROM
				bevomedia_ppc_keywords_stats pks
			JOIN
				bevomedia_ppc_keywords pk
				ON (pks.keywordId = pk.id)
			JOIN
				bevomedia_keyword_tracker_keywords ktk_b
				ON (ktk_b.id = pk.keywordId)
			JOIN
				bevomedia_ppc_adgroups pa
				ON pa.id = pk.adGroupId
			JOIN
				bevomedia_ppc_campaigns pc
				ON (pc.user__id = {$UserID} AND pc.id = pa.campaignId)
			WHERE
				pc.user__id = {$UserID}
				AND pks.StatDate
					BETWEEN '{$StartDate}' AND '{$EndDate}'
				AND pks.clicks > 0
				{$AndSql}
			GROUP BY
				pk.id
			";
			//print '<!-- DEBUG STATS SQL: ' . "\n" . $Sql . "\n" . '-->';
		return $this->_db->fetchAll($Sql);
	}
	
	
	public function GetSearchQueryRows($StartDate, $EndDate, $UserID, $ProviderType = false, $AccountID = false, $CampaignID = false)
	{
		$StartDate = date('Y-m-d', strtotime($StartDate));
		$EndDate = date('Y-m-d', strtotime($EndDate));
		
		$AndSql = "";
		if($ProviderType == 'PPC')
		{
			$AndSql .= 'AND (pc.ProviderType = 1 OR pc.ProviderType = 2 OR pc.ProviderType = 3)';
		}else{
			$AndSql .= ($ProviderType !== false)?' AND pc.ProviderType = '.$ProviderType:'';
		}
		$AndSql .= ($AccountID !== false)?' AND pc.AccountID = '.$AccountID:'';
		$AndSql .= ($CampaignID !== false)?' AND pc.id = '.$CampaignID :'';
		
		$Sql = "SELECT
					query,
					keyword,
					sum(clicks) AS sumClick,
					sum(cost) AS sumCost
				FROM
					bevomedia_ppc_search_query psq
				JOIN
					bevomedia_ppc_advariations pav
					ON pav.apiAdId = psq.ppcAdvariations_id
				JOIN
					bevomedia_ppc_adgroups pa
					ON pa.id = pav.adGroupId
				JOIN
					bevomedia_ppc_campaigns pc
					ON (pc.user__id = {$UserID} AND pc.id = pa.campaignId)
					
				WHERE
					pc.user__id = {$UserID}
					AND psq.statDate
						BETWEEN '{$StartDate}' AND '{$EndDate}'
						{$AndSql}
				GROUP BY
					query
				";
				//print '<!-- DEBUG SEARCHQUERY SQL: ' . "\n" . $Sql . "\n" . '-->';
		return $this->_db->fetchAll($Sql);
	}
	
}



?>