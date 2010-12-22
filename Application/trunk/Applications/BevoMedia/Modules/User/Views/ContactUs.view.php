<div id="pageinfo" class="sub">
	<h2>Contact Us</h2>	
</div>

<h5><?php if($this->Message == 'ACCOUNT_UPDATED'):?>Account Updated<?php endif?>&nbsp;</h5>

<br/>

<div class="clear"></div>


<div style="text-align: center">
<?
	if ($this->Status == 'SENT')
	{
		echo "Message sent.";
		return;
	}
?>
</div>

<form method="post">

	<table width="100%">
		<tr>
			<td>Subject:</td>
			<td>
				<input type="text" name="Subject" value="" id="Subject" size="35" />
			</td>
		</tr>
		<tr valign="top">
			<td>Message:</td>
			<td>
				<textarea name="Message" value="" id="Message_id" cols="30" rows="10"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="Submit" value="Submit" />
			</td>
		</tr>
		
	</table>
	
	
</form>

