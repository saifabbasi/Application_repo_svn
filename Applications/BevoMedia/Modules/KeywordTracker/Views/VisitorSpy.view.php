<?php
$isTrackerPage = true;
$userId = $this->User->id;
?>

<style type='text/css'>
.btable {width: 100%;}
.StackRight {display: none !important;}
.StackLeft {width: 100%;}
</style>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','overview','spy'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<script type="text/javascript" src="/Themes/BevoMedia/jquery.liveTables.js"></script>
<script type="text/javascript" src="/Themes/BevoMedia/jquery.json-2.2.min.js"></script>
<script type="text/javascript">
	Object.size = function(obj) {
	      var size = 0, key;
		      for (key in obj) {
				        if (obj.hasOwnProperty(key)) size++;
						    }
		      return size;
	};
$(function() {
  zeroTime = "0000-00-00 00:00:00";
  opts = {
	liveUpdateInterval: 30000,
	  defineCols: {
		'hhl border': function(r) { return '&nbsp;'; },
		'hhr tail': function(r) { return '&nbsp;'; },
		'extra': function(r) {
		  a = $('<a>').attr('id', r.subId).click(function(link) {
			Shadowbox.open({
				content:    "VisitorInfo.html?subId="+r.subId,
				player:     "iframe",
				height:     120,
				width:      800
			});
			return false;
		  });
		  if(r.optional)
			a.html('Optional Data: ' +r.optional);
		  else if(r.rawKeyword)
			a.html('Search Term: ' +r.rawKeyword);
		  else a.html('More info');
		  return a;
		},
		'clickTime': function(r)
		  {
			d = Date.parse(r.at);
			if(d)
			  return d.toString('yyyy-MM-dd h:mmtt');
			return ''
		  },
		  'creative': function(r) {
			if ( (r.providerType==null) || (r.providerType==1) || (r.providerType==2) || (r.providerType==3) )
			{
				return r.creativeTitle;
			}
			if(r.optional)
			  return r.optional;
			return r.creativeTitle;
		  },
		  'converted': function(r) {
			if(r.conv && r.conv != '0')
			{
			  return '<img src="/Themes/BevoMedia/img/checkmark.png">';
			}
			return '';
		  },
 	},
	cols: {
			'hhl': '',
			'clickTime': 'At',
		    'ipAddress': 'IP',
			'subId': 'Click SubID',
			'creative': 'Creative',
			'extra': 'More info',
			'converted': 'Converted?',
			'hhr': ''},
	nosortCols: ['hhl', 'hhr', 'ipAddress', 'creative', 'converted'],
	order: 'clickTime',
	orderDir: '',
	prependOnTable: false,
	startDate: function() { var date = $('#datepickerVS').val(); var dateArray = date.split(' - '); return dateArray[0]; },
	endDate: function() { var date = $('#datepickerVS').val(); var dateArray = date.split(' - '); return (dateArray.length==1)?dateArray[0]:dateArray[1]; },
	search: function() { return $('#search').val() },
    controls: $('.ltctrl'),
	filters: function() {
	  f = {};
	  return f;
	},
	afterGetData: function(json, obj) {
	  nodata = $('<tr>');
	  nodata.append($('<td>').addClass('hhl'));
	  nodata.append($('<td>').attr('colspan', obj.numCols-2)).html('No visitors in your selected timeframe');
	  nodata.append($('<td>').addClass('hhr'));
	  if(obj.settings.page == 0)
		obj.datas.append(nodata);
	  footer = $('<tr>').addClass('table_footer');
	  footer.append($('<td>').addClass('hhl'));
	  footer.append($('<td>').attr('colspan', 8-2));
	  footer.append($('<td>').addClass('hhr'));
	  obj.datas.append(footer);

	  $('#pages').empty();
	  var i = obj.settings.page-5;
	  
	  if(i < 1) i = 1;
	  skipped = false;
	  for(; i <= Math.ceil(obj.settings.pages); i++)
	  {
		if((obj.settings.page + 5) <= i && (Math.ceil(obj.settings.pages) - 5) >= i)
		{
		  if(!skipped)
		  {
			skipped = true;
	     	$('#pages').append(' ... ');
		  }
		  continue;
		}
		link = $("<a>").html(i).addClass('updateOnClick').attr('id', i).css('cursor', 'pointer').click(function() {
		  obj.settings.page=$(this).attr('id');
		});
		$('#pages').append(link).append(' ');
	  }
	  
	},
  }

  table = $('table#live').liveTable('/BevoMedia/KeywordTracker/VisitorJSON.html', opts);
});
</script>
<style>
 .time, .host, .show{
  text-align: right;
}
</style>


<div>
	<span style="width: 5%; display: inline-block;">Page:&nbsp;</span>
	<span style="width: auto; display: inline-block; " id="pages"></span>
	<span style="width: 250px; float: right; text-align: right;">
		Date(s): <input class="formtxt" type="text" name="DateRange" id="datepickerVS" value="<?php echo date('m/d/Y');?>" readonly="true" />
	</span>
</div>

<br /><br />

<table id="live" cellspacing="0" class="btable" width="600">
</table>
<br/>
<center>
*Campaigns uploaded NOT using the Bevo Editor will show:<br/>
<b>
	"Temporary Ad Variation" for the first day the campaign is live. The appropriate ad variation will fill in after the nightly cron.<br/>
</b>
<br/>
</center>


<script type="text/javascript">
	$(document).ready(function () {
		$('#datepickerVS').daterangepicker( {
												presetRanges: [ {text: 'Today', dateStart: modToday, dateEnd: modToday },
		                                   	   	{text: 'Yesterday', dateStart: modYesterday, dateEnd: modYesterday },
		                        				{text: 'Last 7 days', dateStart: modToday + '-7days', dateEnd: modToday }
	                        				  ],
	                        				  presets: 
		                        			  {
	                        						specificDate: 'Specific Date'
											  }
											}
	  						  );
	});
</script>
