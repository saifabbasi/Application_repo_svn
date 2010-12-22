<table cellspacing=0 cellpadding=0 class='adminPublisherTable' width="100%">
<tr>
	<th>
		ID
	</th>
	<th>
		Name
	</th>
	<th>
		View Publisher
	</th>
	<th>
		Delete
	</th>
</tr>

<?php foreach($this->AllUsers as $Key=>$User):?>
<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?>'>
	<td class='textAlignLeft'>
		<?php print $User->id; ?>
	</td>
	<td class='textAlignLeft'>
		<?php print $User->firstName; ?>
		<?php print $User->lastName; ?>
	</td>
	<td>
		<a target='_parent' href='ViewPublisher.html?id=<?php print $User->id; ?>'>
			View
		</a>
	</td>
	<td>
		<a href='RemoveUsersMentor.html?id=<?php print $User->id; ?>'>
			Remove
		</a>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->AllUsers)):?>
<tr>
	<td colspan="4">
		<i>This Mentor has no Assigned Users</i>
	</td>
</tr>
<?php endif?>
</table>