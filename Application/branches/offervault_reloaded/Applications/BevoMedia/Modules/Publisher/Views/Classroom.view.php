<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/classroom.css" rel="stylesheet" type="text/css" />
<?

require(PATH."classes/clsClassroomSections.php");
require(PATH."classes/clsClassroomChapters.php");
require(PATH."classes/clsKBPosts.php");


//include("session.php");

function ListSections() {
	$objSections = new ClassroomSections();
	$objSections->GetList();
	
	if ($objSections->RowCount == 0) {
		return false;
	}
	
	$intSectionCount = 1;
	
	while ($arrThisRow = $objSections->GetRow()) {
?>
  <ul class="cl-list clnum-<?=$intSectionCount?>">
		<li class="cl-header" style="background: url(/Themes/BevoMedia/img/<?php echo $arrThisRow['image']; ?>) no-repeat;"><?php echo $arrThisRow['title']; ?></li>
<?php ListchaptersBySection($arrThisRow['id']); ?>
		<li class="cl-footer"></li>
	</ul>
<?
		if ($intSectionCount % 2 == 0) {
			echo '<div style="clear: both; height: 1px; overflow: clip;">&nbsp;</div>';
		}
		$intSectionCount++;
	}
}

function ListchaptersBySection($intInSectionID) {
	static $intSectionCount;
	
	$objchapters = new Classroomchapters();
	$objchapters->GetListBySectionID($intInSectionID);
	
	if ($objchapters->RowCount == 0) {
		return false;
	}
	
	$staticURL = array(
				'Content Help'=> '/BevoMedia/Marketplace/Index.html?ServiceID=1',
				'Programming Help'=>'/BevoMedia/Marketplace/Index.html?ServiceID=4',
				'Landing Page Designers'=>'/BevoMedia/Marketplace/Index.html?ServiceID=2',
				'One on One Lessons'=>'/BevoMedia/Marketplace/Index.html?ServiceID=3'
	);
	$staticLinks = array_keys($staticURL);
	while ($arrThisRow = $objchapters->GetRow()) {
?>
	<?php if(in_array($arrThisRow['title'], $staticLinks)):?>
			<li><span class="chapter"><?php if (!$intSectionCount) { ?>chapter<?php } ?> <?php echo $arrThisRow['chapter']; ?></span><span><a href="<?php echo $staticURL[$arrThisRow['title']]?>"><?php echo $arrThisRow['title']; ?> <img src="/Themes/BevoMedia/img/button-clsmall-text.gif" alt="chapter Text" border="0"/></a> <?php if ($arrThisRow['videoUrl']) { ?><a href="<?php echo $staticURL[$arrThisRow['title']]?>"><img src="/Themes/BevoMedia/img/button-clsmall-vid.gif" alt="chapter Video" border="0"/></a><?php } ?></span></li>
	<?php else:?>
			<li><span class="chapter"><?php if (!$intSectionCount) { ?>chapter<?php } ?> <?php echo $arrThisRow['chapter']; ?></span><span><a href="ClassroomChapter.html?ID=<?php echo $arrThisRow['id']; ?>"><?php echo $arrThisRow['title']; ?> <img src="/Themes/BevoMedia/img/button-clsmall-text.gif" alt="chapter Text" border="0"/></a> <?php if ($arrThisRow['videoUrl']) { ?><a href="ClassroomVideo.html?ID=<?php echo $arrThisRow['id']; ?>"><img src="/Themes/BevoMedia/img/button-clsmall-vid.gif" alt="chapter Video" border="0"/></a><?php } ?></span></li>
	<?php endif?>
<?php
	}
	
	$intSectionCount++;
}

function ListPosts() {
	$objPosts = new KBPosts();
	$objPosts->GetList();
	
	if ($objPosts->RowCount == 0) {
		return false;
	}
	
	$objPosts->MovePage(1, 10);
	
	while ($arrThisRow = $objPosts->GetRow()) {
?>
  <li><a href="KBPost.html?ID=<?php echo $arrThisRow['id']; ?>" title="<?php echo $arrThisRow['Feedtitle'] . ': ' . $arrThisRow['title']; ?>"><?php echo $arrThisRow['title']; ?></a><div class="cl-excerpt"><?= $arrThisRow['content']?></div></li>
<?php
	}
	
	if ($objPosts->RowCount > 10) {
?>
  <li style="text-align: right;"><strong><a href="KB.html">More...</a></strong></li>
<?php
	}
}


?>


<?php
//$strPageHead = '<link rel="stylesheet" type="text/css" href="style/classroom.css"/>'; //depreciated 100808
?>

<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu">
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


<div class="classroom-wrapper">

<?php ListSections(); ?>
	<!-- 100608 ADDED: ppv playbook banner -->
	<a class="cl-banner" href="http://www.ppvplaybook.com/amember/go.php?r=1307&i=l0"><img src="/Themes/BevoMedia/img/cl-ppvplaybook-widget.jpg" alt="Find out more about PPV Playbook Now" /></a>
	<div class="clear"></div>
<!-- KB -->
	<ul class="cl-list fullwidth">
		<li class="cl-header">
			<form action="KB.html" method="GET">
				<div class="row">
					<input type="text" class="formtxt" name="search" maxlength="250" value="Search: type + enter" onfocus="if (this.value == 'Search: type + enter') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search: type + enter';}" />
					<input type="submit" class="formsubmit" value="go" />
				</div>
			</form>
		</li>
<?php ListPosts(); ?>
		<li class="cl-footer"></li>
	</ul>
	
</div>