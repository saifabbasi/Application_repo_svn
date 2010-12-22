<?

require("include.php");
require('../classes/clsMarketProjects.php');
require('../classes/clsMarketProjectTerms.php');
require('../classes/clsMarketProviders.php');
require('../classes/clsUserInfo.php');
require('../classes/clsUser.php');
require('auth.php');

LoadProvider();
        
$intID = $_GET['ID'];

if (!is_numeric($intID)) {
	$intID = 0;
}

LegacyAbstraction::doAction('Update', 'UpdateTerms');
LegacyAbstraction::doAction('Accept', 'AcceptTerms');

LoadProject();

function UpdateTerms() {
	global $intProviderID, $intID;
	
	$intDepositVal = $_POST['Deposit'];
	$strTermsVal = $_POST['Terms'];
	
	$strNow = date('Y-m-d H:i:s', time());
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->LastPost = $strNow;
	$objProject->Update();
	
	$objTerms = new MarketProjectTerms();
	$objTerms->ProjectID = $intID;
	$objTerms->UserID = 0;
	$objTerms->ProviderID = $intProviderID;
	$objTerms->Deposit = $intDepositVal;
	$objTerms->Terms = $strTermsVal;
	$objTerms->Date = $strNow;
	$objTerms->Insert();
	
	header('Location: terms.php?ID=' . $intID);
}

function LoadProject() {
	global $intID, $strName, $intUserID, $intProviderID, $strDate, $strLastPost, $intAcceptedID, $intOrderID, $strUserName, $strProviderName, $intDeposit, $strTerms, $strTermDate;
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->GetDetails();
	
	$strName = $objProject->Name;
	$intUserID = $objProject->UserID;
	$strDate = $objProject->Date;
	$strLastPost = $objProject->LastPost;
	$intAcceptedID = $objProject->AcceptedID;
	
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

function AcceptTerms() {
	global $intID, $intProviderID;
	
	$intTermsID = $_GET['TermID'];
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->AcceptedID = $intTermsID;
	$objProject->Update();
	
	// Email User
	$objProject->GetDetails();
	$strProjectName = $objProject->Name;
	$intUserID = $objProject->UserID;
	
	// Load Provider Name/Email
	$objProvider = new MarketProviders();
	$objProvider->ID = $intProviderID;
	$objProvider->GetDetails();
	
	$strProviderName = $objProvider->Name;
	$strProviderEmail = $objProvider->Email;
	
	// Load User Name/Email
	$objUser = new UserInfo();
	$objUser->ID = $intUserID;
	$objUser->GetDetails();
	$strUserName = $objUser->First_Name . ' ' . $objUser->Last_Name;
	
	unset($objUser);
	
	$objUser = new User();
	$objUser->ID = $intUserID;
	$objUser->GetDetails();
	$strUserEmail = $objUser->EMAIL;
	
	// Send Email
	$strMessage = "Hello " . $strUserName . ",\n" . $strProviderName . " has accepted the terms for the project " . $strProjectName . ". Please make the appropriate deposit and work will begin.\n\n
					https://www.bevomedia.com/publisher-market-terms.php?ID=" . $intID;
	$header = "From: Bevo Media Marketplace <market@bevomedia.com>\r\n"; //optional headerfields
	//mail($strUserEmail, 'Bevo Media Marketplace', $strMessage, $header);
	
	
	$MailComponentObject = new MailComponent();
	$MailComponentObject->setFrom('market@bevomedia.com');
	
	$MailComponentObject->setSubject('Bevo Media Marketplace');
	$MailComponentObject->setHTML($strMessage);
	$MailComponentObject->send(array($strUserEmail));	
	
	
	header('Location: terms.php?ID=' . $intID);
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
<?php if ($intAcceptedID == 0 && $arrThisRow['ProviderID'] == 0) { ?>
  <tr>
    <td colspan="2" align="center"><a href="terms.php?ID=<?php echo $intID; ?>&TermID=<?php echo $arrThisRow['ID']; ?>&Action=Accept">Accept Terms</a></td>
  </tr>
<?php } ?>
</table>
<?php
	}
}
   
$strPageTitle = 'Project Terms';
include('templates/terms.tpl.php');

?>