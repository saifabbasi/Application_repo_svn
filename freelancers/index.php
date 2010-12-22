<?php
ob_start();

require('include.php');

require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsMarketProjects.php');

require('auth.php');

LoadProvider();

LegacyAbstraction::doAction('Update', 'UpdateDetails');

LoadProviderDetails();

function LoadProviderDetails() {
	global $intProviderID, $strName, $strEmail, $strEmail2, $strPayPal, $strDesc, $strPriceRange, $strImage, $strThumbImage, $strAccountDate;
	
	$objProvider = new MarketProviders();
	$objProvider->ID = $intProviderID;
	$objProvider->GetDetails();
	
	$strName = $objProvider->name;
	$strEmail = $objProvider->email;
	$strEmail2 = $objProvider->email2;
	$strPayPal = $objProvider->payPal;
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
}

function UpdateDetails() {
	global $intProviderID, $strName, $strEmail, $strEmail2, $strPayPal, $strDesc, $strPriceRange, $strImage, $strThumbImage, $strAccountDate;
	
	$strName = $_POST['Name'];
	$strEmail = $_POST['Email'];
	$strEmail2 = $_POST['Email2'];
	$strPayPal = $_POST['PayPal'];
	$strDesc = $_POST['Desc'];
	$strPriceRange = $_POST['PriceRange'];
	//$strThumbImage = $_POST['ThumbImage'];
	
	$objProvider = new MarketProviders();
	$objProvider->ID = $intProviderID;
	$objProvider->name = $strName;
	$objProvider->email = $strEmail;
	$objProvider->payPal = $strPayPal;
	$objProvider->description = $strDesc;
	$objProvider->priceRange = $strPriceRange;
	
	// Use Image Hash to Prevent Dupe Filenames
	$strHash = date('Ymd', time());
	
	// Handle Attachments
	if (!empty($_FILES['ThumbImage'])) {
		if (!$_FILES['ThumbImage']['error']) {			
			// Save Attachment
			move_uploaded_file($_FILES['ThumbImage']['tmp_name'], '../freelancers/images/' . $strHash . $_FILES['ThumbImage']['name']);
			
			$objProvider->thumbImage = $strHash . $_FILES['ThumbImage']['name'];
		}
	}
	
	
	if (!empty($_FILES['Image'])) {
		if (!$_FILES['Image']['error']) {			
			// Save Attachment

			move_uploaded_file($_FILES['Image']['tmp_name'], '../freelancers/images/' . $strHash . $_FILES['Image']['name']);
			
			$objProvider->image = $strHash . $_FILES['Image']['name'];
		}
	}
	
	$objProvider->Update();
	
	header('Location: index.php');
}

function ListProjects() {
	global $intProviderID;
	
	$objProjects = new MarketProjects();
	$objProjects->GetListByProviderID($intProviderID);
	
	if ($objProjects->RowCount == 0) {
		return false;
	}
	$blnAltRow = false;
	
	while ($arrThisRow = $objProjects->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td><a href="terms.php?ID=<?php echo $arrThisRow['ID']; ?>"><?php echo $arrThisRow['name']; ?></a></td>
	<td><?php echo $arrThisRow['Username']; ?></td>
	<td align="center">$<?php echo $arrThisRow['Deposit']; ?></td>
	<td align="center"><?php echo LegacyAbstraction::FriendlyDateDiff($arrThisRow['lastPost']); ?></td>
  </tr>
<?php
		$blnAltRow = !$blnAltRow;
	}
}

$strPageTitle = 'Main';
include('templates/index.php');

?>