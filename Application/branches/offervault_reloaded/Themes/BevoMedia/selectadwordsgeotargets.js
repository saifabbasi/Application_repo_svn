var selectadwordsgeotargets = {
	_parent_createppc: false,
	_parent_shadowbox: false,
	
	
	_$: function(l)
	{
		return document.getElementById(l);
	},
	
	init: function()
	{
		this._parent_createppc = parent.createppc;
		this._parent_shadowbox = parent.Shadowbox;
		
		this._$('selectadwordsgeotarget').onsubmit = this.onSubmit_submitForm;
	},
	
	onSubmit_submitForm: function(e)
	{
		if(!e)
		{
			var e = window.event;
		}
		selectadwordsgeotargets.submitForm(e);
	},
	
	submitForm: function(e)
	{
		var output = '';
		var curEle = false;
		var none = true;
		this._parent_createppc.resetAdwordsGeoTargetCountry();
		if(e.target == undefined)
			e.target = e.srcElement;
		var x = 0;
		var srcForm = document.forms[e.target.name];
		
		for(ele in srcForm)
		{
			if(srcForm[ele] == undefined)
				continue;
			
			if(ele == 'Country[]')
				curEle = srcForm[ele][x];
			else
				if(srcForm[ele].name != undefined && srcForm[ele].name == 'Country[]')
					curEle = srcForm[ele];
				else
					continue;
			
			x++;
			//alert(x);

			if(curEle.className == undefined)
				continue;
			if(curEle.checked == undefined)
				continue;
			if(curEle.checked == false)
				continue;
			
			//alert(curEle.value + " " + curEle.className);
			this._parent_createppc.addAdwordsGeoTargetCountry(curEle.value, curEle.className);
			none = false;
		}
		if(none)
			this._parent_createppc.setAllAdwordsGeoTargetCountry();
		
		this._parent_shadowbox.close();
		return false;
	}
}