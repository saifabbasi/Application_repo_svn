<script type="text/javascript" src="/Themes/BevoMedia/jqplot/jquery.jqplot.js"></script> 
<script type="text/javascript" src="/Themes/BevoMedia/jqplot/plugins/jqplot.dateAxisRenderer.js"></script> 
<script>
use = [<? foreach($this->days as $d) {?>['<?=$d->day?>',
  <?=-1*$d->day_use ?>],<? } ?>];
totals = [<? foreach($this->days as $d) {?>['<?=$d->day?>',
  <?=$d->day_total ?>],<? } ?>];
$(function() {
  $.jqplot('callsChart', [use, totals], {
	title:'<h3>Your API Call Balance</h3>', 
	  gridPadding:{right:35},
	  axes:{
		xaxis:{
		  renderer:$.jqplot.DateAxisRenderer, 
			tickOptions:{formatString:'%b %#d, %y'},
			max: '<?=date("Y-m-d")?>',
			tickInterval:'1 week'
		},
		yaxis:{
		  padding:0,
			min: 0,
			tickOptions:{formatString:'%.0f'}
		}
	  },
		series:[{lineWidth:4, markerOptions:{style:'square'}}]
  });
});
</script>
<style>
  .box td { text-align: center; }
</style>

<div id="pagemenu">
	<ul>
		<li><a class="active" href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/ApiCalls.html">API Call History<span></span></a></li>
		<li><a href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/PPCQueueProgress.html">Campaign Editor Queue<span></span></a></li>
		<li><a href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/ManageStats.html">Delete Stats<span></span></a></li>
	</ul>
	<ul class="floatright">
		<li><a href="http://ryanbuke.com/" target="_blank">Official Bevo Blog<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div id="callsChart">
</div>
<div class="clear"></div>
<br /><br />
<div class="box box2 noborder">
	<h3>Daily Call Usage Breakdown</h3>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="btable" id="dailyCallsSpent">
		<tr class="table_header">
			<td class="hhl">&nbsp;</td>
			<td style="text-align:center;">Date</td>
			<td style="text-align:center;">Calls Used</td>
			<td style="text-align:center;">Total Call Balance</td>
			<td class="hhr">&nbsp;</td>
		</tr>
		<? foreach(array_reverse($this->days) as $d) { ?>
			<tr>
				<td class="border">&nbsp;</td>
				<td><?=$d->day?></td>
				<td><?=$d->day_use ?></td>
				<td><?=$d->day_total ?></td>
				<td class="tail">&nbsp;</td>
			</tr>
		<? } ?>
		<tr class="table_footer">
			<td class="hhl"></td>
			<td style="border-left: none;" colspan="3"></td>
			<td class="hhr"></td>
		</tr>
	</table>
</div><!--close box-->

<div class="box box2 nomargin">
	<h3>Calls History</h3>
	
	<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable" id="pointHist">
		<tr class="table_header">
			<td class="hhl">&nbsp;</td>
			<td style="text-align: center;">When</td>
			<td style="text-align: center;">Amount</td>
			<td style="text-align: center;">Reason</td>
			<td class="hhr">&nbsp;</td>
		</tr>
		<? foreach($this->last_fifty_txs as $tx) { ?>
			<tr class="<?= $tx->amount > 0 ? 'pos' : 'neg' ?>Trans">
				<td class="border">&nbsp;</td>
				<td><?=$tx->at?></td>
				<td class="callsVal"><?=($tx->amount > 0 ? '+':'').$tx->amount?></td>
				<td><?=$tx->reason?></td>
				<td class="tail">&nbsp;</td>
			</tr>
		<? } ?>
		<tr class="table_footer">
			<td class="hhl"></td>
			<td style="border-left: none;" colspan="3"></td>
			<td class="hhr"></td>
		</tr>
	</table>
</div><!--close box-->
<div class="clear"></div>