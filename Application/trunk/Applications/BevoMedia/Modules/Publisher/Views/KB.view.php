<?

require(PATH."classes/clsKBPosts.php");

// For UTF Characters Pulled from RSS
header('Content-Type: text/html; charset=utf-8');
global $strSearchVal;
if(isset($_GET['search']))
	$strSearchVal = $_GET['search'];

function ListPosts() {
	global $strSearchVal;
	$objPosts = new KBPosts();
	
	if (empty($strSearchVal)) {
		$objPosts->GetList();
	}
	else {
		$objPosts->GetListBySearch($strSearchVal);
	}
	
	if ($objPosts->RowCount == 0) {
		return false;
	}
	
	$objPosts->MovePage(1, 25);
	
	while ($arrThisRow = $objPosts->GetRow()) {
		$strThisContent = strip_tags($arrThisRow['content']);
		
		if (strlen($strThisContent) > 250) {
			$strThisContent = substr($strThisContent, 0, 250) . '...';
		}
		
		// Write Some Highlighter Code Here
		
?>
  <li><a href="KBPost.html?ID=<?php echo $arrThisRow['id']; ?>" title="<?php echo $arrThisRow['Feedtitle'] . ': ' . $arrThisRow['title']; ?>"><?php echo $arrThisRow['title']; ?></a>
	<div class="cl-excerpt"><?php echo $strThisContent; ?></div></li>
<?php
	}
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
	
<div class="classroom-wrapper">

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