<script type="text/javascript" src="/Themes/BevoMedia/jquery.liveTables.js"></script>
<script>
$(function() {
  zeroTime = "0000-00-00 00:00:00";
  opts = {
	liveUpdateInterval: false && 5000,
	  defineCols: {
	  'user' : function(r) { return '<a href=/BevoMedia/Admin/ViewPublisher.html?id='+r['user__id']+'>'+r['user']+'</a>'},
		'started' : function(r) { if(r['started'] == zeroTime) return 'Not started'; else {
		  d = Date.parse(r['started']);
		  if(d)
			return d.toString('yyyy-MM-dd h:mmtt');
		  return '';
		}},
	  'time' : function(r) { if(r['completed'] == zeroTime) return ''; else 
	  {
		t=r['time'];
		s=t+"s"
		if(t>60)
		  s=(parseInt(t/60))+"m"
		if(t>60*60)
		  s=(parseInt(t/3600))+"h"
		return s;
	  }},
	  'host': function(r)
		{
			if(r['host'] == '10.243.34.181') return 'beta';
			if(r['host'] == '10.214.45.157') return 'beta-2';
			if(r['host'] == '10.240.63.209') return 'beta-4';
			if(r['host'] == '10.243.74.22') return  'beta-3';
			return r['host'];
		},
	  'created': function(r)
		{
		  d = Date.parse(r.created);
		  if(d)
			return d.toString('yyyy-MM-dd h:mmtt');
		  return ''
		},
		  'jobId': function(r)
		{
		  return '<a href="/BevoMedia/Admin/QueueItem.html?id='+r['id']+'" target=_blank>'+r['jobId']+'</a>';
		},
		  'show': function(r) { 
			out = ""
			messages = {success: 'green', warning: 'yellow', error: 'red', message: 'black', queued: 'grey'};
			$.each(messages, function(m,c){
			  w = m;
			  if(w[0])
				  w[0] = w[0].toUpperCase();
			  if(r[m] > 0)
			  {
				if(out != "")
				  out += ", "
				out += ""+$('<span>').css('color', c).html(r[m] + " " + w).html()
			  }
			});
			if(out == "")
			  out = "None";
			return out;
		  },
 	},
	  cols: {
		    'created': 'Added to Queue',
			'jobId': 'Job ID',
			'type': 'Type',
			'user': 'User',
			'started': 'Started',
			'time': 'Elapsed Time',
			'host': 'Processed by machine',
			'show': 'More information'},
	nosortCols: ['host', 'jobId', 'messages'],
	order: 'created',
	search: function() { return $('#search').val() },
	startDate: function() { return $('#sdate').val() },
	endDate: function() { return $('#edate').val() } ,
    controls: $('.ltctrl'),
	filters: function() {
	  f = {};
	  gettype = $('input:radio[name=type]:checked').val();
	  if(gettype == 'ppcnet')
	  {
		a = $('#ppcnetfilter').val();
		nets = ['Adwords Update Account', 'MSN Account Update', 'Yahoo Account Update'];
		if(a == 'adwords')
		  f.type = nets[0];
		if(a == 'msn')
		  f.type = nets[1];
		if(a == 'yahoo')
		  f.type = nets[2];
		if(a == 'all')
		  f.type = nets;
	  }
	  if(gettype == 'net')
		f.type__like = '% Stats';
	  if(gettype == 'ppced')
		f.type = 'PPC Editor';
	  return f;
	},
	afterGetData: function(json, obj) {
	  $('tr.dataRow td.show a').live('click', function(){
		$('tr.dataRow#'+$(this).attr('id'));
	  });
	  $('#pages').empty();
	  var i = obj.settings.page - 5;
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
		link = $("<a>").html(i).addClass('updateOnClick').attr('id', i).click(function() {
		  obj.settings.page=$(this).attr('id');
		});
		$('#pages').append(link).append(' ');
	  }
	}
  }
  today = Date.today().toString('MM/dd/yyyy');
  table = $('table').liveTable('/BevoMedia/Admin/QueueJSON.html', opts);
  $('#sdate, #edate').val(today).datepicker();
  $('input:radio[name=type]').change(function() {
	gettype = $('input:radio[name=type]:checked').val();
	$('#ppcnetfilter').hide();
	$('#affnetfilter').hide();
	if(gettype == 'ppcnet')
	  $('#ppcnetfilter').show();
	else
	  $('#ppcnetfilter').val('all');
	if(gettype == 'net')
	  $('#affnetfilter').show();
	else
	  $('#affnetfilter').val('all');
  });
});
</script>
<style>
 .time, .host, .show{
  text-align: right;
}
</style>
	<div align="center">
<?
$Favicons = array('ADWORDS'=>'/Themes/BevoMedia/img/googlefavicon.png', 'YAHOO'=>'/Themes/BevoMedia/img/yahoofavicon.png', 'MSN'=>'/Themes/BevoMedia/img/msnfavicon.png');
?>
	</div>
	
	<br />
<div id="controls">
<div style="float: left">Search: <input type="text"  name="search" id="search" size=12 class="ltctrl" /></div>
<div style="float: right">Dates: <input type="text" name="sdate" id="sdate" size=12 class="ltctrl" /> to <input name="edate" type="text" id="edate" size=12 class="ltctrl" /></div>
<div style="clear: both"></div>
<input type="radio" name="type" value="all" checked="true" class="ltctrl" /> All
<input type="radio" name="type" value="ppcnet" class="ltctrl" />PPC Networks
<select id="ppcnetfilter" style="display: none" class="ltctrl">
  <option value="all" selected=true>
  <option value="adwords">Google Adwords</option>
  <option value="yahoo">Yahoo</option>
  <option value="msn">MSN Ad Center</option>
</select>
<input type="radio" name="type" value="net" class="ltctrl" />Affiliate Networks
<input type="radio" name="type" value="ppced" class="ltctrl" />PPC Editor

<div style="clear: both"></div>
<div style="width: 100%; text-align: center;" id="pages"></div>
</div>
	<table width="100%" id="ppcqueueprogresstable">
	</table>