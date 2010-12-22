<table cellspacing=0 cellpadding=0 class='adminPublisherTable' width="100%">
<tr>
	<th>
		ID
	</th>
	<th>
		Name
	</th>
	<th>
		Insert Email
	</th>
</tr>

<?php foreach($this->AllUsers as $Key=>$User):?>
<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?>'>
	<td class='textAlignLeft'>
		<?php print $User->id; ?>
	</td>
	<td class='textAlignLeft nameCell'>
		<?php print $User->firstName; ?>
		<?php print $User->lastName; ?>
	</td>
	<td>
		<a href='#' onClick='javascript:LayoutAssist.appendValue(parent.document.forms[0].recipients, "<?php print $User->email; ?>")'>
			Add
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