<?php include('templates/header.php'); ?>

<?php include('templates/marketheader.php'); ?>

<h2><?php echo $strName; ?></h2>

<?php if ($intAcceptedID != 0) { ?>

<h3>Accepted Terms</h3>

<table>
  <tr>
    <td><strong>Date:</strong></td>
	<td><?php echo $strTermDate; ?></td>
  </tr>
  <tr>
    <td><strong>Deposit $:</strong></td>
	<td><?php echo $intDeposit; ?> <?php if ($intOrderID != 0) { ?>(Deposited)<?php } ?></td>
  </tr>
  <tr>
    <td><strong>Terms:</strong></td>
	<td><?php echo $strTerms; ?></td>
  </tr>
</table>

<?php if ($intUserComplete != 0) { ?>
<p>
The affiliate has marked this project as complete.
</p>
<?php } ?>

<?php if ($intProviderComplete != 0) { ?>
<p>
The provider has marked this project as complete.
</p>
<?php } ?>

<?php } ?>

<h3>Proposed Terms</h3>

<?php ListTerms(); ?>

<?php include('templates/footer.php'); ?>