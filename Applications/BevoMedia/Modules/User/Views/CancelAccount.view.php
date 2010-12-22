<img src='/Themes/BevoMedia/img/info.gif' />
<br/>
<?php if($this->Message === false):?>
	This will cancel your account with BeVo.<br/>
	Once cancelled, you will not be able to login as a publisher.<br/>
	<br/>
	Are you sure you want to continue?<br/>
	<i>(You can later ask the administrator to re-activacte your account.)</i>
	<br/><br/>
	
	<form method='post'>
		<input type='submit' name='cancelAccountCancelSubmit' value='Cancel My Account' /><br/>
		<input type='submit' name='cancelAccountNevermindSubmit' value='Nevermind' />
	</form>
<?php elseif($this->Message == 'THANKS'):?>
	<br/>
	You have decided to stay with BeVo!
	<br/><br/>
	Thanks!  We appreciate your continued membership on our site!
	<br/><br/>
	
	<a href='#' onClick='javascript:parent.Shadowbox.close();'>Close Window</a>
	<br/><br/>
	<div id='LayoutAssist_Shadowbox_Close_Timer'></div>
	<script language="Javascript">
		LayoutAssist.shadowboxCloseTimer(4);
	</script>
<?php else:?>
	<br/>
	Your BeVo account has been cancelled.
	<br/><br/>
	But if you want to come back, your account will be waiting for you!<br/>
	Just send a message to an administrator to reactivate your account.<br/>
	<br/>
	
	<a href='#' onClick='javascript:LayoutAssist.parentLocation("/");'>Close Window</a>
	<br/><br/>
	<div id='LayoutAssist_Shadowbox_Close_Timer'></div>
	<script language="Javascript">
		LayoutAssist.parentLocationTimer(3.75, '/');
		LayoutAssist.shadowboxCloseTimer(4);
	</script>
<?php endif?>