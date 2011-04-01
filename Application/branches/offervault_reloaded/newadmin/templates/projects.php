<?php include('templates/header.php'); ?>

<?php include('templates/marketheader.php'); ?>

<h1>Projects</h1>

<table class="ListingTable">
  <tr class="HeaderRow">
    <td>Project Name</td>
	<td>Freelancer</td>
	<td>Affiliate</td>
	<td align="center">Deposit</td>
	<td align="center">Last Update</td>
  </tr>
<?php ListProjects(); ?>
</table>

<?php include('templates/footer.php'); ?>