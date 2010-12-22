<h2 class='adminPageHeading'>View Tickets</h2>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr>
	<th>
		Submitted
	</th>
	<th>
		Subject
	</th>
	<th>
		Problem
	</th>
	<th>
		Posted&nbsp;By
	</th>
	<th>
		Solved
	</th>
	<th>
		
	</th>
</tr>

<?php foreach($this->Tickets as $Key=>$Ticket):?>
<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> <?php echo($Ticket->solved)?'solvedTicket':''?>'>
	<td>
		<?php print date('m/d/Y g:m:sA', strtotime($Ticket->created)); ?>
	</td>
	<td class='nameCell'>
		<?php print $Ticket->subject; ?>
	</td>
	<td>
		<?php print $Ticket->problem; ?>
	</td>
	<td>
		<a href='ViewPublisher.html?id=<?php print $Ticket->user__id; ?>'>
			<?php print $Ticket->getUserName(); ?>
		</a>
	</td>
	<td class='textAlignCenter'>
		<?php if($Ticket->solved):?>
			<?php print ($Ticket->solved)?date('m/d/y', strtotime($Ticket->solvedTimestamp)):'Not&nbsp;Solved'; ?>
		<?php else:?>
		<a href='/BevoMedia/Admin/SolveTicket.html?id=<?php print $Ticket->id; ?>'>
			Solve
		</a>
		<?php endif?>
	</td>
	<td>
		<a href='/BevoMedia/Admin/DeleteTicket.html?id=<?php print $Ticket->id; ?>'>
			Delete
		</a>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->Tickets)):?>
<tr>
	<td colspan="7" class="textAlignCenter">
		<i>No Results</i>
	</td>
</tr>
<?php endif?>
</table>


