<?php

require('include.php');
require(PATH.'classes/clsMarketLogins.php');
require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsMarketProviderServices.php');
require(PATH.'classes/clsMarketServices.php');

require('auth.php');

LegacyAbstraction::doAction('Create', 'CreateProvider');

function CreateProvider() {
	global $intProviderID, $strName, $strEmail, $strEmail2, $strPayPal, $strPassword, $strDesc, $strPriceRange, $strImage, $strThumbImage, $strAccountDate, $arrServicesVal;
	
	$strName = $_POST['Name'];
	$strEmail = $_POST['Email'];
	$strEmail2 = $_POST['Email2'];
	$strPayPal = $_POST['PayPal'];
	$strPassword = $_POST['Password'];
	$strDesc = $_POST['Desc'];
	$strPriceRange = $_POST['PriceRange'];
	
	$arrServicesVal = $_POST['Services'];
	
	$objProvider = new MarketProviders();
	$objProvider->Name = $strName;
	$objProvider->Email = $strEmail;
	$objProvider->Email2 = $strEmail2;
	$objProvider->PayPal = $strPayPal;
	$objProvider->Description = $strDesc;
	$objProvider->PriceRange = $strPriceRange;
	$objProvider->AccountDate = date('Y-m-d');
	
	$intProviderID = $objProvider->Insert();
	
	// Provider Login
	$objLogin = new MarketLogins();
	$objLogin->ProviderID = $intProviderID;
	$objLogin->Email = $strEmail;
	$objLogin->Password = $strPassword;
	$objLogin->LastLoginDate = date('0000-00-00');
	$intLoginID = $objLogin->Insert();
	
	// Provider Services
	$objServices = new MarketProviderServices();
	$objServices->DeleteByProviderID($intProviderID);
	
	$objServices->ProviderID = $intProviderID;
	
	if (is_array($arrServicesVal)) {
		if (!empty($arrServicesVal)) {
			foreach ($arrServicesVal as $intThisServiceID) {
				if (is_numeric($intThisServiceID)) {
					$objServices->ServiceID = $intThisServiceID;
					$objServices->Insert();
				}
			}
		}
	}
	
	header('Location: index.php');
}

function ListServiceOptions() {
	$objServices = new MarketServices();
	$objServices->GetList();
	
	if ($objServices->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objServices->GetRow()) {
		echo '<option value="' . $arrThisRow['id'] . '">' . $arrThisRow['name'] . '</option>';
	}
}

$strPageTitle = 'New Freelancer';
include('templates/newfreelancer.php');

?>