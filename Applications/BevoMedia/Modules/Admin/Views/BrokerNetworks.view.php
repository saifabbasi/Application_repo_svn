
<?=$this->TopMenu?>

<a href="/BevoMedia/Admin/BrokerNetworkForm.html?ID=0">Insert</a>

<br /><br />

<table width="500">
	<tr>
		<th>Name</th>
		<th width="65">&nbsp;</th>
		<th width="65">&nbsp;</th>
	</tr>
<?php
	foreach ($this->BrokerNetworks as $BrokerNetwork)
	{
?>
	<tr>
		<td><?=$BrokerNetwork->Name?></td>
		<td>
			<a href="/BevoMedia/Admin/BrokerNetworkForm.html?ID=<?=$BrokerNetwork->ID?>">Edit</a>
		</td>
		<td>
		<?php 
			if ($BrokerNetwork->Enabled)
			{
		?>
			<a href="/BevoMedia/Admin/BrokerNetworks.html?DisableID=<?=$BrokerNetwork->ID?>">Disable</a>
		<?php 
			} else 
			{
		?>
			<a href="/BevoMedia/Admin/BrokerNetworks.html?EnableID=<?=$BrokerNetwork->ID?>">Enable</a>
		<?php 	
			}
		?>
		</td>
	</tr>
<?php 
	}
?>
</table>
