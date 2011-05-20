
<?=$this->TopMenu?>

<a href="/BevoMedia/Admin/BrokerTrackingPlatformForm.html?ID=0">Insert</a>

<br /><br />

<table width="500">
	<tr>
		<th>Name</th>
		<th width="100">&nbsp;</th>
	</tr>
<?php
	foreach ($this->TrackingPlatforms as $TrackingPlatform)
	{
?>
	<tr>
		<td><?=$TrackingPlatform->Name?></td>
		<td>
			<a href="/BevoMedia/Admin/BrokerTrackingPlatformForm.html?ID=<?=$TrackingPlatform->ID?>">Edit</a>
		</td>
	</tr>
<?php 
	}
?>
</table>
