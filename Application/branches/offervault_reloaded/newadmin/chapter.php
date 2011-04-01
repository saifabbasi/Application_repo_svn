<?php

require('include.php');
require(PATH.'classes/clsClassroomSections.php');
require(PATH.'classes/clsClassroomChapters.php');
require('auth.php');

$intChapterID = $_GET['ID'];

if (!is_numeric($intChapterID)) {
	header('Location: classroom.php');
	exit;
}

LegacyAbstraction::doAction('Update', 'UpdateChapter');

LoadChapter();

function LoadChapter() {
	global $intChapterID, $strTitleVal, $strContentVal, $strVideoVal;
	
	$objChapter = new ClassroomChapters();
	$objChapter->ID = $intChapterID;
	$objChapter->GetDetails();
	
	$strTitleVal = $objChapter->Title;
	$strContentVal = $objChapter->Content;
	$strVideoVal = $objChapter->VideoURL;
}


function UpdateChapter() {
	global $intChapterID;
	$strTitleVal = $_POST['Title'];
	$strContentVal = $_POST['Content'];
	$strVideoVal = $_POST['VideoURL'];
	
	$objChapter = new ClassroomChapters();
	$objChapter->ID = $intChapterID;
	$objChapter->Title = $strTitleVal;
	$objChapter->Content = $strContentVal;
	$objChapter->VideoURL = $strVideoVal;
	$objChapter->Update();
	
	header('Location: chapter.php?ID=' . $intChapterID);
}

$strPageTitle = 'Classroom';
include('templates/chapter.php');

?>