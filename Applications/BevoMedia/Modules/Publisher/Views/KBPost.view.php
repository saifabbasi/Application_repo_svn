<?php

require(PATH."classes/clsKBFeeds.php");
require(PATH."classes/clsKBPosts.php");

// For UTF Characters Pulled from RSS
header('Content-Type: text/html; charset=utf-8');
        

global $intID, $strTitle, $strContent, $strPostDate, $strURL, $intFeedID;
global $intFeedID, $strFeedTitle;
$intID = $_GET['ID'];

if (!is_numeric($intID)) {
	header('Location: publisher-classroom.php');
	exit;
}

LoadPost();

function LoadPost() {
	global $intID, $strTitle, $strContent, $strPostDate, $strURL, $intFeedID;
	
	$objPost = new KBPosts();
	$objPost->ID = $intID;
	$objPost->GetDetails();
	
	$intFeedID = $objPost->feedId;
	$strTitle = $objPost->title;
	$strContent = $objPost->content;
	$strPostDate = $objPost->postDate;
	$strURL = $objPost->url;
	
	LoadFeed();
}

function LoadFeed() {
	global $intFeedID, $strFeedTitle;
	
	$objFeed = new KBFeeds();
	$objFeed->ID = $intFeedID;
	$objFeed->GetDetails();
	
	$strFeedTitle = $objFeed->title;
}


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

<div class="pagecontent">
	<h3><?php echo $strTitle; ?></h3>
	
	<p>
	<strong>From:</strong> <a href="<?php echo $strURL; ?>"><?php echo $strFeedTitle; ?></a><br/>
	<strong>Posted:</strong> <?php echo date('Y-m-d h:ia', strtotime($strPostDate)); ?>
	</p>
	
	<p>&nbsp;</p>
	
	<p>
	<?php echo $strContent; ?>
	</p>
</div><!--close pagecontent-->

