<?php include('templates/header.php'); ?>

<?php include('templates/marketheader.php'); ?>

<h1>Freelancers</h1>

<p align="right"><strong><a href="newfreelancer.php">New Freelancer</a></strong></p>

<table class="ListingTable">
  <tr class="HeaderRow">
    <td>Name</td>
	<td width="15%" align="center">Action</td>
  </tr>
<?php ListProviders(); ?>
</table>

<?php include('templates/footer.php'); ?>