<?php if($this->Message == 'TICKET_ADDED'):?>
	Your ticket has been submitted successfully!
	<br/><br/>
	Thanks!
	<br/><br/>
	An admin will review this ticket and take appropriate action soon.
	<br/><br/>
	
	<a href='#' onClick='javascript:parent.Shadowbox.close();'>Close Window</a>
	<br/><br/>
	<div id='LayoutAssist_Shadowbox_Close_Timer'></div>
	<script language="Javascript">
		LayoutAssist.shadowboxCloseTimer(4);
	</script>
<?php else:?>
<form method='post'>

<table width='100%'>

<tr>
	<td class='textAlignRight'>
		Your Name:
	</td>
	<td class='textAlignLeft'>
		<input type='text' disabled="disabled" value='<?php print $this->User->firstName; ?> <?php print $this->User->lastName; ?>'/>
	</td>
	<td rowspan='3'>
		<img src='/Themes/BevoMedia/img/info.gif' />
	</td>
</tr>
<tr>
	<td class='textAlignRight'>
		Your Email:
	</td>
	<td class='textAlignLeft'>
		<input type='text' disabled="disabled" value='<?php print $this->User->email; ?>'/>
	</td>
</tr>
<tr>
	<td class='textAlignRight'>
		Subject:
	</td>
	<td class='textAlignLeft'>
		<input type='text' name='Subject'/>
	</td>
</tr>
<tr>
	<td class='textAlignRight'>
		Problem:
	</td>
	<td colspan='2'>
		<textarea style="width:100%;" name='Problem' rows='5'></textarea>
	</td>
</tr>

<tr><td colspan='3'><input type='submit' name='submitTicketSubmit' /></td></tr>
</table>
</form>
<?php endif?>
