<div id="pagemenu">
	<ul>
		<li><a class="active" href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
		<li><a href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
	</ul>
</div>
<?php 	//conditionally display title + logo of the provider
	//1=Adwords, 2=YSM, 3=AdCenter
	if($this->ProviderType == 1) {
		$customPDtitle = 'Google Adwords';
		$customPDImage = 'logo_googleadwords.png';
	} elseif($this->ProviderType == 2) {
		$customPDtitle = 'Yahoo! Search Marketing';
		$customPDImage = 'logo_ysm.png';
	} elseif($this->ProviderType == 3) {
		$customPDtitle = 'MSN AdCenter';
		$customPDImage = 'logo_msnadcenter.png';
	}
		
	echo $this->PageDesc->ShowDesc($this->PageHelper,false,$customPDtitle,$customPDImage); ?>

<a class="tbtn floatright" href='CreatePPC.html#EditAccount:<?php print $this->Provider; ?>,<?php print $this->AccountID; ?>,<?php print $this->accountName; ?>'>Edit Campaigns</a>
<div class="clear"></div>

<div class="page ppcpage">

<div class="graphnotice nobg">
	<a class="tbtn" href="#" onClick="$('#ChartOptions').toggle(); return false;">Chart Options</a>
	
	<div class="graphnotice">
		<p>To see stats in your PPC account, you must upload a report.</p>
		<a class="btn ppc_uploadstatsnow" href="/BevoMedia/Publisher/<?php print $this->Provider; ?>ManualUpload.html?ID=<?php print $this->AccountID; ?>">Click here to upload stats now</a>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>

<style type="text/css">
.visualize .visualize-info { padding: 3px 5px; background: #fafafa; border: 1px solid #888; position: relative; top: -20px; right: 10px; opacity: .8; }
.visualize .visualize-info { left: -150px; top: 225px; width: 385px; float:right; margin-bottom: 35%; }
</style>

<!-- BEGIN Chart Options Form -->
<div class="graphnotice top" id="ChartOptions" style="display: none;">
<form name="frmChartOpts">
<table>
  <tr>
    <td><label for="Campaigns">Campaigns</label></td>
	<td>
		<select class="formselect" name="Campaigns" id="Campaigns" multiple="multiple">
		<?php foreach($this->CampaignStats as $CampaignStatsRow):?>
			<option value="<?php print $CampaignStatsRow->id; ?>"><?php print $CampaignStatsRow->name; ?></option>
		<?php endforeach?>
		</select>
	</td>
  </tr>
  <tr>
    <td><label for="Field">Statistic:</label></td>
	<td><select class="formselect" name="Field" id="Field">
		<?php foreach($this->CampaignFields as $Field=>$Value):?>
			<option value="<?php echo $Field?>"><?php echo $Field?></option>
		<?php endforeach?>
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center">
    	<input class="formsubmit ppc_updatechart" type="submit" value="Update Chart" onClick="UpdateChart();"/>
    </td>
  </tr>
</table>
</form>
</div>
<!-- ENDOF Chart Options Form -->

<br/>

<!-- BEGIN Chart Options Script Code -->

<script language="Javascript">
var strURL = "/BevoMedia/Publisher/AccountStatsPPC.html?ID=<?php print $this->AccountID?>&Provider=<?php print $this->Provider ?>&DateRange=<?php print urlencode($this->DateRange); ?>";
function UpdateChart() {
	var strSelected = getSelectedCampaigns();
	var strField = document.forms['frmChartOpts'].elements['Field'].value;
	window.location = (strURL + strSelected + '&Field=' + strField);
}
function getSelectedCampaigns() {
	var strSelected = '';
	objSelect = document.forms['frmChartOpts'].elements['Campaigns'];
	if (!objSelect) { return false; }
	
	for (var intX = 0; intX < objSelect.options.length; intX++) {
		if (objSelect.options[intX].selected) {
			strSelected += '&Campaigns[]=' + objSelect.options[intX].value;
		}
	}
	return strSelected;
}
</script>
<!-- ENDOF Chart Options Script Code -->


<!-- BEGIN Chart -->
<script type="text/javascript">
	$(function(){
		//make some charts
		$('#JQueryChartData').visualize({type: 'line'}).appendTo('#JQueryChartDisplay');
	});
</script>

<?php
	$DateRange = date('m/j/Y', strtotime('TODAY - 7 DAYS')) . '-' . date('m/j/Y', strtotime('TODAY'));
	if(isset($_GET['DateRange']))
		$DateRange = $_GET['DateRange'];

	$ChartXML = new ChartXMLHelper();
	
	if(isset($_GET['Field']))
	{
		$ChartXML->Field = $_GET['Field'];
	}
	if(isset($_GET['Campaigns']))
	{
		$ChartXML->StatsShowRows = $_GET['Campaigns'];
	}
	
	$ChartXML->SetDateRange($DateRange);
	
	$ChartXML->LoadAccountStats($this->AccountID, $this->ProviderType);
	
	$Out = $ChartXML->getJQueryChartOutput($this->Provider . ': ' . $this->AccountName, 'JQueryChartData', 'JQueryChartDisplay', '', '0');
	echo $Out;
?>
<!-- ENDOF Chart -->

<!-- BEGIN Date Range Form -->
<form method="get" name="frmRange">
<input type="hidden" name="ID" value="<?php print $this->AccountID?>"/>
<input type="hidden" name="Provider" value="<?php print $this->Provider?>"/>
<p align="right">
<table align="right" cellspacing="0" cellpadding="0" class="datetable">
  <tr>
    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php print $this->DateRange; ; ?>"></td>
	<td><input class="formsubmit" type="submit"></td>
  </tr>
</table>
</p>
</form>
<!-- ENDOF Date Range Form -->

<br/>

<!-- BEGIN Statistics Table -->
<table width="100%" cellspacing="0" class="btable">
	<tbody>
	<!-- BEGIN Table Header -->
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td>
			<a href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Name<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				Campaign
			</a>
		
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'Name'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td style="text-align: center">
			<a href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Impressions<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				Impr
			</a>
		
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'Impressions'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td style="text-align: center">
			<a href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Clicks<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				Clicks
			</a>
		
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'Clicks'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td style="text-align: center">
			<a href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>&sort=CTR<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				CTR
			</a>
		
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'CTR'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td style="text-align: center">
			<a href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>&sort=AvgCPC<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				Avg CPC
			</a>
		
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'AvgCPC'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td style="text-align: center">
			<a href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Cost<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				Cost
			</a>
		
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'Cost'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<!-- ENDOF Table Header -->

	<?php if(!sizeOf($this->CampaignStats)):?>
	<tr>
		<td class="border">&nbsp;</td>
		<td colspan="6" class="textAlignCenter">
			<center>
			<i>You have no reports uploaded for this account. To upload a report, <a href="/BevoMedia/Publisher/<?=$_GET['Provider']?>ManualUpload.html?ID=<?=$_GET['ID']?>">click here</a></i>
			</center>
		</td>
		<td class="tail">&nbsp;</td>
	</tr>
	<?php else:?>
		
	<!-- BEGIN Campaign Stats Row Loop -->
	<?php foreach($this->CampaignStats as $StatRow):?>
	<tr>
		<td class="border">&nbsp;</td>
		<td>
			<?php $DateRangeString = (isset($_GET['DateRange']))?'&DateRange='.$this->DateRange:''?>
			
			<div class="campaignStatusIcon <?php print $StatRow->status; ?>"></div>
			
			<a href="CampaignStatsPPC.html?ID=<?php print $StatRow->id; ?><?php echo $DateRangeString?>">
				<?php print $StatRow->name; ?>
			</a>
		</td>
		<td style="text-align: center;">
			<?php print number_format($StatRow->impressions, 0, '.', ','); ?>
		</td>
		<td style="text-align: center;">
			<?php print number_format($StatRow->clicks, 0, '.', ','); ?>
		</td>
		<td style="text-align: center;">
			<?php print number_format($StatRow->ctr*100, 0, '.', ','); ?>%
		</td>
		<td style="text-align: center;">
			<?php print number_format($StatRow->avgCpc, 2, '.', ','); ?>
		</td>
		<td style="text-align: center;">
			$<?php print number_format($StatRow->cost, 2, '.', ','); ?>
		</td>
		<td class="tail">&nbsp;</td>
	</tr>
	<?php endforeach?>
	<!-- ENDOF Campaign Stats Row Loop -->
	
	<!-- BEGIN Campaign Total Stats Row -->
	<?php $TotalsStatRow = $this->CampaignStatsTotal?>
	<tr>
		<td class="border">&nbsp;</td>
		<td>&nbsp;</td>
		<td style="text-align: center;">
			<?php print number_format($TotalsStatRow->impressions, 0, '.', ','); ?>
		</td>
		<td style="text-align: center;">
			<?php print number_format($TotalsStatRow->clicks, 0, '.', ','); ?>
		</td>
		<td style="text-align: center;">
			<?php print number_format($TotalsStatRow->ctr*100, 0, '.', ','); ?>%
		</td>
		<td style="text-align: center;">
			<?php print number_format($TotalsStatRow->avgCpc, 2, '.', ','); ?>
		</td>
		<td style="text-align: center;">
			$<?php print number_format($TotalsStatRow->cost, 2, '.', ','); ?>
		</td>
		<td class="tail">&nbsp;</td>
	  </tr>
	<!-- ENDOF Campaign Total Stats Row -->
	<?php endif?>
	
	<!-- BEGIN Table Footer -->
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
	
		<td colspan="6">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<!-- ENDOF Table Footer -->
	</tbody>
</table>
<!-- ENDOF Statistics Table -->

<!-- BEGIN Export to CSV Link -->
<a class="tbtn floatright" target='_blank' href='_CSVExportAccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?><?php echo (isset($_GET['sort'])?'&sort='.$_GET['sort']:'')?><?php echo (isset($_GET['sort_order'])?'&sort_order=Desc':'')?>'>
	Export to CSV
</a>
<!-- ENDOF Export to CSV Link -->
</div><!--close page.ppcpage-->