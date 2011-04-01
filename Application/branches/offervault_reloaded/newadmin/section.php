<?php

require('include.php');
require(PATH.'classes/clsClassroomSections.php');
require(PATH.'classes/clsClassroomChapters.php');
require('auth.php');

$strVideoVal = '';
$intSectionID = $_GET['ID'];

if (!is_numeric($intSectionID)) {
	header('Location: classroom.php');
	exit;
}

LegacyAbstraction::doAction('Create', 'CreateChapter');
LegacyAbstraction::doAction('UpdateOrder', 'UpdateOrder');

LoadSection();

function LoadSection() {
	global $intSectionID, $strSectionTitle;
	
	$objSection = new ClassroomSections();
	$objSection->ID = $intSectionID;
	$objSection->GetDetails();
	
	$strSectionTitle = $objSection->Title;
}

function ListChapters() {
	global $intSectionID;
	$objChapters = new ClassroomChapters();
	$objChapters->GetListBySectionID($intSectionID);
	
	if ($objChapters->RowCount == 0) {
		return false;
	}
	$blnAltRow = false;
	
	$intTabIndx = 1;
	
	while ($arrThisRow = $objChapters->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?> id="chapter<?php echo $arrThisRow['id']; ?>" style="display: table-row; width: 100%;">
    <td width="5%"><input type="text" name="chapter[<?php echo $arrThisRow['id']; ?>]" value="<?php echo $arrThisRow['chapter']; ?>" size="3" style="text-align: center;" tabindex="<?php echo $intTabIndx; ?>"/></td>
    <td><a href="chapter.php?ID=<?php echo $arrThisRow['id']; ?>"><?php echo $arrThisRow['title']; ?></a></td>
  </tr>
<?php
		$intTabIndx++;
		$blnAltRow = !$blnAltRow;
	}
}

function CreateChapter() {
	global $intSectionID;
	$strTitleVal = $_POST['Title'];
	$strContentVal = $_POST['Content'];
	$strVideoVal = $_POST['Video'];
	
	$objChapter = new ClassroomChapters();
	
	// Find Last Chapter Pos
	$objChapter->GetListBySectionID($intSectionID);
	$intChapterCount = $objChapter->RowCount + 1;
	
	$objChapter->sectionId = $intSectionID;
	$objChapter->chapter = $intChapterCount;
	$objChapter->title = $strTitleVal;
	$objChapter->videoUrl = $strVideoVal;
	$objChapter->content = $strContentVal;
	$intChapterID = $objChapter->Insert();
	
	header('Location: section.php?ID=' . $intSectionID);
}

function UpdateOrder() {
	global $intSectionID;
	$arrChapters = $_POST['chapter'];
	
	$objChapter = new ClassroomChapters();
	foreach ($arrChapters as $intThisID => $intThisOrder) {
		$objChapter->id = $intThisID;
		$objChapter->chapter = $intThisOrder;
		$objChapter->Update();
	}
	
	header('Location: section.php?ID=' . $intSectionID);
}

$strPageTitle = 'Classroom';
include('templates/section.php');

?>
