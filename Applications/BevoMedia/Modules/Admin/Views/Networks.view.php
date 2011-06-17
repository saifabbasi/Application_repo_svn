<script type="text/javascript" src="/Themes/BevoMedia/jquery.liveTables.js"></script>
<script>
$(function() {
  zeroTime = "0000-00-00 00:00:00";
  opts = {
	liveUpdateInterval: false && 5000,
	  defineCols: {
	  'Username' : function(r) { return '<a href=/BevoMedia/Admin/BrokerNetworkView.html?ID='+r['ID']+'>'+r['Username']+'</a>'},
	  'ID' : function(r) { return '<a href=/BevoMedia/Admin/BrokerNetworkView.html?ID='+r['ID']+'>'+r['ID']+'</a>'},
	  'Created': function(r)
		{
		  d = Date.parse(r.Created);
		  if(d)
			return d.toString('yyyy-MM-dd h:mmtt');
		  return ''
		},
	  'LastLogin': function(r)
		{
		  d = Date.parse(r.LastLogin);
		  if(d)
			return d.toString('yyyy-MM-dd h:mmtt');
		  return 'Never'
		},
	  'Enabled': function(r)
		  {
			if(r.Enabled != 0)
			  return "<a href='/BevoMedia/Admin/BrokerNetworks.html?DisableID="+r.ID+"'>Disable</a>";
			else
			  return "<a href='/BevoMedia/Admin/BrokerNetworks.html?EnableID="+r.ID+"'>Enable</a>";
		  }, 
 	},
	  cols: {
			'ID': 'ID',
		    'Created': 'Join date',
			'Username': 'User',
			'Name': 'Name',
			'LastLogin': 'Last logged in',
			'Enabled': 'Enabled'},
	nosortCols: ['name', 'status'],
	order: 'created',
	search: function() { return $('#search').val() },
    controls: $('.ltctrl'),
	filters: function() {
	  f = {};
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
  table = $('table').liveTable('/BevoMedia/Admin/NetworksJSON.html', opts);
});
</script>
<style>
 .time, .host, .show{
  text-align: right;
}
</style>
	
<?=$this->TopMenu?>
	
	<br />
<div id="controls">
<div style="float: left">Search: <input type="text"  name="search" id="search" size=12 class="ltctrl" /></div>
<div style="clear: both"></div>
<div style="width: 100%; text-align: center;" id="pages"></div>
</div>
	<table width="100%" id="ppcqueueprogresstable">
	</table>