<?php

require('include.php');
require(PATH.'classes/clsMarketPayments.php');
require(PATH.'classes/clsMarketProjects.php');
require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsMarketProviderServices.php');
require(PATH.'classes/clsMarketServices.php');
require(PATH.'classes/clsMarketLogins.php');
require('auth.php');

$intID = $_GET['ID'];

LegacyAbstraction::doAction('Update', 'UpdateProvider');

LoadProvider();
LoadPayments();

function LoadProvider() {
	global $intID, $strName, $strEmail, $strEmail2, $strDesc, $strPriceRange, $strImage, $strThumbImage, $strAccountDate, $arrServicesVal, $strPassword;
	
	$objProvider = new MarketProviders();
	$objProvider->ID = $intID;
	$objProvider->GetDetails();
	
	$objMartketLogin = new MarketLogins();
	$objMartketLogin->GetListByProviderID($intID);
	$strPassword = $objMartketLogin->GetRow();
	$strPassword = $strPassword['password'];
	
	$strName = $objProvider->name;
	$strEmail = $objProvider->email;
	$strEmail2 = $objProvider->email2;
	$strDesc = $objProvider->description;
	$strPriceRange = $objProvider->priceRange;
	$strImage = $objProvider->image;
	$strThumbImage = $objProvider->thumbImage;
	
	
	if (empty($strThumbImage)) {
		$strThumbImage = 'default-40x40.gif';
	}
	
	if (empty($strImage)) {
		$strThumbImage = 'default-130x130.gif';
	}
	
	$arrServicesVal = array();
	
	$objProviderServices = new MarketProviderServices();
	$objProviderServices->GetListByProviderID($intID);
	
	if ($objProviderServices->RowCount > 0) {
		while ($arrThisRow = $objProviderServices->GetRow()) {
			$arrServicesVal[] = $arrThisRow['serviceId'];
		}
	}
}

function LoadPayments() {
	global $intThisMonth, $intThisYear, $intThisTotal;
	
	$intThisMonth = GetPeriodPayments(getFirstOfMonth(time()), date('Y-m-d', time()));
	
	$intThisYear = GetPeriodPayments(getFirstOfYear(time()), date('Y-m-d', time()));
	
	$intThisTotal = GetPeriodPayments();
}

function GetPeriodPayments($inStart = '', $inEnd = '') {
	global $intID;
	$objPayments = new MarketPayments();
	$objPayments->GetListByProviderID($intID, $inStart, $inEnd);
	
	if ($objPayments->RowCount == 0) {
		return 0;
	}
	
	$intTotal = 0;
	
	while ($arrThisRow = $objPayments->GetRow()) {
		$intTotal += $arrThisRow['Amount'];
	}
	
	return $intTotal;
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

function getFirstOfYear($inDate) {
	$intYear = date('Y', $inDate);
	return date('Y-m-d', mktime(0, 0, 0, 1, 1, $intYear));
}

function UpdateProvider() {
	global $intID, $strName, $strEmail, $strEmail2, $strDesc, $strPriceRange, $strImage, $strThumbImage, $strAccountDate, $arrServicesVal;
	
	$strName = $_POST['name'];
	$strEmail = $_POST['Email'];
	$strEmail2 = $_POST['Email2'];
	$strDesc = $_POST['Desc'];
	$strPriceRange = $_POST['PriceRange'];
	
	$arrServicesVal = $_POST['Services'];
	
	$objProvider = new MarketProviders();
	$objProvider->ID = $intID;
	$objProvider->name = $strName;
	$objProvider->email = $strEmail;
	$objProvider->email2 = $strEmail2;
	$objProvider->description = $strDesc;
	$objProvider->priceRange = $strPriceRange;
	
	$objProvider->Update();
	
	
	$objMartketLogin = new MarketLogins();
	$objMartketLogin->UpdatePassword($intID, $_POST['Password']);
	
	
	
	// Provider Services
	$objServices = new MarketProviderServices();
	$objServices->DeleteByProviderID($intID);
	
	$objServices->ProviderID = $intID;
	
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
	
	header('Location: freelancer.php?ID=' . $intID);
}

function ListServiceOptions() {
	global $arrServicesVal;
	
	$objServices = new MarketServices();
	$objServices->GetList();
	
	if ($objServices->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objServices->GetRow()) {
		if (in_array($arrThisRow['id'], $arrServicesVal)) {
			$strSelected = 'selected="selected"';
		}
		else {
			$strSelected = '';
		}
		echo '<option value="' . $arrThisRow['id'] . '"' . $strSelected . '>' . $arrThisRow['name'] . '</option>';
	}
}

function ListProjects() {
	global $intID;
	
	$objProjects = new MarketProjects();
	$objProjects->GetListByProviderID($intID);
	
	if ($objProjects->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objProjects->GetRow()) {
?>
  <tr <?php if (  (isset($blnAltRow)) && ($blnAltRow)  ) { echo 'class="AltRow"'; } ?>>
    <td><a href="terms.php?ID=<?php echo $arrThisRow['ID']; ?>"><?php echo $arrThisRow['name']; ?></a></td>
	<td><?php echo $arrThisRow['Username']; ?></td>
	<td align="center">$<?php echo $arrThisRow['Deposit']; ?></td>
	<td align="center"><?php echo LegacyAbstraction::FriendlyDateDiff($arrThisRow['lastPost']); ?></td>
  </tr>
<?php
	}
}

$strPageTitle = 'Freelancer';
include('templates/freelancer.php');

?>
