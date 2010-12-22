<script>
parent.sb = document;
function doClone()
{
  M_STR = 'bevo_c={AdId}';
  G_STR = 'bevo_c={creative}&bevo_k={keyword}&bevo_m={ifsearch:s}{ifcontent:c}';
  parent.$('.cloneTo:checked', document).each(function(i, obj) { 
    temp = obj.id.split('_')[1];
    ToType = temp.split('-')[0];
    FromType = "<?php echo $this->UPPER_PROVIDERS[$this->Campaign->providerType]; ?>";
    AccountID = temp.split('-')[1];
    campaign = parent.createppc.newCampaignObject();
    campaign.checked = true;
    campaign.curAccountID = AccountID;
    campaign.curAccountType = ToType;
    campaign.curCampaignID = (parent.createppc.newCampaigns.length * -1);
    campaign.name = "<?php echo $this->Campaign->name?>";
    campaign.budget = "<?php echo $this->Campaign->budget?>";
    campaign.searchtarget = "<?php echo $this->Campaign->searchTarget ?>";
    campaign.negativekeywords = Array(<?php $comma = ''; foreach($this->Campaign->negativeKeywords as $kw) { echo $comma .'"'.preg_replace('/^-/', '', $kw) .'"'; $comma = ','; } ?> );
    campaign.geotargets = { countries: Array( <?php $comma = ''; foreach($this->Campaign->geotargetCountries as $country) { echo $comma ."'$country' "; $comma = ','; } ?> ), };
    campaign = parent.createppc._addCampaignObj(campaign);

<?php foreach($this->AdGroups as $ag) { ?>
	  tempAG = parent.createppc.newAdGroupObject();
	  tempAG.checked = true;
	  tempAG.curAccountType = ToType;
	  tempAG.curAccountID = AccountID;
	  tempAG.curCampaignID = campaign.curCampaignID;
	  tempAG.name = "<?php echo $ag->name ?>";
	  tempAG.addistribution = "<?php echo $ag->adDistribution ?>";
	  tempAG.bid = "<?php echo $ag->bid?>";
	  tempAG.contentbid = "<?php echo $ag->contentBid?>";
	  tempAG.negativekeywords = Array(<?php $comma = ''; foreach($ag->negativeKeywords as $kw) {echo $comma . ' "'.preg_replace('/^-/', '', $kw) . '"'; $comma = ','; } ?> );
	  tempAG = parent.createppc._addAdGroupObj(tempAG);

	  <?php foreach($ag->Keywords as $kw) { ?>

		tempKW = parent.createppc.newKeywordObject();
		tempKW.checked = true;
		tempKW.curAccountType = ToType;
		tempKW.curAccountID = AccountID;
		tempKW.curCampaignID = campaign.curCampaignID;
		tempKW.curAdGroupID = tempAG.curAdGroupID;
		tempKW.keyword = "<?php echo $kw->Keyword ?>";
		tempURL = "<?php echo $kw->destURL ?>";
		if(FromType == 'ADWORDS' && FromType != ToType && tempURL.match(G_STR))
		{
		  tempURL = tempURL.replace(G_STR, '');
		  if(ToType == 'MSN')
			tempURL = addUrlVarsMSN(tempURL);
		}
		if(FromType == 'MSN' && FromType != ToType && tempURL.match(M_STR))
		{
		  tempURL = tempURL.replace(M_STR, '');
		  if(ToType == 'ADWORDS')
			tempURL = addUrlVarsAdwords(tempURL);
		}
		tempKW.destinationurl = tempURL;
		tempKW.bid = "<?php echo $kw->maxCPC ?>";
		tempKW.advMatch = "<?php echo $kw->matchType ?>";
		tempKW = parent.createppc._addKeywordsObj(tempKW);
	  <?php } ?>

	  <?php foreach($ag->Variations as $Var) { ?>

		tempAV = parent.createppc.newAdVariationObject();
		tempAV.checked = true;
		tempAV.curAccountType = ToType;
		tempAV.curAccountID = AccountID;
		tempAV.curCampaignID = campaign.curCampaignID;
		tempAV.curAdGroupID = tempAG.curAdGroupID;
		tempAV.description = "<?php echo $Var->description ?>";
		tempURL = "<?php echo $Var->url ?>";
		if(FromType == 'ADWORDS' && FromType != ToType && tempURL.match(G_STR))
		{
		  tempURL = tempURL.replace(G_STR, '');
		  if(ToType == 'MSN')
			tempURL = addUrlVarsMSN(tempURL);
		}
		if(FromType == 'MSN' && FromType != ToType && tempURL.match(M_STR))
		{
		  tempURL = tempURL.replace(M_STR, '');
		  if(ToType == 'ADWORDS')
			tempURL = addUrlVarsAdwords(tempURL);
		}
		tempAV.destinationurl = tempURL;
		tempAV.displayurl = "<?php echo $Var->displayUrl ?>";
		tempAV.title = "<?php echo $Var->title ?>";
		tempAV = parent.createppc._addAdVarsObj(tempAV);
  <?php } ?>
<?php } ?>
  });
  parent.createppc.selectMenuTab('review');
  parent.Shadowbox.close();
}
</script>
<h4>Choose which accounts to clone this campaign to:</h4>
	<table width='100%' id='clone-campaign-table'>
			<tr>
				<td width="33%"><img src='/Themes/BevoMedia/img/adwords_logo.png'></td>
				<td width="33%"><img src='/Themes/BevoMedia/img/ysm_logo.png'></td>
				<td width="33%"><img src='/Themes/BevoMedia/img/adcenter.jpg'></td>
			</tr>
			<tr>
				<td>
				<?php foreach($this->AdwordsResults as $Result):?>
                    <input type="checkbox" class="cloneTo" id='account_ADWORDS-<?php print $Result->id; ?>'><a class='account-select' href="#"><label for="account_ADWORDS-<?php print $Result->id ?>"><?php print $Result->username?></label></a><br />
				<?php endforeach?>
				<?php if(!sizeOf($this->AdwordsResults)):?>
					<br/>
					<a href="/BevoMedia/Publisher/Index.html?OpenPPC=GoogleAdwords">
						You do not currently have any Adwords accounts installed. Please click here to install.
					</a>
				<?php endif?>
				</td>
				<td>
				<?php foreach($this->YahooResults as $Result):?>
                    <input type="checkbox" class="cloneTo" id='account_YAHOO-<?php print $Result->id; ?>'><a class='account-select' href="#"><label for="account_YAHOO-<?php print $Result->id ?>"><?php print $Result->username?></label></a><br />
				<?php endforeach?>
				<?php if(!sizeOf($this->YahooResults)):?>
					<br/>
					<a href="/BevoMedia/Publisher/Index.html?OpenPPC=Yahoo">
						You do not currently have any Yahoo accounts installed. Please click here to install.
					</a>
				<?php endif ?>
				</td>
				<td>
				<?php foreach($this->MSNAdCenterResults as $Result):?>
                    <input type="checkbox" class="cloneTo" id='account_MSN-<?php print $Result->id; ?>'><a class='account-select' href="#"><label for="account_MSN-<?php print $Result->id ?>"><?php print $Result->username?></label></a><br />
				<?php endforeach?>
				<?php if(!sizeOf($this->MSNAdCenterResults)):?>
					<br/>
					<a href="/BevoMedia/Publisher/Index.html?OpenPPC=MSNAdCenter">
						You do not currently have any MSN accounts installed. Please click here to install.
					</a>
				<?php endif?>
				</td>
			</tr>
		</table>
<a href="#" onClick="javascript:doClone()"><h3>All done</h3></a>