<h2 class='adminPageHeading'>Browse Mentors</h2>

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
		Phone
	</th>
	<th>
		AIM
	</th>
	<th>
		Mentor&nbsp;To
	</th>
	<th>
		
	</th>
	<th>
		
	</th>
</tr>

<?php foreach($this->AllMentors as $Key=>$Mentor):?>
<?php if($Mentor->deleted):?>
<tr class='<?php echo($Mentor->deleted)?'deletedPublisher':''?>'>
	<td class='textAlignCenter'>
		<?php print $Mentor->id; ?>
	</td>
	<td class='nameCell'>
		<?php print $Mentor->name; ?>
	</td>
	<td>
		<?php print $Mentor->email; ?>
	</td>
	<td colspan="4" class="textAlignCenter">
	<b>DELETED</b>
	</td>
	<td>
		<a class='blackText' href='RestoreMentor.html?id=<?php print $Mentor->id; ?>'>
			Restore
		</a>
	</td>
</tr>
<?php else:?>
<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?>'>
	<td class='textAlignCenter'>
		<?php print $Mentor->id; ?>
	</td>
	<td class='nameCell'>
		<?php print $Mentor->name; ?>
	</td>
	<td>
		<?php print $Mentor->email; ?>
	</td>
	<td>
		<?php print $Mentor->phone; ?>
	</td>
	<td>
		<?php print $Mentor->aim; ?>
	</td>
	<td>
		<a rel="shadowbox;width=480;height=270;player=iframe" title='Users Assigned to <?php print $Mentor->name; ?>' href='ViewMentorUsers.html?id=<?php print $Mentor->id; ?>'>
			<?php print sizeOf($Mentor->getMentorsUsers()); ?> Users
		</a>
	</td>
	<td>
		<a rel="shadowbox;width=200;height=260;player=iframe" title='Edit Mentor' href='AddMentor.html?EditMentor=<?php print $Mentor->id; ?>'>
			Edit
		</a>
	</td>
	<td>
		<a href='DeleteMentor.html?id=<?php print $Mentor->id; ?>'>
			Delete
		</a>
	</td>
</tr>
<?php endif?>
<?php endforeach?>

<?php if(!sizeOf($this->AllMentors)):?>
	<tr>
		<td class="textAlignCenter" colspan="10">
			<i>No Results</i>
		</td>
	</tr>
<?php endif?>
</table>

<br/>
<a rel="shadowbox;width=200;height=260;player=iframe" title='Add Mentor' href='AddMentor.html'>Add Mentor</a>

