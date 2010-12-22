<?php

require('include.php');

$strRefVal = '';
LegacyAbstraction::doAction('Login', 'TryLogin');

function TryLogin() {
	global $strEmailVal, $strPasswordVal, $strRefVal, $strErr;
	
	$strEmailVal = $_POST['Email'];
	$strPasswordVal = $_POST['Password'];
	$strRefVal = $_POST['Ref'];
	
	if (strlen($strEmailVal) < 1 || strlen($strPasswordVal) < 1) {
		$strErr = 'Please enter a valid email address and password.';
		return false;
	}
	/*
	$objLogin = new MarketLogins();
	$objLogin->TryLogin($strEmailVal);
	
	if ($objLogin->RowCount == 0) {
		$strErr = 'Invalid login. Please try again';
		return false;
	}
	
	$arrRow = $objLogin->GetRow();
	
	// Use Case Sensitive String Comparison
	if (strcmp($strPasswordVal, $arrRow['Password']) != 0) {
		$strErr = 'Invalid login. Please try again.';
		return false;
	}

	$_SESSION['LoginID'] = $arrRow['ID'];
	
	if (strlen($strRefVal) < 1) {
		$strRefVal = 'index.php';
	}
	
	*/
	
	if ($strEmailVal == 'admin@bevomedia.com' && $strPasswordVal == 'yoyoyo_1025') {
		$_SESSION['Admin'] = 1;
	}
	else {
		$strErr = 'Invalid login.';
		return false;
	}
	
	header('Location: index.php');
}
$strPageTitle = 'Login';
include('templates/login.php');

?>