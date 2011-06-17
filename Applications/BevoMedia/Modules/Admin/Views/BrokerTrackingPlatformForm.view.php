
<?=$this->TopMenu?>

<style>
<!--
	#brokerNetworkForm label
	{
		width: 110px;
		display: inline-block;
	}
-->
</style>


<br /><br />

<form method="post">
	<label for="Name">Name:</label>
	<input type="text" id="Name" name="Name" value="<?=isset($this->TrackingPlatform->Name)?$this->TrackingPlatform->Name:''?>" />
	<br />
	<label for="Enabled">Enabled:</label>
	<input type="checkbox" id="Enabled" name="Enabled" value="1" <?=(isset($this->TrackingPlatform->Enabled) && ($this->TrackingPlatform->Enabled==1))?'checked':''?> />
	<br /><br />
	
	<input type="submit" name="Save" value="Save" />
</form>
