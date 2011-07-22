<h3>Users Subscribed to Webinar</h3>

<table width="500">
	<tr>
		<td>ID</td>
		<td>E-mail</td>
		<td>First Name</td>
		<td>Last Name</td>
	</tr>
<?php 
	foreach ($this->Users as $User)
	{
?>
	<tr>
		<td><?=$User->id?></td>
		<td><?=$User->email?></td>
		<td><?=$User->firstName?></td>
		<td><?=$User->lastName?></td>
	</tr>
<?php 
	}
?>
</table>

<br />

<a href="http://bevomedia/BevoMedia/Admin/WebinarUsers.html?exportCSV=1">Export to CSV</a>
