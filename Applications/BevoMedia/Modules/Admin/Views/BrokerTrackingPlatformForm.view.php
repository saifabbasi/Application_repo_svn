
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

<a href="/BevoMedia/Admin/BrokerTrackingPlatforms.html">&lt;- Tracking Platforms</a>

<br /><br />

<form method="post">
	<label for="Name">Name:</label>
	<input type="text" id="Name" name="Name" value="<?=isset($this->TrackingPlatform->Name)?$this->TrackingPlatform->Name:''?>" />
	<br /><br />
	
	<input type="submit" name="Save" value="Save" />
</form>
