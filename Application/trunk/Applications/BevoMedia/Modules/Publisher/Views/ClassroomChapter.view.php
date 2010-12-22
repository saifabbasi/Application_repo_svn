<?php

require(PATH."classes/clsClassroomSections.php");
require(PATH."classes/clsClassroomChapters.php");
require(PATH."classes/clsKBCats.php");
        
global $intID, $strChapterTitle, $strChapterContent;
$intID = $_GET['ID'];

if (!is_numeric($intID)) {
	header('Location: publisher-classroom.php');
}

LoadChapter();

function LoadChapter() {
	global $intID, $strChapterTitle, $strChapterContent;

	$objChapter = new ClassroomChapters();
	$objChapter->ID = $intID;
	$objChapter->GetDetails();
	
	$strChapterTitle = $objChapter->title;
	$strChapterContent = $objChapter->content;
}
?>

<?php
//$strPageHead = '<link rel="stylesheet" type="text/css" href="style/classroom.css"/>'; //depreciated with the dawn of the new design. it's not that this var was being used anywhere anyway.
?>

<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Publisher/Classroom.html">&laquo; Back to the Classroom<span></span></a></li>
		</ul>
		<ul class="floatright">
			<li class="liform"><form action="KB.html" method="GET">
				<label>Search the Bevo Knowledge Base:</label>
				<input type="text" class="formtxt" name="search" maxlength="250" value="Search: type + enter" onfocus="if (this.value == 'Search: type + enter') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search: type + enter';}" />
				<input type="submit" class="formsubmit" value="go" />
			</form></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<center>
<a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Marketplace/MentorshipProgram.html">
	<img style="border: none" src="/Themes/BevoMedia/img/mentorshipprogram_banner.jpg" />
</a>
</center>

<div class="pagecontent">
	<h3><?php echo $strChapterTitle; ?></h3>
	<?php echo ($strChapterContent); ?>
</div>
