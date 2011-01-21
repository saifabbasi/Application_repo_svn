<? /*<div id="pagemenu">
	<ul>
		<li><a class="active" title='Change your Bevo Profile' href='/BevoMedia/User/ChangeProfile.html'>My Profile<span></span></a></li>
		<?php if($this->User->vaultID > 0) { //if verified, show CC page here
		?>
			<li><a href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/CreditCard.html">My Payment Options<span></span></a></li>
		<?php } ?>
		<li><a title='My Products' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/MyProducts.html'>My Products<span></span></a></li>
		<li><a title='View PPC Accounts' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/Index.html#PPC'>My PPC Accounts<span></span></a></li>
		<li><a rel="shadowbox;width=320;height=200;player=iframe" title='Change Bevo Password' href='ChangePassword.html'>My Password<span></span></a></li>
		<li><a rel="shadowbox;width=480;height=250;player=iframe" title='Cancel Bevo Account' href='CancelAccount.html'>Cancel Account<span></span></a></li>
		<li><a title='Billing' href='Invoice.html'>Billing<span></span></a></li>
	</ul>
	
	<?php if($this->User->vaultID == 0) { //if UNverified, show verify link on right
		?>
		<ul class="floatright">
			<li><a title='Verfiy My Account' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/AddCreditCard.html'><strong>Verify Account Now</strong><span></span></a></li>
		</ul>
	<?php } ?>
</div>*/ ?>

<div id="pagemenu">
	<ul>
		<li><a class="active" href='/BevoMedia/User/ChangeProfile.html'>My Account<span></span></a></li>
		<li><a href="/BevoMedia/User/MyProducts.html">My Apps<span></span></a></li>
		<li><a href="BevoMedia/Publisher/Index.html#PPC">My PPC Accounts<span></span></a></li>
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
		<li><a class="active" href="/BevoMedia/User/ChangeProfile.html">My Profile</a></li>
		<?php if($this->User->vaultID > 0) { ?>
			<li><a href="/BevoMedia/User/CreditCard.html">My Payment Options</a></li>
		<?php } ?>
		<li><a href="/BevoMedia/User/Invoice.html">Billing</a></li>
		<li><a href="/BevoMedia/User/ChangePassword.html" rel="shadowbox;width=320;height=200;player=iframe" title="Change Password">Change Password</a></li>
		<li><a href="/BevoMedia/User/CancelAccount.html" rel="shadowbox;width=480;height=250;player=iframe" title="Cancel Bevo Account">Cancel Account</a></li>
	</ul>
</div>

<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
?>

<?php if($this->Message == 'ACCOUNT_UPDATED'):?><h3>Account Updated</h3><?php endif?>
<br/>

<div class="clear"></div>


<form method="post" 
	name="registerForm" 
	class="appform registerForm" 
	onSubmit="javascript:return registerFormValidation.validateForm('ChangeProfile');">

	<label for="firstname_id">
		<span class="label">First Name:</span>
		<input class="formtxt" type="text" name="FirstName" value="<?php print $this->User->firstName; ?>" id="firstname_id" />
		<span class="validation" id="firstname_validation_id">INVALID</span>
	</label>
	
	<label for="lastname_id">
		<span class="label">Last Name:</span>
		<input class="formtxt" type="text" name="LastName" value="<?php print $this->User->lastName; ?>" id="lastname_id" />
		<span class="validation" id="lastname_validation_id">INVALID</span>
	</label>
	
	<label for="companyname_id">
		<span class="label">(Optional) Company Name:</span>
		<input class="formtxt" type="text" name="CompanyName" value="<?php print $this->User->companyName; ?>" id="companyname_id" />
	</label>

	<label for="address_id">
		<span class="label">Address:</span>
		<input class="formtxt" type="text" name="Address" value="<?php print $this->User->address; ?>" id="address_id" />
		<span class="validation" id="address_validation_id">INVALID</span>
	</label>

	<label for="city_id">
		<span class="label">City:</span>
		<input class="formtxt" type="text" name="City" value="<?php print $this->User->city; ?>" id="city_id" />
		<span class="validation" id="city_validation_id">INVALID</span>
	</label>

	<label for="state_id">
		<span class="label">State:</span>
		<input class="formtxt" type="text" name="State" value="<?php print $this->User->state; ?>" id="state_id" />
		<span class="validation" id="state_validation_id">INVALID</span>
	</label>

	<label for="zip_id">
		<span class="label">Zip:</span>
		<input class="formtxt wide_number" type="text" name="Zip" value="<?php print $this->User->zip; ?>" id="zip_id" />
		<span class="validation" id="zip_validation_id">INVALID</span>
	</label>

	<label for="country_id">
		<span class="label">Country:</span>
		<input class="formtxt" type="text" name="Country" value="<?php print $this->User->country; ?>" id="country_id" />
		<span class="validation" id="country_validation_id">INVALID</span>
	</label>

	<label for="phone_id">
		<span class="label">Phone Number:</span>
		<input class="formtxt" type="text" name="Phone" value="<?php print $this->User->phone; ?>" id="phone_id" />
		<span class="validation" id="phone_validation_id">INVALID</span>
	</label>

	<label for="website_id">
		<span class="label">(Optional) Primary Website:</span>
		<input class="formtxt" type="text" name="Website" value="<?php print $this->User->website; ?>" id="website_id" />
	</label>
	
	<label>
		<span class="label">(Optional) Instant Messenger Service:</span>
		<div class="radioFloat">
			<?php $Messengers = PageHelper::GetMessengersViewLabels()?>
			<?php foreach($this->PageHelper->GetMessengers() as $Messenger):?>
				<input <?php print ($this->User->messenger==$Messenger)?'checked="checked"':''; ?> type="radio" name="Messenger" value="<?php echo $Messenger?>"><?php echo $Messengers[$Messenger]?><br/>
			<?php endforeach?>
		</div>
	</label>
	
	<label for="messengerhandle_id">
		<span class="label">(Optional) Messenger Handle:</span>
		<input class="formtxt" type="text" name="MessengerHandle" value="<?php print $this->User->messengerHandle; ?>" id="messengerhandle_id" />
	</label>
	
	<label>
		<span class="label">(Optional) Marketing Method:</span>
		<div class="radioFloat">
			<?php $Methods = PageHelper::GetMarketingMethodsViewLabels()?>
			<?php foreach($this->PageHelper->GetMarketingMethods() as $Method):?>
				<input <?php print ($this->User->marketingMethod==$Method)?'checked="checked"':''; ?> type="radio" name="MarketingMethod" value="<?php echo $Method?>"><?php echo $Methods[$Method]?><br/>
			<?php endforeach?>
		</div>
	</label>
	
	<label for="marketingmethodother_id">
		<span class="label">(Optional) Marketing Method:</span>
		<input class="formtxt" type="text" name="MarketingMethodOther" value="<?php print $this->User->marketingMethodOther; ?>" id="marketingmethodother_id" />
	</label>
	
	<label for="howheard_id">
		<span class="label">How Heard:</span>
		<input class="formtxt" type="text" name="HowHeard" value="<?php print $this->User->howHeard; ?>" id="howheard_id" />
		<span class="validation" id="howheard_validation_id">INVALID</span>
	</label>
	
	<label for="comments_id">
		<span class="label">(Optional) Additional Comments:</span>
		<textarea class="formtxtarea" name="Comments" id="comments_id"><?php print $this->User->comments; ?></textarea>
	</label>
	
	<label for="timezone_id">
		<span class="label">(Optional) Timezone:</span>
		<select class="formselect" name="Timezone" id="timezone_id">
		<?php $tz = new TimezoneHelper()?>
		<?php foreach($tz->getTimezones() as $timezone):?>
			<option <?php echo($timezone->PHPTimezone == $this->User->timezone)?'selected="SELECTED"':''?> value="<?php print $timezone->PHPTimezone; ?>"><?php print $timezone->GMTLabel; ?></option>
		<?php endforeach?>
		</select>
	</label>
	
	
	<br/>
	
	<input class="formsubmit tbtn" type='submit' name='changeProfileFormSubmit' value="Apply Changes" />
	

</form>