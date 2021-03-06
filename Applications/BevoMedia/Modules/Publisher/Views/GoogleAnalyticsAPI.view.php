<?php 
    if ( ($this->User->getVaultID()==0) && (Zend_Registry::get('Instance/Function')!='AddCreditCard') )
	{
		echo '<script type="text/javascript"> window.location = "/BevoMedia/Publisher/Verify.html?ajax=true"; </script>';
		die;
	}		
?>

<script language="javascript" src="/Themes/BevoMedia/jquery.js"></script>
<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>
<style type="text/css">
#tooltip{
	line-height: 1.231; font-family: Arial; font-size: 13px;
	position:absolute;
	border:1px solid #333;
	background:#f7f5d1;
	padding:2px 5px;
	display:none;
	width:285px;
	}
.tooltip {
	color: #ffffff;
	text-decoration: none !important;
	font-weight: bold;
	font-size: 12pt;
	}
.tooltip.defaultLink {
	color: maroon;
	font-size: 12px;
	font-style: normal;
	font-weight: normal;
	font-size: 12px;
	}
.successInstall {
	background-color: #008800;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
.failInstall {
	background-color: #880000;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
	a { font-size: 12px }
</style>

<img src='/Themes/<?php print $this->PageHelper->Application; ?>/img/analytics.jpg' />

<br/><br/>

<?php if($this->InstallSuccess):?>
<div class="successInstall">
		<b><u>Your account has been successfully added.</u></b>
		<br/> Please allow up to 24 hours for your account to
                    be fully loaded on the Bevo interface
</div>
<?php endif?>

<?php if(isset($this->Verified)):?>
	<?php if($this->Verified == true):?>
	<div class="successInstall">
			<b><u>Your account has been successfully verified</u></b>
			<br/>The API has authenticated the account and can now be used on the Bevo interface
	</div>
	<?php endif?>
	<?php if($this->Verified == false):?>
	<div class="failInstall">
			<b><u>Your account could not be verified.</u></b>
			<br/>The API was not able to successfully authenticate your account
	</div>
	<?php endif?>
<?php endif?>

<table class='shadowbox-table <?php print ($this->editEmail)?('displayNone'):(''); ?>'>
	<tr>
		<th colspan='5'>
			<a class="tooltip" title="These accounts are installed and ready to be used for API related functions.  Installed accounts will be used for daily API updates unless disabled.">
				Installed Accounts
				<img src="/Themes/BevoMedia/img/questionMarkIcon.png" width="12" height="12" />
			</a>
		</th>
	</tr>
	
	<?php if(sizeOf($this->InstalledAccounts)):?>
	<?php foreach($this->InstalledAccounts as $Account):?>
	<tr>
		<td class='textAlignLeft'>
			<?php echo $Account->username?>
		</td>
		<td class='textAlignCenter'>
			<?php if($Account->verified):?>
				<a href='?VerifyEmail=<?php print $Account->id; ?>' class='tooltip defaultLink' style="text-decoration: none; color: #0000000;" title="This account has been successfully<br/> verified using the API.<br/><br/>You may re-verify by clicking<br/> this link again.">Verified</a>
			<?php else:?>
				<a class='tooltip defaultLink' title="Attempt to authenticate this<br/> account using the Analytics API." href='?VerifyEmail=<?php print $Account->id; ?>'>
					Verify
				</a>
			<?php endif?>
		</td>
		<td class='textAlignRight'>
			<a href='?DisableDailyUpdate=<?php print $Account->id; ?>'>
				Disable Daily Updates
			</a>
		</td>
		<td class='textAlignRight'>
			<a href='?EditEmail=<?php print $Account->id; ?>'>
				Edit
			</a>
		</td>
		<td class='textAlignRight'>
			<a href='?DeleteEmail=<?php print $Account->id; ?>'>
				Delete
			</a>
		</td>
	</tr>
	<?php endforeach?>
	<?php else:?>
	<tr>
		<td colspan='5'>
			<i>No Installed Accounts</i>
		</td>
	</tr>
	
	<?php endif?>
	<tr>
		<td colspan='5'>
			&nbsp;
		</td>
	</tr>
	
	<tr>
		<th colspan='5'>
			<a class="tooltip" title="These accounts are specifically disabled and will not be used for daily API updates.">
				Disabled Accounts
				<img src="/Themes/BevoMedia/img/questionMarkIcon.png" width="12" height="12" />
			</a>
		</th>
	</tr>
	
	<?php if(sizeOf($this->DisabledAccounts)):?>
	<?php foreach($this->DisabledAccounts as $Account):?>
	<tr>
		<td class='textAlignLeft'>
			<?php echo $Account->username?>
		</td>
		<td class='textAlignRight'>
			<a href='?EnableDailyUpdate=<?php print $Account->id; ?>'>
				Enable Daily Updates
			</a>
		</td>
		<td>
			&nbsp;
		</td>
		<td class='textAlignRight'>
			<a href='?EditEmail=<?php print $Account->id; ?>'>
				Edit
			</a>
		</td>
		<td class='textAlignRight'>
			<a href='?DeleteEmail=<?php print $Account->id; ?>'>
				Delete
			</a>
		</td>
	</tr>
	<?php endforeach?>
	<?php else:?>
	<tr>
		<td colspan='5'>
			<i>No Disabled Accounts</i>
		</td>
	</tr>
	
	<?php endif?>
	<tr>
		<td colspan='5'>
			&nbsp;
		</td>
	</tr>
</table>
	


<table class='shadowbox-table <?php print ($this->editEmail)?('displayNone'):(''); ?>'>
	<tr>
		<th colspan='5'>
			Add Account
		</th>
		</tr>	<form method='post' action='?<?=$this->isApiStr?>'>
	<tr>
		<td class='textAlignLeft'>
			Email:
		</td>
		<td colspan='3' class='textAlignLeft width80Pct'>
			<input type='text' name='Username' value='<?php print $this->usernameFormValue; ?>' />
			<?php if($this->UsernameInvalid):?><span class='validation'>Please provide a valid username.</span><?php endif?>
		</td>
	</tr>
	
	<tr>
		<td class='textAlignLeft'>
			Password:
		</td>
		<td colspan='3' class='textAlignLeft width80Pct'>
			<input type='password' name='Password' value='<?php print $this->passwordFormValue; ?>' />
			<?php if($this->PasswordInvalid):?><span class='validation'>Please provide a valid password.</span><?php endif?>
		</td>
	</tr>
	
	<tr>
		<td colspan='5' class='textAlignLeft'>
			<input type='submit' name='analyticsAddAccountSubmit' />
		</td>
	</tr>
	</form>
</table>


<table class='shadowbox-table <?php print ($this->editEmail)?(''):('displayNone'); ?>'>
	<tr>
		<th colspan='5'>
			Edit Account
		</th>
	</tr>
	<form method='post'>
	<input type='hidden' name='id' value='<?php print $this->idFormValue; ?>' />
	<tr>
		<td class='textAlignLeft'>
			Email:
		</td>
		<td colspan='3' class='textAlignLeft width80Pct'>
			<input type='text' name='Username' value='<?php print $this->usernameFormValue; ?>' />
			<?php if($this->UsernameInvalid):?><span class='validation'>Please provide a valid username.</span><?php endif?>
		</td>
	</tr>
	
	<tr>
		<td class='textAlignLeft'>
			Password:
		</td>
		<td colspan='3' class='textAlignLeft width80Pct'>
			<input type='password' name='Password' value='<?php print $this->passwordFormValue; ?>' />
			<?php if($this->PasswordInvalid):?><span class='validation'>Please provide a valid password.</span><?php endif?>
		</td>
	</tr>
	
	<tr>
		<td colspan='5' class='textAlignLeft'>
			<input type='submit' name='analyticsEditAccountSubmit' />
		</td>
	</tr>
	</form>
</table>
<? if(!empty($_GET['apiKey'])) { ?>
  <br />
  <br />
  <h3>Add this network to my BevoLive account: <?=$this->User->email?></h3>
  You are entering these details on your Bevo Live account.<br />Changes will take up to an hour to appear in your selfhost version.

<? } ?>