
	<table>
		<tr>
			<td>Network Cron</td>
			<td>Date</td>
			<td>Output</td>
		</tr>
<?php 
	$PrintedTypes = array();
	foreach ($this->Offers as $Offer)
	{
		if (in_array($Offer->type, $PrintedTypes)) continue;
		
		if (strlen($Offer->output)>200) {
			
			if (stristr($Offer->output, 'error')) {
				$Offer->output = substr($Offer->output, strpos($Offer->output, 'error'), 200);
			} else { 
				$Offer->output = substr($Offer->output, 0, 200);
			}			
		}
?>
		<tr>
			<td><b><?=$Offer->type?></b></td>
			<td><?=date('m/d/Y H:i:s', strtotime($Offer->started))?></td>
			<td><?=$Offer->output?></td>
		</tr>
<?php 
		$PrintedTypes[] = $Offer->type;
	}
?>
	</table>