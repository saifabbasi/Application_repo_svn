<?

require("include.php");
require(PATH.'classes/clsMarketProjects.php');
require(PATH.'classes/clsMarketProjectTerms.php');
require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsUserInfo.php');
require(PATH.'classes/clsUser.php');
require('auth.php');
        
$intID = $_GET['ID'];

if (!is_numeric($intID)) {
	$intID = 0;
}

LegacyAbstraction::doAction('Complete', 'MarkComplete');

LoadProject();

function LoadProject() {
	global $intID, $strName, $intUserID, $intProviderID, $strDate, $strLastPost, $intAcceptedID, $intOrderID, $intPaid, $strUserName, $strProviderName, $intDeposit, $strTerms, $strTermDate, $intUserComplete, $intProviderComplete;
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->GetDetails();
	
	$strName = $objProject->Name;
	$intUserID = $objProject->UserID;
	$intProviderID = $objProject->ProviderID;
	$strDate = $objProject->Date;
	$strLastPost = $objProject->LastPost;
	$intAcceptedID = $objProject->AcceptedID;
	$intOrderID = $objProject->OrderID;
	$intUserComplete = $objProject->UserComplete;
	$intProviderComplete = $objProject->ProviderComplete;
	$intPaid = $objProject->Paid;
	
	// Load Provider Name
	$objProvider = new MarketProviders();
	$objProvider->ID = $intProviderID;
	$objProvider->GetDetails();
	
	$strProviderName = $objProvider->Name;
	
	// Load User Name
	$objUser = new UserInfo();
	$objUser->ID = $intUserID;
	$objUser->GetDetails();
	
	$strUserName = $objUser->First_Name . ' ' . $objUser->Last_Name;

	// Load Accepted Terms
	if ($intAcceptedID == 0) {
		return false;
	}
	
	$objTerms = new MarketProjectTerms();
	$objTerms->ID = $intAcceptedID;
	$objTerms->GetDetails();
	
	$intDeposit = $objTerms->Deposit;
	$strTerms = $objTerms->Terms;
	$strTermDate = $objTerms->Date;
}

function MarkComplete() {
	global $intID;
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->UserComplete = 1;
	$objProject->ProviderComplete = 1;
	$objProject->LastPost = date('Y-m-d H:i:s', time());
	$objProject->Update();
	
	header('Location: publisher-market-terms.php?ID=' . $intID);
}

function ListTerms() {
	global $intID, $intAcceptedID;
	
	$objTerms = new MarketProjectTerms();
	$objTerms->GetListByProjectID($intID);
	
	if ($objTerms->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objTerms->GetRow()) {
		// Don't List Accepted Terms with Other Terms
		if ($arrThisRow['ID'] == $intAcceptedID) {
			continue;
		}
?>
<table border="1" style="border-collapse: collapse; margin-bottom: 10px;">
<?php if ($arrThisRow['UserID'] != 0) { ?>
  <tr>
    <td><strong>Affiliate:</strong></td>
	<td><?php echo $arrThisRow['UserName']; ?></td>
  </tr>
 <?php } else { ?>
  <tr>
    <td><strong>Freelancer:</strong></td>
	<td><?php echo $arrThisRow['ProviderName']; ?></td>
  </tr>
 <?php } ?>
  <tr>
    <td><strong>Date:</strong></td>
	<td><?php echo $arrThisRow['Date']; ?></td>
  </tr>
  <tr>
    <td><strong>Deposit $:</strong></td>
	<td><?php echo $arrThisRow['Deposit']; ?></td>
  </tr>
  <tr>
    <td><strong>Terms:</strong></td>
	<td><?php echo $arrThisRow['Terms']; ?></td>
  </tr>
</table>
<?php
	}
}
   
$strPageTitle = 'Project Terms';
include('templates/terms.tpl.php');

?>