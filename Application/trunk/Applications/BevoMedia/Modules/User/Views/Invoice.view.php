<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>

<div id="pagemenu">
	<ul>
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
		<li><a class="active" href='/BevoMedia/User/ChangeProfile.html'>My Account<span></span></a></li>
<?php 
	}
?>
		<li><a href="/BevoMedia/User/AppStore.html">My Apps<span></span></a></li>
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
		<li><a href="/BevoMedia/Publisher/Index.html#PPC">My PPC Accounts<span></span></a></li>
<?php 
	}
?>
	</ul>
	
	<?php if($this->User->vaultID == 0) { //if UNverified, show verify link on right
		?>
		<ul class="floatright">
			<li><a title='Verfiy My Account' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/AddCreditCard.html'><strong>Verify Account Now</strong><span></span></a></li>
		</ul>
	<?php } ?>
</div>
<div id="pagesubmenu">
	<ul>
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
		<li><a href="/BevoMedia/User/ChangeProfile.html">My Profile</a></li>
<?php 
	}
?>
		<?php if($this->User->vaultID > 0) { ?>
			<li><a href="/BevoMedia/User/CreditCard.html">My Payment Options</a></li>
		<?php } ?>
		
		<li><a class="active" href="/BevoMedia/User/Invoice.html">Billing</a></li>
		
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
		<li><a href="/BevoMedia/User/Referrals.html">Referrals</a></li>
		<li><a href="/BevoMedia/User/ChangePassword.html" rel="shadowbox;width=320;height=200;player=iframe" title="Change Password">Change Password</a></li>
<?php 
	}
?>
	</ul>
</div>

<?php 
	}
?>

<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
?>

	
	<form method="get" action="" name="frmRange">
	<p align="right">
	<table align="right" cellspacing="0" cellpadding="0" class="datetable">
	  <tr>
	    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php print isset($_GET['DateRange'])?$_GET['DateRange']:$this->defaultDateRange; ; ?>" /></td>
		<td><input class="formsubmit" type="submit" /></td>
	  </tr>
	</table>
	</p>
	</form>
	
	<br /><br /><br />
	
	<table width="80%" style="margin-left: auto; margin-right: auto;">
		<tr style="text-align: left; height: 20px;">
			<th>Product</th>
			<th width="90">Amout</th>
			<th width="90">Date</th>
			<th width="90">Process Date</th>
			<th width="120">&nbsp;</th>
		</tr>
		
	<?php 
		foreach ($this->Payments as $Payment)
		{
	?>
		<tr style="height: 20px;">
			<td><?php echo $Payment->ProductName?></td>
			<td>$<?php echo $Payment->Price?></td>
			<td><?php echo date('m/d/Y', strtotime($Payment->Date))?></td>
			<td><?php echo ($Payment->PaidDate!='0000-00-00 00:00:00')?date('m/d/Y', strtotime($Payment->PaidDate)):'Not processed'?></td>
			<td>
			<?php 
				if ( ($Payment->PaidDate!='0000-00-00 00:00:00') && ($Payment->RequestedInvoice==0) )
				{
			?>
				<a href="/BevoMedia/User/Invoice.html?<?php echo isset($_GET['DateRange'])?'DateRange='.$_GET['DateRange'].'&':''?>Invoice=<?php echo $Payment->ID?>">Request an invoice</a>
			<?php 	
				} else 
				if ($Payment->RequestedInvoice==1) {
			?>
				Invoice requested
			<?php 	
				}
			?>
			</td>
		</tr>
	<?php 
		}
	?>
		
	<?php 
		if (count($this->Payments)==0)
		{
	?>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4" style="text-align: center;">
				There is no billing information for the selected date range.
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	<?php 
		}
	?>
		
	</table>
	