(function($) { 
$.fn.compare = function(t) {
    if (this.length != t.length) { return false; }
    var a = this.sort(),
        b = t.sort();
    for (var i = 0; t[i]; i++) {
        if (a[i] !== b[i]) { 
                return false;
        }
    }
    return true;
};
 $.fn.liveTable = function (updateUrl, opts)
{
   target = this;
   headRow = '';
   numCols= 0
   settings = jQuery.extend({
      id: 'id',
      order: 'id',
      orderDir: 'desc',
      messages: {loading: 'Loading...', none: 'No results'},
      colClass: {
        head: {first: 'first', last: 'last', all: false, row: false},
        data: {first: 'first', last: 'last', all: false, row: false},
      },
      dateCol: false,
      startDate: false,
      endDate: false,
      search: false,
      filters: false,
      prependOnTable: true,
      controls: [],
      page: 1,
      perPage: 70,
      numResults: 0,
      liveUpdateInterval: false,
      cols: {},
      defineCols: {},
      nosortCols: {},
      last_params: {},
      footerRow: false,
      afterGetData: function(json) {},
    }, opts);
   this.settings = settings
   if(settings.liveUpdateInterval)
     liveUpdate = setInterval(getData, settings.liveUpdateInterval)
   searchId = 0;
   function getData() {
    s = $.isFunction(settings.search) ? settings.search() : false;
    sd = $.isFunction(settings.startDate) ? settings.startDate() : false;
    ed = $.isFunction(settings.endDate) ? settings.endDate() : false;
    f = $.isFunction(settings.filters) ? settings.filters() : false;
    searchId = Math.random();
     search_params = {
          o: settings.order,
          o_dir: settings.orderDir,
          search: s,
          startDate: sd,
          endDate: ed,
          filter: $.toJSON(f)
         };
     temp_params = {
          passback: searchId,
          start: settings.perPage * (settings.page-1),
          end: settings.perPage * settings.page,
     };
     params = $.extend(search_params, temp_params);
     $.each(params, function(k, v) {
         if(v == false)
          delete params[k];
         });

     jQuery.getJSON(updateUrl, params, function(json) {
         if(json['passback'] == searchId)
            updateData(json);
      });
  }
   this.getData = getData;
   
   function getDataReset()
   {
     settings.page = 1;
     getData();
   }
   this.getDataReset = getDataReset;
   function updateData(json)
   {
     header = buildHeader();
     target.html(header);
     settings.numResults = json['count'];
     settings.pages = json['count'] / settings.perPage;
     datas = $('<tbody>');
     $.each(json['results'], function(i, obj) {
         id = obj[settings.id];
         
         if (settings.prependOnTable)
         {
        	 datas.prepend(buildRow(obj));
         } else
    	 {
        	 datas.append(buildRow(obj)); 
    	 }
         
     });
     target.append(datas);
     settings.afterGetData(json, this);
   }
   function buildRow(obj)
   {
     row = $('<tr>').addClass('dataRow');
     row.attr('id', obj[settings.id]);
     numCols = 0;
     $.each(settings['cols'], function(k,c)
     {
        numCols++;
        if(obj[k] == null)
          obj[k] = "";
        if($.isFunction(settings.defineCols[k]))
          v = settings.defineCols[k](obj);
        else
          v = obj[k];
        if(!v)
          v = '';
        col = $('<td>').html(v);
        row.append(col.addClass(k));
     });
     return row;
   }
   function buildTable()
   {
     target.append(buildHeader());
     $('tr.table_header td').live('click', function() {
          k = $(this).attr('id');
          if($.inArray(k, settings.nosortCols) != -1)
            return;
          if(settings.order == k && settings.orderDir == 'desc')
          {
              settings.orderDir = 'asc';
          }
          else
          {
            settings.order = k;
            settings.orderDir = 'desc';
          }
          target.getDataReset();
      });
     if(settings.footerRow)
     {
         target.append(settings.footerRow);
     }
   }
   function buildHeader() {
     headRow = $('<tr>').addClass('table_header').addClass(settings.colClass.head.row);
     i = 0;
      $.each(settings['cols'], function(k,c)
      {
        h = $.inArray(k, settings.nosortCols) == -1 ? $('<a>').html(c) : c;
        col = $('<td>').html(h).addClass(k);
        if(i == 0)
          col.addClass(settings.colClass.head.first);
        col.attr('id', k);
        headRow.append(col);
        i++;
      });
      col.addClass(settings.colClass.head.last);
      return headRow;
   }
   $.each(['controls'], function(i,v) {
       if($.isFunction(settings[v].bind))
       {
         settings[v].bind('keypress', getDataReset);
         settings[v].bind('change', getDataReset);
       }
       });
   $('.updateOnClick').live('click', function() {
          getData();
       })
   buildTable();
   getDataReset();
   return this;
};
})(jQuery);
