var createppc = {
	_tabs: new Array('account', 'campaign', 'adgroup', 'advars', 'keywords', 'review', 'output'),
	_prefix: 'createppc_',

	curAccountID: 0,
	curAccountType: 0,
	curCampaignID: 0,
	curAdGroupID: 0,

	curEditCampaignID: false,
	curEditAdGroupID: false,
	
	newCampaigns: new Array(),
	newAdGroups: new Array(),
	newAdVariations: new Array(),
	newKeywords: new Array(),
	
	existCampaigns: new Array(),
	existAdGroups: new Array(),
	existAdVariations: new Array(),
	existKeywords: new Array(),
	
	deleteKeywords: new Array(),
	deleteAdVariations: new Array(),
	
	geoTargetCountries: new Array(),
	
	ppcImages:
	{
		'0':'',
		'ADWORDS':'/Themes/BevoMedia/img/googlefavicon.png',
		'YAHOO':'/Themes/BevoMedia/img/yahoofavicon.png',
		'MSN':'/Themes/BevoMedia/img/msnfavicon.png'
	},
	
	ppcProviders:
	{
		'0':'',
		'1':'ADWORDS',
		'2':'YAHOO',
		'3':'MSN'
	},
	
	_$: function(l)
	{
		return document.getElementById(l);
	},
	
	init: function()
	{
		var newExist = new Array('new', 'exist');
		var holderClone = new Array('Holder', 'Clone');
		var objTypes = new Array('Campaign', 'AdGroup', 'AdVariation', 'Keyword');
		for(var i=0; i<newExist.length; i++)
		{
			for(var j=0; j<holderClone.length; j++)
			{
				for(var k=0; k<objTypes.length; k++)
				{
					this[newExist[i]+objTypes[k]+holderClone[j]+"Base"] = this._$(newExist[i]+objTypes[k]+holderClone[j]).innerHTML;
				}
			}
		}
		
		for(var img in this.ppcImages)
		{
			var tmp = new Image();
			tmp.src = this.ppcImages[img];
		}
		for(var i in this._tabs)
		{
			var menuElement = this._$(this._prefix + 'menu-' + this._tabs[i]);
			menuElement.tab = this._tabs[i];
			menuElement.onclick = this.onClick_menuTab;
			if (menuElement.captureEvents) menuElement.captureEvents(Event.CLICK);
		}

		for(f in document.forms)
		{
			if(document.forms[f].name == undefined)
				continue;
			if(document.forms[f].name.indexOf('create-advar-form') == -1)
				continue;
			document.forms[f]['advar-title'].onkeyup = document.forms[f]['advar-title'].onkeydown = this.onKeyEvent_advarUpdate;
			
			if(document.forms[f]['advar-description'] != undefined)
			{
				document.forms[f]['advar-description'].onkeyup = document.forms[f]['advar-description'].onkeydown = this.onKeyEvent_advarUpdate;
			}else{
				document.forms[f]['advar-description-line1'].onkeyup = document.forms[f]['advar-description-line1'].onkeydown = this.onKeyEvent_advarUpdate;
				document.forms[f]['advar-description-line2'].onkeyup = document.forms[f]['advar-description-line2'].onkeydown = this.onKeyEvent_advarUpdate;
			}
			document.forms[f]['advar-displayurl'].onkeyup = document.forms[f]['advar-displayurl'].onkeydown = this.onKeyEvent_advarUpdate;
			document.forms[f]['advar-destinationurl'].onkeyup = document.forms[f]['advar-destinationurl'].onkeydown = this.onKeyEvent_advarUpdate;
		}
		
		this._$('advarsAdgroupSelect').onchange = this.onChangeEvent_selectAdGroup;
		
		this.hideAllContent();
	},
	
	generateAdVariationHTML: function(base, data)
	{
		base = base.replace(/\{ADVAR_ID\}/g, data.curAdVariationID);
		base = base.replace(/\{TITLE\}/g, data.title);
		base = base.replace(/\{DESCRIPTION\}/g, data.description);
		base = base.replace(/\{DISPLAY_URL\}/g, data.displayurl);
		base = base.replace(/\{ADVAR_API_ID\}/g, data.apiAdVariationID);
		base = base.replace(/%7BURL%7D/g, data.destinationurl);
		return base;
	},
	
	generateKeywordHTML: function(base, data)
	{
		base = base.replace(/\{KEYWORD_NAME\}/g, data.keyword);
		base = base.replace(/\{KEYWORD_BID\}/g, data.bid);
		base = base.replace(/\{KEYWORD_API_ID\}/g, data.apiKeywordID);
		return base;
	},
	
	updateExistAdVariations: function()
	{
		var output = this.existAdVariationHolderBase;
		var newItem;
		var temp;
		var count = 0;
		
		var displayAdVariations = new Array();
		
		for(var i=0; i<this.existAdVariations.length; i++)
		{
			temp = this.existAdVariations[i];
			if(temp.curAdGroupID != this.curAdGroupID)
				continue;

			displayAdVariations.push(temp);
			
			count++;
			newItem = this.generateAdVariationHTML(this.existAdVariationCloneBase, temp);
			newItem = '<div >' + newItem + '</div>';
			output += newItem;
		}
		
		if(count == 0)
		{
			output += "<center><i>No existing ad variations...</i></center>";
		}
		
		this._$('existAdVariationHolder').innerHTML = output;
		
		for(var j=0; j<displayAdVariations.length; j++)
		{
			for(var i=0; i<this.deleteAdVariations.length; i++)
			{
				if(JSON.stringify(this.deleteAdVariations[i]) == JSON.stringify(displayAdVariations[j]))
				{
					this._$('removeAdVarsElement_'+displayAdVariations[j].apiAdVariationID).innerHTML = 'Undelete';
					this._$('removeAdVarsElement_'+displayAdVariations[j].apiAdVariationID).href = 'javascript:createppc.unRemoveAdVarsAPI('+displayAdVariations[j].apiAdVariationID+')';
				}
			}
		}
	},
	
	updateNewAdVariations: function()
	{
		var output = this.newAdVariationHolderBase;
		var newItem;
		var temp;
		var count = 0;
		
		for(var i=0; i<this.newAdVariations.length; i++)
		{
			temp = this.newAdVariations[i];
			if(temp.curAdGroupID != this.curAdGroupID)
				continue;

			count++;
			newItem = this.newAdVariationCloneBase;
			newItem = newItem.replace(/\{ADVAR_ID\}/g, temp.curAdVariationID);
			newItem = newItem.replace(/\{TITLE\}/g, temp.title);
	
			newItem = newItem.replace(/%7BURL%7D/g, temp.destinationurl);
			newItem = newItem.replace(/\{DISPLAY_URL\}/g, temp.displayurl);
			if(temp.curAccountType == 'ADWORDS')
			{
				var tempDescription = temp.description.substring(0,35) + "<br/>" + temp.description.substring(35);
				newItem = newItem.replace(/\{DESCRIPTION\}/g, tempDescription);
			}else{
				newItem = newItem.replace(/\{DESCRIPTION\}/g, temp.description);
			}
			newItem = '<div >' + newItem + '</div>';
			output += newItem;
		}
		if(count == 0)
		{
			output += "<center><i>No existing ad variations...</i></center>";
		}
		
		this._$('newAdVariationHolder').innerHTML = output;
	},
	
	addGeoTargetCountry: function(code, country)
	{
		this.geoTargetCountries[code] = country;
	},

	newAdVariationObject: function()
	{
		return {'checked':true,
				'curAccountType':false,
				'curAccountID':false,
				'curCampaignID':false,
				'curAdGroupID':false,
				'curAdVariationID':false,
				'apiAdVariationID':false,
				'title':false,
				'description':false,
				'displayurl':false,
				'destinationurl':false
		};
	},
	
	addExistAdVariation: function(adgroupid, id, title, descr, displ, desti, idapi)
	{
		var temp = this.newAdVariationObject();
		temp.curAdGroupID = adgroupid;
		temp.curAdVariationID = id;
		temp.apiAdVariationID = idapi;
		temp.title = title;
		temp.description = descr;
		temp.displayurl = displ;
		temp.destinationurl = desti;
		this.existAdVariations.push(temp);
	},
	
	newKeywordObject: function()
	{
		return {'checked':true,
				'curAccountType':false,
				'curAccountID':false,
				'curCampaignID':false,
				'curAdGroupID':false,
				'curKeywordID':false,
				'apiKeywordID':false,
				'name':false,
				'bid':false
			};
	},
	
	updateExistKeywords: function()
	{
		var output = this.existKeywordHolderBase;
		var newItem;
		var temp;
		var count = 0;
		
		var displayKeywords = new Array();
		
		for(var i=0; i<this.existKeywords.length; i++)
		{
			temp = this.existKeywords[i];
			if(temp.curAdGroupID != this.curAdGroupID)
				continue;

			displayKeywords.push(temp);
			
			count++;
			newItem = this.existKeywordCloneBase;
			newItem = newItem.replace(/\{KEYWORD_ID\}/g, temp.id);
			newItem = newItem.replace(/\{KEYWORD_NAME\}/g, temp.keyword + "&nbsp;");
			newItem = newItem.replace(/\{KEYWORD_BID\}/g, temp.bid);
			newItem = newItem.replace(/\{KEYWORD_API_ID\}/g, temp.apiKeywordID);
			newItem = '<div>' + newItem + '</div>';
			output += newItem;
		}
		if(count == 0)
		{
			output += "<center><i>No existing keywords...</i></center>"
		}
		
		this._$('existKeywordHolder').innerHTML = output;
		
		for(var j=0; j<displayKeywords.length; j++)
		{
			for(var i=0; i<this.deleteKeywords.length; i++)
			{
				if(JSON.stringify(this.deleteKeywords[i]) == JSON.stringify(displayKeywords[j]))
				{
					this._$('removeKeywordElement_'+displayKeywords[j].apiKeywordID).innerHTML = 'Undelete';
					this._$('removeKeywordElement_'+displayKeywords[j].apiKeywordID).href = 'javascript:createppc.unRemoveKeywordAPI('+displayKeywords[j].apiKeywordID+')';
				}
			}
		}
	},
	
	updateNewKeywords: function()
	{
		var output = this.newKeywordHolderBase;
		var newItem;
		var temp;
		var count = 0;
		
		for(var i=0; i<this.newKeywords.length; i++)
		{
			temp = this.newKeywords[i];
			if(temp.curAdGroupID != this.curAdGroupID)
				continue;
			
			count++;
			newItem = this.newKeywordCloneBase;
			newItem = newItem.replace(/\{KEYWORD_ID\}/g, temp.curKeywordID);
			newItem = newItem.replace(/\{KEYWORD_NAME\}/g, temp.keyword);
			var bidVal = temp.bid;
			if(temp.curAccountType == 'YAHOO')
				bidVal += ' [' + ((temp.advMatch=='1')?'Advanced Match':'Standard Match') + ']';
			newItem = newItem.replace(/\{KEYWORD_BID\}/g, bidVal);
			var t = JSON.stringify(temp);
			t = encodeURI(t);
			t = t.replace(/\%22/g, '\'');
			t = t.replace(/\%7D|\%7B|\%5B|\%5D/g, '');
			newItem = '<div title="'+ t +'">' + newItem + '</div>';
			//newItem = '<div>' + newItem + '</div>';
			output += newItem;
		}
		if(count == 0)
		{
			output += "<center><i>No new keywords...</i></center>"
		}
		
		this._$('newKeywordHolder').innerHTML = output;
	},
	
	addExistKeyword: function(adgroupid, id, name, cpc, idapi)
	{
		var temp = this.newKeywordObject();
		temp.curAdGroupID = adgroupid;
		temp.curKeywordID = id;
		temp.keyword = name;
		temp.bid = cpc;
		temp.apiKeywordID = idapi;
		this.existKeywords.push(temp);
	},
	
	newAdGroupObject: function()
	{
		return {'checked':true,
				'curAccountType':false,
				'curAccountID':false,
				'curCampaignID':false,
				'curAdGroupID':false,
				'name':false
			};
	},
	
	addExistAdGroup: function(campaignid, id, name)
	{
		var temp = this.newAdGroupObject();
		temp.curCampaignID = campaignid;
		temp.curAdGroupID = id;
		temp.name = name;
		this.existAdGroups.push(temp);
	},
	
	updateExistAdGroups: function()
	{
		var output = this.existAdGroupHolderBase;
		var newItem;
		var temp;
		var count = 0;
		
		for(var i=0; i<this.existAdGroups.length; i++)
		{
			temp = this.existAdGroups[i];
			if(temp.curCampaignID != this.curCampaignID)
				continue;
			
			count++;
			newItem = this.existAdGroupCloneBase;
				newItem = newItem.replace(/\{ADGROUP_ID\}/g, temp.curAdGroupID);
				newItem = newItem.replace(/\{ADGROUP_NAME\}/g, temp.name);
				newItem = '<div>' + newItem + '</div>';
			output += newItem;
		}
		if(count == 0)
		{
			output += "<center><i>No existing ad groups...</i></center>"
		}
		
		this._$('existAdGroupHolder').innerHTML = output;
	},
	
	updateNewAdGroups: function()
	{
		this._$(this.curAccountType + '-adgroup-submit').src = '/Themes/BevoMedia/img/createppc-addadgroup.png';
		
		var output = this.newAdGroupHolderBase;
		var newItem;
		var temp;
		var count = 0;
		
		for(var i=0; i<this.newAdGroups.length; i++)
		{
			temp = this.newAdGroups[i];
			if(temp.curCampaignID != this.curCampaignID)
				continue;
			
			count++;
			newItem = this.newAdGroupCloneBase;
				newItem = newItem.replace(/\{ADGROUP_ID\}/g, temp.curAdGroupID);
				newItem = newItem.replace(/\{ADGROUP_NAME\}/g, temp.name);
				//newItem = '<div>' + newItem + '</div>';
				var t = JSON.stringify(temp);
				t = encodeURI(t);
				t = t.replace(/\%22/g, '\'');
				t = t.replace(/\%7D|\%7B|\%5B|\%5D/g, '');
				newItem = '<div title="'+ t +'">' + newItem + '</div>';
			output += newItem;
		}
		if(count == 0)
		{
			output += "<center><i>No new ad groups...</i></center>"
		}
		
		this._$('newAdGroupHolder').innerHTML = output;
	},
	
	newCampaignObject: function()
	{
		return {'checked':true,
				'curAccountType':false,
				'curAccountID':false,
				'curCampaignID':false,
				'name':false, 
				'budget':false, 
				'geotargets':false,
				'searchtarget':false,
				'negativekeywords':false
			};
	},
	
	addExistCampaign: function(accountid, type, id, name)
	{
		var temp = this.newCampaignObject();
		temp.curAccountType = this.ppcProviders[type];
		temp.curAccountID = accountid;
		temp.curCampaignID = id;
		temp.name = name;
		this.existCampaigns.push(temp);
	},
	
	updateExistCampaigns: function()
	{
		var output = this.existCampaignHolderBase;
		var newItem;
		var temp;
		var count = 0;
		
		for(var i=0; i<this.existCampaigns.length; i++)
		{
			temp = this.existCampaigns[i];
			if(temp.curAccountType != this.curAccountType)
				continue;
			if(temp.curAccountID != this.curAccountID)
				continue;
			
			count++;
			newItem = this.existCampaignCloneBase;
				newItem = newItem.replace(/\{CAMPAIGN_ID\}/g, temp.curCampaignID);
				newItem = newItem.replace(/\{CAMPAIGN_NAME\}/g, temp.name);
				newItem = '<div id="campaignClone_'+temp.curCampaignID+'" >' + newItem + '</div>';
			output += newItem;
		}
		if(count == 0)
		{
			output += "<center><i>No existing campaigns...</i></center>"
		}
		
		this._$('existCampaignHolder').innerHTML = output;
	},
	
	updateNewCampaigns: function()
	{
		if(this._$(this.curAccountType + '-campaign-submit'))
		{
			this._$(this.curAccountType + '-campaign-submit').src = '/Themes/BevoMedia/img/createppc-addcampaign.png';
		}

		this.setCampaignFormLabelH2();
		var formSrc = document.forms['create-campaign-form-'+this.curAccountType];
		formSrc.reset();
		this.resetAdwordsGeoTargetCountry();
		this.setAllAdwordsGeoTargetCountry();
		
		var output = this.newCampaignHolderBase;
		var newItem;
		var temp;
		var count = 0;
		for(var i=0; i<this.newCampaigns.length; i++)
		{
			temp = this.newCampaigns[i];
			if(temp.curAccountType != this.curAccountType)
				continue;
			if(temp.curAccountID != this.curAccountID)
				continue;
			
			count++;
			newItem = this.newCampaignCloneBase;
				newItem = newItem.replace(/\{CAMPAIGN_ID\}/g, temp.curCampaignID);
				newItem = newItem.replace(/\{CAMPAIGN_NAME\}/g, temp.name);
				newItem = '<div id="campaignClone_'+temp.curCampaignID+'" >' + newItem + '</div>';
			output += newItem;
		}
		if(count == 0)
		{
			output += "<center><i>No new campaigns...</i></center>"
		}
		
		this._$('newCampaignHolder').innerHTML = output;
	},
	
	onKeyEvent_advarUpdate: function(e)
	{
		if(!e)
		{
			var e = window.event;
		}
		
		if(!e.target)
			e.target = e.srcElement;

		createppc.updateAdVarPreview(createppc.curAdGroupID);
	},
	
	onChangeEvent_selectAdGroup: function(e)
	{
		if(!e)
		{
			var e = window.event;
		}
		
		if(!e.target)
			e.target = e.srcElement;
		
		if(e.target.value != '')
			createppc.selectAdGroup(e.target.value, createppc.getAdGroupName(e.target.value));
	},
	
	onChangeEvent_selectAdGroupGotoKeywords: function(e)
	{
		if(!e)
		{
			var e = window.event;
		}
		
		if(e.target.value != '')
			createppc.selectAdGroup(e.target.value, createppc.getAdGroupName(e.target.value));
		createppc.selectMenuTab('keywords');
	},
	
	dropDownAccounts: function()
	{
		var grp = this._$('createppc_content-account');
		var itms = grp.getElementsByTagName('a');
		
		var accounts = {'ADWORDS':new Array(), 'YAHOO':new Array(), 'MSN':new Array()};
		
		for(var i in itms)
		{
			var curEle = itms[i];
			if(curEle.id != undefined)
			{
				for(var a in accounts)
				{
					if(curEle.id.indexOf('account_'+a+'-') == 0)
					{
						accounts[a].push(new Array(curEle.id.replace('account_'+a+'-', ''), curEle.innerHTML));
					}
				}
			}
		}

		var output = "<select class='formselect' name='Account'>";
		for(var a in accounts)
		{
			output += "<optgroup label='" + a + "'>";
			for(b in accounts[a])
			{
				output += "<option value='"+ a +"-" + accounts[a][b][0] + "'>&nbsp; " + accounts[a][b][1] + "</option>";
			}
			output += "</optgroup>";
		}
		output += "</select>";
		return output;
	},
	
	dropDownAdGroups: function()
	{
		var output = "<optgroup label='Existing'>";
		var selected;
		for(var n in this.existAdGroups)
		{
			if(this.existAdGroups[n].curCampaignID != this.curCampaignID)
				continue;
			
			selected = false;
			if(this.curAdGroupID == this.existAdGroups[n].curAdGroupID)
				selected = true;
			output += "<option "+((selected==true)?'selected="SELECTED"':'')+" value='"+this.existAdGroups[n].curAdGroupID+"'>&nbsp; "+this.existAdGroups[n].name+"</option>";
		}
		output += "</optgroup><option></option><optgroup label='New'>";
		for(var n in this.newAdGroups)
		{
			if(this.newAdGroups[n].curCampaignID != this.curCampaignID)
				continue;
			
			selected = false;
			if(this.curAdGroupID == this.newAdGroups[n].curAdGroupID)
				selected = true;
			output += "<option "+((selected==true)?'selected="SELECTED"':'')+" value='"+this.newAdGroups[n].curAdGroupID+"'>&nbsp; "+this.newAdGroups[n].name+"</option>";
		}
		output += "</optgroup>";

		var outputAV = 'Ad Groups in this Campaign:';
		var outputKW = outputAV;
		outputAV += "<select class='formselect' id='advarsAdgroupSelect'>";
		outputAV += output;
		outputAV += '</select>';
		outputKW += "<select class='formselect' id='keywordsAdgroupSelect'>";
		outputKW += output;
		outputKW += '</select>';
			
		this._$('advarsAdgroupSelect').parentNode.innerHTML = outputAV;
		this._$('advarsAdgroupSelect').onchange = this.onChangeEvent_selectAdGroup;
		this._$('keywordsAdgroupSelect').parentNode.innerHTML = outputKW;
		this._$('keywordsAdgroupSelect').onchange = this.onChangeEvent_selectAdGroupGotoKeywords;
	},
	
	updateAdVarPreview: function(id)
	{
		var formSrc = document.forms['create-advar-form-'+this.curAccountType];
		var base = "<p class='advariation'><a target='_blank' href='{URL}'>{TITLE}</a><br/><span>{DESCRIPTION}</span><br><span class='display_url'>{DISPLAY_URL}</span><br/></p>";
		var title = formSrc['advar-title'].value;
		if(formSrc['advar-description'])
		{
			var descr = formSrc['advar-description'].value;
		}
		else
		{
			var descr = formSrc['advar-description-line1'].value + "<br/>" + formSrc['advar-description-line2'].value;
		}
		var displ = formSrc['advar-displayurl'].value;
		var desti = formSrc['advar-destinationurl'].value;
		
		base = base.replace(/\{TITLE\}/g, title);
		base = base.replace(/\{DESCRIPTION\}/g, descr);
		base = base.replace(/\{DISPLAY_URL\}/g, displ);
		base = base.replace(/\{URL\}/g, desti);
		
		this._$('advar-preview-'+this.curAccountType).innerHTML = base;
	},
	
	showAdGroup: function(id)
	{
		var lc = this._$('createppc_content-adgroup');
		var c = this._showAdGroupById(lc, id);
	},
	
	_showAdGroupById: function(set, id)
	{
		var fc = set.getElementsByTagName("div");
		for(a in fc)
		{
			if(fc[a] == undefined)
				continue;
			
			if(fc[a].id != undefined)
			{
				if(fc[a].id.indexOf('adgroup-holder_') !== -1)
				{
					fc[a].style.display = 'none';
				}
				if(fc[a].id == 'adgroup-holder_'+id)
				{
					fc[a].style.display = 'block';
				}
			}
		}
	},
	
	showAdVars: function(id)
	{
		var lc = this._$('createppc_content-advars');
		var c = this._showAdVarsById(lc, id);
	},
	
	_showAdVarsById: function(set, id)
	{
		var fc = set.getElementsByTagName("div");
		for(a in fc)
		{
			if(fc[a] == undefined)
				continue;
			
			if(fc[a].id != undefined)
			{
				if(fc[a].id.indexOf('advars-holder_') !== -1)
				{
					fc[a].style.display = 'none';
				}
				if(fc[a].id == 'advars-holder_'+id)
				{
					fc[a].style.display = 'block';
				}
			}
		}
	},
	
	showKeywords: function(id)
	{
		var lc = this._$('createppc_content-keywords');
		var c = this._showKeywordsById(lc, id);
	},
	
	_showKeywordsById: function(set, id)
	{
		var fc = set.getElementsByTagName("div");
		for(a in fc)
		{
			if(fc[a] == undefined)
				continue;
			
			if(fc[a].id != undefined)
			{
				if(fc[a].id.indexOf('keywords-holder_') !== -1)
				{
					fc[a].style.display = 'none';
				}
				if(fc[a].id == 'keywords-holder_'+id)
				{
					fc[a].style.display = 'block';
				}
			}
		}
	},
	
	onClick_menuTab: function(e)
	{
		if(!e)
		{
			var e = window.event;
		}
		
		if(!e.target)
			e.target = e.srcElement;
		
		createppc.selectMenuTab(e.target.tab);
	},
	
	showCampaignForms: function()
	{
		for(var i=1; i<4; i++)
		{
			var temp = document.forms['create-campaign-form-'+this.ppcProviders[i]];
			if(temp == undefined)
				continue;
			
			if(this.curAccountType == this.ppcProviders[i])
				temp.style.display = 'block';
			else
				temp.style.display = 'none';
		}
	},
	
	showBulkAdGroupForms: function()
	{
		for(var i=1; i<4; i++)
		{
			var temp = document.forms['create-bulk-adgroup-form-'+this.ppcProviders[i]];
			if(temp == undefined)
				continue;
			
			if(this.curAccountType == this.ppcProviders[i])
				temp.style.display = 'block';
			else
				temp.style.display = 'none';
		}
	},

	showAdGroupForms: function()
	{
		for(var i=1; i<4; i++)
		{
			var temp = document.forms['create-adgroup-form-'+this.ppcProviders[i]];
			if(temp == undefined)
				continue;
			
			if(this.curAccountType == this.ppcProviders[i])
				temp.style.display = 'block';
			else
				temp.style.display = 'none';
		}
		
		if(this.curAccountType == 'ADWORDS')
		{
			var temp = document.forms['create-adgroup-form-'+this.curAccountType];
			temp['adgroup-content-bid'].disabled = false;
			if(this.getCampaign(this.curCampaignID).searchtarget == 'Search')
			{
				temp['adgroup-content-bid'].disabled = true;
				temp['adgroup-content-bid'].value = '0.00';
			}
		}
		
		this.showBulkAdGroupForms();
	},

	showAdVariationForms: function()
	{
		for(var i=1; i<4; i++)
		{
			var temp = document.forms['create-advar-form-'+this.ppcProviders[i]];
			if(temp == undefined)
				continue;
			
			if(this.curAccountType == this.ppcProviders[i])
				temp.style.display = 'block';
			else
				temp.style.display = 'none';
			
			var temp = this._$('create-bulk-advar-form-descr-'+this.ppcProviders[i]);
			
			if(temp == undefined)
				continue;
			
			if(this.curAccountType == this.ppcProviders[i])
				temp.style.display = 'block';
			else
				temp.style.display = 'none';

		}
	},

	showKeywordForms: function()
	{
		for(var i=1; i<4; i++)
		{
			var temp = document.forms['create-keyword-form-'+this.ppcProviders[i]];
			if(temp == undefined)
				continue;
			
			if(this.curAccountType == this.ppcProviders[i])
				temp.style.display = 'block';
			else
				temp.style.display = 'none';
			
			temp = document.getElementById('create-bulk-keyword-form-standard-match');
			if(temp == undefined)
				continue;			
			temp.style.display = 'none';
		}

		if(this.curAccountType == 'YAHOO')
		{
			document.getElementById('create-bulk-keyword-form-standard-match').style.display = 'block';
		}
		
	},
	
	selectMenuTab: function(tab)
	{
		if(tab == 'campaign')
		{
			if(this.curAccountID == false)
				return;
			this.showCampaignForms();
			this.updateNewCampaigns();
			this.updateExistCampaigns();
		}
		if(tab == 'adgroup')
		{
			if(this.curCampaignID == false)
				return;
			this.showAdGroupForms();
			this.updateExistAdGroups();
			this.updateNewAdGroups();
		}
		if(tab == 'keywords')
		{
			if(this.curAdGroupID == false)
				return;
			this.dropDownAdGroups();
			this.showKeywordForms();
			this.updateNewKeywords();
			this.updateExistKeywords();
		}
		if(tab == 'advars')
		{
			if(this.curAdGroupID == false)
				return;
			this.dropDownAdGroups();
			this.showAdVariationForms();
			this.updateExistAdVariations();
			this.updateNewAdVariations();
		}
		if(tab == 'review')
		{
			this.generateReview();
			this.dropDownAccounts();
		}
		if(tab == 'output')
		{
			this.generateReview();
		}
		
		this.hideAllContent();
		this.clearAllTabs();
		var menuElement = this._$(this._prefix + 'menu-' + tab);
		menuElement.className = 'active';
		var contentElement = this._$(this._prefix + 'content-' + tab);
		contentElement.style.display = 'block';
	},
	
	getCurrentTab: function()
	{
		for(var i in this._tabs)
		{
			var menuElement = this._$(this._prefix + 'menu-' + this._tabs[i]);
			if(menuElement.className == 'active')
			{
				return this._tabs[i];
			}
		}
		return 'output';
	},
	
	clearAllTabs: function()
	{
		for(var i in this._tabs)
		{
			var menuElement = this._$(this._prefix + 'menu-' + this._tabs[i]);
			menuElement.className = '';
		}
	},
	
	hideAllContent: function()
	{
		for(var i in this._tabs)
		{
			var contentElement = this._$(this._prefix + 'content-' + this._tabs[i]);
			contentElement.style.display = 'none';
		}
	},
	
	setLabel: function(labelId, value)
	{
		var label = this._$(labelId);
		label.innerHTML = value;
	},
	
	selectAccount: function(type, id, name)
	{
		var label;
		if(id !== 0)
		{
			label = '<b>Current Account:</b> ' + '<img src="' + this.ppcImages[type] + '" align="top"/>' + name ;
		}
		this.setLabel('createppc_currentLabel-account', label);
		this.selectCampaign(0, false);
		this.selectAdGroup(0, false);
		this.curAccountID = id;
		this.curAccountType = type;
		if(id !== 0)
		{
			this.selectMenuTab('campaign');
		}
	},
	
	selectCampaign: function(id, name)
	{
		var label = '<b>Current Campaign:</b> ';
		//label += '(' + id + ') ';
		if(id == 0)
		{
			label += 'Select...';
		}
		if(id !== 0)
		{
			label += name;
		}
		if(id <= -1)
		{
			label += ' [NEW]';
		}
		
		this.setLabel('createppc_currentLabel-campaign', label);
		this.selectAdGroup(0, false);
		this.curCampaignID = id;
		this.showAdGroup(id);
		if(id != 0)
		{
			this.updateNewCampaigns();
			this.selectMenuTab('adgroup');
		}else{
			this.showAdVars(0);
			this.showKeywords(0);
		}
		
	},
	
	selectAdGroup: function(id, name)
	{
		var label = '<b>Current Ad Group:</b> ';
		//label += '(' + id + ') ';
		if(id == 0)
		{
			label += 'Select...';
		}
		if(id !== 0)
		{
			label += name;
		}
		if(id <= -1)
		{
			label += ' [NEW]';
		}
		this.setLabel('createppc_currentLabel-adgroup', label);
		this.curAdGroupID = id;
		if(id !== 0)
		{
			this.selectMenuTab('advars');
			this.showAdVars(id);
			this.showKeywords(id);
		}
	},
	
	resetAdwordsGeoTargetCountry: function()
	{
		var ele = this._$('campaign-geotargets-'+this.curAccountType);
		ele.innerHTML = "";
	},
	
	setAllAdwordsGeoTargetCountry: function()
	{
		var ele = this._$('campaign-geotargets-'+this.curAccountType);
		var base = "<input type='hidden' name='country-geotargets[]' value='US'><i>United States</i><br/>";
		ele.innerHTML = base;
	},
	
	addAdwordsGeoTargetCountry: function(country, countryLabel)
	{
		var base = "<input type='hidden' name='country-geotargets[]' value='"+ country +"'><i>" + countryLabel + "</i><br/>";
		var ele = this._$('campaign-geotargets-'+this.curAccountType);
		ele.innerHTML += base + "\n";
	},
	
	addCampaignCountryGeotargets: function()
	{
		var output = new Array();
		var geotargets = document.forms['create-campaign-form-'+this.curAccountType]['country-geotargets[]'];
		if(geotargets.name != undefined && geotargets.name.indexOf('country-geotargets[]') == 0)
			geotargets = new Array(geotargets);
		
		for(var a in geotargets)
		{
			if(geotargets[a] == undefined)
				continue;
			if(geotargets[a].name == undefined)
				continue;
			if(geotargets[a].name.indexOf('country-geotargets[]') == -1)
				continue;
				
			output.push(geotargets[a].value);
		}
		if(output.length < 1)
			return 'targetAll';
		
		return output;
	},
	
	addCampaign: function()
	{
		var formSrc = document.forms['create-campaign-form-'+this.curAccountType];
		var id = (this.newCampaigns.length * -1) - 1;
		var name = formSrc['campaign-name'].value;
		if(this.trim(name) == '')
		{
			alert('Please provide a valid campaign name.');
			return;
		}
		var budget = formSrc['campaign-budget'].value;
		if(this.trim(budget) == '' || budget == '0')
		{
			alert('Please provide a valid budget.');
			return;
		}
		var geotargets = {'countries': this.addCampaignCountryGeotargets()};
		var neg = new Array();
		if(formSrc['campaign-neg'] != undefined)
			neg = formSrc['campaign-neg'].value.split("\n");
		
		var targ = 'SearchContent';
		if(this.curAccountType == 'ADWORDS')
		{
			targ = ((formSrc['adgroup-addistribution'][0].checked)?'Search':'') + ((formSrc['adgroup-addistribution'][1].checked)?'Content':'');
		}
		if(targ == '')
		{
			alert('Please select a Content Network target...');
			return;
		}
		errors = this.validateCampaign(this.curAccountID, this.curAccountType, name, budget, geotargets['countries'], neg);
		if(errors.length > 0)
		{
			alert(errors[0]);
			return;
		}
		var temp = {'checked':true, 'curAccountType':this.curAccountType, 'curAccountID':this.curAccountID, 'curCampaignID':id, 'name':name, 'budget':budget, 'geotargets':geotargets, 'negativekeywords':neg, 'searchtarget':targ};
		if(this.curEditCampaignID === false)
		{
			this.newCampaigns.push(temp);
			this.updateNewCampaigns();
			this.selectCampaign(id, name);
			this.setLabel('createppc_currentLabel-campaign', '<b>Current Campaign:</b> ' + name + '[NEW]');
			this.resetAdwordsGeoTargetCountry();
			this.setAllAdwordsGeoTargetCountry();
		}else{
			id = (this.curEditCampaignID * -1) - 1;
			temp.curCampaignID = this.curEditCampaignID;
			this.newCampaigns[id] = temp;
			id = this.curEditCampaignID;
			this.curEditCampaignID = false;
			this.updateNewCampaigns();
		}
		formSrc.reset();
	},
	
	editCampaign: function(id)
	{
		this.curEditCampaignID = id;
		
		var temp = this.getCampaign(id);
		this.setCampaignFormLabelH2(temp.name);
		
		var formSrc = document.forms['create-campaign-form-'+temp.curAccountType];
		
		this._$(temp.curAccountType + '-campaign-submit').src = '/Themes/BevoMedia/img/createppc-update.png';
		formSrc['campaign-name'].value = temp.name;
		formSrc['campaign-budget'].value = temp.budget;
		
		if(temp.negativekeywords.length > 0)
		{
			for(var itm in temp.negativekeywords)
			{
				formSrc['campaign-neg'].value += temp.negativekeywords[itm] + "\n";
			}
		}
		
		var geoTargContainer = this._$(['campaign-geotargets-'+temp.curAccountType]);
		if(temp.geotargets.countries.length > 0)
		{
			geoTargContainer.innerHTML = "";
			for(var itm in temp.geotargets.countries)
			{
				geoTargContainer.innerHTML += '<input type="hidden" value="'+temp.geotargets.countries[itm]+'" name="country-geotargets[]">';
				geoTargContainer.innerHTML += "\n";
				geoTargContainer.innerHTML += '<br/><i>'+this.geoTargetCountries[temp.geotargets.countries[itm]]+'</i>';
				geoTargContainer.innerHTML += "\n";				
			}
		}
		
		if(temp.curAccountType == 'ADWORDS')
		{
			formSrc['adgroup-addistribution'][0].checked = false;
			formSrc['adgroup-addistribution'][1].checked = false;
			if(temp.searchtarget == 'SearchContent')
			{
				formSrc['adgroup-addistribution'][0].checked = true;
				formSrc['adgroup-addistribution'][1].checked = true;
			}else if(temp.searchtarget == 'Search'){
				formSrc['adgroup-addistribution'][0].checked = true;
			}else if(temp.searchtarget == 'Content'){
				formSrc['adgroup-addistribution'][1].checked = true;
			}
		}
	},
	
	setAdGroupFormLabelH2: function(val)
	{
		if(val === undefined)
		{
			val = 'Create Campaign';
		}else{
			val = 'Edit Campaign: ' + val;
		}
		this.setLabel('create-adgroup-form-h2-label', val);
	},
	
	setCampaignFormLabelH2: function(val)
	{
		if(val === undefined)
		{
			val = 'Create Campaign';
		}else{
			val = 'Edit Campaign: ' + val;
		}
		this.setLabel('create-campaign-form-h2-label', val);
	},
	
	removeCampaign: function(id)
	{
		var _id = (id * -1) - 1;
		this.newCampaigns[_id] = false;
		for(var rC in this.newAdGroups)
		{
			if(this.newAdGroups[rC] == false)
				continue;

			if(this.newAdGroups[rC].curCampaignID == id)
			{
				this.removeAdGroup(this.newAdGroups[rC].curAdGroupID);
			}
		}
		this.updateNewCampaigns();
		//this.selectCampaign(0, false);
		this.selectAdGroup(0, false);
		this.selectAccount(this.curAccountType, this.curAccountID, this.getAccountName(this.curAccountType, this.curAccountID));
	},
	
	addAdGroup: function()
	{
		var formSrc = document.forms['create-adgroup-form-'+this.curAccountType];
		var id = (this.newAdGroups.length * -1)-1;
		var name = formSrc['adgroup-name'].value;
		var bid = formSrc['adgroup-bid'].value;
		var contentBid = 0;
		if(formSrc['adgroup-content-bid'] != undefined)
			contentBid = formSrc['adgroup-content-bid'].value;

		if(formSrc['adgroup-neg'] != undefined)
		{
			var neg = formSrc['adgroup-neg'].value;
			neg = neg.split("\n");
		}else{
			neg = new Array();
		}
		var addistribution = 'Search';
		if(this.curAccountType == 'YAHOO' || this.curAccountType == 'MSN')
			addistribution = ((formSrc['adgroup-addistribution'][0].checked)?'Search':'') + ((formSrc['adgroup-addistribution'][1].checked)?'Content':'');

		var temp = {'checked':true, 'curAccountType':this.curAccountType, 'curAccountID':this.curAccountID, 'curCampaignID':this.curCampaignID, 'curAdGroupID':id, 'name':name, 'bid':bid, 'addistribution':addistribution, 'negativekeywords':neg, 'contentbid':contentBid};
		if(temp.addistribution == '')
		{
			alert("Please select a Traffic Source...");
			return;
		}
		
		errors = this.validateAdGroup(this.curAccountID, this.curAccountType, name, bid, contentBid, addistribution, neg);
		if(errors.length > 0)
		{
			alert(errors[0]);
			return;
		}
		if(this.curEditAdGroupID === false)
		{
			this.newAdGroups.push(temp);
			this.updateNewAdGroups();
			this.selectAdGroup(id, name);
			this.setLabel('createppc_currentLabel-adgroup', '<b>Current Ad Group:</b> ' + name + '[NEW]');
		}else{
			id = (this.curEditAdGroupID * -1) - 1;
			temp.curAdGroupID = this.curEditAdGroupID;
			this.newAdGroups[id] = temp;
			id = this.curEditAdGroupID;
			this.curEditAdGroupID = false;
			this.updateNewAdGroups();
		}
		formSrc.reset();
	},
	
	editAdGroup: function(id)
	{
		this._$(this.curAccountType+'-adgroup-submit').src = '/Themes/BevoMedia/img/createppc-update.png';

		this.curEditAdGroupID = id;
		
		var temp = this.getAdGroup(id);
		this.setAdGroupFormLabelH2(temp.name);
		
		var formSrc = document.forms['create-adgroup-form-'+temp.curAccountType];

		formSrc.reset();
		
		formSrc['adgroup-name'].value = temp.name;
		formSrc['adgroup-bid'].value = temp.bid;
		formSrc['adgroup-content-bid'].value = temp.contentbid;

		if(temp.negativekeywords.length > 0)
		{
			for(var itm in temp.negativekeywords)
			{
				formSrc['adgroup-neg'].value += temp.negativekeywords[itm] + "\n";
			}
		}
		
		if(temp.curAccountType !== 'ADWORDS')
		{
			formSrc['adgroup-addistribution'][0].checked = false;
			formSrc['adgroup-addistribution'][1].checked = false;
			
			if(temp.addistribution == 'SearchContent')
			{
				formSrc['adgroup-addistribution'][0].checked = true;
				formSrc['adgroup-addistribution'][1].checked = true;
			}else if(temp.addistribution == 'Search'){
				formSrc['adgroup-addistribution'][0].checked = true;
			}else if(temp.addistribution == 'Content'){
				formSrc['adgroup-addistribution'][1].checked = true;
			}
			
			formSrc['adgroup-bid'].disabled = !formSrc['adgroup-addistribution'][0].checked;
			formSrc['adgroup-content-bid'].disabled = !formSrc['adgroup-addistribution'][1].checked;
			
		}
	},
	
	removeAdGroup: function(id)
	{
		var _id = (id * -1) - 1;
		this.newAdGroups[_id] = false;
		this.updateNewAdGroups();
	},
	
	addAdVar: function()
	{
		var id = (this.newAdVariations.length * -1) - 1;
		var formSrc = document.forms['create-advar-form-'+this.curAccountType];
		var title = formSrc['advar-title'].value;
		if(formSrc['advar-description'])
		{
			var descr = formSrc['advar-description'].value;
		}else{
			var descr = formSrc['advar-description-line1'].value;
			for(var i=0; i<35-formSrc['advar-description-line1'].value.length; i++)
			{
				descr += " ";
			}
			descr += formSrc['advar-description-line2'].value;
		}
		var displ = formSrc['advar-displayurl'].value;
		var desti = formSrc['advar-destinationurl'].value;
		var track = formSrc['track-advar'].checked;
		errors = this.validateAdVar(this.curAccountID, this.curAccountType, title, desti, displ, descr);
		if(errors.length > 0)
		{
			alert(errors[0]);
			return;
		}
		if(track == true)
		{
			if(this.curAccountType == 'MSN')
			{
				desti = this.addUrlVarsMSN(desti);
			}
			if(this.curAccountType == 'ADWORDS')
			{
				desti = this.addUrlVarsAdwords(desti);
			}
			if(this.curAccountType == 'YAHOO')
			{
				//no url modification required
			}
			
		}

		var temp = {'checked':true, 'curAccountType':this.curAccountType, 'curAccountID':this.curAccountID, 'curCampaignID':this.curCampaignID, 'curAdGroupID':this.curAdGroupID, 'curAdVariationID':id, 'title':title, 'description':descr, 'displayurl':displ, 'destinationurl':desti};
		
		if(temp.curAccountType == 'ADWORDS')
		{
			var exPoints = (temp.description.split('!').length-1);
			if(exPoints >= 2)
			{
				alert('Google Adwords does not allow more than one exclamation point in the description...');
				return;
			}
		}

		var addall = formSrc['add-advar-to-all'].checked;
		if(addall)
		{
			this.addAdVarToAll(temp);
		}
		
		this.newAdVariations.push(temp);
		if(this.curAccountType == 'ADWORDS')
		{
			descr = descr.substring(0,34) + "<br/>" + descr.substring(35);
		}
		this.updateNewAdVariations();
		formSrc.reset();
	},
	
	getExistKeywordByAPIID: function(id)
	{
		for(var i=0; i<this.existKeywords.length; i++)
		{
			if(this.existKeywords[i].apiKeywordID == id)
				return this.existKeywords[i];
		}
	},
	
	getExistAdVariationByAPIID: function(id)
	{
		for(var i=0; i<this.existAdVariations.length; i++)
		{
			if(this.existAdVariations[i].apiAdVariationID == id)
				return this.existAdVariations[i];
		}
	},
	
	removeKeywordAPI: function(id)
	{
		if(id == 0)
		{
			alert('This item does not have the required information for a delete operation to complete successfully.'+"\n\n"+'A daily update may fix this problem.')
			return;
		}
		this.deleteKeywords.push(this.getExistKeywordByAPIID(id));
		this._$('removeKeywordElement_'+id).innerHTML = 'Undelete';
		this._$('removeKeywordElement_'+id).href= 'javascript:createppc.unRemoveKeywordAPI('+id+')';
	},
	
	unRemoveKeywordAPI: function(id)
	{
		this.deleteKeywords = this._removeValueFromArray(this.deleteKeywords, this.getExistKeywordByAPIID(id));
		this._$('removeKeywordElement_'+id).innerHTML = 'Delete';
		this._$('removeKeywordElement_'+id).href= 'javascript:createppc.removeKeywordAPI('+id+')';
	},
	
	removeAdVarsAPI: function(id)
	{
		if(id == 0)
		{
			alert('This item does not have the required information for a delete operation to complete successfully.'+"\n\n"+'A daily update may fix this problem.')
			return;
		}
		this.deleteAdVariations.push(this.getExistAdVariationByAPIID(id));
		this._$('removeAdVarsElement_'+id).innerHTML = 'Undelete';
		this._$('removeAdVarsElement_'+id).href= 'javascript:createppc.unRemoveAdVarsAPI('+id+')';
	},
	
	unRemoveAdVarsAPI: function(id)
	{
		this.deleteAdVariations = this._removeValueFromArray(this.deleteAdVariations, this.getExistAdVariationByAPIID(id));
		this._$('removeAdVarsElement_'+id).innerHTML = 'Delete';
		this._$('removeAdVarsElement_'+id).href= 'javascript:createppc.removeAdVarsAPI('+id+')';
	},
	
	_removeValueFromArray: function(arr, val)
	{
		if(arr.length <= 0)
			return arr;
		var output = new Array();
		for(var i=0; i<arr.length; i++)
		{
			if(JSON.stringify(arr[i]) != JSON.stringify(val))
			{
				output.push(arr[i]);
			}
		}
		return output;
	},
	
	removeAdVars: function(id)
	{
		var _id = (id * -1) - 1;
		this.newAdVariations[_id] = false;
		this.updateNewAdVariations();
	},
	
	addKeyword: function()
	{
		var id = (this.newKeywords.length * -1) - 1;
		var formSrc = document.forms['create-keyword-form-'+this.curAccountType];
		var name = formSrc['keyword-name'].value;
		var bid = formSrc['keyword-bid'].value;
		var url = formSrc['keyword-destinationurl'].value;
		var advMatch = '0';
		if(this.curAccountType == 'YAHOO')
			advMatch = ((formSrc['advanced-match'].checked)?'0':'1');
		var temp = {'checked':true, 'curAccountType':this.curAccountType, 'curAccountID':this.curAccountID, 'curCampaignID':this.curCampaignID, 'curAdGroupID':this.curAdGroupID, 'curKeywordID':id, 'keyword':name, 'bid':bid, 'destinationurl':url, 'advMatch':advMatch};
		this.newKeywords.push(temp);
		
		this.updateNewKeywords();
		formSrc.reset();
	},
	
	checkTab: function(evt) {
		if(!evt)
		{
			var evt = window.event;
		}

		var tab = "	";
		if(!evt.target)
			evt.target = evt.srcElement;
		
	    var t = evt.target;
	    if(t.selectionStart == undefined)
	    	var ss = t.value.length;
	    else
	    	var ss = t.selectionStart;

	    if(t.selectionEnd == undefined)
	    	var se = t.value.length;
	    else
	    	var se = t.selectionEnd;
	    
	    
	    if (evt.keyCode == 9) {
	    	if(evt.preventDefault)
	    	{
	    		evt.preventDefault();
	    	}
	        if (ss != se && t.value.slice(ss,se).indexOf("\n") != -1) {
	            var pre = t.value.slice(0,ss);
	            var sel = t.value.slice(ss,se).replace(/\n/g,"\n"+tab);
	            var post = t.value.slice(se,t.value.length);
	            t.value = pre.concat(tab).concat(sel).concat(post);
	            t.selectionStart = ss + tab.length;
	            t.selectionEnd = se + tab.length;
	        } else {
	            t.value = t.value.slice(0,ss).concat(tab).concat(t.value.slice(ss,t.value.length));
	            if (ss == se) {
	                t.selectionStart = t.selectionEnd = ss + tab.length;
	            }
	            else {
	                t.selectionStart = ss + tab.length;
	                t.selectionEnd = se + tab.length;
	            }
	        }
	        setTimeout(
	        		function()
	        		{
	        			var myelement = t;
	        			myelement.focus();
	        	        if (myelement.createTextRange) {
	        	        	var range = myelement.createTextRange(); 
	        	        	range.collapse(true); 
	        	        	range.moveEnd('character', myelement.selectionEnd); 
	        	        	range.moveStart('character', myelement.selectionStart); 
	        	        	range.select();
	        	        	t.selectionStart = t.selectionEnd = undefined;
	        	        }
	        		}
	        		, 1);
	        t.focus();
	        if(!evt.preventDefault)
	        	return false;
	    }
	},
	
	addAdGroupBulk: function()
	{
		var formSrc = document.forms['create-bulk-adgroup-form-'+this.curAccountType];
		var bulk = formSrc['bulk-adgroup'].value;
		var neg = formSrc['bulk-adgroup-neg'].value;
		neg = neg.split("\n");
		var lines = bulk.split("\n");
		for(var i in lines)
		{
			var ag = lines[i].split("\t");
			
			var id = (this.newAdGroups.length * -1) - 1;
			var name = ag[0];
			var bid = 1.5;
			if(name == '')
				continue;
			
			if(ag.length > 1)
				bid = ag[1];
			var contentBid = 0;
			if(ag.length > 2)
				contentBid = ag[2];
			
			var temp = {'checked':true, 'curAccountType':this.curAccountType, 'curAccountID':this.curAccountID, 'curCampaignID':this.curCampaignID, 'curAdGroupID':id, 'name':name, 'bid':bid, 'addistribution':'Search', 'negativekeywords':neg, 'contentbid':contentBid};
			this.newAdGroups.push(temp);
		}
		
		this.updateNewAdGroups();
		formSrc.reset();
	},

	addAdVarBulk: function()
	{
		var formSrc = document.forms['create-bulk-advar-form'];
		var track = formSrc['create-bulk-advar-form-track-advar'].checked;
		var bulk = formSrc['bulk-advar'].value;
		var lines = bulk.split("\n");
		
		var addall = formSrc['add-advar-to-all'].checked;
		
		for(var i in lines)
		{
			var av = lines[i].split("\t");
			
			var id = (this.newAdVariations.length * -1) - 1;
			var title = av[0];
			if(title == '')
				continue;
			
			if(av.length <= 1)
				continue;
			
			
			if(this.curAccountType == 'ADWORDS')
			{
				if(av.length > 1)
					var description = av[1];
				
				for(var i=0; i<35-av[1].length; i++)
					description += " ";
				
				description += av[2];
				
				if(av.length > 3)
					var displ = av[3];
				if(av.length > 4)
					var desti = av[4];
			}else{
				
				if(av.length > 1)
					var description = av[1];
				if(av.length > 2)
					var displ = av[2];
				if(av.length > 3)
					var desti = av[3];
			}
			
			if(track == true)
			{
				if(this.curAccountType == 'MSN')
				{
					desti = this.addUrlVarsMSN(desti);
				}
				if(this.curAccountType == 'ADWORDS')
				{
					desti = this.addUrlVarsAdwords(desti);
				}
				if(this.curAccountType == 'YAHOO')
				{

					//no url modification required
				}
			}
			var temp = {'checked':true, 'curAccountType':this.curAccountType, 'curAccountID':this.curAccountID, 'curCampaignID':this.curCampaignID, 'curAdGroupID':this.curAdGroupID, 'curAdVariationID':id, 'title':title, 'description':description, 'displayurl':displ, 'destinationurl':desti};
			if(addall)
			{
				this.addAdVarToAll(temp);
			}
			this.newAdVariations.push(temp);
		}
		
		this.updateNewAdVariations();
		formSrc.reset();
	},
	
	addAdVarToAll: function(adVar)
	{
		var campaignID = adVar.curCampaignID;
		for(var i=0; i<this.newAdGroups.length; i++)
		{
			if(this.newAdGroups[i] == false)
				continue;
			if(this.newAdGroups[i].curCampaignID !== campaignID)
				continue;
			if(this.newAdGroups[i].curAdGroupID == adVar.curAdGroupID)
				continue;
			
			var temp = this.manuallyConstructJSON(adVar);
			temp.curAdGroupID = this.newAdGroups[i].curAdGroupID;
			this.newAdVariations.push(temp);
		}
		for(var i=0; i<this.existAdGroups.length; i++)
		{
			if(this.existAdGroups[i] == false)
				continue;
			if(this.existAdGroups[i].curCampaignID !== campaignID)
				continue;
			if(this.existAdGroups[i].curAdGroupID == adVar.curAdGroupID)
				continue;
			
			var temp = this.manuallyConstructJSON(adVar);
			temp.curAdGroupID = this.existAdGroups[i].curAdGroupID;
			this.newAdVariations.push(temp);
		}

	},
	
	trim: function(str)
	{
		str = str.replace(new RegExp("^[\\s]+"), '');
		str = str.replace(new RegExp("[\\s]+$"), '');
		return str;
	},
	
	
	addKeywordBulk: function()
	{
		var formSrc = document.forms['create-bulk-keyword-form'];
		var bulk = formSrc['bulk-keyword'].value;
		var lines = bulk.split("\n");
		for(var i in lines)
		{
			var kw = lines[i].split("\t");
			
			var id = (this.newKeywords.length * -1) - 1;
			var name = kw[0];
			name = this.trim(name);
			if(name == '')
				continue;
			
			var bid = '';
			if(kw.length > 1)
			{
				var bid = kw[1];
			}
			
			if(bid == '')
			{
				var ag = this.getAdGroup(this.curAdGroupID);
				if(ag.bid != undefined)
					bid = ag.bid;
				if(ag.contentbid != undefined && bid == '')
					bid = ag.contentbid;
			}
			
			if(kw.length > 2)
				var url = kw[2];
			else
				var url = '';
			
			var advMatch = 1;
			if(this.curAccountType == 'YAHOO')
				advMatch = ((formSrc['keyword-bulk-standard-match'].checked)?'0':'1');
			
			var temp = {'checked':true, 'curAccountType':this.curAccountType, 'curAccountID':this.curAccountID, 'curCampaignID':this.curCampaignID, 'curAdGroupID':this.curAdGroupID, 'curKeywordID':id, 'keyword':name, 'bid':bid, 'destinationurl':url, 'advMatch':advMatch};
			
			
			this.newKeywords.push(temp);
		}
		
		this.updateNewKeywords();
		formSrc.reset();
	},
	
	removeKeyword: function(id)
	{
		var _id = (id * -1) - 1;
		this.newKeywords[_id] = false;

		this.updateNewKeywords();
	},
	
	getAccountName: function(type, id)
	{
		return this._$('account_'+type+'-'+id).innerHTML;
	},
	
	generateCheckbox: function(obj, add)
	{
		var id = obj.curCampaignID;
		var type = 'Campaign';
		if(obj.curAdGroupID != undefined)
		{
			type = 'AdGroup';
			id = obj.curAdGroupID;
		}
		if(obj.curAdVariationID != undefined)
		{
			type = 'AdVariation';
			id = obj.curAdVariationID;
		}
		if(obj.curKeywordID != undefined)
		{
			type = 'Keyword';
			id = obj.curKeywordID;
		}
		
		var output = "<input type='checkbox' class='checkbox' ";
		output += ((add==false)?" disabled=disabled ":'');
		output += ((obj.checked==true)?" checked='CHECKED' ":'');
		output += "onclick='createppc.onChange_generatedCheckbox(" + id + ", \"" + type + "\")' />";
		return output;
	},
	
	onChange_generatedCheckbox: function(id, type)
	{
		var _id = (id * -1) - 1;
		this['new'+type+'s'][_id].checked = !this['new'+type+'s'][_id].checked;
		this.generateReview();
	},
	
	_arrToObj: function(arr)
	{
		var output = {};
		for(var i in arr)
		{
			output[arr[i]] = arr[i];
		}
		return output;
	},
	
	getExistCampaign: function(curCampaignID)
	{
		for(var i=0; i<this.existCampaigns.length; i++)
		{
			if(curCampaignID == this.existCampaigns[i].curCampaignID)
				return this.existCampaigns[i];
		}
	},
	
	getExistAdGroup: function(curAdGroupID)
	{
		for(var i=0; i<this.existAdGroups.length; i++)
		{
			if(curAdGroupID == this.existAdGroups[i].curAdGroupID)
				return this.existAdGroups[i];
		}
	},

	getCampaignName: function(id)
	{
		if(id == 0)
			return false;
		
		if(id < 0)
			return this.newCampaigns[(id*-1)-1].name;
		
		return this.getCampaign(id).name;
	},

	getCampaign: function(id)
	{
		if(id == 0)
			return false;
		
		if(id < 0)
			return this.newCampaigns[(id*-1)-1];
		
		return this.getExistCampaign(id);
	},

	getAdGroup: function(id)
	{
		if(id == 0)
			return false;
		
		if(id < 0)
			return this.newAdGroups[(id*-1)-1];
		
		return this.getExistAdGroup(id);
	},

	getAdGroupName: function(id)
	{
		if(id == 0)
			return false;
		
		if(id < 0)
			return this.newAdGroups[(id*-1)-1].name;
		
		return this.getAdGroup(id).name;
	},
    validateCampaign: function(accountId, accountType, name, budget, geotargetCountries, negativeKeywords)
    {
        var errors = new Array();
        var accountName = this.getAccountName(accountType, accountId);
        if(!name)
          errors.push("Campaign name cannot be empty");
        else if(name.length < 2)
          errors.push("Campaign name must be at least 2 characters");
        var fbudget = parseFloat(budget);
        if(isNaN(fbudget))
          errors.push("Invalid budget: " + budget);
        else if(fbudget <= 0)
          errors.push("Budget must be greater than 0");
        // Ensure no campaign of this name exists
		numYahoo = 0;
        $.each(this.existCampaigns, function (i, exist) {
			if(exist.curAccountType == 'YAHOO')
				numYahoo++;
            if(exist.curAccountID == accountId && exist.name == name)
            {
              errors.push("Account "+accountName+" already contains a campaign named '"+name+"'");
            }
        });
		if(numYahoo > 15 && accountType == 'YAHOO')
			errors.push("Yahoo rate-limits you to creating 15 campaigns at a time using our editor.")
        // Ensure geotargeting present
        if(accountType != 'MSN')
        {
          if(!geotargetCountries || geotargetCountries.length < 1)
            errors.push("You must choose at least 1 target country to display this ad in");
        }
        return errors;
    },
    validateAdGroup: function(accountId, accountType, name, bid, contentbid, addistribution, negativekeywords)
    {
        var errors = new Array();
        if(!name)
          errors.push("Group name cannot be empty");
        else if(name.length < 2)
          errors.push("Group name must be at least 2 characters");
        if(addistribution.match('Search'))
        {
          var fbid = parseFloat(bid);
          if(isNaN(fbid))
            errors.push("Invalid Search CPC: " + bid);
          else if(fbid <= 0.05)
            errors.push("Search CPC must be greater than $0.05");
        }
        if(addistribution.match('Content'))
        {
          var fcbid = parseFloat(contentbid);
          if(isNaN(fcbid))
            errors.push("Invalid Content CPC: " + contentbid);
          else if(fcbid <= 0.05)
            errors.push("Content CPC must be greater than $0.05");
        }
        if(addistribution == '' || !(addistribution.match('Search') || addistribution.match('Content')))
          errors.push("You must choose either the Search network or Content network for this group")
        return errors;
    },
    validateAdVar: function(accountId, accountType, title, url, displayUrl, description)
    {
        var errors = new Array();
        var titleLimit = {'ADWORDS': 25, 'YAHOO': 40, 'MSN': 25};
        var descriptionLimit = {'ADWORDS': 70, 'YAHOO': 70, 'MSN': 70};
        var displayLimit = {'ADWORDS': 35, 'YAHOO': 35, 'MSN': 35};
        title_c = title.replace(/{([^}]+)}/i, '');
        description_c = description.replace(/{([^}]+)}/i, '');
        if(!title)
          errors.push("Title cannot be empty");
        else if (title.length < 2)
          errors.push("Title must be at least 2 characters");
        else if (title_c.length > titleLimit[accountType])
          errors.push("Title cannot be more than " + titleLimit[accountType] + " characters");
        else if (title[0].toUpperCase() != title[0])
          errors.push("Title must start with a capital letter");
        if(!description)
          errors.push("Description cannot be empty");
        else if (description.length < 2)
          errors.push("Description must be at least 2 characters");
        else if (description_c.length > descriptionLimit[accountType])
          errors.push("Description cannot be more than " + descriptionLimit[accountType] + " characters");
        else if (description[0].toUpperCase() != description[0])
          errors.push("Description must start with a capital letter");
		if(!url.match('^http(s?)://'))
		  errors.push("Destination URL must start with http:// or https://");
        if(!displayUrl)
          errors.push("Display URL cannot be empty");
        else if (displayUrl.length > displayLimit[accountType])
          errors.push("Display URL cannot be more than "+displayLimit[accountType]+"characters");
        return errors;
    },
	generateReview: function()
	{
		this._$('existAdVariationHolder').innerHTML = '';
		this._$('existKeywordHolder').innerHTML = '';
		
		var output = '';
		var adwordsEstimate = 0;

		var tempAdGroups = new Array();

		for(var i=0; i<this.newAdGroups.length; i++)
		{
			if(this.newAdGroups[i] !== false)
				tempAdGroups.push(this.newAdGroups[i].curAdGroupID);
		}

		for(var i in this.newAdVariations)
		{
			if(parseInt(this.newAdVariations[i].curAdGroupID) in this._arrToObj(tempAdGroups))
			{
			}else{
				tempAdGroups.push(parseInt(this.newAdVariations[i].curAdGroupID));
			}
		}

		for(var i in this.newKeywords)
		{
			if(this.newKeywords[i] === false)
				continue;

			if(this.newKeywords[i].curAdGroupID in this._arrToObj(tempAdGroups))
			{
			}else{
				tempAdGroups.push(parseInt(this.newKeywords[i].curAdGroupID));
			}
		}

		var tempCampaigns = new Array();
		for(var i=0; i<this.newCampaigns.length; i++)
		{
			if(this.newCampaigns[i] !== false)
				tempCampaigns.push(parseInt(this.newCampaigns[i].curCampaignID));

		}

		for(var i=0; i<tempAdGroups.length; i++)
		{
			var tempAdGroup = false;
			
			if(tempAdGroups[i] > 0)
			{
				tempAdGroup = this.getExistAdGroup(tempAdGroups[i]);
			}else{
				tempAdGroup = this.newAdGroups[(tempAdGroups[i]*-1)-1];
			}
			
			if(tempAdGroup === undefined)
				continue;
			
			if(tempAdGroup.curCampaignID in this._arrToObj(tempCampaigns))
			{
			}else{
				tempCampaigns.push(parseInt(tempAdGroup.curCampaignID));
			}
		}
		
		
		var cStyle;
		
		for(var c in tempCampaigns)
		{
			if(tempCampaigns[c] == undefined)
				continue;
			
			if(tempCampaigns[c] > 0)
			{
				tempCampaign = this.getExistCampaign(tempCampaigns[c]);
			}else{
				tempCampaign = this.newCampaigns[(tempCampaigns[c]*-1)-1];
			}
			
			if(tempCampaign == undefined)
				continue;
			
            var campaignErrors = this.validateCampaign(tempCampaign.curAccountID, tempCampaign.curAccountType, tempCampaign.name, tempCampaign.budget, tempCampaign.geotargets, tempCampaign.negativekeywords); 
            var campaignIsNew = (tempCampaign.curCampaignID<0);
            var campaignHasErrors = (campaignErrors.length > 0) && campaignIsNew;
			cStyle = '';
			if(c%2 == 0)
				cStyle = 'darker';
			output += "<div class='campaignRow "+cStyle+"'>";
			var graytext = (tempCampaign.checked == false)?'graytext':'';
            var errortext = (campaignIsNew && campaignHasErrors) && tempCampaign.checked ? 'error' : '';
			output += "<div class='reviewRow " + graytext +" "+ errortext +"'>";
			if(campaignIsNew)
				output += this.generateCheckbox(tempCampaign, true);
			else
				output += "<input type='checkbox' class='checkbox' style='visibility:hidden;'/>";
			output += "<img align='top' src='" + this.ppcImages[tempCampaign.curAccountType] +"' /> ";
			output += "<b>" + ((!campaignIsNew)?'Update':'Add') + "</b>";
			output += " the Campaign <span class='italicunderline'>" + tempCampaign.name.replace(/^\s+|\s+$/g, '') +"</span> " + ((!campaignIsNew)?'in':'to') + " account <i>" + this.getAccountName(tempCampaign.curAccountType, tempCampaign.curAccountID) + "</i>.<br>";
            if(campaignIsNew && campaignHasErrors && tempCampaign.checked)
              $.each(campaignErrors, function(i, e) { output+= "<span class='errorText'>"+e+"</span><br />";});
            if(campaignIsNew && tempCampaign.checked)
            {
              details = "<div style='margin-left: 20px; font-size: .8em'>";
              details += 'Daily Budget: $'+parseFloat(tempCampaign.budget).toFixed(2) + '<br />';
              if(tempCampaign.curAccountType != 'MSN')
                details += 'Geotarget Countries: ' + tempCampaign.geotargets.countries + '<br />';
              if(tempCampaign.curAccountType != 'YAHOO')
                details += 'Negative Keywords: ' + tempCampaign.negativekeywords;
              details += "</div>";
              output += details;
            }
			if(tempCampaign.curAccountType == 'ADWORDS')
			{
				adwordsEstimate++;
			}
			
			for(var a in tempAdGroups)
			{
				if(tempAdGroups[a] === false)
					continue;

				var tempAdGroup = false;
				
				if(tempAdGroups[a] > 0)
				{
					tempAdGroup = this.getExistAdGroup(tempAdGroups[a]);
				}else{
					tempAdGroup = this.newAdGroups[(tempAdGroups[a]*-1)-1];
				}
				
				if(tempAdGroup === undefined)
					continue;

				var cSet = tempCampaign.checked;
				if(tempAdGroup.curCampaignID == tempCampaign.curCampaignID)
				{
                    var aSet = cSet && tempAdGroup.checked;
                    var adgroupIsNew = (parseInt(tempAdGroup.curAdGroupID) < 0);
                    var adgroupErrors = new Array();
                    if(adgroupIsNew)
                      adgroupErrors = this.validateAdGroup(tempAdGroup.curAccountID, tempAdGroup.curAccountType, tempAdGroup.name, tempAdGroup.bid, tempAdGroup.contentbid, tempAdGroup.addistribution, tempAdGroup.negativekeywords);
                    var adgroupHasErrors = (adgroupErrors.length > 0 && adgroupIsNew) || campaignHasErrors;
					var graytext = !aSet ?'graytext':'';
					var errortext = adgroupHasErrors && aSet ?'error':'';
					output += "<div class='reviewRow indent " + graytext +" "+errortext+"'>";
					if(adgroupIsNew)
						output += this.generateCheckbox(tempAdGroup, cSet);
					else
						output += "<input type='checkbox' class='checkbox' style='visibility:hidden;'/>";
					output += "<b>" + ((!adgroupIsNew)?'Update':'Add') + "</b>";
					output += " the Ad Group <span class='italicunderline'>" + tempAdGroup.name +"</span><br>";
                    if(adgroupIsNew && adgroupHasErrors && aSet)
                      $.each(adgroupErrors, function(i, e) { output+= "<span class='errorText'>"+e+"</span><br />";});
                    if(adgroupIsNew && aSet)
                    {
                      details = "<div style='text-indent: 0px; margin-left: 5px; font-size: .8em'>";
                      if(tempAdGroup.addistribution.match('Search'))
                        details += 'Search CPC: $'+parseFloat(tempAdGroup.bid).toFixed(2)+'<br />';
                      if(tempAdGroup.addistribution.match('Content'))
                        details += 'Content CPC: $'+parseFloat(tempAdGroup.bid).toFixed(2)+'<br />';
                      details += 'Target: '+ tempAdGroup.addistribution + '<br />';
                      details += 'Negative Keywords: ' + tempAdGroup.negativekeywords;
                      details += "</div>";
                      output += details;
                    }
					if(tempAdGroup.curAccountType == 'ADWORDS')
					{
						adwordsEstimate++;
					}
					
					for(var av in this.newAdVariations)
					{
						if(this.newAdVariations[av] == false)
							continue;
						if(this.newAdVariations[av].curAdGroupID == tempAdGroup.curAdGroupID)
						{
                            var tempAV = this.newAdVariations[av];
                            var avSet = aSet && tempAV.checked;
                            var avErrors = this.validateAdVar(tempAV.curAccountId, tempAV.curAccountType, tempAV.title, tempAV.destinationurl, tempAV.displayurl, tempAV.description);
                            var avHasErrors = avErrors.length > 0 || adgroupHasErrors;
							var graytext = !avSet?'graytext':'';
                            var errortext = avHasErrors && avSet ? 'error' : '';
							output += "<div class='reviewRow indentMore " + graytext +" "+errortext+"'>";
							output += this.generateCheckbox(this.newAdVariations[av], aSet);
							output += "<b>Add</b> the Ad Variation <span class='italicunderline'>" + this.newAdVariations[av].title +"</span><br>";
                            if(avSet && avHasErrors)
                              $.each(avErrors, function(i, e) {output += "<span class='errorText'>"+e+"</span><br />";});
                            if(avSet)
                            {
                              details = "<div style='text-indent: 0px; margin-left: 5px; display: block;'>";
                              details += "<p class='advariation' style='float: none;'><a href='"+tempAV.destinationurl+"' target='_blank'>"+tempAV.title+"</a><br />";
                              details += "<span>"+tempAV.description+"</span><br />";
                              details += "<span class='display_url'>"+tempAV.displayurl+"</span>";
                              details += "</p></div>";
                              output += details;
                            }
							output += "</div>";
							if(this.newAdVariations[av].curAccountType == 'ADWORDS')
							{
								adwordsEstimate++;
							}
						}
					}
					

					for(var kw in this.newKeywords)
					{
						if(this.newKeywords[kw] == false)
							continue;
						if(this.newKeywords[kw].curAdGroupID == tempAdGroup.curAdGroupID)
						{
                            var tempKW = this.newKeywords[kw];
                            var kwSet = aSet && tempKW.checked;
							var graytext = !kwSet ?'graytext':'';
							output += "<div class='reviewRow indentMore " + graytext +"'>";
							output += this.generateCheckbox(this.newKeywords[kw], aSet);
							output += "<b>Add</b> the Keyword <span class='italicunderline'>" + this.newKeywords[kw].keyword +"</span><br>";
                            if(kwSet)
                            {
                              details = "<div style='text-indent: 0px; margin-left: 5px; font-size:.8em'>";
                              if(tempKW.curAccountType == 'YAHOO')
                                details += (tempKW.advancedmatch ? 'Advanced Match' : 'Standard Match') + "<br />";
                              if(tempKW.destinationurl && tempKW.destinationurl != '' && tempKW.destinationurl != 'http://' && tempKW.curAccountType != 'ADWORDS')
                                details += "Destination: " + tempKW.destinationurl + "<br />";
                              if(tempKW.bid && parseFloat(tempKW.bid) > 0)
                                details += "Bid: $" + parseFloat(tempKW.bid).toFixed(2) + "<br />";
                              details += "</div>";
                              output += details;
                            }
							output += "</div>";
							if(this.newKeywords[kw].curAccountType == 'ADWORDS')
							{
								adwordsEstimate++;
							}
						}
					}
					output += "</div>";
				}
			}
            output += "</div>";
			
			if(campaignIsNew)
			{
				output += "<div class='floatRight'>";
				output += "<form class='appform'>";
					output += "Add this campaign to another account: ";
					output += "<input type='hidden' name='CampaignID' value='" + tempCampaign.curCampaignID +"'>";
					output += this.dropDownAccounts();
					output += "<input class='formsubmit cpe_add' type='submit' onclick='javascript:createppc.onClick_cloneCampaign(event); return false;' value='Add' />";
				output += "</form>";
				output += "</div>";
				output += "<div class='clear'></div>";
			}
			output += "</div>";
            output += "<hr />";
		}
		
		if(output == '')
		{
			output = '<center><i>No changes have been made.</i></center>';
		}else{
			output = "<h3>Select Changes to Process</h3>" + output;
		}
		
		if(this.deleteAdVariations.length > 0)
		{
			output += '<br/><h3>Items to Delete</h3>';
			for(var i=0; i<this.deleteAdVariations.length; i++)
			{
				cStyle = '';
				if(i%2 == 0)
					cStyle = 'darker';

				output += "<div class='campaignRow "+cStyle+"'>";
				
				output += this.generateAdVariationHTML(this.existAdVariationCloneBase, this.deleteAdVariations[i]);
				output += '<b>Campaign:</b> ' + this.getCampaignName(this.getAdGroup(this.deleteAdVariations[i].curAdGroupID).curCampaignID);
				output += ' &nbsp; ';
				output += '<b>AdGroup:</b> ' + this.getAdGroupName(this.deleteAdVariations[i].curAdGroupID);
				output += "</div>";
				
				if(this.getCampaign(this.getAdGroup(this.deleteAdVariations[i].curAdGroupID).curCampaignID).curAccountType == 'ADWORDS')
				{
					adwordsEstimate++;
				}
			}
		}
		
		if(this.deleteKeywords.length > 0)
		{
			output += '<br/><h3>Items to Delete</h3>';
			for(var i=0; i<this.deleteKeywords.length; i++)
			{
				cStyle = '';
				if(i%2 == 0)
					cStyle = 'darker';

				output += "<div class='campaignRow "+cStyle+"'>";
				
				output += this.generateKeywordHTML(this.existKeywordCloneBase, this.deleteKeywords[i]);
				output += '<b>Campaign:</b> ' + this.getCampaignName(this.getAdGroup(this.deleteKeywords[i].curAdGroupID).curCampaignID);
				output += ' &nbsp; ';
				output += '<b>AdGroup:</b> ' + this.getAdGroupName(this.deleteKeywords[i].curAdGroupID);
				output += "</div>";
				
				if(this.getCampaign(this.getAdGroup(this.deleteKeywords[i].curAdGroupID).curCampaignID).curAccountType == 'ADWORDS')
				{
					adwordsEstimate++;
				}
			}
		}
		
		this._$('reviewOutput').innerHTML = output;
		
		for(var i=0; i<this.deleteAdVariations.length; i++)
		{
			this._$('removeAdVarsElement_'+this.deleteAdVariations[i].apiAdVariationID).innerHTML = 'Undelete';
			this._$('removeAdVarsElement_'+this.deleteAdVariations[i].apiAdVariationID).href = 'javascript:createppc.unRemoveAdVarsAPI('+this.deleteAdVariations[i].apiAdVariationID+'); createppc.generateReview();';
		}
		
		for(var i=0; i<this.deleteKeywords.length; i++)
		{
			this._$('removeKeywordElement_'+this.deleteKeywords[i].apiKeywordID).innerHTML = 'Undelete';
			this._$('removeKeywordElement_'+this.deleteKeywords[i].apiKeywordID).href = 'javascript:createppc.unRemoveKeywordAPI('+this.deleteKeywords[i].apiKeywordID+'); createppc.generateReview();';
		}
		
	},
	
	onClick_cloneCampaign: function(e)
	{
		if(!e)
		{
			var e = window.event;
		}
		
		createppc.cloneCampaign(e);
	},
	
	cloneCampaign: function(event)
	{
        M_STR = 'bevo_c={AdId}';
        G_STR = 'bevo_c={creative}&bevo_k={keyword}&bevo_m={ifsearch:s}{ifcontent:c}';
		var CampaignID = event.target.form['CampaignID'].value;
		var Account = event.target.form['Account'].value;
		Account = Account.split("-");
		var AccountType = Account[0];
		var AccountID = Account[1];
		
		
		CampaignID = (CampaignID * -1) - 1;
		var temp = this._cloneObj(this.newCampaigns[CampaignID]);
		var ToType = AccountType;
		var FromType = temp.curAccountType;

		var addTheseNegs = false;
		if((FromType == 'ADWORDS' && ToType == 'YAHOO') || (FromType == 'MSN' && ToType == 'YAHOO'))
		{
			//	FROM ADWORDS OR MSN >>[TO]>> YAHOO:	No negative kws at campaign level
			//		Prompt if user wants to move campaign level keywords to adgroups
			if(temp.negativekeywords.length > 0)
			{
				var negsInAdGroups = false;
				var countAdGroups = 0;
				for(var a in this.newAdGroups)
				{
					if(this.newAdGroups[a].curCampaignID != (CampaignID*-1)-1)
						continue;
					countAdGroups++;
					if(this.newAdGroups[a].negativekeywords == undefined || this.newAdGroups[a].negativekeywords.length == 0)
						continue;
					negsInAdGroups = true;
				}
				if(!negsInAdGroups && countAdGroups > 0)
				{
					var PromptMsg = 'Yahoo does not support negative keywords at the Campaign level.' + "\n" + 'Would you like to copy the Campaign level negative keywords to each Ad Group?' + "\n";
					PromptMsg += "The following negative keywords will be applied to each Ad Group: \n\t[";
					PromptMsg += temp.negativekeywords;
					PromptMsg += "]";
					var MergeFrom = confirm(PromptMsg);
					if(MergeFrom)
					{
						addTheseNegs = temp.negativekeywords;
					}
				}
				
			}
		}
		
		temp.curAccountType = AccountType;
		temp.curAccountID = AccountID;
		temp = this._addCampaignObj(temp);
		for(var ag in this.newAdGroups)
		{
			var tempAG = this._cloneObj(this.newAdGroups[ag]);
			if(tempAG == false)
				continue;
			if(tempAG.curCampaignID != (CampaignID*-1)-1)
				continue;

			if(addTheseNegs !== false)
			{
				tempAG.negativekeywords = addTheseNegs;
			}
			tempAG.curAccountType = AccountType;
			tempAG.curAccountID = AccountID;
			tempAG.curCampaignID = temp.curCampaignID;
			tempAG = this._addAdGroupObj(tempAG);
			for(var kw in this.newKeywords)
			{
				var tempKW = this._cloneObj(this.newKeywords[kw]);
				if(tempKW == false)
					continue;
				
				if(tempKW.curAdGroupID != this.newAdGroups[ag].curAdGroupID)
					continue;
				
				tempKW.curAccountType = tempAG.curAccountType;
				tempKW.curAccountID = tempAG.curAccountID;
				tempKW.curCampaignID = tempAG.curCampaignID;
				tempKW.curAdGroupID = tempAG.curAdGroupID;
				tempKW = this._addKeywordsObj(tempKW);
			}
			
			for(var av in this.newAdVariations)
			{
				var tempAV = this._cloneObj(this.newAdVariations[av]);
				if(tempAV == false)
					continue;
				
				if(tempAV.curAdGroupID != this.newAdGroups[ag].curAdGroupID)
					continue;
                var tracking = false;
				if(FromType == 'ADWORDS' && tempAV.destinationurl.match(G_STR))
                {
                  tempAV.destinationurl = tempAV.destinationurl.replace(G_STR, '');
                  tracking = true;
                }
                if(FromType == 'MSN' && tempAV.destinationurl.match(M_STR))
                {
                  tempAV.destinationurl = tempAV.destinationurl.replace(M_STR, '');
                  tracking = true;
                }
				if(tracking)
                {
                  if(ToType == 'MSN')
                  {
                      tempAV.destinationurl = this.addUrlVarsMSN(tempAV.destinationurl);
                  }
                  if(ToType == 'ADWORDS')
                  {
                      tempAV.destinationurl = this.addUrlVarsAdwords(tempAV.destinationurl);
                  }
                }
				tempAV.curAccountType = tempAG.curAccountType;
				tempAV.curAccountID = tempAG.curAccountID;
				tempAV.curCampaignID = tempAG.curCampaignID;
				tempAV.curAdGroupID = tempAG.curAdGroupID;
				tempAV = this._addAdVarsObj(tempAV);
			}
			
		}
		
		this.selectMenuTab('review');
	},
	
	_cloneObj: function(obj)
	{
		var temp = new Object();
		for(var o in obj)
		{
			temp[o] = obj[o];
		}
		return temp;
	},
	
	_addAdVarsObj: function(obj)
	{
		var newAdVar = obj;
		if(newAdVar == false)
			return false;
		
		var id = (this.newAdVariations.length * -1) - 1;
		newAdVar.curAdVariationID = id;
		this.newAdVariations.push(newAdVar);
		
		var title = newAdVar.title;
		var descr = newAdVar.description;
		var displ = newAdVar.displayurl;
		var desti = newAdVar.destinationurl;

		/*
		var clone = this._$('newAdVarsClone');
		var base = clone.innerHTML;
			base = base.replace(/\{ADVAR_ID\}/g, id);
			base = base.replace(/\{TITLE\}/g, title);
			base = base.replace(/\{DESCRIPTION\}/g, descr);
			base = base.replace(/\{DISPLAY_URL\}/g, displ);
			base = base.replace(/\{URL\}/g, desti);
			base = '<div id="advarsClone_'+id+'" >' + base + '</div>';
		this._$('newAdVars-'+newAdVar.curAdGroupID).innerHTML += base;
		*/
		return newAdVar;
	},

	
	_addKeywordsObj: function(obj)
	{
		var newKeyword = obj;
		if(newKeyword == false)
			return;
		
		var id = (this.newKeywords.length * -1) - 1;
		newKeyword.curKeywordID = id;
		this.newKeywords.push(newKeyword);
		
		var name = newKeyword.keyword;
		var bid = newKeyword.bid;
		var url = newKeyword.destinationurl;
		
		/*
		var clone = this._$('newKeywordsClone');
		var base = clone.innerHTML;
			base = base.replace(/\{KEYWORD_ID\}/g, id);
			base = base.replace(/\{KEYWORD_NAME\}/g, name);
			base = base.replace(/\{KEYWORD_BID\}/g, bid);
			base = '<div id="keywordsClone_'+id+'" >' + base + '</div>';
		this._$('newKeywords-'+newKeyword.curAdGroupID).innerHTML += base;
		*/
		
		return newKeyword;
	},

	_addAdGroupObj: function(obj)
	{
		var newAdGroup = obj;
		if(newAdGroup == false)
			return;
		
		var id = (this.newAdGroups.length * -1) - 1;
		newAdGroup.curAdGroupID = id;
		this.newAdGroups.push(newAdGroup);
		
		var name = newAdGroup.name;
		var bid = newAdGroup.bid;
		var addistribution = newAdGroup.addistribution;

		return newAdGroup;
	},
	

	_addCampaignObj: function(obj)
	{
		var newCampaign = obj;
		if(newCampaign == false)
			return;
		var id = (this.newCampaigns.length * -1) - 1;
		newCampaign.curCampaignID = id;
		this.newCampaigns.push(newCampaign);
		
		var name = newCampaign.name;
		var budget = newCampaign.budget;
		
		return newCampaign;
	},
	
	submitAPI: function()
	{
		var temp = new Object();
		function clean_array(arr) {
			return $.map(arr, function(n, i) { if(n) return n; return; });
		}
		temp.newCampaigns = clean_array(this.newCampaigns);
		temp.newAdGroups = clean_array(this.newAdGroups);
		temp.newAdVariations = clean_array(this.newAdVariations);
		temp.newKeywords = clean_array(this.newKeywords);
		
		temp.deleteKeywords = clean_array(this.deleteKeywords);
		temp.deleteAdVariations = clean_array(this.deleteAdVariations);
		
		temp.curAccountID = this.curAccountID;
		temp.curAccountType = this.curAccountType;
		temp.curCampaignID = this.curCampaignID;
		temp.curAdGroupID = this.curAdGroupID;
		document.forms['submitViaAPI'].elements[0].value = JSON.stringify(temp);
		document.forms['submitViaAPI'].submit();
	},
	
	saveSession: function()
	{
		var temp = new Object();
		
		temp.newCampaigns = this.newCampaigns;
		temp.newAdGroups = this.newAdGroups;
		temp.newAdVariations = this.newAdVariations;
		temp.newKeywords = this.newKeywords;
		
		temp.deleteAdVariations = this.deleteAdVariations;
		
		temp.curAccountID = this.curAccountID;
		temp.curAccountType = this.curAccountType;
		temp.curCampaignID = this.curCampaignID;
		temp.curAdGroupID = this.curAdGroupID;
		
		
		var output = JSON.stringify(temp);
		document.forms['saveSession'].elements[0].value = output;
		document.forms['saveSession'].submit();
	},
	
	loadSession: function(obj)
	{
		this.killClones();

		for(var o in obj)
		{
			this[o] = null;
			this[o] = obj[o];
		}
		
		var cCID = this.curCampaignID;
		var cAGID = this.curAdGroupID;
		
		this.selectAccount(this.curAccountType, this.curAccountID, this.getAccountName(this.curAccountType, this.curAccountID))
		this.selectCampaign(cCID, this.getCampaignName(cCID));
		this.selectAdGroup(cAGID, this.getAdGroupName(cAGID));

		this.selectMenuTab('review');
		//this.selectAccount(this.curAccountType, this.curAccountID);
	},
	
	killClones: function()
	{
		var campaignContent = this._$('createppc');
		var campaignContentCollection = campaignContent.getElementsByTagName('div');
		var kill = new Array();
		for(var i in campaignContentCollection)
		{
			var ele = campaignContentCollection[i];
			if(ele == undefined)
				continue;
			
			if(ele.id != undefined)
			{
				if(ele.id.indexOf('Clone_') != -1)
				{
					kill.push(ele);
				}
				
				if(ele.id.indexOf('-holder_') != -1)
				{
					if(ele.className == 'CLONE')
					{
						kill.push(ele);
					}
				}
			}
		}
		for(var i in kill)
		{
			kill[i].parentNode.removeChild(kill[i]);
		}
		
	},

	manuallyConstructJSON: function(obj)
	{
		var output = new Object();
		for(var o in obj)
		{
			output[o] = obj[o];
		}
		
		return output;
	},
	
	contains: function(a, obj)
	{
	  for(var i = 0; i < a.length; i++) {
	    if(a[i] === obj){
	      return true;
	    }
	  }
	  return false;
	},
	
	addUrlVarsMSN: function(url)
	{
		if(url.indexOf('.') == -1)
			return url;

		if(url.indexOf('?') !== -1)
		{
			var sp = url.split('?');
			var pa = (sp[1]);
			if(sp.length==1)
				pa = '';
			sp = sp[0];

			var pai = pa.split('&');

			//bevo_r={QueryString}&bevo_k={keyword}&bevo_c={AdId}&bevo_m={MatchType}
			var params = new Array('bevo_r={QueryString}', 'bevo_k={keyword}', 'bevo_c={AdId}', 'bevo_m={MatchType}', 'bevo_p=msn');
			if(nUrl = url.match(/[a-zA-Z0-9_\/\:\-\.]+\/dc\/[A-Z]{31}.+\?/))
			{
				url = nUrl;
				var params = new Array('bevo_c={AdId}');
			}
			
			outStr = '';
			for(var i in pai)
			{
				if(!this.contains(params, pai[i]))
				{
					outStr += pai[i] + '&';
				}
			}
			for(var i in params)
			{
				outStr += params[i] + '&';
			}

			outStr = outStr.substring(0, outStr.length-1);
			var fin = sp + '?' + outStr;
			fin = fin.replace('?&', '?');

			return fin;
		}

		return url + "?" + "bevo_r={QueryString}&bevo_k={keyword}&bevo_c={AdId}&bevo_m={MatchType}&bevo_p=msn";
	},
	
	addUrlVarsAdwords: function(url)
	{

		if(url.indexOf('.') == -1)
			return url;

		if(url.indexOf('?') !== -1)
		{
			var sp = url.split('?');
			var pa = (sp[1]);
			if(sp.length==1)
				pa = '';
			sp = sp[0];

			var pai = pa.split('&');

			var params = new Array('bevo_k={keyword}', 'bevo_c={creative}', 'bevo_m={ifsearch:s}{ifcontent:c}', 'bevo_p=google');

			if(nUrl = url.match(/[a-zA-Z0-9_\/\:\-\.]+\/dc\/[A-Z]{31}.+\?/))
			{
				url = nUrl;
				var params = new Array('bevo_c={creative}', 'bevo_p=google');
			}
			
			outStr = '';
			for(var i in pai)
			{
				if(!this.contains(params, pai[i]))
				{
					outStr += pai[i] + '&';
				}
			}
			for(var i in params)
			{
				outStr += params[i] + '&';
			}

			outStr = outStr.substring(0, outStr.length-1);
			var fin = sp + '?' + outStr;
			final = fin.replace('?&', '?');

			return fin;
		}

		return url + "?" + "bevo_k={keyword}&bevo_c={creative}&bevo_m={ifsearch:s}{ifcontent:c}&bevo_p=google";
	},
	
	hideSessionManagement: function()
	{
		this._$('session-management-main').style.display = 'none';
	},
	showSessionManagement: function()
	{
		this.hideSaveSession();
		this.hideLoadSession();
		this._$('session-management-main').style.display = 'block';
	},

	hideSaveSession: function()
	{
		this._$('session-management-save').style.display = 'none';
	},
	showSaveSession: function()
	{
		this.hideLoadSession();
		this.hideSessionManagement();
		this._$('session-management-save').style.display = 'block';
	},

	hideLoadSession: function()
	{
		this._$('session-management-load').style.display = 'none';
	},
	showLoadSession: function()
	{
		this.hideSaveSession();
		this.hideSessionManagement();
		this._$('session-management-load').style.display = 'block';
	}

}
