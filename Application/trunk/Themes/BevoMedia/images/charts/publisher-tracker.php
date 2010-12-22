<?php
  require("../path.php");
require_once(PATH . "Legacy.Abstraction.class.php");
  require(PATH . 'classes/clsTrackerStats.php');

$userID = $_GET['userId'];

include(PATH . "session.php");
require(PATH . 'inc_daterange.php');
require('datewalker_inc.php');

if (LegacyAbstraction::$strStartDateVal == LegacyAbstraction::$strEndDateVal) {
	LegacyAbstraction::$strStartDateVal = date('Y-m-d', strtotime(LegacyAbstraction::$strEndDateVal . '-7days'));
}

function ListProfit() {
	global $userId;
	$arrDates = GetDateArray(LegacyAbstraction::$strStartDateVal, LegacyAbstraction::$strEndDateVal);
	
	$arrRows = array();
	
	foreach ($arrDates as $arrThisDate) {
		$objStats = new TrackerStats();
		$objStats->GetCost($userId, $arrThisDate[0], $arrThisDate[1]);
		if ($objStats->RowCount != 0) {
			$arrThisCost = $objStats->GetRow();
			$intThisCost = $arrThisCost['NetCost'];
		}
		unset($objStats);
		
		$objStats = new TrackerStats();
		$objStats->GetRevenue($userId, $arrThisDate[0], $arrThisDate[1]);
		if ($objStats->RowCount != 0) {
			$arrThisRev = $objStats->GetRow();
			$intThisRev = $arrThisRev['NetRevenue'];
		}
		unset($objStats);
		
		if (!$intThisCost) {
			$intThisCost = 0;
		}
		
		if (!$intThisRev) {
			$intThisRev = 0;
		}
		
		$intThisProfit = ($intThisRev - $intThisCost);
		
		if ($intThisCost > 0) {
			$intThisROI = $intThisProfit / $intThisCost;
		}
		else {
			$intThisROI = 0;
		}
		
		$arrRows[] = array('Date' => $arrThisDate[0],
							'Revenue' => $intThisRev,
							'Cost' => $intThisCost,
							'Profit' => $intThisProfit,
							'ROI' => $intThisROI);
	}
	
	$strOut = '';

	$strOut = '<row><string>Revenue</string>';
	foreach ($arrRows as $arrThisRow) {
		$strOut .= '<number>' . $arrThisRow['Revenue'] . '</number>';
	}
	$strOut .= '</row>';
	
	$strOut .= '<row><string>Cost</string>';
	foreach ($arrRows as $arrThisRow) {
		$strOut .= '<number>' . $arrThisRow['Cost'] . '</number>';
	}
	$strOut .= '</row>';
	
	$strOut .= '<row><string>Profit</string>';
	foreach ($arrRows as $arrThisRow) {
		$strOut .= '<number>' . $arrThisRow['Profit'] . '</number>';
	}
	$strOut .= '</row>';

	return $strOut;
}

$strStats = ListProfit();
if (strlen($strStats) > 0) {
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
	
	echo $strStats;
}
?>
	</chart_data>
</chart>
