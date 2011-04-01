<h1>
	Publisher CSV Export
</h1>

<form method='post'>

<br/>
<b>Status:</b>
<br/>

<input checked='checked' type='radio' name='status' value='active'/>Active<br/>
<input type='radio' name='status' value='deleted'/>Deleted<br/>

<br/>

<b>Self Hosted:</b>
<br/>
<input checked='checked' type='radio' name='self-hosted' value='no'/>Not Self Hosted<br/>
<input type='radio' name='self-hosted' value='premium'/>Premium<br/>
<input type='radio' name='self-hosted' value='deluxe'/>Deluxe<br/>

<br/>

<input type='submit' />

</form>

<?php if(isset($this->output)):?>

<textarea style='width:100%;' rows='20' wrap='off'><?php echo $this->output?></textarea>

<?php endif?>