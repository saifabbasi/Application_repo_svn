<?php if($this->Message === false):?>
<form method='post' class='addMentorForm'>

	<label>Old Password:
	<input type='password' name='OldPassword' />
	</label>
	<br/>
	
	<label>New Password:
	<input type='password' name='NewPassword' />
	</label>
	<br/>
	
	<label>Re-Enter New Password:
	<input type='password' name='ReNewPassword' />
	</label>
	<br/>
	
	<br/>
	
	<input type='submit' name='changePasswordSubmit' value='Change Password' />
	
</form>
<?php elseif($this->Message == 'INVALID_PASS'):?>
<center>
	<br/><br/>
	<i>
	Please provide a valid password.
	</i>
	<br/><br/>
	<a href='ChangePassword.html'>Back</a>
</center>
<?php else:?>
<center>
	<br/><br/>
	<i>
	Password changed successfully.
	</i>
	<br/><br/>
	<a href='#' onClick='javascript:parent.Shadowbox.close();'>Close Window</a>
	<br/><br/>
	<div id='LayoutAssist_Shadowbox_Close_Timer'></div>
	<script language="Javascript">
		LayoutAssist.shadowboxCloseTimer(4);
	</script>
</center>
<?php endif?>