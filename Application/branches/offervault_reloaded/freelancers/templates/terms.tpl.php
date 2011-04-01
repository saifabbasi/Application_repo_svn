<?php include('templates/header.php'); ?>

<h2><?php echo $strName; ?></h2>

<?php if ($intAcceptedID != 0) { ?>

<h3>Accepted Terms</h3>

<table>
  <tr>
    <td><strong>Date:</strong></td>
	<td><?php echo $strTermDate; ?></td>
  </tr>
  <tr>
    <td><strong>Project Price $:</strong></td>
	<td><?php echo $intDeposit; ?> <?php if ($intOrderID != 0) { ?>(Deposited)<?php } ?></td>
  </tr>
  <tr>
    <td><strong>Project Agreement:</strong></td>
	<td><?php echo $strTerms; ?></td>
  </tr>
</table>

<?php if ($intOrderID != 0) { ?>
<p>
The terms have been accepted and a deposit has been made. You should be in contact with the affiliate and begin work.
</p>

<?php if ($intUserComplete != 0) { ?>
<p>
The affiliate has marked this project as complete.
</p>
<?php } ?>

<?php if ($intProviderComplete != 0) { ?>
<p>
You have marked this project as complete.
</p>
<?php } else { ?>
<p>
<strong><a href="terms.php?ID=<?php echo $intID; ?>&Action=Complete">Mark Completed</a></strong>
</p>
<?php } ?>

<?php } ?>

<?php } ?>

<h3>Proposed Project Agreement</h3>

<?php ListTerms(); ?>

<?php if ($intAcceptedID == 0) { ?>

<h3>Submit Propsed Agreement</h3>

<form method="post" action="terms.php?ID=<?php echo $intID; ?>&Action=Update">
<table>
  <tr id="ShowPrice">
    <td><label for="Deposit">Project Price $:</label></td>
	<td><?php echo $intLastDeposit; ?> <a href="#ShowPrice" onClick="$('#EditPrice').show(); $('#ShowPrice').hide(); ">Edit</a></td>
  </tr>
  <tr id="EditPrice" style="display: none;">
    <td><label for="Deposit">Project Price $:</label></td>
	<td><input type="text" name="Deposit" id="Deposit" value="<?php echo $intLastDeposit; ?>"/></td>
  </tr>
  <tr>
    <td><label for="Terms">Project Terms:</label></td>
	<td><textarea name="Terms" id="Terms" cols="40" rows="4"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Submit"/></td>
  </tr>
</table>
</form>

<?php } ?>

<?php include('templates/footer.php'); ?>