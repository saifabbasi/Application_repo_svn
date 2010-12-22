<?php

function GetDateArray($inStartDate, $inEndDate) {
	$strStartDate = strtotime($inStartDate);
	$strEndDate = strtotime($inEndDate);
	
	$intDateDiff = ($strEndDate - $strStartDate)/86400;
	
	$arrDates = array();
	
	// If Date Diff > 3mnths, Group by Month
	// Start with First Day of Month
	if ($intDateDiff > 90) {
		$strStartDate = strtotime(getFirstOfMonth($strStartDate));
		$strLastDate = date('Y-m-d', strtotime(date('Y-m-d', $strStartDate) . '+1month'));
//		$strLastDate = getFirstOfMonth($strLastDate);
		
		$arrDates[] = array(date('Y-m-d', $strStartDate), date('Y-m-d', strtotime($strLastDate . '-1day')));
		
		while (strtotime($strLastDate) < $strEndDate) {
			$strNextLastDate = date('Y-m-d', strtotime($strLastDate . '+1month'));
//			$strNextLastDate = getFirstOfMonth($strNextLastDate);
			$arrDates[] = array($strLastDate, date('Y-m-d', strtotime($strNextLastDate . '-1day')));
			$strLastDate = $strNextLastDate;
		}
		
		return $arrDates;
	}
	
	// If Date Diff > 3wks, Group by Week
	// Start with Monday of First Week
	if ($intDateDiff > 21) {
		$strStartDate = strtotime(getMondayDate($strStartDate));
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

function getMondayDate($inDate) {
	$intDayNum = date('N', $inDate);
	if ($intDayNum > 1) {
		return date('Y-m-d', strtotime(date('Y-m-d', $inDate) . '-' . ($intDayNum - 1) . 'days'));
	}
}

function getFirstOfMonth($inDate) {
	$intMonth = date('m', $inDate);
	$intYear = date('Y', $inDate);
	return date('Y-m-d', mktime(0, 0, 0, $intMonth, 1, $intYear));
}

?>