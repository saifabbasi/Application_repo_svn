

<style type='text/css'>
.btable {width: 100%;}
</style>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','subids','lookup'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
	
<form method="get">
	<div class="floatright">
		Search for a SubID:<br />
		<textarea name="search" class="formtxtarea" style="width: 200px; height: 75px;"><?=isset($_GET['search'])?$_GET['search']:''?></textarea><br />
		(multiple SubIDs separated by comma)<br />
		<div style="text-align: right; margin-right: 0px; padding-right: 0px;">
			<input class="formsubmit btn_go_flush" type="submit" />
		</div>
	</div>
</form>

<div class="clear"></div>

<table id="live" cellspacing="0" class="btable" width="600">
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td class="clickTime">At</td>
		<td class="ipAddress">IP</td>
		<td class="subId">Click SubID</td>
		<td class="creative">Creative</td>
		<td class="extra">More Info</td>
		<td class="converted">Converted?</td>
		<td class="hhr">&nbsp;</td>
	</tr>
<?
	foreach ($this->Results as $Result)
	{
?>
	<tr class="dataRow">
		<td class="hhl">&nbsp;</td>
		<td class="clickTime"><?=date('Y-m-d g:iA', strtotime($Result->at))?></td>
		<td class="ipAddress"><?=$Result->ipAddress?></td>
		<td class="subId"><?=$Result->subId?></td>
		<td class="creative"><?=$Result->creativeTitle?></td>
		<td class="extra">
<?
	if($Result->optional)
		$ATitle = 'Optional Data: '.$Result->optional; else
	if($Result->rawKeyword)
		$ATitle = 'Search Term: '.$Result->rawKeyword; else
		$ATitle = 'More info';
?>
			<a rel="shadowbox;width=800;height=120;player=iframe" href="SubIDLookupVisitorInfo.html?subId=<?=$Result->subId?>"><?=$ATitle?></a>		
		</td>
		<td class="converted">
<?
	if ($Result->conv!=0)
	{
?>
			<img src="/Themes/BevoMedia/img/checkmark.png">
<?
	}
?>
		</td>
		<td class="hhr">&nbsp;</td>
	</tr>
<?
	}
?>	
	
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="6">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	
</table>
