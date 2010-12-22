<?php


require('include.php');
require(PATH.'classes/clsKBFeeds.php');
require(PATH.'classes/clsKBPosts.php');
require('auth.php');

// For UTF Characters Pulled from RSS
header('Content-Type: text/html; charset=utf-8');

LegacyAbstraction::doAction('Create', 'CreateFeed');


function ListFeeds() {
	$objFeeds = new KBFeeds();
	$objFeeds->GetList();
	
	if ($objFeeds->RowCount == 0) {
		return false;
	}
	$blnAltRow = false;
	
	$objFeeds->MovePage(1, 25);
	
	while ($arrThisRow = $objFeeds->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td><a href="kbfeed.php?ID=<?php echo $arrThisRow['id']; ?>"><?php echo $arrThisRow['title']; ?></a></td>
    <td align="center"><a href="kbfeed.php?ID=<?php echo $arrThisRow['id']; ?>&Action=UpdatePosts">Update</a></td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
}

function ListPosts() {
	$objPosts = new KBPosts();
	$objPosts->GetList();
	
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
    <td align="center"><a href="kbfeed.php?ID=<?php echo $arrThisRow['feedId']; ?>"><?php echo "<PRE>"; $arrThisRow['Feedtitle']; ?></a></td>
    <td align="center"><?php echo $strThisPostDate; ?></td>
  </tr>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?> id="post<?php echo $arrThisRow['id']; ?>" style="display: none;">
    <td colspan="3"><?php echo $arrThisRow['content']; ?></td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
}

function CreateFeed() {
	$strTitleVal = $_POST['title'];
	$strURLVal = $_POST['url'];
	
	if (strlen($strTitleVal) < 1 || strlen($strURLVal) < 1) {
		return false;
	}
	
	$objFeed = new KBFeeds();
	$objFeed->title = $strTitleVal;
	$objFeed->url = $strURLVal;
	$objFeed->Insert();
	
	header('Location: kb.php');
}

$strPageTitle = 'Feeds';
include('templates/kb.php');

?>
