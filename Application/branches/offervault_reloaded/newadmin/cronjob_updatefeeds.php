<?php

require('include.php');
require(PATH.'classes/clsKBFeeds.php');
require(PATH.'classes/clsKBPosts.php');

// For UTF Characters Pulled from RSS
header('Content-Type: text/html; charset=utf-8');

UpdateFeeds();

function UpdateFeeds() {
	$objFeeds = new KBFeeds();
	$objFeeds->GetList();
	
	if ($objFeeds->RowCount == 0) {
		return false;
	}
	
	$arrRows = $objFeeds->GetRows();
	
	foreach ($arrRows as $arrThisRow) {
		if ($arrThisRow['ID'] && strlen($arrThisRow['URL']) > 1) {
			UpdatePosts($arrThisRow['ID'], $arrThisRow['URL']);
		}
	}
}

function UpdatePosts($intInID, $strInURL) {
	$strResp = CurlIt($strInURL);
	
	if (!$strResp || strlen($strResp) < 1) {
		return false;
	}
	
	$objXML = simplexml_load_string($strResp);
	
	if (!objXML) {
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
		
		$objPost->FeedID = $intInID;
		$objPost->URL = $strThisURL;
		$objPost->Title = $strThisTitle;
		$objPost->Content = $strThisContent;
		$objPost->PostDate = $strThisDate;
		
		// Check If Exists
		$objPost->ExistsByGUID($intInID, $strThisGUID);
		
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
	$objFeed->ID = $intID;
	$objFeed->LastRequest = date('Y-m-d H:i:s', time());
	$objFeed->Update();
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

?>