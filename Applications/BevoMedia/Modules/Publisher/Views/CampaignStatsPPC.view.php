<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
		<li><a href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
	</ul>
</div>
<?php 	//conditionally display logo of the provider and title of the campaign
	//1=Adwords, 2=YSM, 3=AdCenter
	$customPDtitle = $this->campaignName ? 'PPC Campaign Stats for "'.$this->campaignName.'"' : false;
	$customPDImage = false;
	
	if($this->ProviderType == 1)
		$customPDImage = 'logo_googleadwords.png';
	elseif($this->ProviderType == 2) 
		$customPDImage = 'logo_ysm.png';
	elseif($this->ProviderType == 3) 
		$customPDImage = 'logo_msnadcenter.png';
		
	echo $this->PageDesc->ShowDesc($this->PageHelper,false,$customPDtitle,$customPDImage); ?>

<style type="text/css">
.visualize .visualize-info { padding: 3px 5px; background: #fafafa; border: 1px solid #888; position: relative; top: -20px; right: 10px; opacity: .8; }
.visualize .visualize-info { left: -150px; top: 225px; width: 385px; float:right; margin-bottom: 35%; }
</style>

<a class="tbtn floatright" href="#" onClick="$('#ChartOptions').toggle();">Chart Options</a>
<a class="tbtn floatright" href='CreatePPC.html#EditCampaign:<?php print $this->Provider; ?>,<?php print $this->AccountID; ?>,<?php print $this->AccountName; ?>,<?php print $this->CampaignID; ?>,<?php print $this->CampaignName; ?>'>Edit Campaign</a>

<div class="clear"></div>

<!-- BEGIN Chart Options Form -->
<div id="ChartOptions" style="display: none; background-color: #FDFED4; padding: 5px;">
<form name="frmChartOpts">
<table>
  <tr>
    <td><label for="AdGroups">Ad Groups</label></td>
	<td>
		<select name="AdGroups" id="AdGroups" multiple="multiple">
		<?php foreach($this->AdGroupStats as $AdGroupStatsRow):?>
			<option value="<?php print $AdGroupStatsRow->id; ?>"><?php print $AdGroupStatsRow->name; ?></option>
		<?php endforeach?>
		</select>
	</td>
  </tr>
  <tr>
    <td><label for="Field">Statistic:</label></td>
	<td><select name="Field" id="Field">
		<?php foreach($this->AdGroupFields as $Field=>$Value):?>
			<option value="<?php echo $Field?>"><?php echo $Field?></option>
		<?php endforeach?>
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="button" value="Update Chart" onClick="UpdateChart();"/></td>
  </tr>
</table>
</form>
</div>
<!-- ENDOF Chart Options Form -->

<br/>
<!-- BEGIN Chart Options Script Code -->
<script language="Javascript">
var strURL = "/BevoMedia/Publisher/CampaignStatsPPC.html?ID=<?php print $this->CampaignID?>&DateRange=<?php print urlencode($this->DateRange)?>";
function UpdateChart() {
	var strSelected = getSelectedAdGroups();
	var strField = document.forms['frmChartOpts'].elements['Field'].value;
	window.location = (strURL + strSelected + '&Field=' + strField);
}
function getSelectedAdGroups() {
	var strSelected = '';
	objSelect = document.forms['frmChartOpts'].elements['AdGroups'];
	if (!objSelect) { return false; }
	
	for (var intX = 0; intX < objSelect.options.length; intX++) {
		if (objSelect.options[intX].selected) {
			strSelected += '&AdGroups[]=' + objSelect.options[intX].value;
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
	$ChartXML->SetDateRange($DateRange);
	
	
	if(isset($_GET['Field']))
	{
		$ChartXML->Field = $_GET['Field'];
	}
	if(isset($_GET['AdGroups']))
	{
		$ChartXML->StatsShowRows = $_GET['AdGroups'];
	}
	
	if($this->User->IsSelfHosted() == '1')
	{
		$ChartXML->LoadCampaignStats(-1);
	}else{
		$ChartXML->LoadCampaignStats($this->CampaignID);
	}
	$Out = $ChartXML->getJQueryChartOutput($this->CampaignName, 'JQueryChartData', 'JQueryChartDisplay', '', '0');
	echo $Out;
?>
<!-- ENDOF Chart -->

<div class="clear"></div>

<!-- BEGIN Date Range Form -->
<form method="get" name="frmRange">
<input type="hidden" name="ID" value="<?php print $this->CampaignID?>"/>
<p align="right">
<table align="right" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="text" name="DateRange" id="datepicker" value="<?php print $this->DateRange; ; ?>" style="width: 125px;"/></td>
	<td><input type="submit" class='gobutton-submit' src="" width="30" height="25">
  </tr>
</table>
</p>
</form>
<!-- ENDOF Date Range Form -->

<br class='clearBoth'/>

<!-- BEGIN Breadcrumbs -->
<div class="floatLeft">
	<a href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>"><?php print $this->AccountName; ?></a>
	&raquo;
	<?php print $this->CampaignName; ?>
</div>
<!-- ENDOF Breadcrumbs -->

<br/>

<!-- BEGIN Statistics Table -->
<table width="100%" cellspacing="0" class="btable">
	<tbody>
	<!-- BEGIN Table Header -->
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td>
			<a href="CampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Name<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				Ad Group
			</a>
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'Name'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td style="text-align: center;">
			<a href="CampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Impressions<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
		<td style="text-align: center;">
			<a href="CampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Clicks<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
		<td style="text-align: center;">
			<a href="CampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=CTR<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
		<td style="text-align: center;">
			<a href="CampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=AvgCPC<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				AvgCPC
			</a>
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'AvgCPC'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td style="text-align: center;">
			<a href="CampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Cost<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
	
	<?php if(!sizeOf($this->AdGroupStats)):?>
	<tr>
		<td class="border">&nbsp;</td>
		<td colspan="6" class="textAlignCenter">
			<center>
				<i>This campaign does not have any ad groups...</i>
			</center>
		</td>
		<td class="tail">&nbsp;</td>
	</tr>
	<?php else:?>
	
	<!-- BEGIN Ad Group Stats Row Loop -->
	<?php foreach($this->AdGroupStats as $StatRow):?>
	<tr>
		<td class="border">&nbsp;</td>
		<td>
			<a href="AdGroupStatsPPC.html?ID=<?php print $StatRow->id; ?>&DateRange=<?php print $this->DateRange; ?>">
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
	<!-- ENDOF Ad Group Stats Row Loop -->
	
	<!-- BEGIN Ad Group Total Stats Row -->
	<?php $TotalsStatRow = $this->AdGroupStatsTotal?>
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
	<!-- ENDOF Ad Group Total Stats Row -->
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

<a class="tbtn floatright" target='_blank' href='_CSVExportCampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>&DateRange=<?php print $this->DateRange; ?><?php echo (isset($_GET['sort'])?'&sort='.$_GET['sort']:'')?><?php echo (isset($_GET['sort_order'])?'&sort_order=Desc':'')?>'>Export to CSV</a>