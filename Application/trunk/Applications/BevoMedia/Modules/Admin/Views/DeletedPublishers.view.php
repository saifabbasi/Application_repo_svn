<h2 class='adminPageHeading'>Deleted Publishers</h2>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr>
	<th class='textAlignCenter'>
		ID
	</th>
	<th>
		Name
	</th>
	<th>
		Email
	</th>
	<th>
		
	</th>
</tr>

<?php foreach($this->AllUsers as $Key=>$User):?>
<?php if($User->deleted):?>
<tr class='<?php echo($User->deleted)?'deletedPublisher':''?>'>
	<td class='textAlignCenter'>
		<?php print $User->id; ?>
	</td>
	<td class='nameCell'>
		<?php print $User->firstName; ?> <?php print $User->lastName; ?>
	</td>
	<td>
		<?php print $User->email; ?>
	</td>
	<td>
		<a class='blackText' href='RestoreUser.html?id=<?php print $User->id; ?>'>
			Restore
		</a>
	</td>
</tr>
<?php else:?>
<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?>'>
	<td class='textAlignCenter'>
		<?php print $User->id; ?>
	</td>
	<td class='nameCell'>
		<?php print $User->firstName; ?> <?php print $User->lastName; ?>
	</td>
	<td>
		<?php print $User->email; ?>
	</td>
	<td>
		<?php print ($User->enabled)?'Enabled':'Disabled'; ?>
	</td>
	<td>
		<a href='ViewNotes.html?id=<?php print $User->id; ?>'>
			Notes (<?php print $User->getNoteCount(); ?>)
		</a>
	</td>
	<td>
		<a href='ViewPublisher.html?id=<?php print $User->id; ?>'>
			View
		</a>
	</td>
	<td>
		<a href='EditPublisher.html?id=<?php print $User->id; ?>'>
			Edit
		</a>
	</td>
	<td>
		<?php if($User->enabled):?>
		<a href='DisableUser.html?id=<?php print $User->id; ?>'>
			Disable
		</a>
		<?php else:?>
		<a href='EnableUser.html?id=<?php print $User->id; ?>'>
			Enable
		</a>
		<?php endif?>
	</td>
	<td>
		<a href='DeleteUser.html?id=<?php print $User->id; ?>'>
			Delete
		</a>
	</td>
</tr>
<?php endif?>
<?php endforeach?>

<?php if(!sizeOf($this->AllUsers)):?>
	<tr>
		<td class="textAlignCenter" colspan="5">
			<i>No Results</i>
		</td>
	</tr>
<?php endif?>
</table>
