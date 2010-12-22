<?php
/**
 * Generates XML output for use with XML/SWF Charts.
 */

/**
 * Generates XML output for use with XML/SWF Charts.
 *
 * This class, tied with the {@see Network_Stats} class, will generate the XML output necessary to properly render basic line, bar and column charts
 * for XML/SWF charts.
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 * @todo		Add sample chart XML output.
 */
Class ChartXMLHelper {
	
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * The default number of objects to be output.
	 *
	 * @var Integer $DefaultItemOutput
	 */
	Private $DefaultItemOutput = 3;
	
	/**
	 * License key to allow further interaction with XML/SWF charts.
	 * If the key does not match the domain the user will be redirected to the XML/SWF homepage when they click on the chart.
	 *
	 * @var String $License
	 */
	Public $License = 'GTA9I-PM7Q.O.945CWK-2XOI1X0-7L';
	
	/**
	 * Chart type.
	 * @version 0.1: 'Line', 'Bar' and 'Column' are supported.
	 * @var String $ChartType
	 */
	Public $ChartType = 'Line';
	
	/**
	 * @var Array $SeriesColor
	 */
	Public $SeriesColor = array('203070','FF8800','840048');
	
	/**
	 * @var String $DateRange
	 */
	Public $DateRange = "1/1/2009 - 12/31/2009";
	
	/**
	 * @var String $Field
	 */
	Public $Field = 'impressions';
	
	/**
	 * @var Mixed $StatsShowRows
	 */
	Public $StatsShowRows = false;
	
	Private $Data = array();
	Private $DateRangeArray = array();
	
	Private $ChartOutput = false;
	
	/**
	 * Constructor
	 */
	Public Function __construct()
	{
		
	}
	
	/**
	 * When a user attempts to echo this class the chart XML output will be returned.
	 * Be sure to set a date range using SetDateRange and that data has been loaded.
	 * @see SetDateRange()
	 * @see LoadAdGroupStats()
	 * @see LoadCampaignStats()
	 * @see LoadAccountStats()
	 * @see LoadPPCManagerStats()
	 *
	 * @return String
	 */
	Public Function __toString()
	{
		if($this->ChartOutput === false)
		{
			$this->WriteHeader();
			$this->WriteData();
			$this->WriteFooter();
		}
		return $this->ChartOutput;
	}

	/**
	 * Returns object containing row and column data.
	 *
	 * @return array
	 */
	Public Function GetJQueryChartData()
	{
		$Output = new stdClass();
		$Output->Dates = array();
		
		if (isset($this->CustomDateRangeArray))
		{
			foreach($this->CustomDateRangeArray as $Date)
			{
				$Output->Dates[] = $Date;
			}
		} else
		{
			foreach($this->DateRangeArray as $Date)
			{
				$Output->Dates[] = date('m-d', strtotime($Date[0]));
			}
		}
		
		$Output->Data = array();
		foreach($this->Data as $StatRow)
		{
			$Output->Data[] = $StatRow;
		}
		
		return $Output;
	}
	
	
	/**
	 * Returns object containing row and column data.
	 *
	 * @return array
	 */
	Public Function GetJQueryChartOutput($Caption = 'Table Caption', $TableID='JQueryChartData', $TableDisplayID='JQueryChartDisplay', $TableClass = '', $CustomMargin = '0')
	{
		$Data = $this->GetJQueryChartData();
		$HasData = $this->CheckData($Data);
		
		$LeftOffset = 'margin-left:'.$CustomMargin.'px;';
		if($HasData)
		{
			$LeftOffset = '';
		}
		$Output = '<div style="'. $LeftOffset .'" class="Parent'.$TableClass.'"><table class="JQueryChartData '. $TableClass .'" style="display:none;" id="' . $TableID . '">';
		$Output .= "\n\t";
			$Output .= '<caption>' . $Caption . '</caption>';
		
		$Output .= "\n\t";
			$Output .= '<thead>';
		
		$Output .= "\n\t\t";
				$Output .= '<tr>';
		$Output .= "\n\t\t\t";
					$Output .= '<td></td>';
				
		foreach($Data->Dates as $Date)
		{
			$Output .= "\n\t\t\t";
					$Output .= '<th>' . $Date . '</th>';
		}
		
		$Output .= "\n\t\t";
				$Output .= '</tr>';
		$Output .= "\n\t";
			$Output .= '</thead>';
			
		$Output .= "\n\t";
			$Output .= '<tbody>';
			
		foreach($Data->Data as $DataRow)
		{
			$Output .= "\n\t\t";
					$Output .= '<tr>';
			$Output .= "\n\t\t\t";
						$Output .= '<th>' . $DataRow->Name . '</th>';
					
			foreach($DataRow->Stats as $Stats)
			{
				$Output .= "\n\t\t\t";
						$Output .= '<td>' . $Stats . '</td>';
			}
			$Output .= "\n\t\t";
					$Output .= '</tr>';
		}
		$Output .= "\n\t";
			$Output .= '</tbody>';
		$Output .= "\n";
			$Output .= '</table>';
		$Output .= '<div class="JQueryChartDisplay '. $TableClass .'" id="'. $TableDisplayID .'"></div></div>';
		
		return $Output;
	}
	
	/**
	 * Sets the date range for the chart in the format "MM/DD/YY - MM/DD/YY".
	 * <code>
	 * //Create a new ChartXMLHelper instance
	 * $ChartXML = new ChartXMLHelper();
	 *
	 * //Set the date range to the range from January 1, 1970 to June 15, 1970
	 * $ChartXML->SetDateRange('1/1/1970 - 6/15/1970');
	 * </code>
	 *
	 * @param String $DateRange
	 */
	Public Function SetDateRange($DateRange)
	{
		$this->DateRange = $DateRange;
		$Date = explode('-', $this->DateRange);
		if(sizeOf($Date) < 2)
		{
			$Date[1] = $Date[0];
		}
		$DateWalk = $this->GetDateArray($Date[0], $Date[1]);
		$this->DateRangeArray = $DateWalk;
	}
	
	Public Function GetDateRangeArray()
	{
		return $this->DateRangeArray;
	}
	
	/**
	 * Populate this object with stats from the specified Ad Group matching $AdGroupID.
	 *
	 * @param Integer $AdGroupID
	 */
	Public Function LoadAdGroupStats($AdGroupID)
	{
		$this->Data = array();
		$Stats = new Network_Stats();
		$AdGroups = $Stats->GetAllKeywordsForAdGroup($AdGroupID);
		foreach($AdGroups as $AdGroup)
		{
			if($this->StatsShowRows !== false)
			{
				if(!in_array($AdGroup->ID, $this->StatsShowRows))
				{
					continue;
				}
			}else{
				if(sizeOf($this->Data) >= $this->DefaultItemOutput)
				{
					continue;
				}
			}
			
			$Temp = new stdClass();
			$Temp->Name = $AdGroup->FormattedName;
			$Temp->Stats = array();
			foreach($this->DateRangeArray as $DateRange)
			{
				$StatRow = $Stats->GetKeywordStats($AdGroup->ID, $DateRange[0], $DateRange[1]);
				$Temp->Stats[] = $StatRow->{$this->Field};
			}
			if($this->StatsShowRows === false)
			{
				$TempSum = 0;
				foreach($Temp->Stats as $Stat)
				{
					$TempSum += $Stat;
				}
				if($TempSum == 0)
					continue;
			}
			
			$this->Data[] = $Temp;
		}
	}
	
	/**
	 * Populate this object with stats from the specified Campaign matching $CampaignID.
	 *
	 * @param Integer $CampaignID
	 */
	Public Function LoadCampaignStats($CampaignID)
	{
		$this->Data = array();
		$Stats = new Network_Stats();
		$AdGroups = $Stats->GetAllAdGroupsForCampaign($CampaignID);
		foreach($AdGroups as $AdGroup)
		{
			if($this->StatsShowRows !== false)
			{
				if(!in_array($AdGroup->ID, $this->StatsShowRows))
				{
					continue;
				}
			}else{
				if(sizeOf($this->Data) >= $this->DefaultItemOutput)
				{
					continue;
				}
			}
						
			$Temp = new stdClass();
			$Temp->Name = $AdGroup->Name;
			$Temp->Stats = array();
			foreach($this->DateRangeArray as $DateRange)
			{
				$StatRow = $Stats->GetAdGroupStats($AdGroup->ID, $DateRange[0], $DateRange[1]);
				$Temp->Stats[] = $StatRow->{$this->Field};
			}
			$this->Data[] = $Temp;
		}
	}
	
	/**
	 * Populate this object with stats from the specified Account matching $AccountID and $ProviderType.
	 *
	 * @param Integer $AccountID
	 * @param Integer $ProviderType
	 */
	Public Function LoadAccountStats($AccountID, $ProviderType)
	{
		$this->Data = array();
		$Stats = new Network_Stats();
		$Campaigns = $Stats->GetAllCampaignsForAccount($AccountID, $ProviderType);
		foreach($Campaigns as $Campaign)
		{
			if($this->StatsShowRows !== false)
			{
				if(!in_array($Campaign->ID, $this->StatsShowRows))
				{
					continue;
				}
			}else{
				if(sizeOf($this->Data) >= $this->DefaultItemOutput)
				{
					continue;
				}
			}
						
			$Temp = new stdClass();
			$Temp->Name = $Campaign->Name;
			$Temp->Stats = array();
			foreach($this->DateRangeArray as $DateRange)
			{
				$StatRow = $Stats->GetCampaignStats($Campaign->ID, $DateRange[0], $DateRange[1]);
				$Temp->Stats[] = $StatRow->{$this->Field};
			}
			$this->Data[] = $Temp;
		}
	}
	
	/**
	 * Populate this object with Analytics stats for the User matching $User_ID.
	 *
	 * @param Integer $User_ID
	 */
	Public Function LoadAnalyticsDetailStats($User_ID)
	{
		$this->Data = array();
		$Stats = new Accounts_Analytics();

		$Rows = array('Visits', 'PageViews', 'Total');
		
		foreach($Rows as $Label)
		{
			$Temp = new stdClass();
			$Temp->Name = $Label;
			foreach($this->DateRangeArray as $DateRange)
			{
				$StatRow = $Stats->GetMonthlyStats($User_ID, $DateRange[0], $DateRange[1]);
				$Temp->Stats[] = $StatRow->{$Label};
			}
			$this->Data[] = $Temp;
		}
	}
	
	/**
	 * Populate this object with Analytics stats for the User matching $User_ID.
	 *
	 * @param Integer $User_ID
	 */
	Public Function LoadAnalyticsTrafficSourcesStats($User_ID, $DomainID)
	{
		$this->Data = array();
		$Stats = new Accounts_Analytics();

		$StartDate = $this->DateRangeArray[0][0];
		$EndDate = $this->DateRangeArray[sizeOf($this->DateRangeArray)-1][1];
		$this->DateRangeArray = array($StartDate);
		
		$StatData = $Stats->GetTrafficSourcesStats($User_ID, $DomainID, $StartDate, $EndDate);
		
		$Rows = array('DirectTrafficPercentValue'=>'Direct Traffic', 'SearchEnginesPercentValue'=>'Search Engines');
		
		foreach($Rows as $Key=>$Label)
		{
			$Temp = new stdClass();
			$Temp->Name = $Label;
			$Temp->Stats[] = @$StatData->$Key;

			$this->Data[] = $Temp;
		}
	}
	
	/**
	 * Populate this object with Analytics stats for the User matching $User_ID.
	 *
	 * @param Integer $User_ID
	 */
	Public Function LoadAnalyticsNewVisitorStats($User_ID, $DomainID)
	{
		$this->Data = array();
		$Stats = new Accounts_Analytics();

		$StartDate = $this->DateRangeArray[0][0];
		$EndDate = $this->DateRangeArray[sizeOf($this->DateRangeArray)-1][1];
		$this->DateRangeArray = array($StartDate);
		$StatData = $Stats->GetNewVisitorStats($User_ID, $DomainID, $StartDate, $EndDate);
		
		$Rows = array('NewVisitors'=>'New Visitors', 'ReturningVisitors'=>'Returning Visitors');
		
		foreach($Rows as $Key=>$Label)
		{
			$Temp = new stdClass();
			$Temp->Name = $Label;
			$Temp->Stats[] = $StatData[$Key];

			$this->Data[] = $Temp;
		}
	}

	/**
	 * Populate this object with Analytics stats for the User matching $User_ID and domain matching $DomainID.
	 *
	 * @param Integer $User_ID
	 */
	Public Function LoadAnalyticsDemographStats($User_ID, $DomainID)
	{
		$this->Data = array();
		$Stats = new Accounts_Analytics();

		$StartDate = $this->DateRangeArray[0][0];
		$EndDate = $this->DateRangeArray[sizeOf($this->DateRangeArray)-1][1];
		
		$StatData = $Stats->GetDemographicStats($User_ID, $DomainID, $StartDate, $EndDate);
		
		foreach($StatData as $StatRow)
		{
			if($StatRow->Name == '(not set)')
				continue;
				
			$Temp = new stdClass();
			$Temp->Name = $StatRow->Name;
			$Temp->Stats[] = $StatRow->Value;

			$this->Data[] = $Temp;
		}
	}
	
	/**
	 * Populate this object with stats from the specified Accounts belonging to the User matching $User_ID.
	 *
	 * @param Integer $User_ID
	 */
	Public Function LoadPPCManagerStats($User_ID)
	{
		$this->Data = array();
		$Stats = new Network_Stats();

		$ProviderArr = array(1=>'Google', 2=>'Yahoo', 3=>'MSN');
		
		foreach($ProviderArr as $Type=>$Label)
		{
			$Temp = new stdClass();
			$Temp->Name = $Label;
			foreach($this->DateRangeArray as $DateRange)
			{
				$StatRow = $Stats->GetAllPPCStatsForUser($Type, $User_ID, $DateRange[0], $DateRange[1]);
				$Temp->Stats[] = $StatRow->{$this->Field};
			}
			$this->Data[] = $Temp;
		}
	}
	
	Public Function LoadTopBarStats($User_ID)
	{
		$this->Data = array();
		$Stats = new Network_Stats();
		
//		$Costs = $Stats->GetTopBarChartCosts($User_ID, '2010-08-01', '2010-08-31');
//		$Earnings = $Stats->GetTopBarChartEarning($User_ID, '2010-08-01', '2010-08-31');
//test

		$Costs = $Stats->GetTopBarChartCosts($User_ID);
		$Earnings = $Stats->GetTopBarChartEarning($User_ID);
		
		
		$CostsItem = new stdClass();
		$EaringsItem = new stdClass();
		$ProfitItem = new stdClass();
		
		$CostsItem->Name = 'Expenses';
		$EaringsItem->Name = 'Earnings';
		$ProfitItem->Name = 'Net Profit';
		
		$this->DateRangeArray = array();
		 
		
		for ($i=1; $i<=date('j'); $i++)
		{
//			$Date = date('Y').'-08-'.$i;
//			$Date = date('Y-m-d', strtotime($Date));
//test
			
			$this->CustomDateRangeArray[] = $i;
			
			$Date = date('Y').'-'.date('m').'-'.$i;
			$Date = date('Y-m-d', strtotime($Date));
			
			
			$CostsItem->Stats[] = floatval(@$Costs[$Date]->cost);
			$EaringsItem->Stats[] = floatval(@$Earnings[$Date]->REVENUE);
			$ProfitItem->Stats[] = @$Earnings[$Date]->REVENUE-@$Costs[$Date]->cost;
			
		}
		
		$this->Data[] = $CostsItem;
		$this->Data[] = $EaringsItem;
		$this->Data[] = $ProfitItem;
		
	}
	
	Public Function SetData($D)
	{
		$this->Data = $D;
	}
	
	Public Function SetDateRangeArray($DRA)
	{
		$this->DateRangeArray = $DRA;
	}
	
	
	/**
	 * Private Functions
	 */
	Private Function WriteHeader()
	{
		$this->ChartOutput .="<chart>\n\t<license>{$this->License}</license>\n\t<chart_type>{$this->ChartType}</chart_type>\n\t<series_color>\n";

		foreach($this->SeriesColor as $Color)
		{
			$this->ChartOutput .= "\t\t<color>$Color</color>\n";
		}

		$this->ChartOutput .= "\t</series_color>\n";
	}
	
	Private Function WriteData()
	{
		$this->ChartOutput .= "\t<chart_data>\n";
		if(!$this->Data)
		{
			$this->WriteNoDataDateRow();
		}else{
			$this->WriteDateRow();
		}
		$this->WriteDataRows();
		$this->ChartOutput .= "\t</chart_data>\n";
	}
	
	Private Function WriteDataRows()
	{
		if(!$this->Data)
		{
			$Data = new stdClass();
			$Data->Name = 'No Data Available';
			$Data->Stats = array();
			for($i=0; $i<20; $i++)
			{
				$Data->Stats[] = (sin($i*100) * 2)+5;
			}
			
			$this->Data[] = $Data;
			
			$Data = new stdClass();
			$Data->Name = 'No Data Available';
			$Data->Stats = array();
			$Offset = rand(1,5);
			for($i=0; $i<20; $i++)
			{
				$Data->Stats[] = (sin(($i+$Offset)*100) * 5)+5;
			}
			$this->Data[] = $Data;
		}
			
		foreach($this->Data as $Data)
		{
			$this->ChartOutput .= "\t\t<row>\n";
			$this->ChartOutput .= "\t\t\t<string >{$Data->Name}</string>\n";
			foreach($Data->Stats as $Stats)
			{
				$this->ChartOutput .= "\t\t\t<number shadow='shadow1'>{$Stats}</number>\n";
			}
			$this->ChartOutput .= "\t\t</row>\n";
		}
	}

	Private Function WriteNoDataDateRow()
	{
		$this->ChartOutput .= "\t\t<row>\n\t\t\t<null />\n";
		for($i=0; $i<20; $i++)
		{
			$this->ChartOutput .= "\t\t\t<string>" . ($i+1) . "</string>\n";
		}
		$this->ChartOutput .= "\t\t</row>\n";
	}
	
	Private Function WriteDateRow()
	{
		$this->ChartOutput .= "\t\t<row>\n\t\t\t<null />\n";
		foreach($this->DateRangeArray as $Date)
		{
			$this->ChartOutput .= "\t\t\t<string>" . date('m-d', strtotime($Date[0])) . "</string>\n";
		}
		$this->ChartOutput .= "\t\t</row>\n";
	}
	
	Private Function WriteFooter()
	{
		$this->ChartOutput .= "\t<filter><shadow id='shadow1' distance='3' angle='50' blurX='6' blurY='6' alpha='50' /></filter>\n";
		$this->ChartOutput .= "</chart>";
	}
	
	
	Private Function GetDateArray($StartDate, $EndDate)
	{
		return $this->datewalker_inc_GetDateArray($StartDate, $EndDate);
	}
	
	Private Function CheckData($D)
	{
		$Output = false;
		foreach($D->Data as $DataRow)
		{
			foreach($DataRow->Stats as $StatRow)
			{
				if($StatRow > 0)
					$Output = true;
			}
		}
		return $Output;
	}
	
	/*
	 * Functions from datewalker_inc.php
	 * Author unknown.
	 * Prefixed to datewalker_inc_*
	 * Modified to reference renamed functions that have prefix of datewalker_inc_*
	 * Removed commented functionality
	 */
	Private Function datewalker_inc_GetDateArray($inStartDate, $inEndDate)
	{
		$strStartDate = strtotime($inStartDate);
		$strEndDate = strtotime($inEndDate);

		$intDateDiff = ($strEndDate - $strStartDate)/86400;

		$arrDates = array();
		
		// If Date Diff > 3mnths, Group by Month
		// Start with First Day of Month
		if ($intDateDiff > 90) {
			$strStartDate = strtotime($this->datewalker_inc_getFirstOfMonth($strStartDate));
			$strLastDate = date('Y-m-d', strtotime(date('Y-m-d', $strStartDate) . '+1month'));
			
			$arrDates[] = array(date('Y-m-d', $strStartDate), date('Y-m-d', strtotime($strLastDate . '-1day')));
			
			while (strtotime($strLastDate) < $strEndDate) {
				$strNextLastDate = date('Y-m-d', strtotime($strLastDate . '+1month'));
				$arrDates[] = array($strLastDate, date('Y-m-d', strtotime($strNextLastDate . '-1day')));
				$strLastDate = $strNextLastDate;
			}
			
			return $arrDates;
		}
		
		// If Date Diff > 3wks, Group by Week
		// Start with Monday of First Week
		if ($intDateDiff > 21) {
			$strStartDate = strtotime($this->datewalker_inc_getMondayDate($strStartDate));
			$strLastDate = date('Y-m-d', strtotime(date('Y-m-d', $strStartDate) . '+1weeks'));

			$arrDates[] = array(date('Y-m-d', $strStartDate), date('Y-m-d', strtotime($strLastDate . '-1day')));
			
			while (strtotime($strLastDate) < $strEndDate) {
				$strNextLastDate = date('Y-m-d', strtotime($strLastDate . '+1weeks'));
				$arrDates[] = array($strLastDate, date('Y-m-d', strtotime($strNextLastDate . '-1day')));
				$strLastDate = $strNextLastDate;
			}
			
			return $arrDates;
		}
		
		// Else Date Diff < 3wks, Group By Day
		$arrDates = array();
		$arrDates[] = array(date('Y-m-d', $strStartDate), date('Y-m-d', $strStartDate));
		
		for ($intX = 1; $intX < $intDateDiff; $intX++) {
			$strThisDate = date('Y-m-d', strtotime(date('Y-m-d', $strStartDate) . '+' . $intX . 'days'));
			$arrDates[] = array($strThisDate, $strThisDate);
		}
		
		$arrDates[] = array(date('Y-m-d', $strEndDate), date('Y-m-d', $strEndDate));
		return $arrDates;
	}
	
	
	Private Function datewalker_inc_getMondayDate($inDate) {
		$intDayNum = date('N', $inDate);
		if ($intDayNum > 1) {
			return date('Y-m-d', strtotime(date('Y-m-d', $inDate) . '-' . ($intDayNum - 1) . 'days'));
		}
		return date('Y-m-d', $inDate);
	}
	
	Private Function datewalker_inc_getFirstOfMonth($inDate) {
		$intMonth = date('m', $inDate);
		$intYear = date('Y', $inDate);
		return date('Y-m-d', mktime(0, 0, 0, $intMonth, 1, $intYear));
	}
}

?>
