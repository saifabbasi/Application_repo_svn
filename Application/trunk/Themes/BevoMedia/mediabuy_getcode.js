var mediabuy_getcode = {
	dropdown: false,
	additionalIdNames: new Array(
			'mediabuy_campaign_id',
			'mediabuy_campaign-desc_id', 
			'mediabuy_adgroup_id', 
			'mediabuy_adgroup-desc_id', 
			'mediabuy_advar_id',
			'mediabuy_cost_id'
			),
	additionalIdNamesPPV: new Array(
			'ppv_campaign_id',
			'ppv_campaign-desc_id', 
			'ppv_cpm_id'
			),
	additionalIdNamesAdOn: new Array(
			'adon_campaign_id',
			'adon_campaign-desc_id', 
			'adon_cpm_id'
			),
	additionalIdNamesMediaTraffic: new Array(
			'medtraf_campaign_id',
			'medtraf_campaign-desc_id', 
			'medtraf_cpm_id'
			),
	additionalIdNamesDirCPV: new Array(
			'dircpv_campaign_id',
			'dircpv_campaign-desc_id', 
			'dircpv_cpm_id'
			),
	additionalIdNamesLeadImpact: new Array(
			'leadimpact_campaign-desc_id',
			'leadimpact_campaign_id', 
			'leadimpact_cpm_id'
			),
		
	campaigns_adon: new Array(),
	adgroups_adon: new Array(),
	
	campaigns_mediabuy: new Array(),
	adgroups_mediabuy: new Array(),
	
	campaigns_ppv: new Array(),
	adgroups_ppv: new Array(),

	campaigns_medtraf: new Array(),
	adgroups_medtraf: new Array(),

	campaigns_dircpv: new Array(),
	adgroups_dircpv: new Array(),

	addCampaign: function(name, id, type)
	{
		var temp = new Object();
		temp.id = id;
		temp.name = name;
		this['campaigns_'+type].push(temp);
	},
	
	addAdGroup: function(name, id, type)
	{
		var temp = new Object();
		temp.id = id;
		temp.name = name;
		this['adgroups_'+type].push(temp);
	},
	
	formatName: function(search, name)
	{
		var rgx = RegExp(search,"ig");
		var srch = name.match(rgx);
		
		return name.replace(rgx,'<b>' + srch[0] + '</b>');
	},
	
	strposArr: function(str, arr)
	{
		if(arr.length == 0)
			return false;
		
		var output = new Array();
		if(str == '')
			return output;
		for(var i=0; i<arr.length; i++)
		{
			var pos = arr[i].name.toLowerCase().indexOf( (str+'').toLowerCase());
			if(pos >= 0)
			{
				arr[i].formattedName = this.formatName(str, arr[i].name);
				output.push(arr[i]);
			}
		}
		return output;
	},
	
	adGroupAutoComplete: function(tar, type)
	{
		var val = tar.value;
		var id = tar.id;
		var arr = this.strposArr(val, this['adgroups_'+type]);
		this.genChoiceTable(id, arr);
	},
	
	campaignAutoComplete: function(tar, type)
	{
		var val = tar.value;
		var id = tar.id;
		var arr = this.strposArr(val, this['campaigns_'+type]);
		this.genChoiceTable(id, arr);
	},
	
	removeChoiceTable: function(id)
	{
		if(document.getElementById('choice_table-'+id))
			document.getElementById(id).parentNode.removeChild(document.getElementById('choice_table-'+id));
	},
	
	genChoiceTable: function(id, arr)
	{
		document.getElementById(id+'-id').value = '';
		this.removeChoiceTable(id);
		
		if(arr === false)
			return false;
		
		var tbl = document.createElement('table');
		tbl.id = 'choice_table-'+id;
		tbl.className = 'mediabuy_getcode-choice_table';
		for(var i=0; i<arr.length; i++)
		{
			var row = tbl.insertRow(-1);
			var cell = row.insertCell(-1);
			cell.innerHTML = '<a href="#" onClick="document.getElementById(\'' + id + '\').value = \'' + arr[i].name + '\'; document.getElementById(\'' + id + '-id\').value = \'' + arr[i].id+ '\';return false;">' + arr[i].formattedName + '</a>';
		}
		
		if(arr.length == 0)
		{

			var row = tbl.insertRow(-1);
			row.width = '100%';
			var cell = row.insertCell(-1);
			cell.innerHTML = '<i>Choices will appear after you begin typing...</i>';
		}
		var ele = document.getElementById(id);
		ele.parentNode.appendChild(tbl);
	},
	
	selectEvent: function(e)
	{
		var l = document.getElementById('se');
		if(l.value == 'other')
		{
			mediabuy_getcode.showAdditional();
		}else{
			mediabuy_getcode.hideAdditional();
		}
		if(l.value == 'trafficvance')
		{
			mediabuy_getcode.showPPV();
		}else{
			mediabuy_getcode.hidePPV();
		}
		if(l.value == 'adon')
		{
			mediabuy_getcode.showAdOn();
		}else{
			mediabuy_getcode.hideAdOn();
		}
		if(l.value == 'mediatraffic')
		{
			mediabuy_getcode.showMedTraf();
		}else{
			mediabuy_getcode.hideMedTraf();
		}
		if(l.value == 'leadimpact')
		{
			mediabuy_getcode.showLeadImpact();
		}else{
			mediabuy_getcode.hideLeadImpact();
		}
		if(l.value == 'dircpv')
		{
			mediabuy_getcode.showDirCPV();
		}else{
			mediabuy_getcode.hideDirCPV();
		}
	},
	
	showAdOn: function()
	{
		for(var i=0; i<this.additionalIdNamesAdOn.length; i++)
		{
			document.getElementById(this.additionalIdNamesAdOn[i]).style.display = '';
		}
	},
	
	hideAdOn: function()
	{
		for(var i=0; i<this.additionalIdNamesAdOn.length; i++)
		{
			document.getElementById(this.additionalIdNamesAdOn[i]).style.display = 'none';
		}
	},
	
	showPPV: function()
	{
		for(var i=0; i<this.additionalIdNamesPPV.length; i++)
		{
			document.getElementById(this.additionalIdNamesPPV[i]).style.display = '';
		}
	},
	
	hidePPV: function()
	{
		for(var i=0; i<this.additionalIdNamesPPV.length; i++)
		{
			document.getElementById(this.additionalIdNamesPPV[i]).style.display = 'none';
		}
	},
	
	showMedTraf: function()
	{
		for(var i=0; i<this.additionalIdNamesMediaTraffic.length; i++)
		{
			document.getElementById(this.additionalIdNamesMediaTraffic[i]).style.display = '';
		}
	},
	
	hideMedTraf: function()
	{
		for(var i=0; i<this.additionalIdNamesMediaTraffic.length; i++)
		{
			document.getElementById(this.additionalIdNamesMediaTraffic[i]).style.display = 'none';
		}
	},
	
	showLeadImpact: function()
	{
		for(var i=0; i<this.additionalIdNamesLeadImpact.length; i++)
		{
			document.getElementById(this.additionalIdNamesLeadImpact[i]).style.display = '';
		}
	},
	
	hideLeadImpact: function()
	{
		for(var i=0; i<this.additionalIdNamesLeadImpact.length; i++)
		{
			document.getElementById(this.additionalIdNamesLeadImpact[i]).style.display = 'none';
		}
	},
	
	showDirCPV: function()
	{
		for(var i=0; i<this.additionalIdNamesMediaTraffic.length; i++)
		{
			document.getElementById(this.additionalIdNamesDirCPV[i]).style.display = '';
		}
	},
	
	hideDirCPV: function()
	{
		for(var i=0; i<this.additionalIdNamesMediaTraffic.length; i++)
		{
			document.getElementById(this.additionalIdNamesDirCPV[i]).style.display = 'none';
		}
	},
	
	showAdditional: function()
	{
		for(var i=0; i<this.additionalIdNames.length; i++)
		{
			document.getElementById(this.additionalIdNames[i]).style.display = '';
		}
	},
	
	hideAdditional: function()
	{
		for(var i=0; i<this.additionalIdNames.length; i++)
		{
			document.getElementById(this.additionalIdNames[i]).style.display = 'none';
		}
	},
	
	init: function(dropdownValue)
	{
		var ele = document.getElementById('se');
		if (ele.addEventListener){
			ele.addEventListener('change', this.selectEvent, false); 
		} else if (ele.attachEvent){
			ele.attachEvent('onchange', this.selectEvent);
		}
		if(dropdownValue)
		{
			ele.value = dropdownValue;
		}else{
			ele.value = 'google';
		}
		this.selectEvent(false);
		
		var tar = document.getElementById('campaign_input_id');
		if (tar.addEventListener){
			tar.addEventListener('focus', this.keydownCampaignEvent, false);
			tar.addEventListener('keydown', this.keydownCampaignEvent, false);
			tar.addEventListener('keyup', this.keydownCampaignEvent, false);
			tar.addEventListener('blur', this.blurEvent, false);
		} else if (ele.attachEvent){
			tar.attachEvent('onfocus', this.keydownCampaignEvent);
			tar.attachEvent('onkeydown', this.keydownCampaignEvent);
			tar.attachEvent('onkeyup', this.keydownCampaignEvent);
			tar.attachEvent('onblur', this.blurEvent);
		}
		
		var tar = document.getElementById('ppv_campaign_input_id');
		if (tar.addEventListener){
			tar.addEventListener('focus', this.keydownCampaignEvent, false);
			tar.addEventListener('keydown', this.keydownCampaignEvent, false);
			tar.addEventListener('keyup', this.keydownCampaignEvent, false);
			tar.addEventListener('blur', this.blurEvent, false);
		} else if (ele.attachEvent){
			tar.attachEvent('onfocus', this.keydownCampaignEvent);
			tar.attachEvent('onkeydown', this.keydownCampaignEvent);
			tar.attachEvent('onkeyup', this.keydownCampaignEvent);
			tar.attachEvent('onblur', this.blurEvent);
		}
		
		var tar = document.getElementById('adon_campaign_input_id');
		if (tar.addEventListener){
			tar.addEventListener('focus', this.keydownCampaignEvent, false);
			tar.addEventListener('keydown', this.keydownCampaignEvent, false);
			tar.addEventListener('keyup', this.keydownCampaignEvent, false);
			tar.addEventListener('blur', this.blurEvent, false);
		} else if (ele.attachEvent){
			tar.attachEvent('onfocus', this.keydownCampaignEvent);
			tar.attachEvent('onkeydown', this.keydownCampaignEvent);
			tar.attachEvent('onkeyup', this.keydownCampaignEvent);
			tar.attachEvent('onblur', this.blurEvent);
		}
		
		var tar = document.getElementById('medtraf_campaign_input_id');
		if (tar.addEventListener){
			tar.addEventListener('focus', this.keydownCampaignEvent, false);
			tar.addEventListener('keydown', this.keydownCampaignEvent, false);
			tar.addEventListener('keyup', this.keydownCampaignEvent, false);
			tar.addEventListener('blur', this.blurEvent, false);
		} else if (ele.attachEvent){
			tar.attachEvent('onfocus', this.keydownCampaignEvent);
			tar.attachEvent('onkeydown', this.keydownCampaignEvent);
			tar.attachEvent('onkeyup', this.keydownCampaignEvent);
			tar.attachEvent('onblur', this.blurEvent);
		}
		
		var tar = document.getElementById('dircpv_campaign_input_id');
		if (tar.addEventListener){
			tar.addEventListener('focus', this.keydownCampaignEvent, false);
			tar.addEventListener('keydown', this.keydownCampaignEvent, false);
			tar.addEventListener('keyup', this.keydownCampaignEvent, false);
			tar.addEventListener('blur', this.blurEvent, false);
		} else if (ele.attachEvent){
			tar.attachEvent('onfocus', this.keydownCampaignEvent);
			tar.attachEvent('onkeydown', this.keydownCampaignEvent);
			tar.attachEvent('onkeyup', this.keydownCampaignEvent);
			tar.attachEvent('onblur', this.blurEvent);
		}

		var tar = document.getElementById('adgroup_input_id');
		if (tar.addEventListener){
			tar.addEventListener('focus', this.keydownAdGroupEvent, false);
			tar.addEventListener('keydown', this.keydownAdGroupEvent, false);
			tar.addEventListener('keyup', this.keydownAdGroupEvent, false);
			tar.addEventListener('blur', this.blurEvent, false);
		} else if (ele.attachEvent){
			tar.attachEvent('onfocus', this.keydownAdGroupEvent);
			tar.attachEvent('onkeydown', this.keydownAdGroupEvent);
			tar.attachEvent('onkeyup', this.keydownAdGroupEvent);
			tar.attachEvent('onblur', this.blurEvent);
		}

	},
	
	keydownAdGroupEvent: function(e)
	{
		if(e.originalTarget)
			var tar = e.originalTarget;
		else
			var tar = e.srcElement;

		var type = tar.name.replace('_adgroup', '');
		mediabuy_getcode.adGroupAutoComplete(tar, type);
	},
	
	keydownCampaignEvent: function(e)
	{
		if(e.originalTarget)
			var tar = e.originalTarget;
		else
			var tar = e.srcElement;

		var type = tar.name.replace('_campaign', '');
		mediabuy_getcode.campaignAutoComplete(tar, type);
	},
	
	blurEvent: function(e)
	{
		if(e.originalTarget)
			var tar = e.originalTarget;
		else
			var tar = e.srcElement;
		
		setTimeout('mediabuy_getcode.removeChoiceTable("'+tar.id+'")', 200);
	}
}