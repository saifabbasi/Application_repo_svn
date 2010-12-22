<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
		<li><a href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
	</ul>
</div>
<?php 	//conditionally display logo of the provider and title of the campaign
	//1=Adwords, 2=YSM, 3=AdCenter
	$customPDtitle = $this->AdGroupName ? 'Ad Variation Stats for "'.$this->AdGroupName.'"' : false;
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
	<a class="tbtn floatleft" href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>"><?php print $this->AccountName; ?></a> &raquo; 
	<a class="tbtn floatleft" href='CampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>'><?php print $this->CampaignName; ?></a> 
	&raquo;	AdGroup &quot;<?php print $this->AdGroupName; ?>&quot;
	<div class="clear"></div>
</div>
<!-- ENDOF Breadcrumbs -->
<div class="floatright">
	<a class="tbtn floatright" href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>">View Ad Group Keywords</a>
	<a class="tbtn floatright" href='CreatePPC.html#EditAdGroup:<?php print $this->Provider; ?>,<?php print $this->AccountID; ?>,<?php print $this->AccountName; ?>,<?php print $this->CampaignID; ?>,<?php print $this->CampaignName; ?>,<?php print $this->AdGroupID; ?>,<?php print $this->AdGroupName; ?>,AdVariations'>Edit Ad Group</a>
	<div class="clear"></div>	
</div>
<div class="clear"></div>

<!-- BEGIN Date Range Form -->
<form method="get" name="frmRange">
<input type="hidden" name="ID" value="<?php print $this->AdGroupID; ; ?>"/>
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

<br/>

<?php if(!sizeOf($this->AdVarStats)):?>
<center>
	<i>This ad group does not contain any ad variations...</i>
</center>
<?php else:?>

<!-- BEGIN Statistics Loop -->
<?php foreach($this->AdVarStats as $Ad):?>
	<p style="float:left; width: 45%; margin: 10px; line-height: 1.231; font-family: Arial; font-size: 13px;">
		<a style="text-decoration: underline; font-size: 123.1%; color: rgb(0, 0, 222);" href="<?php print $Ad->destinationUrl; ?>"><?php print $Ad->title; ?></a>

		<br>
		<span><?php print $Ad->description; ?></span><br>
		<span style="color: rgb(0, 128, 0);"><span style="font-weight: bolder;"><?php print $Ad->displayUrl; ?></span></span>
	</p>
	
	<p style='float: right; width: 45%;'>
		<table style='width: 320px !important;' cellspacing='0' cellpadding='0' class='btable'>
			<tr class='table_header'>
				<td class='hhl'> </td>
		<td style="text-align: center;">
			<a href="AdGroupAdVariationsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Impressions<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
			<a href="AdGroupAdVariationsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Clicks<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
			<a href="AdGroupAdVariationsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=AvgCPC<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				CPC
			</a>
			<?php if(isset($_GET['sort']) && $_GET['sort'] == 'AvgCPC'):?>
				<?php if(isset($_GET['sort_order'])):?>
					<img src='/assets/images/sort_asc.gif'/>
				<?php else:?>
					<img src='/assets/images/sort_desc.gif'/>
				<?php endif?>
			<?php endif?>
		</td>
		<td>
			Pos.
		</td>
		<td style="text-align: center;">
			<a href="AdGroupAdVariationsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Cost<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
		<td style="text-align: center;">
			<a href="AdGroupAdVariationsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=CTR<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
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
		<td class='hhr'> </td>
			</tr>
			<tr>
				<td class='border'> </td>
				<td style='text-align:center;'><?php print $Ad->impressions; ?></td>
				<td style='text-align:center;'><?php print $Ad->clicks; ?></td>
				<td style='text-align:center;'>$<?php print number_format($Ad->avgCpc, 2); ?></td>
				<td style='text-align:center;'><?php print ($this->Provider=='Adwords')?'N/A':@((!$Ad->pos)?'0':(number_format($Ad->pos, 1))); ?></td>
				<td style='text-align:center;'>$<?php print number_format($Ad->cost, 2); ?></td>
				<td style='text-align:center;'><?php print number_format($Ad->ctr, 3); ?>%</td>
				<td class='tail'> </td>
			</tr>
			<tr class='table_footer'>
				<td class='hhl'> </td>
				<td colspan='6'> </td>
				<td class='hhr'> </td>
			</tr>
		</table>
	</p>
	<br style='clear:both;'>
<?php endforeach?>

<?php endif?>
<!-- ENDOF Statistics Loop -->


<!-- BEGIN Export to CSV Link -->
<a class="tbtn floatright" target='_blank' href='_CSVExportAdGroupAdVariationsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?><?php echo (isset($_GET['sort'])?'&sort='.$_GET['sort']:'')?><?php echo (isset($_GET['sort_order'])?'&sort_order=Desc':'')?>'>Export to CSV</a>
<!-- ENDOF Export to CSV Link -->