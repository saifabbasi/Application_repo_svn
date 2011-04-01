<?php

require('include.php');
require(PATH.'classes/clsKBFeeds.php');
require(PATH.'classes/clsKBPosts.php');
require('auth.php');

// For UTF Characters Pulled from RSS
header('Content-Type: text/html; charset=utf-8');

$intID = $_GET['ID'];

if (!is_numeric($intID)) {
	header('Location: kb.php');
}

LegacyAbstraction::doAction('Update', 'UpdateFeed');

LoadFeed();

// Call After Feed URL is Loaded
LegacyAbstraction::doAction('UpdatePosts', 'UpdatePosts');

function LoadFeed() {
	global $intID, $strTitleVal, $strURLVal;
	
	$objFeed = new KBFeeds();
	$objFeed->ID = $intID;
	$objFeed->GetDetails();
	
	$strTitleVal = $objFeed->title;
	$strURLVal = $objFeed->url;
}

function ListPosts() {
	global $intID;
	$objPosts = new KBPosts();
	$objPosts->GetList($intID);
	
	if ($objPosts->RowCount == 0) {
		return false;
	}
	$blnAltRow = false;
	
	while ($arrThisRow = $objPosts->GetRow()) {
		if (date('Y-m-d', strtotime($arrThisRow['postDate'])) == date('Y-m-d', time())) {
			// If Today, Show Time
			$strThisPostDate = date('h:ia', strtotime($arrThisRow['postDate']));
		}
		else {
			// Not Today, Show Date
			$strThisPostDate = date('Y-m-d', strtotime($arrThisRow['postDate']));
		}
		
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td><a href="#post<?php echo $arrThisRow['id']; ?>" onclick="$('#post<?php echo $arrThisRow['id']; ?>').toggle();"><?php echo $arrThisRow['title']; ?></a></td>
    <td align="center"><?php echo $strThisPostDate; ?></td>
  </tr>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?> id="post<?php echo $arrThisRow['id']; ?>" style="display: none;">
    <td colspan="2"><?php echo $arrThisRow['content']; ?></td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
}

function UpdateFeed() {
	global $intID, $strTitleVal, $strURLVal;
	
	$strTitleVal = $_POST['Title'];
	$strURLVal = $_POST['URL'];
	
	if (strlen($strTitleVal) < 1 || strlen($strURLVal) < 1) {
		return false;
	}
	
	$objFeed = new KBFeeds();
	$objFeed->id = $intID;
	$objFeed->title = $strTitleVal;
	$objFeed->url = $strURLVal;
	$objFeed->Update();
	
	header('Location: kbfeed.php?ID=' . $intID);
}

function UpdatePosts() {
	global $intID, $strURLVal;
	
	$strResp = CurlIt($strURLVal);
	
	if (!$strResp || strlen($strResp) < 1) {
		return false;
	}
	
	$objXML = simplexml_load_string($strResp);
	
	if (!$objXML) {
		return false;
	}
	
	foreach ($objXML->channel->item as $objThisItem) {
		$strThisTitle = (string) $objThisItem->title;
		$strThisURL = (string) $objThisItem->link;
		$strThisGUID = (string) $objThisItem->guid;
		$strThisContent = (string) $objThisItem->description;
		$strThisDate = (string) $objThisItem->pubDate;
		
		// Cleanup
		$strThisTitle = strip_tags($strThisTitle);
		$strThisContent = strip_tags($strThisContent);
		
		$strThisDate = date('Y-m-d H:i:s', strtotime($strThisDate));
		
		$objPost = new KBPosts();
		
		$objPost->feedId = $intID;
		$objPost->url = $strThisURL;
		$objPost->title = $strThisTitle;
		$objPost->content = $strThisContent;
		$objPost->postDate = $strThisDate;
		
		// Check If Exists
		$objPost->ExistsByGUID($intID, $strThisGUID);
		
		if ($objPost->RowCount != 0) {
			// Post Exists, Update
			$arrRow = $objPost->GetRow();
			$objPost->ID = $arrRow['ID'];
			$objPost->Update();
		}
		else {
			// New Post
			$objPost->GUID = $strThisGUID;
			$objPost->Insert();
		}
	}
	
	$objFeed = new KBFeeds();
	$objFeed->id = $intID;
	$objFeed->lastRequest = date('Y-m-d H:i:s', time());
	$objFeed->Update();
	
	header('Location: kbfeed.php?ID=' . $intID);
}

function CurlIt($strInURL, $arrInCurlOpts = array()) {
	$ch = curl_init($strInURL);    
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

	if (!empty($arrInCurlOpts)) {
		foreach ($arrInCurlOpts as $strThisOpt => $strThisVal) {
			curl_setopt($ch, constant($strThisOpt), $strThisVal);
		}
	}
	$result = curl_exec($ch);
	return $result;
}

$strPageTitle = 'Feeds';
include('templates/kbfeed.php');

?>