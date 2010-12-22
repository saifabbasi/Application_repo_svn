<?php

require ("../path.php");
require_once(PATH . "Legacy.Abstraction.class.php");

require(PATH . 'classes/clsNetworks.php');
require(PATH .'classes/clsNetworkSubIDs.php');
require(PATH . 'classes/clsNetworkStats.php');

$userID = $_GET['userId'];
include (PATH . "session.php");

$intNetworkID = $_GET['Network'];
if (!is_numeric($intNetworkID)) {
	exit;
}

require(PATH . 'inc_daterange.php');
require('datewalker_inc.php');

// Display Statistic
$arrFields = array('NetRevenue', 'ClickSum', 'ConvSum', 'ConvRatio', 'EPC');	// Available Statistics
$strField = $_GET['Field'];
if (empty($strField) || !in_array($strField, $arrFields)) {
	$strField = 'NetRevenue';
}

// If Single Date, Use Week Range
if (LegacyAbstraction::$strStartDateVal == LegacyAbstraction::$strEndDateVal) {
	LegacyAbstraction::$strStartDateVal = date('Y-m-d', strtotime(LegacyAbstraction::$strEndDateVal . '-7days'));
}

$arrOffers = $_GET['Offers'];

LoadNetworkOfferStats();
LoadNetworkStats();

function LoadNetworkOfferStats() {
	global $userId, $intNetworkID, $arrOfferStats;
	
	$arrDates = GetDateArray(LegacyAbstraction::$strStartDateVal, LegacyAbstraction::$strEndDateVal);
	$arrOfferStats = array();
	foreach ($arrDates as $arrThisDate) {
		$objNetworkSubIDs = new NetworkSubIDs();
		$objNetworkSubIDs->GetListByUserID($userId, $intNetworkID, $arrThisDate[0], $arrThisDate[1]);
	
		if ($objNetworkSubIDs->RowCount == 0) {
			continue;
		}
		
		while ($arrThisRow = $objNetworkSubIDs->GetRow()) {
			if (!$arrThisRow['OFFER_ID']) {
				continue;
			}
			
			if (empty($arrThisRow['OfferTitle'])) {
				$arrThisRow['OfferTitle'] = 'Offer #' . $arrThisRow['OFFER_ID'];
			}
			
			// Calculate Conversion Ratio and EPC
			if (!$arrThisRow['ClickSum']) {
				$arrThisRow['ConvRatio'] = 0;
				$arrThisRow['EPC'] = 0;
			}
			else {
				$arrThisRow['ConvRatio'] = round($arrThisRow['ConvSum'] / $arrThisRow['ClickSum'], 2);
				$arrThisRow['EPC'] = round($arrThisRow['NetRevenue'] / $arrThisRow['ClickSum'], 2);
			}
			
			// Index Offer Stats by OfferID, By Date
			$arrOfferStats[$arrThisRow['OFFER_ID']]['OfferTitle'] = $arrThisRow['OfferTitle'];
			$arrOfferStats[$arrThisRow['OFFER_ID']][$arrThisDate[0]] = $arrThisRow;
		}
		
		unset($objNetworkSubIDs);
	}
}

function LoadNetworkStats() {
	global $userId, $intNetworkID, $arrOfferStats, $arrStats;
	
	$arrDates = GetDateArray(LegacyAbstraction::$strStartDateVal, LegacyAbstraction::$strEndDateVal);
	$arrStats = array();
	
	foreach ($arrDates as $arrThisDate) {
		$objNetworkStats = new NetworkStats();
		$objNetworkStats->GetListByUserID($userId, $intNetworkID, $arrThisDate[0], $arrThisDate[1]);
	
		if ($objNetworkStats->RowCount == 0) {
			continue;
		}
		
		// Index Total Stats By Date
		$arrStats[$arrThisDate[0]] = $objNetworkStats->GetRow();
		
		unset($objNetworkStats);
		
		if (!empty($arrOfferStats)) {
			// Subtract Known Offer Stats from Total Stats
			$intTotalOfferRev = 0;
			$intTotalOfferClicks = 0;
			$intTotalOfferConv = 0;
			// Loop Offers
			foreach ($arrOfferStats as $arrThisOffer) {
				// This Offer Has No Stats for This Date
				if (!is_array($arrThisOffer[$arrThisDate[0]])) {
					continue;
				}
				// This Offer, This Date
				$arrThisOfferStats = $arrThisOffer[$arrThisDate[0]];
				
				$intTotalOfferRev += $arrThisOfferStats['NetRevenue'];
				$intTotalOfferClicks += $arrThisOfferStats['ClickSum'];
				$intTotalOfferConv += $arrThisOfferStats['ConvSum'];
			}
			
			$arrStats[$arrThisDate[0]]['NetRevenue'] += (0 - $intTotalOfferRev);
			$arrStats[$arrThisDate[0]]['ClickSum'] += (0 - $intTotalOfferRev);
			$arrStats[$arrThisDate[0]]['ConvSum'] += (0 - $intTotalOfferRev);
		}
	
		// Calculate Conversion Ratio and EPC (Post Subtracting Unknowns)
		if (!$arrStats[$arrThisDate[0]]['ClickSum']) {
			$arrStats[$arrThisDate[0]]['ConvRatio'] = 0;
			$arrStats[$arrThisDate[0]]['EPC'] = 0;
		}
		else {
			$arrStats[$arrThisDate[0]]['ConvRatio'] = (round($arrStats[$arrThisDate[0]]['ConvSum'] / $arrStats[$arrThisDate[0]]['ClickSum'], 2) * 100);
			$arrStats[$arrThisDate[0]]['EPC'] = round($arrStats[$arrThisDate[0]]['NetRevenue'] / $arrStats[$arrThisDate[0]]['ClickSum'], 2);
		}
	}
}

function ListOfferStats() {
	global $arrOfferStats, $arrStats, $strField, $arrOffers;
	
	$arrDates = GetDateArray(LegacyAbstraction::$strStartDateVal, LegacyAbstraction::$strEndDateVal);
	
	$strOut = '';
	
	// Display Unknown Stats
	$strOut .= '<row><string>Unknown Offer Totals</string>';
	foreach ($arrDates as $arrThisDate) {
		$arrThisStats = $arrStats[$arrThisDate[0]];
		$strOut .= '<number>' . round($arrThisStats[$strField]) . '</number>';
	}
	$strOut .= '</row>';
	
	$intControl = 0;
	
	// Display Known Stats by Offer
	foreach ($arrOfferStats as $intThisOfferID => $arrThisOffer) {
		// If Offer Filter Set, Ignore Non Selected Offers
		if (!empty($arrOffers)) {
			if (!in_array($intThisOfferID, $arrOffers)) {
				continue;
			}
		}
		$strOut .= '<row><string>' . htmlentities($arrThisOffer['OfferTitle']) . '</string>';

		foreach ($arrDates as $arrThisDate) {
			if (!is_array($arrThisOffer[$arrThisDate[0]])) {
				$strOut .= '<number>0</number>';
			}
			else {
				$arrThisOfferStats = $arrThisOffer[$arrThisDate[0]];
				$strOut .= '<number>' . round($arrThisOfferStats[$strField]) . '</number>';
			}
		}
		$strOut .= '</row>';
		
		$intControl++;
		
		// If Offer Filter Not Set, Only Show First 3 Offers
		if (empty($arrOffers)) {
			if ($intControl == 3) {
				break;
			}
		}
	}
	
	return $strOut;
}

$strOfferStats = ListOfferStats();

if (strlen($strOfferStats) > 0) {
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
	
	echo $strOfferStats;
}
?>
	</chart_data>
</chart>
