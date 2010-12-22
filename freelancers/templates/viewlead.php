<?php include('header.php'); ?>

<ul>
  <li><strong>ID:</strong> <?php echo $intLeadID; ?></li>
  <li><strong>Product:</strong> <?php echo $strProductName; ?></li>
  <li><strong>User:</strong> <?php echo $strUserName; ?></li>
  <li><strong>Date:</strong> <?php echo $strCreateDate; ?></li>
</ul>


<table>
<?php ListLeadPosts(); ?>
</table>

<p>
<form method="post" enctype="multipart/form-data" action="viewlead.php?ID=<?php echo $intLeadID; ?>&Action=Post">
<input type="hidden" name="ProviderID" value="<?php echo $intProviderID; ?>"/>
<table>
  <tr>
    <td><label for="Post">Post:</td>
    <td><textarea name="Post" id="Post" cols="40" rows="4"></textarea></td>
  </tr>
  <tr>
    <td><label for="Attachment">Attachment:</label></td>
	<td><input type="file" name="Attachment" id="Attachment"/></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Post"/></td>
  </tr>
</table>
</form>
</p>
  

<?php include('footer.php'); ?>