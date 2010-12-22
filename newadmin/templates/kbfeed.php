<?php include('templates/header.php'); ?>

<?php include('templates/classroomheader.php'); ?>

<h1><?php echo $strTitleVal; ?></h1>

<form method="post" action="kbfeed.php?ID=<?php echo $intID; ?>&Action=Update">
<table>
  <tr>
    <td><label for="Title">Title:</label></td>
	<td><input type="text" name="Title" id="Title" value="<?php echo $strTitleVal; ?>" maxlength="50"/></td>
  </tr>
  <tr>
    <td><label for="URL">RSS URL:</label></td>
	<td><input type="text" name="URL" id="URL" value="<?php echo $strURLVal; ?>" maxlength="250"/></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Update"/>
  </tr>
</table>
</form>

<h2>Posts</h2>

<table class="ListingTable">
  <tr class="HeaderRow">
    <td>Title</td>
	<td width="15%" align="center">Date</td>
  </tr>
<?php ListPosts(); ?>
</table>

<?php include('templates/footer.php'); ?>