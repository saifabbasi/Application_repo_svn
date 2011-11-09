<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
		<li><a href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
	</ul>
	<ul class="floatright">
		<li><a class="active" href="/BevoMedia/Publisher/PPCQueueProgress.html">Campaign Editor Queue<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false); ?>

<?php
$CPQ = new CreatePPCQueue();
$Favicons = array('ADWORDS'=>'/Themes/BevoMedia/img/googlefavicon.png', 'YAHOO'=>'/Themes/BevoMedia/img/yahoofavicon.png', 'MSN'=>'/Themes/BevoMedia/img/msnfavicon.png');
$link = $this->{'System/BaseURL'}.'BevoMedia/Publisher/PPCQueueProgress.html?' . htmlentities($_SERVER['QUERY_STRING']);
?>
  <?php if(empty($this->QueueItems)): ?><h4>You have no queued items.</h4><? endif ?>
<div id="ppcqueueprogresstable" class="statusDivs">
<?php foreach($this->QueueItems as $Item):?>
	<?php
		$Class = '';
		$SubProcesses = $CPQ->SubProcesses($Item->id);
		$TempProcesses = $CPQ->ParseDescription($Item->envelope, 'Array');
		$subcounts = array('success' => 0, 'error' => 0, 'in-progress' => 0, 'queued' => 0, 'warning' => 0, 'message' => 0);
		$itemProviders = array('ADWORDS' => false, 'MSN' => 'false', 'YAHOO' => false);
		foreach($SubProcesses as $k=>$sub)
		{
		  $itemProviders[$sub->provider] = true;
		  $subcounts[$sub->status] += 1;
		}
		$subsummary = array();
		foreach($subcounts as $status => $count)
		  if(!empty($count))
			$subsummary[] = ucfirst(str_replace('-', ' ', $status)) . ': ' . $count;
	?>
<div id='subsummary<?=$Item->id?>' style='border: 5px solid #eeeeee; padding: 5px;'>
<h3>
<? foreach($itemProviders as $p => $show) {
  $P = ucfirst(strtolower($p));
  if($show === true && isset($Favicons[$p]))
	echo "<img src='{$Favicons[$p]}' title='This job will affect campaigns on your {$P} account'/>&nbsp;";
} ?>
<?= !empty($subcounts['queued']) ? 'Queued' : 'Completed' ?> Job
<?= empty($subcounts['queued']) ? '<small><a href="'.$link.'&amp;hide='.$Item->id.'">Clear completed job</a></small>' : '' ?>
	  <span style='float: right'><?= count($SubProcesses) ?> Items</span></h3>
	<div style='display: block; height: 10px; width: 98%; border: 1px solid grey; margin: 7px 0px;'>
	<? foreach($subcounts as $status => $count){
	  if($count) { ?>
		  <div class='<?= $status ?>' style='height: 10px; width: <?= (101*$count / count($SubProcesses))-1?>%; display: inline-block'>&nbsp;</div>
	<? } } ?>
	</div>
  <?= implode($subsummary, ', '); ?><span style='float: right'><a onClick="javascript:$('#subs<?=$Item->id?>').toggle()">[+] Show/hide job details</a></span>
  <div class='subprocesses' id='subs<?=$Item->id?>' style='display: none'>
  <?php foreach($SubProcesses as $Key => $Process):?>
	  <div class='process <?php echo $Process->status?>'>
		  <div style='float: right; font-size: .8em; font-weight: bold; padding-right: 10px;'>
			  <?php echo ucfirst(str_replace('-', ' ', $Process->status)); ?>
			  <?php if($Process->started > 0) { ?>
			  Started: <?php print date('Y-m-d g:i a', strtotime($Process->started)); ?>&nbsp;
			  <? } if ($Process->completed > 0) { ?>
			  Completed: <?php print date('Y-m-d g:i a', strtotime($Process->completed)); ?>
			  <? } ?>
		  </div>
		  <img align="left" src="<?php echo $Favicons[$Process->provider] ?>" />&nbsp;
		  <br />
			<?php echo $Process->description ?>
		  <br />
			<? if(!empty($Process->output)) { ?><a onClick="javascript:$('#output<?php echo $Process->id ?>').toggle()">[+] Show/hide output</a>
			<div class='output' id='output<?php echo $Process->id ?>' style="<?= ($Process->status != 'error') ? 'display: none' : '' ?>">
  <pre style='white-space:pre-wrap'>
  <?php
				echo $Process->output;
  ?>
  </pre>
		  </div>
		  <? } ?>
	  </div>
	  <?php endforeach?>
	</div>
</div>
<br />
<?php endforeach?>
</div>