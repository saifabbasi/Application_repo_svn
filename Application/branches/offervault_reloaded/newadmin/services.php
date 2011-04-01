<?php

require('include.php');
require(PATH.'classes/clsMarketServices.php');
require('auth.php');

LegacyAbstraction::doAction('Update', 'UpdateServices');

function ListServices() {
	$objServices = new MarketServices();
	$objServices->GetList();
	
	if ($objServices->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objServices->GetRow()) {
?>
  <tr>
    <td><input type="hidden" name="ID[]" value="<?php echo $arrThisRow['id']; ?>"/><label for="Desc<?php echo $arrThisRow['id']; ?>"><?php echo $arrThisRow['name']; ?></label></td>
	<td><textarea name="Desc[]" id="Desc<?php echo $arrThisRow['id']; ?>" cols="40" rows="3"><?php echo $arrThisRow['description']; ?></textarea></td>
  </tr>
<?php
	}
}

function UpdateServices() {
	$arrIDs = $_POST['ID'];
	$arrDescs = $_POST['Desc'];
	
	$intCount = count($arrIDs);
	
	$objServices = new MarketServices();
	for ($intX = 0; $intX < $intCount; $intX++) {
		$intThisID = $arrIDs[$intX];
		$strThisDesc = $arrDescs[$intX];
		
		if (!is_numeric($intThisID)) {
			continue;
		}
		
		$objServices->ID = $intThisID;
		$objServices->Description = $strThisDesc;
		$objServices->Update();
	}
	
	header('Location: services.php');
}

$strPageTitle = 'Main';
include('templates/services.php');

?>
