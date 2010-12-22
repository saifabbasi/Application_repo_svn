<h2 class='adminPageHeading'>Email Publishers</h2>

<form method='post' class='emailPublishers'>
<b>Subject:</b><br/>
<input type='text' name='subject' class='width100Pct' />

<br/>

<?php /*

<b>Recipients:</b>
	<a rel="shadowbox;width=480;height=270;player=iframe" title='Add Users to Recipient List' href='AddUsersToEmailPublishers.html'>
		Add
	</a>
	

<div class='floatRight'>
	<b>Method:</b>
	<label>
		<input checked="checked" type='radio' name='method' value='THIS_SET'>
		Only These Users
	</label>
	<label>
		<input type='radio' name='method' value='ALL_EXCEPT_SET'>
		All Users Except This Set
	</label>
</div>
<br/>



<input type='text' name='recipients' class='width100Pct' />

<br/>
*/ ?>

<b>Message:</b><br/>
<textarea rows='5' name='message' class='width100Pct'></textarea>

<br/>
<input type='submit' name='emailPublishersSubmit' />

<?php if($this->SuccessSent !== false):?>
<b>
	<?php print $this->successSent; ?> emails have been sent.
</b>
<?php endif?>
</form>
