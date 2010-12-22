<?php include('templates/header.php'); ?>

<?php include('templates/marketheader.php'); ?>

<h1>Freelancer</h1>

<div style="float: left; width: 50%;">
<form method="post" enctype="multipart/form-data" action="freelancer.php?ID=<?php echo $intID; ?>&Action=Update">
<table>
  <tr>
    <td><label for="Name">Name:</label></td>
	<td><input type="text" name="Name" id="Name" value="<?php echo htmlspecialchars($strName); ?>" maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="Email2">Bevo Email:</label></td>
	<td><input type="text" name="Email2" id="Email2" value="<?php echo htmlspecialchars($strEmail2); ?>" maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="Email">Email:</label></td>
	<td><input type="text" name="Email" id="Email" value="<?php echo htmlspecialchars($strEmail); ?>" maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="Email">Password:</label></td>
	<td><input type="text" name="Password" id="Password" value="<?php echo htmlspecialchars($strPassword); ?>" maxlength="50"></td>
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
    <td><label for="Services">Services:</label></td>
	<td><select name="Services[]" id="Services" multiple="multiple" size="4"><?php ListServiceOptions(); ?></select></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Update"/></td>
  </tr>
</table>
</form>
</div>

<div style="width: 50%; float: left;">

<div style="border: 1px solid #808080; background-color: #F7F7F7; width: 250px;">
<table width="250">
  <tr>
    <td colspan="2"><label>Payments</label></td>
  </tr>
  <tr>
    <td><label>This Month:</label></td>
	<td>$<?php echo $intThisMonth; ?></td>
  </tr>
  <tr>
    <td><label>This Year:</label></td>
	<td>$<?php echo $intThisYear; ?></td>
  </tr>
  <tr>
    <td><label>Total:</label></td>
	<td>$<?php echo $intThisTotal; ?></td>
  </tr>
</table>
</div>

</div>

<div style="clear: both; height: 1px; overflow: clip;">&nbsp;</div>

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

<?php include('templates/footer.php'); ?>