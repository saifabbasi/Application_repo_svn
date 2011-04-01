<?php

require('include.php');
require(PATH.'classes/clsMarketPayments.php');
require(PATH.'classes/clsMarketProjects.php');
require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsMarketProviderServices.php');
require(PATH.'classes/clsMarketServices.php');
require('auth.php');

LegacyAbstraction::doAction('MarkPaid', 'MarkPaid');

LoadPayments();

function LoadPayments() {
	global $intID, $arrPayments;
	
	$objProjects = new MarketProjects();
	$objProjects->GetUnpaidListGroupByProvider();
	
	$arrPayments = array();
	
	if ($objProjects->RowCount == 0) {
		return false;
	}
	
	$arrPayments = $objProjects->GetRows();
}

function ListPayments2() {
	$objProjects = new MarketProjects();
	$objProjects->GetPendingListGroupByProvider();
	
	$intTotal = 0;
	$intBevoTotal = 0;
	$intGross = 0;
	$blnAltRow = false;
	
	while ($arrThisRow = $objProjects->GetRow()) {
		$intTotal += $arrThisRow['netPayment'];
		$intBevoTotal += ($arrThisRow['netPayment'] * .15);
		$intGross += ($arrThisRow['netPayment'] - ($arrThisRow['netPayment'] * .15));
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
	<td><a href="freelancer.php?ID=<?php echo $arrThisRow['providerId']; ?>"><?php echo $arrThisRow['Providername']; ?></a></td>
	<td><?php echo $arrThisRow['PayPal']; ?></td>
	<td align="center">$<?php echo $arrThisRow['netPayment']; ?></td>
	<td align="center">$<?php echo ($arrThisRow['netPayment'] * .15); ?></td>
	<td align="center">$<?php echo $arrThisRow['netPayment'] - ($arrThisRow['netPayment'] * .15); ?></td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td colspan="2"></td>
	<td align="center">$<?php echo $intTotal; ?></td>
	<td align="center">$<?php echo $intBevoTotal; ?></td>
	<td align="center">$<?php echo $intGross; ?></td>
  </tr>
<?php
}

function ListPayments() {
	global $arrPayments;
	
	$intTotal = 0;
	$intBevoTotal = 0;
	$intGross = 0;
	$blnAltRow = false;
	
	for ($intX = 0; $intX < count($arrPayments); $intX++) {
		$arrThisRow = $arrPayments[$intX];
		$intTotal += $arrThisRow['netPayment'];
		$intBevoTotal += ($arrThisRow['netPayment'] * .15);
		$intGross += ($arrThisRow['netPayment'] - ($arrThisRow['netPayment'] * .15));
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
	<td><a href="freelancer.php?ID=<?php echo $arrThisRow['providerId']; ?>"><?php echo $arrThisRow['Providername']; ?></a></td>
	<td><?php echo $arrThisRow['PayPal']; ?></td>
	<td align="center">$<?php echo $arrThisRow['netPayment']; ?></td>
	<td align="center">$<?php echo ($arrThisRow['netPayment'] * .15); ?></td>
	<td align="center">$<?php echo $arrThisRow['netPayment'] - ($arrThisRow['netPayment'] * .15); ?></td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td colspan="2"></td>
	<td align="center">$<?php echo $intTotal; ?></td>
	<td align="center">$<?php echo $intBevoTotal; ?></td>
	<td align="center">$<?php echo $intGross; ?></td>
  </tr>
<?php
}

function ListPaid() {
	$objPayments = new MarketPayments();
	$objPayments->GetList();
	
	if ($objPayments->RowCount == 0) {
		return false;
	}
	$blnAltRow = false;
	
	while ($arrThisRow = $objPayments->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td><a href="freelancer.php?ID=<?php echo $arrThisRow['providerId']; ?>"><?php echo $arrThisRow['Providername']; ?></a></td>
    <td align="center"><?php echo $arrThisRow['amount']; ?></td>
    <td align="center"><?php echo $arrThisRow['date']; ?></td>
  </tr>
<?php
		$blnAltRow = !$blnAltRow;
	}
}

function OutputMassPay() {
	global $arrPayments;
	
	for ($intX = 0; $intX < count($arrPayments); $intX++) {
		$arrThisRow = $arrPayments[$intX];
		echo $arrThisRow['PayPal'] . "\t" . round(($arrThisRow['netPayment'] - ($arrThisRow['netPayment'] * .15)), 2) . "\tUSD\tPayment " . date('Y-m-d', time()) . "\n";
	}
}

function MarkPaid() {
	global $arrPayments;
	
	$arrProvidersVal = $_POST['ID'];
	$arrPaymentsVal = $_POST['Payment'];
	
	$arrProviderPayments = array();
	
	$intCount = count($arrProvidersVal);
	
	for ($intX = 0; $intX < $intCount; $intX++) {
		$intThisProvider = $arrProvidersVal[$intX];
		$intThisPayment = $arrPaymentsVal[$intX];
		
		$arrProviderPayments[$intThisProvider] = $intThisPayment;
	}
	
	LoadPayments();
	
	$objProjects = new MarketProjects();
	
	foreach ($arrPayments as $arrThisPayment) {
		// Make Sure Posted Payments Match Current DB Payments
		if ($arrProviderPayments[$arrThisPayment['providerId']] == ($arrThisPayment['netPayment'] - ($arrThisPayment['netPayment'] * .15))) {
			// Log Payment
			$objPayments = new MarketPayments();
			$objPayments->date = date('Y-m-d', time());
			$objPayments->providerId = $arrThisPayment['providerId'];
			$objPayments->amount = $arrProviderPayments[$arrThisPayment['providerId']];
			$objPayments->Insert();
			unset($objPayments);
			
			// Mark Paid
			$objProjects->MarkPaid($arrThisPayment['providerId']);			
		}
	}
	header('Location: payments.php');
}

$strPageTitle = 'Payments';
include('templates/payments.php');

?>
