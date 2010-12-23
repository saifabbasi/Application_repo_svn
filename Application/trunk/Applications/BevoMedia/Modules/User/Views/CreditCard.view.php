<?php /* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<?php /*<li><a class="active" href="/BevoMedia/User/AccountInformation.html">My Account<span></span></a></li> */ ?>
			<li><a title='Change your Bevo Profile' href='/BevoMedia/User/ChangeProfile.html'>My Profile<span></span></a></li>
			<?php if($this->User->vaultID > 0) { //if verified, show CC page here
			?>
				<li><a class="active" href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/CreditCard.html'">My Payment Options<span></span></a></li>
			<?php } ?>
			<li><a title='My Products' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/MyProducts.html'>My Products<span></span></a></li>
			<li><a title='View PPC Accounts' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/Index.html#PPC'>My PPC Accounts<span></span></a></li>
			<li><a rel="shadowbox;width=320;height=200;player=iframe" title='Change Bevo Password' href='ChangePassword.html'>My Password<span></span></a></li>
			<li><a rel="shadowbox;width=480;height=250;player=iframe" title='Cancel Bevo Account' href='CancelAccount.html'>Cancel Account<span></span></a></li>
			<li><a title='Billing' href='Invoice.html'>Billing<span></span></a></li>
			<?php /*<li><a title='Referrals' href='Referrals.html'>Referrals<span></span></a></li>*/?>
		</ul>
		
		<?php if($this->User->vaultID == 0) { //if UNverified, show verify link on right
			?>
			<ul class="floatright">
				<li><a title='Verfiy My Account' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/AddCreditCard.html'><strong>Verify Account Now</strong><span></span></a></li>
			</ul>
		<?php } ?>
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
	
	