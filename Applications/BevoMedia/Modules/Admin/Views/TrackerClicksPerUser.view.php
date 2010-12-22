

	Total Users With Clicks: <?php echo count($this->UsersData); ?>
		
	<br /><br />
		
	<table width="600">
		<tr>
			<th>Total Clicks</th>
			<th>User ID</th>
			<th>E-mail</th>
			<th>Name</th>
		</tr>
<?php 
	foreach ($this->UsersData as $User)
	{
?>
		<tr>
			<td><?=$User->TotalClicks?></td>
			<td><a href="/BevoMedia/Admin/ViewPublisher.html?id=<?=$User->user__id?>"><?=$User->user__id?></a></td>
			<td><?=$User->email?></td>
			<td><?=$User->firstName.' '.$User->lastName?></td>
		</tr>
<?php 
	}
?>
	</table>

<?php
//	print_r($this->UsersData);