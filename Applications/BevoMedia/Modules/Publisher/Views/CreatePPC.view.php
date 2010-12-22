<script src='/Themes/BevoMedia/createppc.js'></script>
<script src='/Themes/BevoMedia/LayoutAssist.js'></script>

<script language="javascript">
<?php foreach($this->campaignSet as $cS):?>
<?php foreach($this->{$cS} as $AccountID=>$Campaigns):?>
<?php foreach($Campaigns as $Campaign):?>
createppc.addExistCampaign('<?php print $Campaign->accountId; ?>','<?php print $Campaign->providerType; ?>','<?php print $Campaign->id; ?>','<?php print str_replace("'", '', htmlentities($Campaign->name)); ?>');
<?php endforeach?>
<?php endforeach?>
<?php endforeach?>

<?php foreach($this->adgroupSet as $campaignID=>$AdGroups):?>
<?php foreach($AdGroups as $AdGroup):?>
createppc.addExistAdGroup('<?php print $campaignID?>','<?php print $AdGroup->id; ; ?>','<?php print str_replace("'", '', htmlentities($AdGroup->name)); ?>');
<?php endforeach?>
<?php endforeach?>

<?php foreach($this->advarSet as $adGroupID=>$AdVariations):?>
<?php foreach($AdVariations as $AdVariation):?>
createppc.addExistAdVariation('<?php print $adGroupID?>','<?php print $AdVariation->id; ; ?>','<?php print str_replace("'", '', htmlentities($AdVariation->title)); ?>', '<?php print str_replace("'", '', htmlentities($AdVariation->description)); ?>','<?php print str_replace("'", '', htmlentities($AdVariation->displayUrl)); ?>','<?php print str_replace("'", '', htmlentities($AdVariation->url)); ?>','<?php print $AdVariation->apiAdId; ?>');
<?php endforeach?>
<?php endforeach?>

<?php foreach($this->keywordSet as $adGroupID=>$Keywords):?>
<?php foreach($Keywords as $Keyword):?>
createppc.addExistKeyword('<?php print $adGroupID?>','<?php print $Keyword->id?>','<?php print str_replace(array("'","\'"), '', htmlentities($Keyword->Keyword)); ?>','<?php print $Keyword->maxCPC; ?>','<?php print $Keyword->apiKeywordId; ?>');
<?php endforeach?>
<?php endforeach?>

<?php foreach($this->Countries as $Country):?>
createppc.addGeoTargetCountry("<?php print $Country->code; ?>", "<?php print $Country->country; ?>");
<?php endforeach?>

var clone_box;
</script>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
			<li><a class="active" href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
		</ul>
		<ul class="floatright">
			<li class="label" id="createppc_currentLabel-account"><b>Account:</b> (select below)</li>
			<li class="label" id="createppc_currentLabel-campaign"><b>Campaign:</b> (select below)</li>
			<li class="label" id="createppc_currentLabel-adgroup"><b>Ad Group:</b> (select below)</li>
		</ul>
	</div>
	
	<div id="pagesubmenu">
		<ul>
			<li><a href="#" id='createppc_menu-account' accesskey='a' title='Account Tab (Access Key: A)'>Account</a></li>
			<li><a href="#" id='createppc_menu-campaign' accesskey='s' title='Campaign Tab (Access Key: S)'>Campaign</a></li>
			<li><a href="#" id='createppc_menu-adgroup' accesskey='d' title='Ad Group Tab (Access Key: D)'>Ad Group</a></li>
			<li><a href="#" id='createppc_menu-advars' accesskey='f' title='Ad Variations Tab (Access Key: F)'>Ad Variations</a></li>
			<li><a href="#" id='createppc_menu-keywords' accesskey='g' title='Keywords Tab (Access Key: G)'>Keywords</a></li>
		</ul>
		<ul class="floatright">
			<li><a href="#" id='createppc_menu-review' accesskey='r' title='Review Tab (Access Key: R)'>Review</a></li>
			<li><a href="#" id='createppc_menu-output' accesskey='t' title='Output Tab (Access Key: T)'>Output</a></li>
		</ul>
		<div class="clear"></div>
	</div>

	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
	
<div id="createppc">
<div id='createppc_content-account'>
	<div class="box box3">
		<div class="imgbox"><img src="/Themes/BevoMedia/img/adwords_logo.png" alt="" /></div>
		
		<?php foreach($this->AdwordsResults as $Result):?>
			<a class='account-select' id='account_ADWORDS-<?php print $Result->id; ?>' href='#' onClick="javascript:createppc.selectAccount('ADWORDS', '<?php print $Result->id; ?>', '<?php print $Result->username; ?>'); return false;"><?php print $Result->username?></a>
		<?php endforeach?>
		<?php if(!sizeOf($this->AdwordsResults)):?>
			<a class="noticelink" href="/BevoMedia/Publisher/Index.html#PPC">
				You do not currently have any Adwords accounts installed. Please click here to install.
			</a>
		<?php endif?>
	
	</div><!--close box-->
	<div class="box box3">
		<div class="imgbox"><img src="/Themes/BevoMedia/img/ysm_logo.png" alt="" /></div>
		
		<?php foreach($this->YahooResults as $Result):?>
			<a class='account-select' id='account_YAHOO-<?php print $Result->id; ?>' href='#' onClick="javascript:createppc.selectAccount('YAHOO', '<?php print $Result->id; ?>', '<?php print $Result->username; ?>'); return false;"><?php print $Result->username?></a>
		<?php endforeach?>
		<?php if(!sizeOf($this->YahooResults)):?>
			<a class="noticelink" href="/BevoMedia/Publisher/Index.html#PPC">
				You do not currently have any Yahoo accounts installed. Please click here to install.
			</a>
		<?php endif?>
	
	</div><!--close box-->
	<div class="box box3 nomargin">
		<div class="imgbox"><img src="/Themes/BevoMedia/img/adcenter.jpg" alt="" /></div>
		
		<?php foreach($this->MSNAdCenterResults as $Result):?>
			<a class='account-select' id='account_MSN-<?php print $Result->id; ?>' href='#' onClick="javascript:createppc.selectAccount('MSN', '<?php print $Result->id; ?>', '<?php print $Result->username; ?>'); return false;"><?php print $Result->username?></a>
		<?php endforeach?>
		<?php if(!sizeOf($this->MSNAdCenterResults)):?>
			<a class="noticelink" href="/BevoMedia/Publisher/Index.html#PPC">
				You do not currently have any MSN accounts installed. Please click here to install.
			</a>
		<?php endif?>
	
	</div><!--close box-->
	<div class="clear"></div>
</div><!--close #createppc_content-account-->

<div id='createppc_content-campaign'>
	<div class="box box2" id="existCampaignHolder">
		<h3>Select Existing Campaign</h3>
		
		<div id='existCampaignClone' style="display:none;">
			<a href='#' onClick="javascript:createppc.selectCampaign('{CAMPAIGN_ID}','{CAMPAIGN_NAME}'); return false;" class='campaign-select'>
				{CAMPAIGN_NAME}
			</a>
			
			<a class='floatRight' href='#' onclick="Shadowbox.open({content: 'CreatePPC_CrossPostExistingCampaign.html?ID={CAMPAIGN_ID}', player: 'iframe', title: '{CAMPAIGN_NAME}', height: 480, width: 640});">Clone Campaign</a>
			<br class='clearBoth'/>
		</div>
	
	</div><!--close box-->
	<div class="box box2 nomargin" id="newCampaignHolder">
		<h3>Select New Campaign</h3>
		
		<div id='newCampaignClone' style="display:none;">
			<a href='#' onClick="javascript:createppc.selectCampaign('{CAMPAIGN_ID}','{CAMPAIGN_NAME}'); return false;" class='campaign-select'>
				{CAMPAIGN_NAME}
			</a>
			<a class='floatRight' href='#' onClick="javascript:createppc.removeCampaign('{CAMPAIGN_ID}'); return false;">Remove</a>
			<a class='floatRight marginRight5Px' href='#' onClick="javascript:createppc.editCampaign('{CAMPAIGN_ID}'); return false;">Edit</a>
			<br class='clearBoth'/>
		</div>

	</div><!--close box-->
	<div class="clear"></div>
	
	<div class="box boxfull bordertop">
		<h3 id='create-campaign-form-h2-label'>Create Campaign</h3>
		<form name='create-campaign-form-ADWORDS' class="appform">
			<label>
				<span>Campaign Name:</span>
				<input class="formtxt" type='text' name='campaign-name' />
			</label>
			
			<label>
				<span>Daily Budget:</span>
				<input class="formtxt wide_number" type='text' name='campaign-budget' autocomplete="off" />
			</label>
			
			<label>
				<span>Content Network:</span>
				<input class="formcheck" type='checkbox' value='Search' checked='checked' name='adgroup-addistribution' /> Google Search
			</label>
			
			<label>
				<span>&nbsp;</span>
				<input class="formcheck" type='checkbox' value='Content' checked='checked' name='adgroup-addistribution' /> Content Network
			</label>
			
			<label>
				<span>Geotargeting:</span>
				<a class="tbtn" href='SelectAdwordsGeoTargets.html' rel='shadowbox;width=800;height=480;player=iframe'>Select Country Geotargets</a>
			</label>
			
			<label>
				<span>&nbsp;&nbsp;<i>Countries:</i></span>
				<div style='display:block; padding-left:105px;' id='campaign-geotargets-ADWORDS'>
					<input type='hidden' name='country-geotargets[]' value='US'/><i>United States</i>
				</div>
			</label>
			<br/>
			
			<label>
				<span>Campaign Negative Keywords:</span>
				<textarea class="formtxtarea" name='campaign-neg'></textarea>
			</label>
			
			<label>
				<span>&nbsp;</span>
				<i>Separate negative keywords one per line.</i>
			</label>
				
			
			<input class="formsubmit cpe_addcampaign" type="submit" onClick='javascript: createppc.addCampaign(); return false;' name='campaign-submit' value='Add Campaign' />
			
		</form>
		<form name='create-campaign-form-YAHOO' class="appform">
			<label>
				<span>Campaign Name:</span>
				<input class="formtxt" type='text' name='campaign-name' />
			</label>
			
			<label>
				<span>Daily Spending Limit:</span>
				<input class="formtxt wide_number" type='text' value="" name='campaign-budget' autocomplete="off" />
			</label>
			
			<br style='clearBoth'/>
			
			<label>
				<span>Geotargeting:</span>
				<a class="tbtn" href='SelectAdwordsGeoTargets.html' rel='shadowbox;width=800;height=480;player=iframe'>Select Country Geotargets</a>
			</label>
			
			<label>
				<span>&nbsp;&nbsp;<i>Countries:</i></span>
				<div style='display:block; padding-left:105px;' id='campaign-geotargets-YAHOO'>
					<input type='hidden' name='country-geotargets[]' value='US'/><i>United States</i>
				</div>
				<br/><i>Yahoo accounts can only target regions within their scope.<br/>For example, if your account is US-based, you will only be allowed to target US-based regions.<br/><a href="http://searchmarketing.yahoo.com/international.php" target="_blank">More Information may be found here.</a></i><br/>
			</label>
			<br/>
			
			<input class="formsubmit cpe_addcampaign" type='submit' onClick='javascript: createppc.addCampaign(); return false;' name='campaign-submit' id='YAHOO-campaign-submit' value='Add Campaign' />
			
		</form>
		<form name='create-campaign-form-MSN' class="appform">
			<label>
				<span>Campaign Name:</span>
				<input class="formtxt" type='text' name='campaign-name' />
			</label>
			
			<label>
				<span>Daily Budget:</span>
				<input class="formtxt wide_number" type='text' <?php echo ($cS == 'YAHOO')?'disabled="DISABLED" value="0"':''?> name='campaign-budget' autocomplete="off" />
			</label>
			
			<label style="display:none;">
				<span>Geotargeting:</span>
				<a class="tbtn" href='SelectAdwordsGeoTargets.html' rel='shadowbox;width=800;height=480;player=iframe'>Select Country Geotargets</a>
			</label>
			
			<label style="display:none;">
				<span>&nbsp;&nbsp;<i>Countries:</i></span>
				<div style='display:none; padding-left:105px;' id='campaign-geotargets-MSN'>
					<input type='hidden' name='country-geotargets[]' value='US'/><i>United States</i>
				</div>
			</label>
			<br style="display:none;"/>
			
			
			<label>
				<span>Campaign Negative Keywords:</span>
				<textarea class="formtxtarea" name='campaign-neg'></textarea>
			</label>
			
			<label>
				<span>&nbsp;</span>
				<i>Separate negative keywords one per line.</i>
			</label>
				
			<input class="formsubmit cpe_addcampaign" type='submit' onClick='javascript: createppc.addCampaign(); return false;' name='campaign-submit' value='Add Campaign' />
			
		</form>	
	</div><!--close box-->
</div><!--close #createppc_content-campaign-->

<div id='createppc_content-adgroup'>
	<div class="box box2" id="existAdGroupHolder">
		<h3>Select Existing AdGroup</h3>
		
		<div id='existAdGroupClone' style='display:none;'>
			<a href='#' onClick="javascript:createppc.selectAdGroup('{ADGROUP_ID}','{ADGROUP_NAME}'); return false;" class='tbtn campaign-select'>
				{ADGROUP_NAME}
			</a>
			<div class="clear"></div>
		</div>	
	</div><!--close box-->
	<div class="box box2 nomargin" id="newAdGroupHolder">
		<h3>Select New AdGroup</h3>
		
		<div id='newAdGroupClone' style='display:none;'>
			<a href='#' onClick="javascript:createppc.selectAdGroup('{ADGROUP_ID}','{ADGROUP_NAME}'); return false;" class='tbtn campaign-select'>
				{ADGROUP_NAME}
			</a>
			<a class='floatRight tbtn' href='#' onClick="javascript:createppc.removeAdGroup('{ADGROUP_ID}'); return false;">Remove</a>
			<a class='floatRight marginRight5Px tbtn' href='#' onClick="javascript:createppc.editAdGroup('{ADGROUP_ID}'); return false;">Edit</a>
			<div class="clear"></div>
		</div>
	</div><!--close box-->
	<div class="clear"></div>
	
	<div class="box boxfull bordertop">
		<h3 id='create-adgroup-form-h2-label'>Create AdGroup</h3>
		<form name='create-adgroup-form-ADWORDS' class="appform smallform">
			<label>
				<span>AdGroup Name:</span>
				<input class="formtxt" type='text' name='adgroup-name' />
			</label>
	
			<label>
				<span>Search CPC:</span>
				<input class="formtxt wide_number" type='text' value='' name='adgroup-bid' autocomplete="off" />
			</label>
	
			<label>
				<span>Content CPC:</span>
				<input class="formtxt wide_number" type='text' value='' name='adgroup-content-bid' autocomplete="off" />
			</label>

			<label>
				<span>Ad Group Negative Keywords:</span>
				<textarea class="formtxtarea" name='adgroup-neg'></textarea>
			</label>
			
			<label>
				<span>&nbsp;</span>
				<i>Separate negative keywords one per line.</i>
			</label>
			
			<input class="formsubmit cpe_addadgroup" type='submit' onClick='javascript: createppc.addAdGroup(); return false;' name='adgroup-submit' value='Add AdGroup' id='ADWORDS-adgroup-submit'/>
		</form>
		
		<form name='create-adgroup-form-YAHOO' class="appform">
			<label>
				<span>AdGroup Name:</span>
				<input class="formtxt" type='text' name='adgroup-name' />
			</label>
	
			<label>
				<span>Search CPC:</span>
				<input class="formtxt wide_number" type='text' value='' id='yahoo-adgroup-bid-id' name='adgroup-bid' autocomplete="off" />
			</label>
	
			<label>
				<span>Content CPC:</span>
				<input class="formtxt wide_number" type='text' value='' id='yahoo-adgroup-content-bid-id' name='adgroup-content-bid' autocomplete="off" />
			</label>
			
			<label>
				<span>Traffic Source:</span>
				<input class="formcheck" type='checkbox' value='Search' checked='checked' name='adgroup-addistribution'
						onClick='LayoutAssist.toggleEnabledAndValue("yahoo-adgroup-bid-id",this.checked,"0.00")' /> Search
			</label>
			
			<label>
				<span>&nbsp;</span>
				<input class="formcheck" type='checkbox' value='Content' checked='checked' name='adgroup-addistribution'
						onClick='LayoutAssist.toggleEnabledAndValue("yahoo-adgroup-content-bid-id",this.checked,"0.00")' /> Content
			</label>
			
			<label>
				<span>Ad Group Negative Keywords:</span>
				<textarea class="formtxtarea" name='adgroup-neg'></textarea>
			</label>
			
			<label>
				<span>&nbsp;</span>
				<i>Separate negative keywords one per line.</i>
			</label>
			
			<input class="formsubmit cpe_addadgroup" type='submit' onClick='javascript: createppc.addAdGroup(); return false;' name='campaign-submit' value='Add AdGroup' id='YAHOO-adgroup-submit'/>
		</form>
		
		<form name='create-adgroup-form-MSN' class="appform">
			<label>
				<span>AdGroup Name:</span>
				<input class="formtxt" type='text' name='adgroup-name' />
			</label>
	
			<label>
				<span>Search CPC:</span>
				<input class="formtxt wide_number" type='text' value='' id='msn-adgroup-bid-id' name='adgroup-bid' autocomplete="off" />
			</label>
	
			<label>
				<span>Content CPC:</span>
				<input class="formtxt wide_number" type='text' value='' id='msn-adgroup-content-bid-id' name='adgroup-content-bid' autocomplete="off" />
			</label>
			
			<label>
				<span>Traffic Source:</span>
				<input class="formcheck" type='checkbox' value='Search' checked='checked' name='adgroup-addistribution'
						onClick='LayoutAssist.toggleEnabledAndValue("msn-adgroup-bid-id",this.checked,"0.00")' /> Search
			</label>
			
			<label>
				<span>&nbsp;</span>
				<input class="formcheck" type='checkbox' value='Content' checked='checked' name='adgroup-addistribution'
						onClick='LayoutAssist.toggleEnabledAndValue("msn-adgroup-content-bid-id",this.checked,"0.00")' /> Content
			</label>
						
			<label>
				<span>Ad Group Negative Keywords:</span>
				<textarea class="formtxtarea" name='adgroup-neg'></textarea>
			</label>
			
			<label>
				<span>&nbsp;</span>
				<i>Separate negative keywords one per line.</i>
			</label>
			
			<input class="formsubmit cpe_addadgroup" type='submit' onClick='javascript: createppc.addAdGroup(); return false;' name='campaign-submit' value='Add AdGroup' id='MSN-adgroup-submit'/>
		</form>
	</div><!--close box-->
	
	<div class="box boxfull bordertop">
		<h3>Bulk Create Ad Groups</h3>
		
		<form name='create-bulk-adgroup-form-ADWORDS' class="appform">
		
			<div class="formbox wide2x3">
				<label>
					<span>Enter an Ad Group and its Default CPC seperated by tabs. A new line for each Ad Group.</span><br />
					<pre>ADGROUPNAME	DEFAULTCPC</pre>
					<div class="clear"></div>
					<textarea class='bulkTextBox formtxtarea' onkeydown='javascript:createppc.checkTab(event);' rows='4' name='bulk-adgroup'></textarea><!--bulkTextBox-->
					<div class="clear"></div>
				</label>
			</div>
			<div class="formbox wide3 nomargin topspace">
				<label>
					<span>Ad Group Negative Keywords:</span>
					<textarea class="formtxtarea" name="bulk-adgroup-neg"></textarea>
					<i>Separate negative keywords one per line.</i>
				</label>
			</div>
				<div class="clear"></div>
			<input class="formsubmit cpe_bulkadd" type='submit' onClick='javascript: createppc.addAdGroupBulk(); return false;' name='adgroup-bulk-button' value='Add Ad Group Bulk' />
		</form>
		
		<form name='create-bulk-adgroup-form-YAHOO'>
			<p>Enter an Ad Group, its Search CPC and its Content CPC seperated by tabs. A new line for each Ad Group.</p>
			
			<pre>ADGROUPNAME	SEARCHCPC	CONTENTCPC</pre>
			<textarea class='bulkTextBox formtxtarea' onkeydown='javascript:createppc.checkTab(event);' rows='4' name='bulk-adgroup'></textarea>
			<br/>
			<label>
				<span>Ad Group Negative Keywords:</span>
				<textarea class="formtxtarea" name="bulk-adgroup-neg"></textarea>
			</label>
			<label>
				<span>&nbsp;</span>
				<i>Separate negative keywords one per line.</i>
			</label>
			<input class="formsubmit cpe_bulkadd" type='submit' onClick='javascript: createppc.addAdGroupBulk(); return false;' name='adgroup-bulk-button' value='Add Ad Group Bulk' />
		</form>
		
		<form name='create-bulk-adgroup-form-MSN'>
			<p>
				Enter an Ad Group and its Default CPC seperated by tabs. A new line for each Ad Group. <br/>
			</p>
			<pre>ADGROUPNAME	DEFAULTCPC</pre>
			<textarea class='bulkTextBox formtxtarea' onkeydown='javascript:createppc.checkTab(event);' rows='4' name='bulk-adgroup'></textarea>
			
			<br/>
			<label>
				<span>Traffic Source:</span>
				<input class="formcheck" type='checkbox' value='Search' checked='checked' name='adgroup-addistribution' /> Search
			</label>
			
			<label>
				<span>&nbsp;</span>
				<input class="formcheck" type='checkbox' value='Content' checked='checked' name='adgroup-addistribution' /> Content
			</label>
			
			<br/>
			<label>
				<span>Ad Group Negative Keywords:</span>
				<textarea class="formtxtarea" name="bulk-adgroup-neg"></textarea>
			</label>
			<label>
				<span>&nbsp;</span>
				<i>Separate negative keywords one per line.</i>
			</label>
			<input class="formsubmit cpe_bulkadd" type='submit' onClick='javascript: createppc.addAdGroupBulk(); return false;' name='adgroup-bulk-button' value='Add Ad Group Bulk' />
		</form>	
	</div><!--close box-->
</div><!--close #createppc_content-adgroup-->

<div id='createppc_content-advars'>
	<div class="box boxfull borderbutt" id="advars-holder_0">
		Please select an ad group...
	</div><!--close box-->
	
	<div class="box boxfull borderbutt hasselect">
		<p class="floatleft">Ad Groups in this Campaign:</p> <select class="formselect" id='advarsAdgroupSelect'></select>
	</div><!--close box-->
	
	<div class="box box2" id="existAdVariationHolder">
		<h3>Existing Ad Variations</h3>
		
		<div id='existAdVariationClone' style='display:none;'>
			<p class='advariation'><a target='_blank' href='{URL}'>{TITLE}</a><br/><span>{DESCRIPTION}</span><br><span class='display_url'>{DISPLAY_URL}</span><br/></p>
			<a id='removeAdVarsElement_{ADVAR_API_ID}' href="javascript:createppc.removeAdVarsAPI('{ADVAR_API_ID}')" class='tbtn'>Delete</a>
			<div class="clear"></div>
		</div>		
	
	</div><!--close box-->
	<div class="box box2 nomargin" id="newAdVariationHolder">
		<h3>New Ad Variations</h3>
		
		<div id='newAdVariationClone' style='display:none;'>
			<p class='advariation'><a target='_blank' href='{URL}'>{TITLE}</a><br/><span>{DESCRIPTION}</span><br><span class='display_url'>{DISPLAY_URL}</span><br/></p>
			<a class='tbtn' href='#' onClick="javascript:createppc.removeAdVars('{ADVAR_ID}'); return false;">Remove</a>
			<div class="clear"></div>
		</div>
	
	</div><!--close box-->
	<div class="clear"></div>
	
	<div class="box boxfull bordertop ">
		<h3>Create Ad Variation</h3>
		
		<form name='create-advar-form-ADWORDS' class="appform">
			<div class='floatleft' id='advar-preview-ADWORDS'>
				<p class='advariation'>
					<a target='_blank' href="http://www.destination.url">Title</a><br/>
					<span>Ad Variation Description</span><br>
					<span class='display_url'>http://www.display.url</span><br/>
				</p>
			</div>
			
			<div class="formbox wide2x3">
				<label>
					<span>Headline:</span>
					<input class="formtxt" type='text' name='advar-title' maxlength="25" autocomplete="off" />
				</label>
	
				<label>
					<span>Line 1:</span>
					<input class="formtxt" type='text' name='advar-description-line1' maxlength="35" autocomplete="off"/>
				</label>
				<label>
					<span>Line 2:</span>
					<input class="formtxt" type='text' name='advar-description-line2' maxlength="35" autocomplete="off" />
				</label>
				
				<label>
					<span>Display URL:</span>
					<input class="formtxt" type='text' value='' name='advar-displayurl' maxlength="35" />
				</label>
				
				<label>
					<span>Destination URL:</span>
					<input class="formtxt" type='text' value='http://' name='advar-destinationurl' />
				</label>
				
				<label>
					<span>Tracking Vars:</span>
					<input class="formcheck" type='checkbox' value='1' name='track-advar' /> Check this box to automatically add tracking variables to the url.
				</label>
				
				<label>
					<span>Add to All:</span>
					<input class="formcheck" type='checkbox' value='1' name='add-advar-to-all' /> Check here to add this ad variation to all of the adgroups in this campaign.
				</label>
				
				<label><span></span>
					<input class="formsubmit cpe_addadvariation" type='submit' onClick='javascript: createppc.addAdVar(); return false;' name='campaign-submit' value='Add AdGroup' />
				</label>
			</div><!--close formbox-->
			<div class="clear"></div>
		</form>
		
		<form name='create-advar-form-YAHOO' class="appform">
			<div class='floatleft' id='advar-preview-YAHOO'>
				<p class='advariation'>
					<a target="_blank" href="http://www.destination.url">Title</a><br/>
					<span>Ad Variation Description</span><br>
					<span class='display_url'>http://www.display.url</span><br/>
				</p>
			</div>
			<div class="formbox wide2x3">
				<label>
					<span>Headline:</span>
					<input class="formtxt" type='text' name='advar-title' autocomplete="off" />
				</label>
	
				<label>
					<span>Description:</span>
					<input class="formtxt" type='text' name='advar-description' maxlength="70" autocomplete="off" />
				</label>
				
				<label>
					<span>Display URL:</span>
					<input class="formtxt" type='text' value='' name='advar-displayurl' maxlength="35" />
				</label>
				
				<label>
					<span>Destination URL:</span>
					<input class="formtxt" type='text' value='http://' name='advar-destinationurl' />
				</label>
				
				
				<label style="display:none;">
					<span>Tracking Vars:</span>
					<input class="formcheck" type='checkbox' value='1' name='track-advar' /> Check this box to automatically add tracking variables to the url.
				</label>
				
				<label>
					<span>Add to All:</span>
					<input class="formcheck" type='checkbox' value='1' name='add-advar-to-all' /> Check here to add this ad variation to all of the adgroups in this campaign.
				</label>
				
				<label><span></span>
					<input class="formsubmit cpe_addadvariation" type='submit' onClick='javascript: createppc.addAdVar(); return false;' name='campaign-submit' value='Add AdGroup' />
				</label>
			</div><!--close formbox-->
		</form>
		
		<form name='create-advar-form-MSN' class="appform">
			<div class='floatRight' id='advar-preview-MSN'>
				<p class='advariation'>
					<a target="_blank" href="http://www.destination.url">Title</a><br/>
					<span>Ad Variation Description</span><br>
					<span class='display_url'>http://www.display.url</span><br/>
				</p>
			</div>
			<div class="formbox wide2x3">			
				<label>
					<span>Headline:</span>
					<input class="formtxt" type='text' name='advar-title' maxlength="35" autocomplete="off" />
				</label>
	
				<label>
					<span>Description:</span>
					<input class="formtxt" type='text' name='advar-description' maxlength="70" autocomplete="off" />
				</label>
				
				<label>
					<span>Display URL:</span>
					<input class="formtxt" type='text' value='' name='advar-displayurl' maxlength="35" />
				</label>
				
				<label>
					<span>Destination URL:</span>
					<input class="formtxt" type='text' value='http://' name='advar-destinationurl' />
				</label>
				
				<label>
					<span>Tracking Vars:</span>
					<input class="formcheck" type='checkbox' value='1' name='track-advar' /> Check this box to automatically add tracking variables to the url.
				</label>
				
				<label>
					<span>Add to All:</span>
					<input class="formcheck" type='checkbox' value='1' name='add-advar-to-all' /> Check here to add this ad variation to all of the adgroups in this campaign.
				</label>
				
				<label><span></span>
					<input class="formsubmit cpe_addadvariation" type='submit' onClick='javascript: createppc.addAdVar(); return false;' name='campaign-submit' value='Add AdGroup' />
				</label>
			</div><!--close formbox-->
		</form>	
	</div><!--close box-->
	
	<div class="box boxfull bordertop">
		<h3>Bulk Create Ad Variations</h3>
		
		<p>
			Enter a headline, a description, the display url and the destination url seperated by tabs.<br/>
			A new line for each ad variation. <br/>
		</p>
		<form name='create-bulk-advar-form' class="appform">
			<pre id='create-bulk-advar-form-descr-ADWORDS'>HEADLINE	LINE1	LINE2	DISPLAYURL	DESTINATIONURL</pre>
			<pre id='create-bulk-advar-form-descr-YAHOO'>HEADLINE	DESCRIPTION	DISPLAYURL	DESTINATIONURL</pre>
			<pre id='create-bulk-advar-form-descr-MSN'>HEADLINE	DESCRIPTION	DISPLAYURL	DESTINATIONURL</pre>
		
			<textarea class='bulkTextBox formtxtarea widefull' onkeydown='javascript:createppc.checkTab(event);' rows='4' name='bulk-advar'></textarea>
			<br/>
			<br/>
			
			<label>
				<span>Tracking Vars:</span>
				
				<input type='checkbox' name='create-bulk-advar-form-track-advar' />
				Check this box to add tracking variables to the url's for these ads.
			</label>
			<div class="clear"></div>
			<label>
				<span>Add to All:</span>
				<input type='checkbox' value='1' name='add-advar-to-all' /> Check here to add this ad variation to all of the adgroups in this campaign.
			</label>
			<div class="clear"></div>
			
			<input class="formsubmit cpe_bulkadd" type='submit' onClick='javascript: createppc.addAdVarBulk(); return false;' name='advar-bulk-button' value='Add AdVar Bulk' />
		</form>
	
	</div><!--close box-->
</div><!--close #createppc_content-advars-->

<div id='createppc_content-keywords'>	
	<div class="box boxfull borderbutt hasselect">
		<p class="floatleft">Ad Groups in this Campaign:</p> <select class="formselect" id='keywordsAdgroupSelect'></select>
	</div><!--close box-->
	
	<div class="box box2" id="existKeywordHolder">
		<h3>Existing Keywords</h3>
		
		<div id='existKeywordClone' style='display:none;'>
			<span class='newKeywordSpan'>
				{KEYWORD_NAME}
			</span>
			<span class='floatLeft'>
				{KEYWORD_BID}
			</span>
			<a class="tbtn" id='removeKeywordElement_{KEYWORD_API_ID}' href="javascript:createppc.removeKeywordAPI('{KEYWORD_API_ID}')">Delete</a>
			<div class="clear"></div>
		</div>
	
	</div><!--close box-->
	<div class="box box2 nomargin" id="newKeywordHolder">
		<h3>New Keywords</h3>
		
		<div id='newKeywordClone' style='display:none;'>
			<span class='newKeywordSpan'>
				{KEYWORD_NAME}
			</span>
			<span class='floatLeft'>
				{KEYWORD_BID}
			</span>
			<a class='tbtn' href='#' onClick="javascript:createppc.removeKeyword('{KEYWORD_ID}'); return false;">Remove</a>
			<div class="clear"></div>
		</div>
	
	</div><!--close box-->
	<div class="clear"></div>
	
	<div class="box box2 bordertop">
		<h3>Create Keyword</h3>
		
		<form name='create-keyword-form-ADWORDS' class="appform labelfloat">
			<label>
				<span>Keyword:</span>
				<input class="formtxt" type='text' name='keyword-name' autocomplete="off" />
			</label>

			<label>
				<span>Bid:</span>
				<input class="formtxt wide_number" type='text' name='keyword-bid' />
			</label>
			
			<label style="display: none">
				<span>Destination URL:</span>
				<input class="formtxt" type='text' value='' name='keyword-destinationurl' />
			</label>
			
			<input class="formsubmit cpe_addkeyword" type='submit' onClick='javascript: createppc.addKeyword(); return false;' name='campaign-submit' value='Add AdGroup' />
		</form>
		
		<form name='create-keyword-form-YAHOO' class="appform labelfloat">
			<label>
				<span>Keyword:</span>
				<input class="formtxt" type='text' name='keyword-name' autocomplete="off" />
			</label>

			<label>
				<span>Bid:</span>
				<input class="formtxt wide_number" type='text' name='keyword-bid' />
			</label>
			
			<label>
				<span>Destination URL:</span>
				<input class="formtxt" type='text' value='http://' name='keyword-destinationurl' />
			</label>
			
			<label>
				<span>Standard Match:</span>
				<input class="formcheck" type='checkbox' value='0' name='advanced-match' />
				Check this box to enable <a target="_blank" title="Learn more about Matching for keywords." href="http://help.yahoo.com/l/us/yahoo/ysm/sps/articles/manage_keywords4.html#standard">Standard Matching</a> for this keyword.
			</label>
			<br class='clearBoth'/>
			
			<input class="formsubmit cpe_addkeyword" type='submit' onClick='javascript: createppc.addKeyword(); return false;' name='campaign-submit' value='Add AdGroup' />
		</form>
		
		<form name='create-keyword-form-MSN' class="appform labelfloat">
			<label>
				<span>Keyword:</span>
				<input class="formtxt" type='text' name='keyword-name' autocomplete="off" />
			</label>

			<label>
				<span>Bid:</span>
				<input class="formtxt wide_number" type='text' name='keyword-bid' />
			</label>
			
			<label>
				<span>Destination URL:</span>
				<input class="formtxt" type='text' value='http://' name='keyword-destinationurl' />
			</label>
			
			<input class="formsubmit cpe_addkeyword" type='submit' onClick='javascript: createppc.addKeyword(); return false;' name='campaign-submit' value='Add AdGroup' />
		</form>
	
	</div><!--close box-->
	<div class="box box2 bordertop nomargin">
		<h3>Bulk Create Keywords</h3>
		<p>Enter a keyword, its Max CPC and Destination URL seperated by tabs. A new line for each keyword.</p>
		
		<pre>KEYWORD	MAXCPC	DESTINATIONURL</pre>
		<form name='create-bulk-keyword-form' class="appform">
			<textarea class='bulkTextBox formtxtarea' onkeydown='javascript:createppc.checkTab(event);' rows='4' name='bulk-keyword'></textarea>
			<br/>
			<!-- <p>
				MAXCPC and DESTINATIONURL are not required.
			</p>-->
			<br/>

			<label id="create-bulk-keyword-form-standard-match">
				<span>
					Standard Match:
				</span>
				<input class="formcheck" type='checkbox' name='keyword-bulk-standard-match'/>
				Check this box to enable <a target="_blank" href="http://help.yahoo.com/l/us/yahoo/ysm/sps/articles/manage_keywords4.html#standard" title="Learn more about Matching for keywords">Standard Matching</a> for these keywords.
			</label>
			<br/>

			<input class="formsubmit cpe_bulkadd" type='submit' onClick='javascript: createppc.addKeywordBulk(); return false;' name='keyword-bulk-button' value='Add Keyword Bulk' />
		</form>	
	</div><!--close box-->
</div><!--close #createppc_content-keywords-->

<div id='createppc_content-review'>
	<div id='reviewOutput'>
	</div>
</div>

<div id='createppc_content-output'>
	<form name='submitViaAPI' method='post' action='CreatePPCSubmit.html' class="appform">
		<input type='hidden' name='jsonObj' value='' />
		<input type='hidden' name='curAdwordsAPICredit' id='curAdwordsAPICredit_id' value='<?php print $this->User->GetAdwordsAPIBalance(); ?>' />
		<h3>Submit Via API:</h3>
		
		<div class='container-box textAlignLeft' style="padding:10px;">
			
			Submit your request to the appropriate publisher API's to create all of the Campaigns, Ad Groups, Keywords and Ad Variations in your respective accounts.
			<br/><br/>
			<label>
				<input type='checkbox' name='autosave' checked=CHECKED /> &nbsp; <u>Autosave</u>: Save your data before it is submitted to the API's for processing.<br/>This will store and let you redo the request in case Google, Yahoo or MSN's servers encounter an error.  This is <b>highly recommended</b> for large submissions.
			</label>
			
			<div id='OutputTab_AdwordsAPIEstimate' style='display:none;'></div>
	
			<br/>
			
			<?php 
				$signedUp = ( (($this->User->vaultID==0) && !$this->User->IsSubscribed(User::PRODUCT_FREE_PPC)) || 
							 (($this->User->vaultID!=0) && !$this->User->IsSubscribed(User::PRODUCT_PPC_YEARLY_CHARGE))
						   ) 
			?>
			
			<input class="formsubmit cpe_buildnow" type='submit' onClick='<?php if (!$signedUp) { ?>createppc.submitAPI(); return false;<?php } ?>' id='submit-api' name='submit-api' value='SUBMITAPI' />
		</div>
	</form> 
	<br/>
	
	<br/><br/>
	
	<h3>Session Management:</h3>
	<div class='container-box textAlignCenter'>
	
		<!-- BEGIN Session Management Main View -->
		<div id='session-management-main'>
			<h3>Save Session</h3>
			Saving your current session will store the current Campaigns, Ad Groups, Keywords and Ad Variations that you have created for processing.  After you save a session you can resume your work in a different browser, on a different computer or simply at a later date.<br/>
			<input class="formsubmit cpe_savesession" type='submit' onClick='createppc.showSaveSession(); return false;' name='save-session' value='SAVESESSION' /><br/>
			
			<br/><hr color='#DFDFDF'/><br/>
			
			<h3>Load Session</h3>
			When you load a session all of the Campaigns, Ad Groups, Keywords and Ad Variations from that session are restored to the exact state of when you had saved.<br/>
			<input class="formsubmit cpe_loadsession" type='submit' onClick='createppc.showLoadSession(); return false;' name='load-session' value='LOADSESSION' />
			<br/><br/>
		</div><!--close session-management-main-->
		<!-- ENDOF Session Management Main View -->
		
		<!-- BEGIN Session Management Save View -->
		<div id='session-management-save' style='display:none;'>
		
			<form class="appform" name='saveSession' method='post' action='CreatePPCSubmit.html?saveSession=TRUE'>
			<h3>Save Session</h3>
				Choose a label for this session when saving so that you can easily identify it later.
				<br/><br/>
				<input type='hidden' name='jsonObj' value='' />
				<b>Label:</b> <input class="formtxt" type='text' name='label'/>
				<br/><br/>
				Saving your current session will store the current Campaigns, Ad Groups, Keywords and Ad Variations that you have created for processing.  After you save a session you can resume your work in a different browser, on a different computer or simply at a later date.
				<br/>
				<input class="formsubmit cpe_savesession" type='submit' onClick='createppc.saveSession(); return false;' name='save-session' value='SAVESESSION' />
			</form>
			
			<br/><br/>
			
			<a class="tbtn" href='#' onClick='javascript: createppc.showSessionManagement(); return false;'>Back to Session Management</a>
		</div><!--close session-management-save-->
		<!-- ENDOF Session Management Save View -->
		
		<!-- BEGIN Session Management Load View -->
		<div id='session-management-load' style='display:none;'>
			<table style="width:100%; border-top: solid 1px #000000; border-bottom: solid 1px #000000;">
				<tr >
					<th style="width: 30%; border-bottom: solid 2px #000000" >
						Date
					</th>
					<th style="border-bottom: solid 2px #000000;">
						Label
					</th>
					<th style="border-bottom: solid 2px #000000;">
						Content
					</th>
					<th style="border-bottom: solid 2px #000000;">
						
					</th>
					<th style="border-bottom: solid 2px #000000;">
						
					</th>
				</tr>
			<?php if($this->savedSessions === false):?>
				You do not currently have a saved session.  You may save a session by using the button above.
			<?php else:?>
				<?php foreach($this->savedSessions as $key => $savedSession):?>
				<tr style='background-color: <?php echo ($key%2)?'#ddf0f9':'#ffffff'?>'>
					<td class='textAlignLeft'>
						<?php print date('F j, Y, g:i a', strtotime($savedSession->created)); ?>
					</td>
					<td style="white-space:nowrap">
						<?php print ($savedSession->label == '')?'<i>[No Label]</i>':$savedSession->label; ?>
					</td>
					<td>
						<abbr title="<?php print $savedSession->ContentExtended; ?>">
							<?php print $savedSession->Content; ?>
						</abbr>
					</td>
					<td class='textAlignCenter'>
						<a class="tbtn" href='#' onClick='createppc.loadSession(createppc_savedSession_<?php echo $key?>); '>Load</a>
					</td>
					<td class='textAlignCenter'>
						<a class="tbtn" href='CreatePPCSubmit.html?DELETE=<?php print $savedSession->id; ?>'>Delete</a>
					</td>
				</tr>
				<?php endforeach?>
			<?php endif?>
					
					
			</table>
			<br/><br/>
			
			<a class="tbtn" href='#' onClick='javascript: createppc.showSessionManagement(); return false;'>Back to Session Management</a>
		</div><!--close #session-management-load-->
		<!-- ENDOF Session Management Load View -->
	<?php if($this->savedSessions === false):?>
		<!-- You do not currently have a saved session.  You may save a session by using the button above. -->
	<?php else:?>
		<br />
		<?php foreach($this->savedSessions as $savedSession):?>
			<?php print $savedSession->label; ?> <?php print $savedSession->created; ?><br />
		<?php endforeach?>
	<?php endif?>
	</div><!--close container-box textAlignCenter-->
		
</div><!--close #createppc_content-output-->
</div><!--close #createppc-->

<iframe width='1' height='1' style='display:none;' src="/BevoMedia/User/EmptyPage.html"></iframe>


<script language='javascript'>
	<?php if($this->savedSessions !== false):?>
	<?php foreach($this->savedSessions as $key => $savedSession): ?>
		var createppc_savedSession_<?php print $key?> = <?php print str_replace("'", "\'", $savedSession->json); ?>;
	<?php endforeach?>
	<?php endif?>
	createppc.init();
	createppc.selectMenuTab('account');
	var loc = document.location.href;
	if(loc.indexOf('#') != -1)
	{
		var locArr = loc.split('#');
		if(locArr[1].length > 6)
		{
			var itm = locArr[1].split(':');
			var providerType = {'Adwords':'ADWORDS', 'MSN':'MSN', 'Yahoo':'YAHOO'};
			document.location.href = loc.replace(locArr[1], '');
			var acc = itm[1].split(',');
			if(itm[0] == 'EditAccount')
			{
				createppc.selectAccount(providerType[acc[0]], acc[1], acc[2]);
			}else if(itm[0] == 'EditCampaign')
			{
				createppc.selectAccount(providerType[acc[0]], acc[1], acc[2]);
				createppc.selectCampaign(acc[3], acc[4]);
			}else if(itm[0] == 'EditAdGroup')
			{
				createppc.selectAccount(providerType[acc[0]], acc[1], acc[2]);
				createppc.selectCampaign(acc[3], acc[4]);
				createppc.selectAdGroup(acc[5], acc[6]);
				if(acc[7] == 'Keywords')
					createppc.selectMenuTab('keywords');
			}
		}
	}
	
	function confirmLeavePage()
	{
		if(createppc.getCurrentTab() == "output")
		{
			return;
		}
		createppc.selectMenuTab('output');
		createppc.showSaveSession();
		return "Your campaign changes will be lost. \nAre you sure you want to leave the Bevo Editor? \n\nYou can save your changes by pushing 'cancel' and using the form below.";
	}
	window.onbeforeunload = confirmLeavePage;

</script>

<div id="KeepAlive" style="display: none;"></div>
<script type="text/javascript">
	function KeepAlive()
	{
		$('#KeepAlive').load('/BevoMedia/User/KeepAlive.html');
	}
	setInterval('KeepAlive()', 60000);
</script>


<script type="text/javascript">
$(document).ready(function () {	
<?php 
	if ( (($this->User->vaultID==0) && !$this->User->IsSubscribed(User::PRODUCT_FREE_PPC)) || 
		 (($this->User->vaultID!=0) && !$this->User->IsSubscribed(User::PRODUCT_PPC_YEARLY_CHARGE))
	   ) 
	{
?>
	$('#submit-api').click(function() {
		var a = document.createElement('a');
		a.href = '/BevoMedia/Publisher/VerifyPPC.html?ajax=true';
		a.rel = 'shadowbox;width=640;height=480;player=iframe';
		Shadowbox.open(a);

	    return false;
	});
	
<?php 
	}
?>
});
</script>
