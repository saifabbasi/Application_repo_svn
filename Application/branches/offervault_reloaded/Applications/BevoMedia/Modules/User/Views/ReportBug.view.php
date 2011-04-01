<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/bob.style.css" rel="stylesheet" type="text/css" />

<?php
	if (isset($this->MessageSent) && ($this->MessageSent==true) )
	{
?>
	<div>
		Your bug has been submitted. Thanks for your help!
	</div>
<?
		return;
	}
?>


<form method="post">
	<div style="text-align: left;">
		<b>Bug Description:</b><br />
	</div>
	<textarea name="BugDescription" cols="73" rows="20"></textarea>
	
	<br /><br />
	
	
	
	<input type="submit" name="Send" value="Send" class="formSubmit" />
	
	
</form>