<?php /* ################################################################################# OUTPUT ############################ */ ?>
<div id="pagemenu">
	<ul>
		<li><a class="active" href='/BevoMedia/User/ChangeProfile.html'>My Account<span></span></a></li>
		<li><a href="/BevoMedia/User/MyProducts.html">My Apps<span></span></a></li>
		<li><a href="/BevoMedia/Publisher/Index.html#PPC">My PPC Accounts<span></span></a></li>
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
		<li><a href="/BevoMedia/User/ChangeProfile.html">My Profile</a></li>
		<?php if($this->User->vaultID > 0) { ?>
			<li><a href="/BevoMedia/User/CreditCard.html">My Payment Options</a></li>
		<?php } ?>
		<li><a href="/BevoMedia/User/Invoice.html">Billing</a></li>
		<li><a class="active" href="/BevoMedia/User/Referrals.html">Referrals</a></li>
		<li><a href="/BevoMedia/User/ChangePassword.html" rel="shadowbox;width=320;height=200;player=iframe" title="Change Password">Change Password</a></li>
	</ul>
</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
	?>
	
	<?php 
		$url = 'http://beta.bevomedia.com/BevoMedia/User/Register.html/'.md5($this->User->id);
	?>
	
	Your referral url is: <a href="<?php echo $url?>"><?php echo $url;?></a> 
	
	<br /><br />
	
	<table width="400">
		<tr style="text-align: left;">
			<th>User</th>
			<th>Signed Up</th>
			<th width="75">Revenue</th>
		</tr>
		
	<?php 
		$totalRevenue = 0;
		$user = new User();
		foreach ($this->Referrals as $Referral)
		{
	?>
		<tr>
			<td><?php echo $Referral->id?></td>
			<td><?php echo date('m/d/Y', strtotime($Referral->Date));?></td>
			<td>$<?php echo number_format($Referral->Total, 2)?></td>
		</tr>
	<?php 
			$totalRevenue += number_format($Referral->Total, 2);
		}
		
		if (count($this->Referrals)==0) {
	?>
		<tr>
			<td colspan="3" style="text-align: center;">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: center;">There are any referrals made.</td>
		</tr>
	<?php 
		}
	?>	
		
	</table>
	
	<br /><br />
	
	<div>
		<b>Referral Terms:</b><br /><br />
		Total Referred $<?php echo number_format($totalRevenue, 2)?><br />
		Payments are made on are made upon request on NET30<br/>
		Minimum Payment $100<br />
		W9 Required.<br />
		Payment Via Check/Wire/Paypal<br />
		Refunded items or fraudulent purchases will not be credited.<br />
		Users recieve 10% commission of their referred user's payments to Bevo for 1 calendar year.<br/>
		You may request your referral payment by emailing <a href="referrals@bevomedia.com">referrals@bevomedia.com</a>.
	</div>
	
	
	