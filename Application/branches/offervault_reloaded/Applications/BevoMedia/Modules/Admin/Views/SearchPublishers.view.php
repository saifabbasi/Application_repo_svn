<h2 class='adminPageHeading'>Search Publishers</h2>

<form method='get'>
<b>Search:</b> 
<input type='text' name='searchValue'>
<input type='submit' value='Search' />
</form>

<br/>

<form method='post'>
<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr>
	<th class='textAlignCenter'>
		<input 	type='checkbox'
				name='UserCB_ALL' 
				value='ALL' 
				onClick="javascript: LayoutAssist.toggleCheckboxes(document.forms[1]['User_CB[]'], this.checked);" />
	</th>
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
		Mentor
	</th>
	<th>
		Enabled
	</th>
	<th colspan='5'>
		
	</th>
</tr>

<?php foreach($this->AllUsers as $Key=>$User):?>
<?php if($User->deleted):?>
<tr class='<?php echo($User->deleted)?'deletedPublisher':''?>'>
	<td class='textAlignCenter'>
		<input type='checkbox' name='User_CB[]' value='<?php print $User->id; ?>' />
	</td>
	<td class='textAlignCenter'>
		<?php print $User->id; ?>
	</td>
	<td class='nameCell'>
		<?php print $User->firstName; ?> <?php print $User->lastName; ?>
	</td>
	<td>
		<?php print $User->email; ?>
	</td>
	<td colspan="6" class="textAlignCenter">
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
		<input type='checkbox' name='User_CB[]' value='<?php print $User->id; ?>' />
	</td>
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
		<?php print $User->getMentorName(); ?>
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
		<td class="textAlignCenter" colspan="10">
			<i>No Results</i>
		</td>
	</tr>
<?php endif?>
</table>

<br/>
<b>Mentor:</b> Assign Selected Users to
	<select name='Mentor_ID'>
	<?php foreach($this->Mentor->GetAllNonDeletedMentors() as $Mentor):?>
		<option value='<?php print $Mentor->id; ?>'><?php print $Mentor->name; ?></option>
	<?php endforeach ?>
	</select>
<input type='submit' name='assignMentorsToUser' value='Assign' />
<br/>

<b>Clear Mentor:</b> Remove Mentor from Selected Users
<input type='submit' name='clearMentorsFromUsers' value='Clear' />

</form>
