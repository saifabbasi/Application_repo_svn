<script type="text/javascript">
this.tooltip = function(){
 yOffset = 20;
 xOffset = 10;
 $("a.tooltip").hover(function(e){
	this.t = this.title;
	this.title = "";
	$("body").append("<p id='tooltip'>"+ this.t +"</p>");
	$("#tooltip")
		.css("top",(e.pageY - xOffset) + "px")
		.css("left",(e.pageX + yOffset) + "px")
		.fadeIn("fast");
 		},function(){
			this.title = this.t;
			$("#tooltip").remove();
		}
	);
 $("a.tooltip").mousemove(
		function(e){
			$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
		}
	);
};

// starting the script on page load
$(document).ready(function(){
 tooltip();
});
</script>
<style type="text/css">
#tooltip{
	line-height: 1.231; font-family: Arial; font-size: 13px;
	position:absolute;
	border:1px solid #333;
	background:#f7f5d1;
	padding:2px 5px;
	display:none;
	width:267px;
	}
</style>

<!-- BEGIN Logo and Description Header -->
<div class="SkyBox">
	<div class="SkyBoxTopLeft">
		<div class="SkyBoxTopRight">
			<div class="SkyBoxBotLeft">
				<div class="SkyBoxBotRight">
					<table width="550" cellspacing="0" cellpadding="5" border="0">
						<tr valign="top">
							<td width="208" height="108" align="center" valign="middle">
								<?php /*
								       * Conditionally display logo of the provider.
								       * Values preset within this system denote the following:
								       * 1 = Google Adwords
								       * 2 = Yahoo Search Marketing
								       * 3 = MSN (Microsoft) Ad Center
								       */?>
								<?php if($this->ProviderType == 1):?>
									<img src="/Themes/BevoMedia/img/google.gif" border=0 alt="">
								<?php elseif($this->ProviderType == 2):?>
									<img src="/Themes/BevoMedia/img/yahoo2.gif" width="175" border=0 alt="">
								<?php elseif($this->ProviderType == 3):?>
									<img src="/Themes/BevoMedia/img/msn.gif" width="175" border=0 alt="">
								<?php endif?>
								
							</td>
							<td class="main">
							
								<h4><?php print $this->adGroupName; ?></h4>
							
								<br/>
								<p>Analyze your PPC expenses at the Keyword level. The time period of the data can be changed which will be reflected in the graph and table.</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ENDOF Logo and Description Header -->


<!-- BEGIN Blue Header Buttons -->
<table align="center" style="margin:auto;">
<tr>
	<td>
		<a href="PPCManager.html">
			<img src="/Themes/BevoMedia/img/overview_big.jpg"/>
		</a>
	</td>
	<td>
		<a href="CreatePPC.html">
			<img src="/Themes/BevoMedia/img/newcampaign_big.jpg"/>
		</a>
	</td>
	<td>
		<a href="PPCTutorials.html">
			<img src="/Themes/BevoMedia/img/tutorials_big.jpg"/>
		</a>
	</td>
</tr>
</table>
<!-- ENDOF Blue Header Buttons -->

<br class="clearBoth"/>


<!-- BEGIN Breadcrumbs -->
<div>
	<a href="AccountStatsPPC.html?ID=<?php print $this->AccountID; ?>&Provider=<?php print $this->Provider; ?>&DateRange=<?php print $this->DateRange; ?>"><?php print $this->AccountName; ?></a>
	&raquo;
	<a href='CampaignStatsPPC.html?ID=<?php print $this->CampaignID; ?>'><?php print $this->CampaignName; ?></a>
	&raquo;
	<?php print $this->AdGroupName; ?>
</div>
<!-- ENDOF Breadcrumbs -->


<!-- BEGIN Optimize Ad Variation URL -->
<div style="float: right;">
<?php if($this->Provider !== 'Yahoo'):?>
<a <?php echo($this->FormatURLAdsCount == 0)?'style="color: #888888;"':''?> title="There are <?php print $this->formatURLAdsCount; ?> ads that can be optimized." href="AdGroupAdURLFormat.html?ID=<?php print $this->AdGroupID; ?>">Optimize Ad Variation URL Format</a>
<?php endif?>
</div>
<!-- ENDOF Optimize Ad Variation URL -->


<!-- BEGIN View Keywords Link -->
<div style="float: left;">
<a href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>">View Ad Group Keywords</a>
</div>
<br class="clearBoth"/>
<!-- ENDOF View Keywords Link -->

<br/>

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

<!-- BEGIN Statistics Table -->
<table width="100%" cellspacing="0" class="btable">
	<tbody>
	<!-- BEGIN Table Header -->
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td colspan="2">
			<a href="AdGroupStatsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?>&sort=Name<?php echo (!isset($_GET['sort_order'])?'&sort_order=Desc':'')?>">
				Search Query
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
	
	<?php if(!sizeOf($this->QueryStats)):?>
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
	<?php foreach($this->QueryStats as $StatRow):?>
	<tr>
		<td colspan='8'>
			<strong><?php print $StatRow->Keyword->formattedName; ?></strong>
		</td>
		<td class="tail">&nbsp;</td>
	</tr>
		<?php if(sizeOf($StatRow->Stats) > 0):?>
		<?php foreach($StatRow->Stats as $Stat):?>
		<tr>
			<td class="border">&nbsp;</td>
			<td class="border">
				<a class="tooltip" title="<?php print $Stat->adVarPreview; ?>">
					<img width="12" src="/Themes/BevoMedia/img/icon_magnify.png" border=0/>
				</a>
			</td>
			<td>
				<?php print $Stat->name; ?>
			</td>
			<td style="text-align: center;">
				<?php print number_format($Stat->impressions, 0, '.', ','); ?>
			</td>
			<td style="text-align: center;">
				<?php print number_format($Stat->clicks, 0, '.', ','); ?>
			</td>
			<td style="text-align: center;">
				<?php print number_format($Stat->ctr*100, 0, '.', ','); ?>%
			</td>
			<td style="text-align: center;">
				<?php print number_format($Stat->avgCpc, 2, '.', ','); ?>
			</td>
			<td style="text-align: center;">
				$<?php print number_format($Stat->cost, 2, '.', ','); ?>
			</td>
			<td class="tail">&nbsp;</td>
		</tr>
		<?php endforeach?>
		<?php endif?>
	<?php endforeach?>
	<!-- ENDOF Ad Group Stats Row Loop -->
	
	<?php endif?>
	
	<!-- BEGIN Table Footer -->
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
	
		<td colspan="7">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<!-- ENDOF Table Footer -->
	</tbody>
</table>
<!-- ENDOF Statistics Table -->



<!-- BEGIN Export to CSV Link -->
<div class='floatRight'>
	<a target='_blank' href='_CSVExportAdGroupAdVariationsPPC.html?ID=<?php print $this->AdGroupID; ?>&DateRange=<?php print $this->DateRange; ?><?php echo (isset($_GET['sort'])?'&sort='.$_GET['sort']:'')?><?php echo (isset($_GET['sort_order'])?'&sort_order=Desc':'')?>'>
		Export to CSV
	</a>
</div>
<!-- ENDOF Export to CSV Link -->