<?php

require('include.php');
require(PATH.'classes/clsNetworks.php');
require(PATH.'classes/clsNetworkOfMonth.php');
require('auth.php');

LegacyAbstraction::doAction('Create', 'CreateNetwork');

function ListNetworkOfMonths() {
	$objNetworks = new NetworkOfMonth();
	$objNetworks->GetList();
	$blnAltRow = false;
	if ($objNetworks->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objNetworks->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
  <?php /*?>
    <td><a href="network.php?ID=<?php echo $arrThisRow['ID']; ?>"><?php echo $arrThisRow['NetworkTitle']; ?></a></td>
  <?php */?>
    <td><?php echo $arrThisRow['NetworkTitle']; ?></td>
	<td align="center"><a href="<?php echo $arrThisRow['url']; ?>" target="network">Website</a></td>
	<td align="center"><?php echo date('M Y', strtotime($arrThisRow['date'])); ?></td>
  </tr>
<?php
	$blnAltRow = !$blnAltRow;
	}
}

function CreateNetwork() {
	$intNetworkIDVal = $_POST['Network'];
	$strContentVal = $_POST['Content'];
	
	if (!is_numeric($intNetworkIDVal)) {
		return false;
	}
	
	$strDate = date('Y-m-d H:i:sa', time());
	
	$objNetwork = new NetworkOfMonth();
	$objNetwork->NetworkID = $intNetworkIDVal;
	$objNetwork->Content = $strContentVal;
	$objNetwork->Date = $strDate;
	$objNetwork->Insert();
	
	header('Location: networkofmonth.php');
}

function ListNetworkSelect() {
	$objNetworks = new Networks();
	$objNetworks->GetList();
	
	if ($objNetworks->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objNetworks->GetRow()) {
?><option value="<?php echo $arrThisRow['ID']; ?>"><?php echo $arrThisRow['title']; ?></option>
<?php
	}
}

$strPageTitle = 'Network of Month';
include('templates/networkofmonth.php');

?>
