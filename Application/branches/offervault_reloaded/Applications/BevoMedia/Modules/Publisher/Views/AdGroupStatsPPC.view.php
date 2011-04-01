<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
		<li><a href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
	</ul>
</div>
<?php 	//conditionally display logo of the provider and title of the campaign
	//1=Adwords, 2=YSM, 3=AdCenter
	$customPDtitle = $this->AdGroupName ? 'AdGroup Stats for "'.$this->AdGroupName.'"' : false;
	$customPDImage = false;
	
	if($this->ProviderType == 1)
		$customPDImage = 'logo_googleadwords.png';
	elseif($this->ProviderType == 2) 
		$customPDImage = 'logo_ysm.png';
	elseif($this->ProviderType == 3) 
		$customPDImage = 'logo_msnadcenter.png';
		
	echo $this->PageDesc->ShowDesc($this->PageHelper,false,$customPDtitle,$customPDImage); ?>


<!-- BEGIN Breadcrumbs -->
<div class="floatleft">
	<a class="tbtn floatleft" href="AccountStatsPPC.html?ID=<?php print $this->AccountID?>&Provider=<?php print $this->Provider?>&DateRange=<?php print $this->DateRange ?>"><?php print $this->AccountName; ?></a> &raquo; 
	<a class="tbtn floatleft" href='CampaignStatsPPC.html?ID=<?php print $this->CampaignID?>'><?php print $this->CampaignName ?></a> 
	&raquo; AdGroup &quot;<?php print $this->AdGroupName; ?>&quot;
	<div class="clear"></div>
</div>	
<!-- ENDOF Breadcrumbs -->

<div class="floatright">
	<a class="tbtn floatright" href="AdGroupAdVariationsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>">View Ad Group Ad Variations</a>
	<a class="tbtn floatright" href='CreatePPC.html#EditAdGroup:<?php print $this->Provider; ?>,<?php print $this->AccountID; ?>,<?php print $this->AccountName; ?>,<?php print $this->CampaignID; ?>,<?php print $this->CampaignName; ?>,<?php print $this->AdGroupID; ?>,<?php print $this->AdGroupName; ?>,Keywords'>Edit Ad Group</a>
	<div class="clear"></div>
</div>
<div class="clear"></div>

<!-- BEGIN Date Range Form -->
<form method="get" name="frmRange">
<input type="hidden" name="ID" value="<?php print $this->AdGroupID; ; ?>"/>
<p align="right">
<table align="right" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="text" name="DateRange" id="datepicker" value="<?php print $this->DateRange?>" style="width: 125px;"/></td>
	<td><input type="submit" class='gobutton-submit' src="" width="30" height="25">
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
			<a href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Name<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				Keyword
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
			<a href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Impressions<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
			<a href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Clicks<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
			<a href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=CTR<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
			<a href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=AvgCPC<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
			<a href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Cost<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
	
	<?php if(!sizeOf($this->KeywordStats)):?>
	<tr>
		<td class="border">&nbsp;</td>
		<td colspan="6" class="textAlignCenter">
			<center>
				<i>This ad group does not have any keywords...</i>
			</center>
		</td>
		<td class="tail">&nbsp;</td>
	</tr>
	<?php else:?>
	
	<!-- BEGIN Ad Group Stats Row Loop -->
	<?php foreach($this->KeywordStats as $StatRow):?>
	<tr>
		<td class="border">&nbsp;</td>
		<td>
			<?php print $StatRow->formattedName; ?>
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
	<?php $TotalsStatRow = $this->KeywordStatsTotal?>
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


<!-- BEGIN Export to CSV Link -->
<a class="tbtn floatright" target='_blank' href='_CSVExportAdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?><?php echo (isset($_GET['sort'])?'&sort='.$_GET['sort']:'')?><?php echo (isset($_GET['sort_order'])?'&sort_order=Desc':'')?>'>Export to CSV</a>
<!-- ENDOF Export to CSV Link -->