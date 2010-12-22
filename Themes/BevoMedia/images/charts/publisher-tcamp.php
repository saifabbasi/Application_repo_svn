<?php
  require("../path.php");
require_once(PATH . "Legacy.Abstraction.class.php");
  require(PATH . 'classes/clsKeywordTrackerClicks.php');
  require(PATH . 'classes/clsKeywordTrackerKeywords.php');
  require(PATH . 'classes/clsPPCKeywordStats.php');

$userID = $_GET['userId'];

include(PATH . "session.php");

require(PATH . 'inc_daterange.php');
require('datewalker_inc.php');

$arrFieldsVal = $_GET['Fields'];

$arrFields = array('NetClicks', 'KeywordClicks', 'AvgCPC', 'AvgCPM', 'NetCost', 'AvgPos', 'NetCtr', 'NetRevenue', 'AvgEPC', 'Profit');
if (empty($arrFieldsVal)) {
	$arrFieldsVal = array('NetClicks', 'KeywordClicks');
}

if (LegacyAbstraction::$strStartDateVal == LegacyAbstraction::$strEndDateVal) {
	LegacyAbstraction::$strStartDateVal = date('Y-m-d', strtotime(LegacyAbstraction::$strEndDateVal . '-7days'));
}

function ListCampaignStats() {
	global $userId, $arrFieldsVal;
	
	// Convert to MySQL Date Format but Dont Update Global Vars
	$strStartDate = date('Y-m-d', strtotime(LegacyAbstraction::$strStartDateVal));
	$strEndDate = date('Y-m-d', strtotime(LegacyAbstraction::$strEndDateVal));

	$arrTotalStats = array();
	
	$arrDates = GetDateArray(LegacyAbstraction::$strStartDateVal, LegacyAbstraction::$strEndDateVal);
	foreach ($arrDates as $arrThisDate) {
		$arrTotalStats[$arrThisDate[0]] = array();
		$arrTotalStats[$arrThisDate[0]]['KeywordClicks'] = 0;
		$arrTotalStats[$arrThisDate[0]]['KeywordConversions'] = 0;
		$arrTotalStats[$arrThisDate[0]]['NetRevenue'] = 0;
		$arrTotalStats[$arrThisDate[0]]['NetImpr'] = 0;
		$arrTotalStats[$arrThisDate[0]]['NetClicks'] = 0;
		$arrTotalStats[$arrThisDate[0]]['AvgCPC'] = 0;
		$arrTotalStats[$arrThisDate[0]]['AvgCPM'] = 0;
		$arrTotalStats[$arrThisDate[0]]['NetCost'] = 0;
		$arrTotalStats[$arrThisDate[0]]['AvgPos'] = 0;
		$arrTotalStats[$arrThisDate[0]]['NetCtr'] = 0;
		$arrTotalStats[$arrThisDate[0]]['AvgEPC'] = 0;
		$arrTotalStats[$arrThisDate[0]]['Profit'] = 0;
		
		$objClicks = new KeywordTrackerClicks();
		$objClicks->GetCampaignStats($userId, $arrThisDate[0], $arrThisDate[1]);
		
		if ($objClicks->RowCount == 0) {
			continue;
		}
		
		$arrStats = LoadAccountStats($arrThisDate[0], $arrThisDate[1]);
		
		while ($arrThisRow = $objClicks->GetRow()) {
			if (!$arrThisRow['Name']) {
				continue;
			}
			$arrThisStats = $arrStats[$arrThisRow['ID']];
			
			if (!is_array($arrThisStats)) {
				$arrThisStats['NetImpr'] = 0;
				$arrThisStats['NetClicks'] = 0;
				$arrThisStats['AvgCPC'] = 0;
				$arrThisStats['AvgCPM'] = 0;
				$arrThisStats['NetCost'] = 0;
				$arrThisStats['AvgPos'] = 0;
			}
			
			if ($arrThisStats['NetClicks'] != 0) {
				$arrThisStats['NetCtr'] = round($arrThisRow['KeywordClicks'] / $arrThisStats['NetClicks'], 2);
			}
			else {
				$arrThisStats['NetCtr'] = 0;
			}
			
			if ($arrThisStats['NetClicks'] > 0) {
				$arrThisStats['AvgCPC'] = $arrThisStats['NetCost'] / $arrThisStats['NetClicks'];
			}
			else {
				$arrThisStats['AvgCPC'] = 0;
			}
			
			// Determine EPC, Avoid Division by Zero Error
			if ($arrThisStats['NetClicks'] == 0) {
				$arrThisStats['AvgEPC'] = 0;
			}
			else {
				$arrThisStats['AvgEPC'] = round((float) $arrThisRow['KeywordRevenue'] / $arrThisStats['NetClicks'], 2);
			}
			
			if (!is_numeric($arrThisRow['KeywordClicks'])) {
				$arrThisRow['KeywordClicks'] = 0;
			}
			
			if (!is_numeric($arrThisStats['NetClicks'])) {
				$arrThisStats['NetClicks'] = 0;
			}
			
			if (!is_numeric($arrThisRow['KeywordConversions'])) {
				$arrThisRow['KeywordConversions'] = 0;
			}
			
			$arrThisStats['Profit'] = $arrThisRow['KeywordRevenue'] - $arrThisStats['NetCost'];
			
			// Join Arrays
			foreach (array_keys($arrThisStats) as $strThisKey) {
				$strThisVal = $arrThisStats[$strThisKey];
				$arrThisRow[$strThisKey] = $strThisVal;
			}
			
			$arrTotalStats[$arrThisDate[0]]['KeywordClicks'] += $arrThisRow['KeywordClicks'];
			$arrTotalStats[$arrThisDate[0]]['KeywordConversions'] += $arrThisRow['KeywordConversions'];
			$arrTotalStats[$arrThisDate[0]]['NetRevenue'] += $arrThisRow['NetRevenue'];
			$arrTotalStats[$arrThisDate[0]]['NetImpr'] += $arrThisRow['NetImpr'];
			$arrTotalStats[$arrThisDate[0]]['NetClicks'] += $arrThisRow['NetClicks'];
			$arrTotalStats[$arrThisDate[0]]['AvgCPC'] += $arrThisRow['AvgCPC'];
			$arrTotalStats[$arrThisDate[0]]['AvgCPM'] += $arrThisRow['AvgCPM'];
			$arrTotalStats[$arrThisDate[0]]['NetCost'] += $arrThisRow['NetCost'];
			$arrTotalStats[$arrThisDate[0]]['AvgPos'] += $arrThisRow['AvgPos'];
			$arrTotalStats[$arrThisDate[0]]['NetCtr'] += $arrThisRow['NetCtr'];
			$arrTotalStats[$arrThisDate[0]]['AvgEPC'] += $arrThisRow['AvgEPC'];
			$arrTotalStats[$arrThisDate[0]]['Profit'] += $arrThisRow['Profit'];
		}
	}
	
	foreach ($arrFieldsVal as $strThisField) {
		$strOut .= '<row><string>' . $strThisField . '</string>';
		foreach ($arrDates as $arrThisDate) {
			$strOut .= '<number>' . $arrTotalStats[$arrThisDate[0]][$strThisField] . '</number>';
		}
		$strOut .= '</row>';
	}
	
	return $strOut;
}

function LoadAccountStats($strInStartDate, $strInEndDate) {
	global $userId;
	
	$objStats = new PPCKeywordStats();
	$objStats->GetStatsByAccount($userId, 0, 0, $strInStartDate, $strInEndDate);
	
	if ($objStats->RowCount == 0) {
		return false;
	}
	
	$arrStats = array();
	
	while ($arrThisRow = $objStats->GetRow()) {
		$arrStats[$arrThisRow['ID']] = $arrThisRow;
	}
	
	return $arrStats;
}

$strCampaigns = ListCampaignStats();
if (strlen($strCampaigns) > 0) {
	$blnChartHasData = true;
}

?><chart>
	<license>GTA9I-PM7Q.O.945CWK-2XOI1X0-7L</license>
	<series_color> 
        <color>ACACAC</color> 
        <color>0000FF</color> 
        <color>000080</color> 
    </series_color> 
<?php if (!$blnChartHasData) { ?>
	<chart_rect hide='true' />
	<legend  layout='hide' />
<?php } else { ?>
	<chart_data>
<?php 
	// Print Date Header
	$arrDates = GetDateArray(LegacyAbstraction::$strStartDateVal, LegacyAbstraction::$strEndDateVal);
	echo '<row><null/>';
	foreach ($arrDates as $arrThisDate) {
		echo '<string>' . date('m-d', strtotime($arrThisDate[0])) . '</string>';
	}
	echo '</row>';
	
	echo $strCampaigns;
}
?>
	</chart_data>
</chart>
