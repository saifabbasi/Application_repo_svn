<?php include('templates/header.php'); ?>

<?php include('templates/marketheader.php'); ?>

<h1>New Freelancer</h1>

<form method="post" enctype="multipart/form-data" action="newfreelancer.php?Action=Create">
<table>
  <tr>
    <td><label for="Name">Name:</label></td>
	<td><input type="text" name="Name" id="Name" value="<?php echo htmlspecialchars(isset($strName)?$strName:''); ?>" maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="Email2">Bevo Email:</label></td>
	<td><input type="text" name="Email2" id="Email2" value="<?php echo htmlspecialchars(isset($strEmail2)?$strEmail2:''); ?>"  maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="Email">Email:</label></td>
	<td><input type="text" name="Email" id="Email" value="<?php echo htmlspecialchars(isset($strEmail)?$strEmail:''); ?>"  maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="PayPal">PayPal:</label></td>
	<td><input type="text" name="PayPal" id="PayPal" value="<?php echo htmlspecialchars(isset($strPayPal)?$strPayPal:''); ?>"  maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="Password">Password:</label></td>
	<td><input type="password" name="Password" id="Password"></td>
  </tr>
  <tr>
    <td><label for="Desc">Description:</label></td>
	<td><textarea name="Desc" id="Desc" cols="30" rows="3"><?php echo htmlspecialchars(isset($strDesc)?$strDesc:''); ?></textarea></td>
  </tr>
  <tr>
    <td><label for="PriceRange">Price Range:</label></td>
	<td><input type="text" name="PriceRange" id="PriceRange" value="<?php echo htmlspecialchars(isset($strPriceRange)?$strPriceRange:''); ?>"  maxlength="50"></td>
  </tr>
  <tr>
    <td><label for="Services">Services:</label></td>
	<td><select name="Services[]" id="Services" multiple="multiple" size="4"><?php ListServiceOptions(); ?></select></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Create"/></td>
  </tr>
</table>
</form>

<?php include('templates/footer.php'); ?>