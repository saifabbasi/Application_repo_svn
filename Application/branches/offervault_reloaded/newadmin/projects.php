<?php

require('include.php');
require(PATH.'classes/clsMarketProjects.php');
require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsMarketProviderServices.php');
require(PATH.'classes/clsMarketServices.php');
require('auth.php');


function ListProjects() {
	global $intID;
	
	$objProjects = new MarketProjects();
	$objProjects->GetList();
	
	if ($objProjects->RowCount == 0) {
		return false;
	}
	$blnAltRow = false;
	
	while ($arrThisRow = $objProjects->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td><a href="terms.php?ID=<?php echo $arrThisRow['ID']; ?>"><?php echo $arrThisRow['name']; ?></a></td>
	<td><a href="freelancer.php?ID=<?php echo $arrThisRow['providerId']; ?>"><?php echo $arrThisRow['Providername']; ?></a></td>
	<td><?php echo $arrThisRow['Username']; ?></td>
	<td align="center">$<?php echo $arrThisRow['Deposit']; ?></td>
	<td align="center"><?php echo LegacyAbstraction::FriendlyDateDiff($arrThisRow['lastPost']); ?></td>
  </tr>
<?php
	$blnAltRow = !$blnAltRow;
	}
}

$strPageTitle = 'Projects';
include('templates/projects.php');

?>
