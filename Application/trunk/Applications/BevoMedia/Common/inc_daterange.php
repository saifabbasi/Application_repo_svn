<?php


LegacyAbstraction::$strDateRangeVal = false;
if(isset($_GET['DateRange']))
	LegacyAbstraction::$strDateRangeVal = $_GET['DateRange'];

LegacyAbstraction::ParseDateRange();

function ParseDateRange() {
	
	
	// No Date Picked, Use Todays Date
	if (empty(LegacyAbstraction::$strDateRangeVal)) {
		LegacyAbstraction::$strStartDateVal = date('n/d/Y', time() - (60*60*24*1));
		LegacyAbstraction::$strEndDateVal = date('n/d/Y', time());
		LegacyAbstraction::$strDateRangeVal = LegacyAbstraction::$strStartDateVal.' - '.LegacyAbstraction::$strEndDateVal;
		return LegacyAbstraction::$strDateRangeVal;
	}
	
	// Single Date (Doesn't Contain a - Character)
	if (strpos(LegacyAbstraction::$strDateRangeVal, '-') === false) {
		LegacyAbstraction::$strStartDateVal = LegacyAbstraction::$strDateRangeVal;
		LegacyAbstraction::$strEndDateVal = LegacyAbstraction::$strDateRangeVal;
		return LegacyAbstraction::$strDateRangeVal;
	}
	else {
		// Split Date Range, Assign to StartDate and EndDate
		$arrDate = explode('-', LegacyAbstraction::$strDateRangeVal);
		LegacyAbstraction::$strStartDateVal = trim($arrDate[0]);
		LegacyAbstraction::$strEndDateVal = trim($arrDate[1]);
		return LegacyAbstraction::$strDateRangeVal;
	}
}

?>

