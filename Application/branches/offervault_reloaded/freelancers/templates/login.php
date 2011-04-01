<?php include('templates/header.php'); ?>

<form method="post" action="login.php?Action=Login">
<input type="hidden" name="Ref" value="<?php echo (isset($strRefVal))?htmlspecialchars($strRefVal):''; ?>"/>
<table>
  <tr>
    <td><label for="Email">Email:</label></td>
	<td><input type="text" name="Email" id="Email"/></td>
  </tr>
  <tr>
    <td><label for="Password">Password:</label></td>
	<td><input type="password" name="Password" id="Password"/></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Login"/></td>
  </tr>
</table>
</form>


<?php include('templates/footer.php'); ?>