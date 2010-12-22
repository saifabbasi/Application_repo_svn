<style>
  .process.warning { background: yellow; }
  .process.queued { background: gray; }
  .process.message { background: white; }
  .process.error { background: red; }
  .process.success { background: green; }

</style>
<?
  echo "<pre class='textAlignLeft'>";
  
  echo "<br /><hr /><br /><b>Log:</b><br /><br /><hr /><br />";
  
  
  $subjobs = $this->db->fetchAll("SELECT * FROM bevomedia_queue_log WHERE queueId=".$_GET['id']);
	foreach($subjobs as $k=>$sub) { ?>
		<div class='process <?= $sub->status?>'>
		<?= $sub->description ?><br />
		<? if(!empty($sub->output)) { ?>
		  <div style='border: 1px solid grey; margin: 5px; padding: 5px;'><pre style='white-space:pre-wrap'><?= $sub->output ?></pre></div>
		<? } ?>
		</div>
	  <? } 
  	
	echo "<br /><hr /><br /><b>Output:</b><br /><br /><hr /><br />";

	echo $this->{'output'};
	
	echo "<br /><hr /><br /><b>Job:</b><br /><br /><hr /><br />";
	
	echo highlight_string($this->{'envelope'});
	
	echo "</pre>";
?>