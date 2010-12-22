<?php include('templates/header.php'); ?>

<?php include('templates/classroomheader.php'); ?>

<h1>Knowledgebase</h1>

<h2>Feeds</h2>

<table class="ListingTable">
  <tr class="HeaderRow">
    <td>Title</td>
	<td width="10%" align="center">Action</td>
  </tr>
<?php ListFeeds(); ?>
</table>

<h2>Recent Posts</h2>

<table class="ListingTable">
  <tr class="HeaderRow">
    <td>Title</td>
	<td width="15%" align="center">Feed</td>
	<td width="10%" align="center">Date</td>
  </tr>
<?php ListPosts(); ?>
</table>

<h3>Add Feed</h3>

<form method="post" action="kb.php?Action=Create">
<table>
  <tr>
    <td><label for="Title">Title:</label></td>
	<td><input type="text" name="Title" id="Title" maxlength="50"/></td>
  </tr>
  <tr>
    <td><label for="URL">RSS URL:</label></td>
	<td><input type="text" name="URL" id="URL" maxlength="250"/></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Create"/>
  </tr>
</table>
</form>

<?php include('templates/footer.php'); ?>