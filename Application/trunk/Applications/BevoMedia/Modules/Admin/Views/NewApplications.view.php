<h2 class='adminPageHeading'>New Applications</h2>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr>
	<th>
		Name
	</th>
	<th>
		Email
	</th>
	<th>
		Application Date
	</th>
	<th>
		Address
	</th>
	<th>
		Comments
	</th>
	<th>
		Notes
	</th>
	<th colspan='2'>
		
	</th>
</tr>

<?php foreach($this->AllUsers as $Key=>$User):?>
<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?>'>
	<td class='nameCell'>
		<?php print $User->firstName; ?> <?php print $User->lastName; ?>
	</td>
	<td>
		<?php print $User->email; ?>
	</td>
	<td>
		<?php print date('m/d/Y g:m:sA', strtotime($User->created)); ?>
	</td>
	<td>
		<?php print $User->address; ?><br/>
		<?php print $User->city; ?> <?php print $User->state; ?>, <?php print $User->zip; ?><br/>
		<?php print $User->country; ?>
	</td>
	<td>
		<?php print ($User->comments)?$User->Comments:'(N/A)'; ?>
	</td>
	<td>
		<a href='ViewNotes.html?id=<?php print $User->id; ?>'>
			Notes (<?php print $User->getNoteCount(); ?>)
		</a>
	</td>
	<td>
		<a href='EnableUser.html?id=<?php print $User->id; ?>'>
			Accept
		</a>
	</td>
	<td>
		<a class='redLinkForced' href='DenyUser.html?id=<?php print $User->id; ?>' rel='shadowbox;width=380;height=200;player=iframe' title='Deny this User!'>
			Deny!
		</a>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->AllUsers)):?>
<tr>
	<td colspan="7" class="textAlignCenter">
		<i>No Results</i>
	</td>
</tr>
<?php endif?>
</table>
