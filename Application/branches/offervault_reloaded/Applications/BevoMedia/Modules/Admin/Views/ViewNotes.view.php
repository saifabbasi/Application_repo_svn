<h2 class='adminPageHeading'>View Notes for <?php print $this->User->firstName; ?> <?php print $this->User->lastName; ?></h2>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr>
	<th>
		Date
	</th>
	<th>
		Note
	</th>
	<th>
		Posted&nbsp;By
	</th>
	<th>
		
	</th>
</tr>

<?php foreach($this->Notes as $Key=>$Note):?>
<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?>'>
	<td>
		<?php print date('m/d/Y\&\n\b\s\p\;g:m:sA', strtotime($Note->created)); ?>
	</td>
	<td>
		<?php print $Note->note; ?>
	</td>
	<td class='nameCell'>
		<?php print $Note->getAdminName(); ?>
	</td>
	<td>
		<a href='/BevoMedia/Admin/DeleteNote.html?id=<?php print $Note->id; ?>'>
			Delete
		</a>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->Notes)):?>
<tr>
	<td colspan="7" class="textAlignCenter">
		<i>No Results</i>
	</td>
</tr>
<?php endif?>
</table>

<br/>
<a rel="shadowbox;width=320;height=240;player=iframe" title='Add Note for <?php print $this->User->firstName; ?> <?php print $this->User->lastName; ?>' href='AddNote.html?id=<?php print $this->User->id; ?>'>Add Note</a>

