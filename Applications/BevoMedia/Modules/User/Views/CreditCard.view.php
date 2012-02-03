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
			<li><a class="active" href="/BevoMedia/User/CreditCard.html">My Payment Options</a></li>
		<?php } ?>
		<li><a href="/BevoMedia/User/Invoice.html">Billing</a></li>
		
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
	        
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
	?>


<?php 
		if (isset($_GET['Error']))
		{
?>
		<div style="border: 1px #E85163 solid; background-color: #F5AEB6; width: 100%; height: 45px; line-height: 45px; color: #000; text-align: center;">
			<?php echo $_GET['Error'];?>
		</div>
		<br />
<?php 
		}
?>


<?php 
		if (isset($_GET['RemovedSuccess']))
		{
?>
		<div style="border: 1px #78C773 solid; background-color: #BEFFBA; width: 100%; height: 45px; line-height: 45px; color: #000; text-align: center;">
			Your credit card was removed successfully.
		</div>
		<br />
<?php 
		}
?>



	<br />

	<div>
		<?php 
			if ($this->User->vaultLast4Digits>0) {
		?>
		You have Credit Card on file ending with <?php echo $this->User->vaultLast4Digits ?>.
		<?php 
			} else {
		?>
		You do not have any credit cards on file.
		<?php 
			}
		?>
	</div>
	
	<br />
	
	<?php 
		if ($this->User->vaultLast4Digits>0) {
	?>
	<a href="/BevoMedia/User/AddCreditCard.html">Update Credit Card</a>
	|
	<a href="/BevoMedia/User/DeleteCreditCard.html" onclick="return confirm('Are you sure you want to remove your credit card?');">Delete Credit Card</a>
	<?php
		} else {
	?>
	<a href="/BevoMedia/User/AddCreditCard.html">Add Credit Card</a>
	<?php 
		} 
	?>
	
	
