
	<table>
		<tr>
			<td>Network Cron</td>
			<td>Date</td>
			<td>Output</td>
		</tr>
<?php 
	foreach ($this->Offers as $Offer)
	{
		if (strlen($Offer->output)>200) {
			$Offer->output = substr($Offer->output, 0, 200);
		}
?>
		<tr>
			<td><b><?=$Offer->type?></b></td>
			<td><?=date('m/d/Y H:i:s', strtotime($Offer->started))?></td>
			<td><?=$Offer->output?></td>
		</tr>
<?php 
	}
?>
	</table>