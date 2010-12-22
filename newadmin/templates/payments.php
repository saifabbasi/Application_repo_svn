<?php include('templates/header.php'); ?>

<?php include('templates/marketheader.php'); ?>

<h1>Payments Owed</h1>

<table class="ListingTable">
  <tr class="HeaderRow">
	<td>Provider</td>
	<td>PayPal</td>
	<td align="center">Revenue</td>
	<td align="center">Bevo Cut</td>
	<td align="center">Gross</td>
  </tr>
<?php ListPayments(); ?>
</table>

<h2>PayPal Mass Pay</h2>

<p>
<textarea cols="100" rows="6" readonly="readonly"  onclick="this.focus(); this.select">
<?php OutputMassPay(); ?>
</textarea>
</p>

<form method="post" action="payments.php?Action=MarkPaid">
<?php

foreach ($arrPayments as $arrThisPayment) {
	echo '<input type="hidden" name="ID[]" value="' . $arrThisPayment['providerId'] . '"/>';
	echo '<input type="hidden" name="Payment[]" value="' . ($arrThisPayment['netPayment'] - ($arrThisPayment['netPayment'] * .15)) . '"/>';
}

?>
<p align="center"><input type="submit" value="Mark Paid"/></p>

</form>

<h1>Pending Payments</h1>

<table class="ListingTable">
  <tr class="HeaderRow">
	<td>Provider</td>
	<td>PayPal</td>
	<td align="center">Revenue</td>
	<td align="center">Bevo Cut</td>
	<td align="center">Gross</td>
  </tr>
<?php ListPayments2(); ?>
</table>

<h1>Paid</h1>

<table class="ListingTable">
  <tr class="HeaderRow">
	<td>Provider</td>
	<td align="center">Amount</td>
	<td align="center">Date</td>
  </tr>
<?php ListPaid(); ?>
</table>

<?php include('templates/footer.php'); ?>
