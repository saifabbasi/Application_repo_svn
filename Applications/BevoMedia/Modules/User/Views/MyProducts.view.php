
	<?php /* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<?php /*<li><a class="active" href="/BevoMedia/User/AccountInformation.html">My Account<span></span></a></li> */ ?>
			<li><a title='Change your Bevo Profile' href='/BevoMedia/User/ChangeProfile.html'>My Profile<span></span></a></li>
			<?php if($this->User->vaultID > 0) { //if verified, show CC page here
			?>
				<li><a href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/CreditCard.html">My Payment Options<span></span></a></li>
			<?php } ?>
			<li><a class="active" title='My Products' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/MyProducts.html'>My Products<span></span></a></li>
			<li><a title='View PPC Accounts' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/Index.html#PPC'>My PPC Accounts<span></span></a></li>
			<li><a rel="shadowbox;width=320;height=200;player=iframe" title='Change Bevo Password' href='ChangePassword.html'>My Password<span></span></a></li>
			<li><a rel="shadowbox;width=480;height=250;player=iframe" title='Cancel Bevo Account' href='CancelAccount.html'>Cancel Account<span></span></a></li>
			<li><a title='Billing' href='Invoice.html'>Billing<span></span></a></li>
			<?php /*<li><a title='Referrals' href='Referrals.html'>Referrals<span></span></a></li>*/?>
		</ul>
		
		<ul class="floatright">
			<li><a title='View PPC Accounts' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/Index.html#PPC'>My PPC Accounts<span></span></a></li>
		</ul>
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
				if (stristr($Product->ProductName, 'ppvspy')) {
			?>
				
				<?php 
					if (strtotime($Product->Date)>time()) {
						echo "Feature not active yet.";
					} else {
						if ($Product->Cancelled==1) {
							echo "Subscription cancelled.";
						} else {
							if ($Product->ProductName!=User::PRODUCT_PPVSPY_YEARLY) {
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
					if ( ($Product->TermLength==30) && !$this->User->IsSubscribed(User::PRODUCT_PPVSPY_YEARLY) ) {
				?>				
				<a href="/BevoMedia/User/MyProducts.html?UpgradeID=<?php echo $Product->ID?>" onclick="return confirm('Are you sure you want to change your subscription to lifetime?'); ">Upgrade to lifetime for $999</a>
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
	 
	 
	 
