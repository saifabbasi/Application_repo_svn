<h2 class='adminPageHeading'>Browse Self Hosted Publishers</h2>

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
		Self Hosted
	</th>
	<th>
		Last Update
	</th>
	<th>
		Update Cooldown
	</th>
	<th colspan='3'>
		
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
	<td colspan="5" class="textAlignCenter">
	<b>DELETED</b>
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
		<a href='ViewPublisher.html?id=<?php print $User->id; ?>'>
			<?php print $User->firstName; ?> <?php print $User->lastName; ?>
		</a>
	</td>
	<td>
		<?php print $User->email; ?>
	</td>
	<td>
		<?php print $User->membershipType?>
	</td>
	<td>
		<?php 
		require_once(PATH . 'SelfHostedUpdate.class.php');
		$Temp = new SelfHostedUpdate($User->id);
		echo $Temp->getCooldownDate() ?>
	</td>
	<td>
		<?php echo $Temp->getCooldownRemaining()?> seconds
	</td>
	<td>
		<a href='ViewNotes.html?id=<?php print $User->id; ?>'>
			Notes (<?php print $User->getNoteCount(); ?>)
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
		<td class="textAlignCenter" colspan="10">
			<i>No Results</i>
		</td>
	</tr>
<?php endif?>
</table>

