<?php

require(PATH."classes/clsClassroomSections.php");
require(PATH."classes/clsClassroomChapters.php");
require(PATH."classes/clsKBCats.php");


global $intID, $strChapterTitle, $strChapterVideo, $strVideoID;
$intID = $_GET['ID'];

if (!is_numeric($intID)) {
	header('Location: publisher-classroom.php');
}

LoadChapter();

function LoadChapter() {
	global $intID, $strChapterTitle, $strChapterVideo, $strVideoID;
	
	$objChapter = new ClassroomChapters();
	$objChapter->ID = $intID;
	$objChapter->GetDetails();
	
	$strChapterTitle = $objChapter->title;
	$strChapterContent = $objChapter->content;
	$strChapterVideo = $objChapter->videoURL;
	
	$arrURL = parse_url($strChapterVideo);
	parse_str($arrURL['query'], $arrQuery);
	
	$strVideoID = $arrQuery['v'];
}

if (isset($_GET['videoId'])) {
	$strVideoID = $_GET['videoId'];
}
?>

<?php 
//$strPageHead = '<link rel="stylesheet" type="text/css" href="style/classroom.css"/>'; //depreciated 100808
?>

<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Publisher/Classroom.html">&laquo; Back to the Classroom<span></span></a></li>
			<li><a href="/BevoMedia/Publisher/OvernightAffiliate.html">Overnight Affiliate<span></span></a></li>
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


<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/mpnewb.style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
$(document).ready(function() {
$.fn.pause = function(duration){$(this).animate({dummy:1},duration);return this;};
$('a#mpnewb_button').click(function(){
	if($(this).hasClass('isclosed')) {$('div#mpnewb').show(function(){
		$('a#mpnewb_button').removeClass('isclosed').addClass('isopen');$(this).slideDown(400);
		$('div.mpnewb-rightmost').pause(1000).fadeIn(400);$('div.mpnewb-price').pause(1400).slideDown(400);
	});
	} else {
		$('div#mpnewb').hide(function(){$('a#mpnewb_button').removeClass('isopen').addClass('isclosed');$(this).slideUp(100);});
	}
});
});
</script>
<!-- ENDOF Newbies Package -->

<div class="classroom-wrapper">

<h2><?php echo $strChapterTitle; ?></h2>

<p>&nbsp;</p>

<p align="center">
<object type="application/x-shockwave-flash" style="width:450px; height:366px;" data="http://www.youtube.com/v/<?php echo $strVideoID; ?>">
<param name="movie" value="http://www.youtube.com/v/<?php echo $strVideoID; ?>" />
</object>
</p>

</div>
