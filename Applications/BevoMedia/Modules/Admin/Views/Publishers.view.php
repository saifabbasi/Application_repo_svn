<script type="text/javascript" src="/Themes/BevoMedia/jquery.liveTables.js"></script>
<script>
$(function() {
  zeroTime = "0000-00-00 00:00:00";
  opts = {
	liveUpdateInterval: false && 5000,
	  defineCols: {
	  'user' : function(r) { return '<a href=/BevoMedia/Admin/ViewPublisher.html?id='+r['id']+'>'+r['email']+'</a>'},
	  'id' : function(r) { return '<a href=/BevoMedia/Admin/ViewPublisher.html?id='+r['id']+'>'+r['id']+'</a>'},
	  'created': function(r)
		{
		  d = Date.parse(r.created);
		  if(d)
			return d.toString('yyyy-MM-dd h:mmtt');
		  return ''
		},
	  'lastLogin': function(r)
		{
		  d = Date.parse(r.lastLogin);
		  if(d)
			return d.toString('yyyy-MM-dd h:mmtt');
		  return 'Never'
		},
	  'enabled': function(r)
		  {
			if(r['enabled'] != 0)
			  return "<a href='http://beta.bevomedia.com/BevoMedia/Admin/DisableUser.html?id="+r['id']+"'>Disable</a>";
			else
			  return "<a href='http://beta.bevomedia.com/BevoMedia/Admin/EnableUser.html?id="+r['id']+"'>Enable</a>";
		  }, 
 	},
	  cols: {
			'id': 'ID',
		    'created': 'Join date',
			'user': 'User',
			'name': 'Name',
			'lastLogin': 'Last logged in',
			'enabled': 'Enabled'},
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
  table = $('table').liveTable('/BevoMedia/Admin/PublisherJSON.html', opts);
});
</script>
<style>
 .time, .host, .show{
  text-align: right;
}
</style>
	
	<br />
<div id="controls">
<div style="float: left">Search: <input type="text"  name="search" id="search" size=12 class="ltctrl" /></div>
<div style="clear: both"></div>
<div style="width: 100%; text-align: center;" id="pages"></div>
</div>
	<table width="100%" id="ppcqueueprogresstable">
	</table>