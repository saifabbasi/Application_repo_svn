
	<div align="center">
<?
	foreach ($this->Pages as $Key => $Page)
	{
?>
		<a href="<?=$this->{'System/BaseURL'};?>BevoMedia/Admin/Crons.html?Page=<?=$Key?>"><?=$Page?></a>
<?
	}
?>
	</div>
	
	<br />

	<table width="100%">
		<tr>
			<th>Name</th>
			<th>Started</th>
			<th>Completed</th>
			<th>Result</th>
		</tr>
<?
	foreach ($this->CronsData as $Row)
	{
	  $messages = $this->db->fetchAll("SELECT * FROM bevomedia_crons_logs_messages WHERE logId={$Row->id}");
	  $messages_counts = $this->db->fetchAll("SELECT count(*), status FROM bevomedia_crons_logs_messages WHERE logId={$Row->id} GROUP BY status");
	  $MESSAGES = array();
	  foreach($messages_counts as $k => $r)
		$MESSAGES[] = ucfirst(str_replace('-', ' ', $r->status)) .': '. $r->{'count(*)'};
?>
		<tr>
			<td><?=$Row->name?></td>
			<td><?=($Row->started!='0000-00-00 00:00:00')?date('m/d/Y g:i a', strtotime($Row->started)):'Not started'?></td>
			<td><?=($Row->completed!='0000-00-00 00:00:00')?date('m/d/Y g:i a', strtotime($Row->completed)):'Not completed'?></td>
			<td><a onClick="javascript:$('#output<?= $Row->id ?>').toggle()"><?= implode($MESSAGES, ', ')?></a>
            <td><a rel="shadowbox;width=800;height=600;player=iframe" href="<?=$this->{'System/BaseURL'};?>BevoMedia/Admin/CronItem.html?id=<?=$Row->id?>"><?= htmlentities(substr($Row->result, 0, 40))?></a></td>
		</tr>
		<tr>
			<td colspan=5>
				<div class='output statusDivs' id='output<?= $Row->id ?>' style='display:none'>
				<?php foreach ($messages as $message) { ?>
					<div class='message <?= $message->status?>'>
						<?= $message->name ?>
						<?php if(!empty($message->result)) { ?>
						<div style='border: 1px solid grey; margin: 5px; padding: 5px;'>
							<pre style='white-space: pre-wrap;'><?= $message->result ?></pre>
						</div>
						<?php } ?>
					</div>
				<?php } ?>
				</div>
			</td>
		</tr>
<?
	}
?>
	</table>

<?
	
	
?>

