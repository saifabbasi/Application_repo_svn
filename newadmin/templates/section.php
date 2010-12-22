<?php
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

<h1><?php echo $strSectionTitle; ?></h1>

<form method="post" action="section.php?ID=<?php echo $intSectionID; ?>&Action=UpdateOrder">
<table id="ChapterList" class="ListingTable">
  <tr class="HeaderRow">
    <td width="5%">Chapter</td>
	<td width="95%">Title</td>
  </tr>
<?php ListChapters(); ?>
</table>

<p align="center"><input type="submit" value="Update Order"/></p>
</form>

<h2>Create Chapter</h2>

<form method="post" action="section.php?ID=<?php echo $intSectionID; ?>&Action=Create">
<table>
  <tr>
    <td><label for="Title">Title:</label></td>
	<td><input type="text" name="Title" id="Title"/></td>
  </tr>
  <tr>
    <td><label for="Video">Video URL:</label></td>
	<td><input type="text" name="Video" id="Video" value="<?php echo $strVideoVal; ?>"/></td>
  </tr>
  <tr>
    <td><label for="Content">Content:</label></td>
	<td><textarea name="Content" id="Content" cols="40" rows="8"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Create"/></td>
  </tr>
</table>
</form>

<script type="text/javascript"> 
// When the document is ready set up our sortable with it's inherant function(s) 
$(document).ready(function() { 
  $("#ChapterList").sortable({
	items  : 'div',
    handle : '.handle', 
    update : function () { 
      var strOrder = $('#ChapterList').sortable('serialize');
	  alert(strOrder);
	  $('#ChapterOrder').val(strOrder);
      //$("#info").load("process-sortable.php?"+order); 
    } 
  }); 
}); 
</script>

<?php include('templates/footer.php'); ?>