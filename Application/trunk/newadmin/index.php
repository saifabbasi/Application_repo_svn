<?php

require('include.php');
require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsMarketProviderServices.php');
require(PATH.'classes/clsMarketProjects.php');
require('auth.php');

LegacyAbstraction::doAction('Delete', 'DeleteFreelancer');

function ListProviders() {
	$objProviders = new MarketProviders();
	$objProviders->GetList();
	
	if ($objProviders->RowCount == 0) {
		return false;
	}
	$blnAltRow = false;
	
	while ($arrThisRow = $objProviders->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td><a href="freelancer.php?ID=<?php echo $arrThisRow['ID']; ?>"><?php echo $arrThisRow['name']; ?></a></td>
	<td align="center"><a href="index.php?ID=<?php echo $arrThisRow['ID']; ?>&Action=Delete">Delete</a></td>
  </tr>
<?php
		$blnAltRow = !$blnAltRow;
	}
}

function DeleteFreelancer() {
	$intID = $_GET['id'];
	
	$objProvider = new MarketProviders();
	$objProvider->ID = $intID;
	$objProvider->Delete();
	
	$objServices = new MarketProviderServices();
	$objServices->DeleteByProviderID($intID);
	
	header('Location: index.php');
}

$strPageTitle = 'Main';
include('templates/index.php');

?>
