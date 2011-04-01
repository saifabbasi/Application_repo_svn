<form method='post' class='addMentorForm'>
	<label>Name:
		<input type='text' name='Name' value='<?php print $this->Mentor->name; ?>' />
	</label>
	<br/>
	
	<label>Email:
		<input type='text' name='Email' value='<?php print $this->Mentor->email; ?>' />
	</label>
	<br/>
	
	<label>Phone:
		<input type='text' name='Phone'  value='<?php print $this->Mentor->phone; ?>'/>
	</label>
	<br/>
	
	<label>AIM:
		<input type='text' name='AIM'  value='<?php print $this->Mentor->aim; ?>'/>
	</label>
	
	<br/><br/>
	
<?php if(!isset($this->Mentor->id)):?>
	<input type='submit' name='addMentorSubmit'/>
<?php else:?>
	<input type='submit' name='editMentorSubmit' value='Update' />
<?php endif?>
</form>