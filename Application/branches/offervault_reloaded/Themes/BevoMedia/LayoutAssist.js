var LayoutAssist = {
	_shadowbox_close_timer_div: 'LayoutAssist_Shadowbox_Close_Timer',
	_assistSaved: new Array,
	
	_$: function(eleName)
	{
		if(document.getElementById(eleName))
		{
			return document.getElementById(eleName);
		}
	},
	
	shadowboxCloseTimerUpdate: function(seconds)
	{
		this._$(this._shadowbox_close_timer_div).innerHTML = 'Closing in ' + seconds + '...';
		if(seconds == 0)
		{
			parent.Shadowbox.close();
		}
	},
	
	shadowboxCloseTimer: function(seconds)
	{
		for(var i=seconds; i>=0; i--)
		{
			setTimeout("LayoutAssist.shadowboxCloseTimerUpdate("+i+")", (1000*seconds)-(i*1000));
		}
	},
	
	selfLocationTimer: function(seconds, location)
	{
		setTimeout("LayoutAssist.selfLocation('"+location+"', "+seconds+")", (1000*seconds));
	},
	
	selfLocation: function(location)
	{
		if(location == '[SELF.LOCATION.HREF]')
			self.location.href = self.location.href;
		else
			self.location.href = location;
	},
	
	parentLocationTimer: function(seconds, location)
	{
		setTimeout("LayoutAssist.parentLocation('"+location+"', "+seconds+")", (1000*seconds));
	},
	
	parentLocation: function(location)
	{
		if(location == '[PARENT.LOCATION.HREF]')
			parent.location.href = parent.location.href;
		else
			parent.location.href = location;
	},
	
	toggleCheckboxes: function(elements, checked)
	{
		for(ele in elements)
		{
			if(elements[ele].type == 'checkbox')
			{
				elements[ele].checked = checked;
			}
		}
	},
	
	appendValue: function(ele, val)
	{
		if(ele.value.length > 0)
			ele.value += ',';
		
		ele.value += val;
	},
	
	toggleEnabledAndValue: function(ele, state, val)
	{
		this._$(ele).disabled = !state;
		if(!state)
		{
			this._assistSaved['tEAV:'+ele] = this._$(ele).value;
			this._$(ele).value = val;
		}
		else
		{
			this._$(ele).value = this._assistSaved['tEAV:'+ele];
		}
		//alert(ele + "\n" + state + "\n" + val);
	}
	
}