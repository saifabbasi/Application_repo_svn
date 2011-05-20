
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

<a href="/Bevomedia/Admin/BrokerNetworks.html">&lt;- Broker Networks</a>

<br /><br />

<form id="brokerNetworkForm" method="post">
	<label for="Username">Username:</label>
	<input type="text" id="Username" name="Username" value="<?=isset($this->BrokerNetwork->Username)?$this->BrokerNetwork->Username:''?>" />
	<br /><br />
	
	<label for="Password">Password:</label>
	<input type="text" id="Password" name="Password" value="<?=isset($this->BrokerNetwork->Password)?$this->BrokerNetwork->Password:''?>" />
	<br /><br />
	
	<label for="TrackingPlatformID">Tracking Platform:</label>
	<select id="TrackingPlatformID" name="TrackingPlatformID">
	<?php 
		foreach ($this->TrackingPlatforms as $TrackingPlatform)
		{
			$selected = '';
			if ($TrackingPlatform->ID==$this->BrokerNetwork->TrackingPlatformID) $selected = 'selected';
	?>
		<option value="<?=$TrackingPlatform->ID?>" <?=$selected?>><?=$TrackingPlatform->Name?></option>
	<?php 
		}

	?>
	</select>
	<br /><br />
	
	<label for="AffiliateNetworkID">Affiliate Network:</label>
	<select id="AffiliateNetworkID" name="AffiliateNetworkID">
	<?php 
		foreach ($this->AffiliateNetworks as $AffiliateNetwork)
		{
			$selected = '';
			if ($AffiliateNetwork->id==$this->BrokerNetwork->NetworkID) $selected = 'selected';
	?>
		<option value="<?=$AffiliateNetwork->id?>" <?=$selected?>><?=$AffiliateNetwork->title?></option>
	<?php 
		}

	?>
	</select>
	<br /><br />
	
	<label for="Name">Name:</label>
	<input type="text" id="Name" name="Name" value="<?=isset($this->BrokerNetwork->Name)?$this->BrokerNetwork->Name:''?>" />
	<br /><br />
	
	<label for="Email">Email:</label>
	<input type="text" id="Email" name="Email" value="<?=isset($this->BrokerNetwork->Email)?$this->BrokerNetwork->Email:''?>" />
	<br /><br />
	
	<label for="Phone">Phone:</label>
	<input type="text" id="Phone" name="Phone" value="<?=isset($this->BrokerNetwork->Phone)?$this->BrokerNetwork->Phone:''?>" />
	<br /><br />
	
	
	<input type="submit" name="Save" value="Save" />
</form>
