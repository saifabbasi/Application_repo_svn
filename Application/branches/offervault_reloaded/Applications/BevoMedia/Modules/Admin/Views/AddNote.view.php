<form style='width:100%; height:210px;' method='post'>
	<textarea name='Note' class='width100Pct height90Pct' rows='6'></textarea>
	<input type='hidden' name='User_ID' value='<?php print $this->User_ID; ?>'/>
	<input type='submit' name='addNoteSubmit'/>
</form>