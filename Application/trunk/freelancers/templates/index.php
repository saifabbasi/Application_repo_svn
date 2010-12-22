<?php include('templates/header.php'); ?>

<div id="Edit" style="display: none;">

<p><strong>Edit Profile</strong></p>

<form method="post" enctype="multipart/form-data" action="index.php?Action=Update">
<table>
  <tr>
    <td><label for="Name">Name:</label></td>
	<td><input type="text" name="Name" id="Name" value="<?php echo htmlspecialchars($strName); ?>" maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="Email2">Bevo Email:</label></td>
	<td><input type="hidden" name="Email2" id="Email2" value="<?php echo htmlspecialchars($strEmail2); ?>"><?php echo htmlspecialchars($strEmail2); ?></td>
  </tr>
  <tr>
    <td><label for="Email">Email:</label></td>
	<td><input type="text" name="Email" id="Email" value="<?php echo htmlspecialchars($strEmail); ?>"></td>
  </tr>
  <tr>
    <td><label for="PayPal">PayPal:</label></td>
	<td><input type="text" name="PayPal" id="PayPal" value="<?php echo htmlspecialchars($strPayPal); ?>"></td>
  </tr>
  <tr>
    <td><label for="Desc">Description:</label></td>
	<td><textarea name="Desc" id="Desc" cols="30" rows="3"><?php echo htmlspecialchars($strDesc); ?></textarea></td>
  </tr>
  <tr>
    <td><label for="PriceRange">Price Range:</label></td>
	<td><input type="text" name="PriceRange" id="PriceRange" value="<?php echo htmlspecialchars($strPriceRange); ?>"  maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="ThumbImage">Image (130x130):</label></td>
	<td><input type="file" name="Image" id="Image"/></td>
  </tr>
  <tr>
    <td><label for="ThumbImage">Thumb Image (40x40):</label></td>
	<td><input type="file" name="ThumbImage" id="ThumbImage"/></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Update"/> or <a href="#" onclick="CancelEdit();">Cancel</a></td>
  </tr>
</table>
</form>

</div>

<div id="View">

<p><strong>Profile</strong> [<a href="#" onclick="EditProfile();">Edit</a>]</p>

<table>
  <tr>
    <td><label for="Name">Name:</label></td>
	<td><?php echo htmlspecialchars($strName); ?></td>
  </tr>
  <tr>
    <td><label for="Email2">Bevo Email:</label></td>
	<td><?php echo htmlspecialchars($strEmail2); ?></td>
  </tr>
  <tr>
    <td><label for="Email">Email:</label></td>
	<td><?php echo htmlspecialchars($strEmail); ?></td>
  </tr>
  <tr>
    <td><label for="PayPal">PayPal:</label></td>
	<td><?php echo htmlspecialchars($strPayPal); ?></td>
  </tr>
  <tr>
    <td><label for="Desc">Description:</label></td>
	<td><?php echo htmlspecialchars($strDesc); ?></td>
  </tr>
  <tr>
    <td><label for="PriceRange">Price Range:</label></td>
	<td><?php echo htmlspecialchars($strPriceRange); ?></td>
  </tr>
  <tr>
    <td><label for="ThumbImage">Image (130x130):</label></td>
	<td><img src="../freelancers/images/<?php echo $strImage; ?>" width="130" height="130"/></td>
  </tr>
  <tr>
    <td><label for="ThumbImage">Thumb Image (40x40):</label></td>
	<td><img src="../freelancers/images/<?php echo $strThumbImage; ?>" width="40" height="40"/></td>
  </tr>
</table>
</div>

<h2>Projects</h2>

<table class="ListingTable">
  <tr class="HeaderRow">
    <td>Project Name</td>
	<td>Affiliate</td>
	<td align="center">Deposit</td>
	<td align="center">Last Update</td>
  </tr>
<?php ListProjects(); ?>
</table>

<script>

function EditProfile() {
	$('#Edit').show();
	$('#View').hide();
}
function CancelEdit() {
	$('#Edit').hide();
	$('#View').show();
}
</script>

<?php include('templates/footer.php'); ?>