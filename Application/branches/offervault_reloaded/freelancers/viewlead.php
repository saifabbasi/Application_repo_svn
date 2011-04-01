<?php

require('include.php');
require('../classes/clsMarketLeads.php');
require('../classes/clsMarketLeadPosts.php');
require('../classes/clsMarketLeadAttach.php');
require('../classes/clsMarketProducts.php');
require('../classes/clsUserInfo.php');
require('../classes/clsUser.php');
require('auth.php');

$intLeadID = $_GET['ID'];

if (!is_numeric($intLeadID)) {
	$intLeadID = 0;
}

LegacyAbstraction::doAction('Post', 'PostComment');

LoadLead();
LoadAttachments();
LoadUser();
LoadProduct();
LoadProvider();

function LoadLead() {
	global $intLeadID, $intUserID, $intProductID, $strCreateDate;
	
	$objLead = new MarketLeads();
	$objLead->ID = $intLeadID;
	$objLead->GetDetails();

	$intUserID = $objLead->UserID;
	$intProductID = $objLead->ProductID;
	$strCreateDate = $objLead->CreateDate;
}

function LoadUser() {
	global $intUserID, $strUserName;
	
	$objUser = new UserInfo();
	$objUser->ID = $intUserID;
	$objUser->GetDetails();
	
	$strUserName = $objUser->First_Name . ' ' . $objUser->Last_Name;
}

function LoadProduct() {
	global $intProductID, $strProductName;
	
	$objProduct = new MarketProducts();
	$objProduct->ID = $intProductID;
	$objProduct->GetDetails();
	
	$strProductName = $objProduct->Name;
}

function ListLeadPosts() {
	global $intLeadID, $arrAttach;
	
	$objPosts = new MarketLeadPosts();
	$objPosts->GetListByLeadID($intLeadID);
	
	if ($objPosts->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objPosts->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td>
<?php if ($arrThisRow['UserID']) { ?><strong>Affiliate:</strong> <?php echo $arrThisRow['UserName']; ?> <?php echo LegacyAbstraction::FriendlyDateDiff($arrThisRow['PostDate']); ?><?php } ?><br/>
<?php if ($arrThisRow['ProviderID']) { ?><strong>Provider:</strong> <?php echo $arrThisRow['ProviderName']; ?> <?php echo LegacyAbstraction::FriendlyDateDiff($arrThisRow['PostDate']); ?><?php } ?><br/>
<?php echo $arrThisRow['Post']; ?>
<?php if (!empty($arrAttach[$arrThisRow['ID']])) { $arrThisAttach = $arrAttach[$arrThisRow['ID']]; ?><br/><img src="../../images/icons/attach.png" alt="Attachment"/>&nbsp;<a href="attach.php?ID=<?php echo $arrThisAttach['ID']; ?>"><?php echo $arrThisAttach['Filename']; ?></a><?php } ?>
    </td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
}

function LoadAttachments() {
	global $intLeadID, $arrAttach;
	
	$arrAttach = array();
	
	$objAttach = new MarketLeadAttachments();
	$objAttach->GetListByLeadID($intLeadID);
	
	if ($objAttach->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objAttach->GetRow()) {
		$arrAttach[$arrThisRow['PostID']] = array('ID' => $arrThisRow['ID'], 'Filename' => $arrThisRow['Filename']);
	}
}

function PostComment() {
	global $intLeadID, $intProviderID;
	
	$strCommentVal = $_POST['Post'];
	$intProviderID = $_POST['ProviderID'];
	
	if (strlen($strCommentVal) < 1) {
		return false;
	}
	
	$objPost = new MarketLeadPosts();
	$objPost->LeadID = $intLeadID;
	$objPost->ProviderID = $intProviderID;
	$objPost->Post = $strCommentVal;
	$objPost->PostDate = date('Y-m-d H:i:s', time());
	$intPostID = $objPost->Insert();
	
	// Update Lead Last Update Date
	$objLead = new MarketLeads();
	$objLead->ID = $intLeadID;
	$objLead->ProviderID = $intProviderID;
	$objLead->LastUpdate = date('Y-m-d H:i:s', time());
	$objLead->Update();
	
	// Handle Attachment
	if (!empty($_FILES['Attachment'])) {
		if (!$_FILES['Attachment']['error']) {		
			// Insert Record and Get ID
			$objAttach = new MarketLeadAttachments();
			$objAttach->PostID = $intPostID;
			$objAttach->Filename = $_FILES['Attachment']['name'];
			$intAttachID = $objAttach->Insert();
			
			// Save Attachment, Append Row ID
			move_uploaded_file($_FILES['Attachment']['tmp_name'], '../attachments/' . $intAttachID . $_FILES['Attachment']['name']);
		}
	}
	
	header('Location: viewlead.php?ID=' . $intLeadID);
}

$strPageTitle = 'View Lead';

include('templates/viewlead.php');

?>