<h2 class='adminPageHeading'>View All</h2>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
	<tr>
		<th class='textAlignCenter'>ID</th>
		<th>Name</th>
		<th>Email</th>
		<th>PPC</th>
		<th>PPV</th>
		<th>MediaBuy</th>
		<th colspan='5'></th>
	</tr>

	<?php foreach($this->AllUsers as $Key=>$User):?>

	<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?>'>
		<td class='textAlignCenter'><?php print $User->id; ?></td>
		<td class='nameCell'><?php print $User->firstName; ?> <?php print $User->lastName; ?>
		</td>
		<td><?php print $User->email; ?></td>
		<td><?php print (isset($User->PPCStats) && $User->PPCStats && $User->PPCStats->clickDate!=0)?date('m/d/Y', strtotime($User->PPCStats->clickDate)):'N/A'; ?>
		</td>
		<td><?php print (isset($User->PPVStats) && $User->PPVStats && $User->PPVStats->clickDate!=0)?date('m/d/Y', strtotime($User->PPVStats->clickDate)):'N/A'; ?>
		</td>
		<td><?php print (isset($User->MediaBuyStats) && $User->MediaBuyStats && $User->MediaBuyStats->clickDate!=0)?date('m/d/Y', strtotime($User->MediaBuyStats->clickDate)):'N/A'; ?>
		</td>
		<td><a href='ViewPublisher.html?id=<?php print $User->id; ?>'> View </a>
		</td>
	</tr>

	<?php endforeach?>

	<?php if(!sizeOf($this->AllUsers)):?>
	<tr>
		<td class="textAlignCenter" colspan="10"><i>No Results</i></td>
	</tr>
	<?php endif?>
</table>
