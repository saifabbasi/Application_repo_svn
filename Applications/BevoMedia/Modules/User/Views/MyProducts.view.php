<div id="pagemenu">
	<ul>
		<li class="haskids">
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
			<a href="/BevoMedia/User/ChangeProfile.html">My Account <img src="/Themes/BevoMedia/img_new/icon_arrsmall_down_w.png" alt="" /><span></span></a>
<?php 
	} else
	{
?>
			<a href="/BevoMedia/User/CreditCard.html">My Account <img src="/Themes/BevoMedia/img_new/icon_arrsmall_down_w.png" alt="" /><span></span></a>
<?php 
	}
?>
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
				<li><a href="/BevoMedia/User/Invoice.html">Billing</a></li>
				
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
				<li><a href="/BevoMedia/User/ChangePassword.html" rel="shadowbox;width=320;height=200;player=iframe" title="Change Password">Change Password</a></li>
				<li><a href="/BevoMedia/User/CancelAccount.html" rel="shadowbox;width=480;height=250;player=iframe" title="Cancel Bevo Account">Cancel Account</a></li>
<?php 
	}
?>
			</ul>
		</li>
		<li><a class="active" href="/BevoMedia/User/AppStore.html">My Apps<span></span></a></li>
		
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
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
?>
	
	<table width="80%" style="margin-left: auto; margin-right: auto;">
		<tr style="text-align: left; height: 20px;">
			<th>Product</th>
			<th width="90">Price</th>
			<th width="180">&nbsp;</th>
			
		</tr>

	<?php 
		foreach ($this->Products as $Product) {
	?>
		<tr>
			<td><?php echo $Product->ProductName?></td>
			<td>$<?php echo number_format($Product->Price, 2)?></td>
			<td>
			<?php 
				if ( (stristr($Product->ProductName, 'ppvspy')) || (stristr($Product->ProductName, 'adscout')) ) {
			?>
				
				<?php 
					if (strtotime($Product->Date)>time()) {
						echo "Feature not active yet.";
					} else {
						if ($Product->Cancelled==1) {
							echo "Subscription cancelled.";
						} else {
							if (($Product->ProductName!=User::PRODUCT_PPVSPY_YEARLY) && ($Product->ProductName!=User::PRODUCT_ADWATCHER_YEARLY)) {
				?>					
					<a href="/BevoMedia/User/MyProducts.html?ID=<?php echo $Product->ID?>" onclick="return confirm('Are you sure you want to stop your subscription?'); ">Cancel feature</a>
				<?php 
							} else {
								echo 'This feature is a one time fee';	
							}
						}
					}
				?>
				
				
				
				<?php 
					if ( ($Product->TermLength==30) && !$this->User->IsSubscribed(User::PRODUCT_PPVSPY_YEARLY) &&
						(stristr($Product->ProductName, 'ppvspy'))
					) {
				?>				
				<a href="/BevoMedia/User/MyProducts.html?UpgradeID=<?php echo $Product->ID?>" onclick="return confirm('Are you sure you want to change your subscription to lifetime?'); ">Upgrade to lifetime for $999</a>
				<?php 
					}
				?>
				
				<?php 
					if ( ($Product->TermLength==30) && !$this->User->IsSubscribed(User::PRODUCT_ADWATCHER_YEARLY) &&
						(stristr($Product->ProductName, 'adscout'))
					) {
				?>				
				<a href="/BevoMedia/User/MyProducts.html?UpgradeAdWatcherID=<?php echo $Product->ID?>" onclick="return confirm('Are you sure you want to change your subscription to lifetime?'); ">Upgrade to lifetime for $399</a>
				<?php 
					}
				?>
				
			<?php 
				} else {
			?>
				This feature is a one time fee
			<?php 	
				}
			?>
			</td>
		</tr>
	<?php 
		}		
	?>
	
	 </table>
	 
	 
	 
