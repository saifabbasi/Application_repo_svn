<?php include('templates/header.php'); ?>

<?php include('templates/marketheader.php'); ?>

<h1>Services</h1>

<form method="post" action="services.php?Action=Update">
<table class="ListingTable">
  <tr class="HeaderRow">
    <td width="20%">Name</td>
	<td width="80%">Description</td>
  </tr>
<?php ListServices(); ?>
</table>

<p align="center"><input type="submit" value="Update"/></p>
</form>

<?php include('templates/footer.php'); ?>