<?php
$strPageHead = '<!-- Load TinyMCE --> 
<script type="text/javascript" src="http://bevomedia.rcs.us/JS/tiny_mce/jquery.tinymce.js"></script> 
<script type="text/javascript"> 
	$().ready(function() {
		$("textarea").tinymce({
			// Location of TinyMCE script
			script_url : "http://bevomedia.rcs.us/JS/tiny_mce/jquery.tinymce.js"});
	});
</script> 
<!-- /TinyMCE --> ';
// ^^^^	OLD TINYMCE JQUERY BASED - DOES NOT WORK
//	>>	1Uncaught ReferenceError: tinymce is not defined

$strPageHead = '
<!-- TinyMCE -->
<script type="text/javascript" src="/JS/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "simple"
	});
</script>
<!-- /TinyMCE -->';

?>
<?php include('templates/header.php'); ?>

<?php include('templates/classroomheader.php'); ?>

<h1><?php echo $strTitleVal; ?></h1>

<form method="post" action="chapter.php?ID=<?php echo $intChapterID; ?>&Action=Update">
<table>
  <tr>
    <td><label for="Title">Title:</label></td>
	<td><input type="text" name="Title" id="Title" value="<?php echo $strTitleVal; ?>"/></td>
  </tr>
  <tr>
    <td><label for="Video">Video URL:</label></td>
	<td><input type="text" name="VideoURL" id="Video" value="<?php echo $strVideoVal; ?>"/></td>
  </tr>
  <tr>
    <td><label for="Content">Content:</label></td>
	<td><textarea name="Content" id="Content" cols="40" rows="8"><?php echo $strContentVal; ?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Update"/></td>
  </tr>
</table>
</form>

<?php include('templates/footer.php'); ?>