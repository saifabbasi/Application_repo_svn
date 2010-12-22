<?php
require('include.php');

LegacyAbstraction::doAction('Login', 'TryLogin');

function TryLogin() {
	global $strEmailVal, $strPasswordVal, $strRefVal, $strErr;
	
	if (isset($_POST['Email']))
		$strEmailVal = $_POST['Email']; else
		$strEmailVal = '';
		
	if (isset($_POST['Password']))
		$strPasswordVal = $_POST['Password']; else
		$strPasswordVal = ''; 
		
	if (isset($_POST['Ref']))
		$strRefVal = $_POST['Ref']; else
		$strRefVal = '';
	
	if (strlen($strEmailVal) < 1 || strlen($strPasswordVal) < 1) {
		$strErr = 'Please enter a valid email address and password.';
		return false;
	}
	
	$objLogin = new MarketLogins();
	$objLogin->TryLogin($strEmailVal);
	
	if ($objLogin->RowCount == 0) {
		$strErr = 'Invalid login. Please try again';
		return false;
	}
	$arrRow = $objLogin->GetRow();
	
	// Use Case Sensitive String Comparison
	if (strcmp($strPasswordVal, $arrRow['password']) != 0) {
		$strErr = 'Invalid login. Please try again.';
		return false;
	}
	

	$_SESSION['LoginID'] = $arrRow['id'];
	
	if (strlen($strRefVal) < 1) {
		$strRefVal = 'index.php';
	}
	
	if(isset($strRefVal))
	{
		header('Location: ' . $strRefVal);
	}else{
		header('Location: index.php');
	}
}
$strPageTitle = 'Login';
include('templates/login.php');

?>