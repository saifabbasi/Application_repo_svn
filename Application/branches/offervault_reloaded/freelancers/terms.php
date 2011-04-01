<?

require("include.php");
require(PATH.'classes/clsMarketProjects.php');
require(PATH.'classes/clsMarketProjectTerms.php');
require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsUserInfo.php');
require(PATH.'classes/clsUser.php');
require('auth.php');

LoadProvider();
        
$intID = $_GET['ID'];

if (!is_numeric($intID)) {
	$intID = 0;
}

LegacyAbstraction::doAction('Update', 'UpdateTerms');
LegacyAbstraction::doAction('Accept', 'AcceptTerms');
LegacyAbstraction::doAction('Complete', 'MarkComplete');

LoadProject();

function UpdateTerms() {
	global $intProviderID, $intID;
	$intDepositVal = $_POST['Deposit'];
	$strTermsVal = $_POST['Terms'];
	
	$strNow = date('Y-m-d H:i:s', time());
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->lastPost = $strNow;
	$objProject->Update();
	
	$objTerms = new MarketProjectTerms();
	$objTerms->projectId = $intID;
	$objTerms->userId = 0;
	$objTerms->providerId = $intProviderID;
	$objTerms->deposit = $intDepositVal;
	$objTerms->terms = $strTermsVal;
	$objTerms->date = $strNow;
	$objTerms->Insert();
	
	header('Location: terms.php?ID=' . $intID);
}

function LoadProject() {
	global $intID, $strName, $intUserID, $intProviderID, $strDate, $strLastPost, $intAcceptedID, $intOrderID, $strUserName, $strProviderName, $intDeposit, $strTerms, $strTermDate, $intUserComplete, $intProviderComplete;
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->GetDetails();
	
	$strName = $objProject->Name;
	$intUserID = $objProject->user__id;
	$strDate = $objProject->date;
	$strLastPost = $objProject->lastPost;
	$intAcceptedID = $objProject->acceptedId;
	$intOrderID = $objProject->orderId;
	$intUserComplete = $objProject->userComplete;
	$intProviderComplete = $objProject->providerComplete;
	
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
	
	$intDeposit = $objTerms->deposit;
	$strTerms = $objTerms->terms;
	$strTermDate = $objTerms->date;
}

function AcceptTerms() {
	global $intID, $intProviderID;
	
	$intTermsID = $_GET['TermID'];
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->acceptedId = $intTermsID;
	$objProject->lastPost = date('Y-m-d H:i:s', time());
	$objProject->Update();

	// Email User
	$objProject->GetDetails();
	$strProjectName = $objProject->Name;
	$intUserID = $objProject->user__id;
	
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

function MarkComplete() {
	global $intID;
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->ProviderComplete = 1;
	$objProject->LastPost = date('Y-m-d H:i:s', time());
	$objProject->Update();
	unset($objProject);
	
	// Email User
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->GetDetails();
	$strProjectName = $objProject->Name;
	$intUserID = $objProject->UserID;
	
	// Load Provider Name/Email
	$objProvider = new MarketProviders();
	$objProvider->ID = $intID;
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
	$strMessage = "Hello " . $strUserName . ",\n" . $strProviderName . " has marked the project " . $strProjectName . " as completed. Please visit the project page and confirm completion.\n\n
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
	global $intID, $intAcceptedID, $intLastDeposit;
	
	$objTerms = new MarketProjectTerms();
	$objTerms->GetListByProjectID($intID);
	
	if ($objTerms->RowCount == 0) {
		return false;
	}
	
	$arrRows = $objTerms->GetRows();
	
	$intCount = count($arrRows);
	for ($intX = 0; $intX < $intCount; $intX++) {
		$arrThisRow = $arrRows[$intX];
		
		// Don't List Accepted Terms with Other Terms
		if ($arrThisRow['id'] == $intAcceptedID) {
			continue;
		}
		
		$intLastDeposit = $arrThisRow['deposit'];
?>
<table border="1" style="border-collapse: collapse; margin-bottom: 10px;">
<?php if ($arrThisRow['userId'] != 0) { ?>
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
	<td><?php echo $arrThisRow['date']; ?></td>
  </tr>
  <tr>
    <td><strong>Project Price $:</strong></td>
	<td><?php echo $arrThisRow['deposit']; ?></td>
  </tr>
  <tr>
    <td><strong>Project Agreement:</strong></td>
	<td><?php echo $arrThisRow['terms']; ?></td>
  </tr>
<?php if ($intAcceptedID == 0 && $arrThisRow['providerId'] == 0 && ($intX+1) == $intCount) { ?>
  <tr>
    <td colspan="2" align="center"><a href="terms.php?ID=<?php echo $intID; ?>&TermID=<?php echo $arrThisRow['id']; ?>&Action=Accept">Accept Terms</a></td>
  </tr>
<?php } ?>
</table>
<?php
	}
}
   
$strPageTitle = 'Project Terms';
include('templates/terms.tpl.php');

?>