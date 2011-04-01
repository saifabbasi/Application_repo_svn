<table style="width: 100%">
<tr>
	<th>Submitted</th>
	<th>Status</th>
	<th>Type</th>
	<th>Project Name</th>
	<th>Contact</th>
	<th>Phone</th>
	<th>more...</th>
</tr>
<?php 
foreach($this->jobs as $job)
{
?>
<?php $disp_status = array(
			'pendingApproval' => 'Pending Approval',
			'pendingAccept' => 'Waiting to be accepted by user',
			'accepted' => 'Accepted, waiting for payment',
			'paid' => 'Paid: $' . $job->quotedPrice,
			'complete' => 'Complete'
		);?>
<tr>
	<td><?= date('M d h:i a', strtotime($job->created)) ?></td>
	<td><?= $disp_status[$job->status]?></td>
	<td><?= strtoupper($job->projectType) ?></td>
	<td><?= $job->projectName ?></td>
	<td><a href="mailto: <?= $job->contactEmail ?>&subject=<?= urlencode(strtoupper($job->projectType).': '.$job->projectName)?>"><?= $job->contactName?></a></td>
	<td><?= $job->contactPhone ?></td>
	<td onClick="$('.jtoggle#j<?=$job->id?>').toggle();">
		<a style="display: none" class="jtoggle" id="j<?=$job->id?>">[ - ]</span>
		<a class="jtoggle" id="j<?=$job->id?>">[ + ]</span>
	</td>
</tr>
<tr style="display: none" class="jtoggle" id="j<?=$job->id?>">
	<td colspan=4>
		
		<h3><?= $disp_status[$job->status]?></h3>
		<?php if($job->status == 'pendingApproval' || $job->status == 'pendingAccept') { ?>
			<form action="MarketplaceAccept.html" method="POST">
			<input type="hidden" name="id" value="<?= $job->id ?>" />
			<textarea cols=85 rows=12 name="description"><?= $job->description ?></textarea>
			<br />
			Quote Price $<input value="<?=number_format($job->quotedPrice, 2)?>" name="price"/>
			<input type="submit" name="submit" value="Approve job" />
			</form>
		<?php } elseif ($job->status == 'accepted' || $job->status=='paid' || $job->status == 'complete') { ?>
			<?= $job->description?><br />
		<?php } ?>
	</td>
	<td>
		<?php if($job->status == 'paid') {?>
			<a href="MarketplaceCompleted.html?id=<?=$job->id?>">Mark as complete</a>
		<?php }?>
	</td>
</tr>
<?php } ?>
</table>