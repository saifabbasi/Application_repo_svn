<script>
$(function() {
  today = Date.today().toString('MM/dd/yyyy');
  $('#sdate, #edate').val(today).datepicker();
  $('#AffAccts').toggle($('#Aff').attr('checked'));
  $('#PPCAccts').toggle($('#PPC').attr('checked'));
  $('#CampaignDivDelete').toggle($('#campaigns').attr('checked'));
  $('#CampaignStatsDivDelete').toggle($('#campaignStats').attr('checked'));
  $('#PPC').change(function() {
		$('#PPCAccts').toggle($('#PPC').attr('checked'));
  });
  $('#Aff').change(function() {
		$('#AffAccts').toggle($('#Aff').attr('checked'));
  });
  $('#campaigns').change(function() {
	  $('#CampaignDivDelete').toggle();
  });
  $('#campaignStats').change(function() {
	  $('#CampaignStatsDivDelete').toggle();
  });
});
</script>
<style>
  div.maindelete { margin: 5px; font-size: 15px; padding: 5px}
  div.subdelete { margin: 5px; border: 2px #ccc solid; padding: 5px; display: none; }
</style>

<div id="pagemenu">
	<ul>
		<li><a href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/ApiCalls.html">API Call History<span></span></a></li>
		<li><a href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/PPCQueueProgress.html">Campaign Editor Queue<span></span></a></li>
		<li><a class="active" href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/ManageStats.html">Delete Stats<span></span></a></li>
	</ul>
	<ul class="floatright"><li><a href="http://ryanbuke.com/" target="_blank">Official Bevo Blog<span></span></a></li></ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<form onsubmit="return confirm('Are you sure you want to delete these stats?');" method="POST">

	<b><?= $this->deleted ? "Your stats have been cleared!<br /><br />" : ''?></b>
	
	Date Range: 
	
	<input class="formtxt" type="text" id="date" name="date" value="" />
	
	<?php /*
	
		//TODO: rewrite the controller to accept the POST date range in datepicker format using the normal datepicker (below)
		//then we can replace the above date fields with the below
		
		<table cellspacing="0" cellpadding="0" class="datetable floatleft">
			<tr>
				<td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>" /></td>
				<td><input class="formsubmit" type="submit" /></td>
			</tr>
		</table>
		
		*/ ?>

<div class="clear"></div>

<div id="KWDiv" class="maindelete">
	<input type="checkbox" name="kw" id="kw" /> Delete Tracker Stats
</div>
<div id="PPCDiv" class="maindelete">
	<input type="checkbox" name="ppc" id="PPC" /> Delete PPC Stats
  
	<div id="PPCAccts" class="subdelete">
		<table width='100%'>
			<tr>
				<td width="33%"><img src='/Themes/BevoMedia/img/adwords_logo.png' /></td>
				<td width="33%"><img src='/Themes/BevoMedia/img/ysm_logo.png' /></td>
				<td width="33%"><img src='/Themes/BevoMedia/img/adcenter.jpg' /></td>
			</tr>
			<tr>
				<td>
				<?php
					if (is_array($this->AdwordsResults)) 
					foreach($this->AdwordsResults as $Result):
				?>
					<input type="checkbox" checked="checked" class="PPCAcct Adwords" name="Adwords[<?=$Result->id?>]" /><?php print $Result->username?><br />
				<?php endforeach?>
				<?php if(!sizeOf($this->AdwordsResults)):?>
					<br/>
					<a href="/BevoMedia/Publisher/Index.html#PPC">
						You do not currently have any Adwords accounts installed. Please click here to install.
					</a>
				<?php endif?>
				</td>
				<td>
				<?php
					if (is_array($this->YahooResults)) 
					foreach($this->YahooResults as $Result):
				?>
					<input type="checkbox" checked="checked" class="PPCAcct Yahoo" name="Yahoo[<?=$Result->id?>]" /><?php print $Result->username?><br />
				<?php endforeach?>
				<?php if(!sizeOf($this->YahooResults)):?>
					<br/>
					<a href="/BevoMedia/Publisher/Index.html#PPC">
						You do not currently have any Yahoo accounts installed. Please click here to install.
					</a>
				<?php endif?>
				</td>
				<td>
				<?php
					if (is_array($this->MSNResults)) 
					foreach($this->MSNResults as $Result):
				?>
					<input type="checkbox" checked="checked" class="PPCAcct MSN" name="MSN[<?=$Result->id?>]" /><?php print $Result->username?><br />
				<?php endforeach?>
				<?php if(!sizeOf($this->MSNResults)):?>
					<br/>
					<a href="/BevoMedia/Publisher/Index.html#PPC">
						You do not currently have any MSN accounts installed. Please click here to install.
					</a>
				<?php endif?>
				</td>
			</tr>
		</table>
	</div>
</div><!--close #PPCDiv-->

<div id="AffDiv" class="maindelete">
	<input type="checkbox" name="aff" id="Aff" /> Delete Affiliate Network Stats and SubIDs
	
	<div class="subdelete" id="AffAccts">
		<table width="100%">
		  <tr>
			<th>&nbsp;</th>
			<th>Network</th>
			<th>Account</th>
		  </tr>
		  <tr>
		  	<td><input type="checkbox" checked="checked" name="AffNetwork[0]" /></td>
		  	<td>Manually Uploaded SubIDs</td>
		  	<td>&nbsp;</td>
		  </tr>
		  <?
		  	if (is_array($this->AffResults)) 
		  	foreach($this->AffResults as $Result) { 
	  	  ?>
		  <tr>
		  	<td><input type="checkbox" checked="checked" name="AffNetwork[<?=$Result->network__id?>]" /></td>
		  	<td>
				<img src="/Themes/BevoMedia/img/networklogos/<?php print $Result->network__id; ; ?>.png" alt="<?php print htmlentities($Result->title); ; ?>" />
			</td>
			<td><?= $Result->loginId?></td>
		  </tr>
		  <? } ?>
		</table>
	</div>
</div><!--close #AffDiv-->


<div id="CampaignDiv" class="maindelete">
	<input type="checkbox" name="campaigns" id="campaigns" /> Delete Campaigns
	
	<div class="subdelete" id="CampaignDivDelete">
	<?php 
		foreach ($this->PPCCampaigns as $Campaign)
		{
	?>
		<label>
			<input type="checkbox" name="deleteCampaigns[]" value="<?=$Campaign->id?>" /> <?=htmlentities($Campaign->name)?>
		</label>
		<br />
	<?php 
		}
	?>
	</div>	
</div>



<div id="CampaignStatsDiv" class="maindelete">
	<input type="checkbox" name="campaignStats" id="campaignStats" /> Delete Campaign Stats
	
	<div class="subdelete" id="CampaignStatsDivDelete">
	<?php 
		foreach ($this->PPCCampaigns as $Campaign)
		{
	?>
		<label>
			<input type="checkbox" name="deleteCampaignStats[]" value="<?=$Campaign->id?>" /> <?=htmlentities($Campaign->name)?>
		</label>
		<br />
	<?php 
		}
	?>
	</div>	
</div>


<input class="formsubmit delstat_delete" type="submit" name="Delete" value="Delete" />
</form>

<script type="text/javascript">
	$(document).ready(function() {

		$('#date').daterangepicker();

	});
</script>
