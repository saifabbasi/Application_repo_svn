/* it's soapdesigned.com */
$(document).ready(function() {
	//ini
	updateOrowSelelistBtn();
		
	//paginate
	$('#opagi .numbers a.j_num:not(.active), #opagi .numbers a.j_prevnext').live('click', function() {
		s = ovault_cache.current_searchstring + '&newpage='+$(this).data('page'); //newpage overrides page
		
		if (ovault_cache.sort_by!='') {
			s += '&sort_by='+ovault_cache.sort_by+'&sort_by_direction='+ovault_cache.sort_by_direction;
		}
		
		ovault_cache.current_page = $(this).data('page');
		
		doSearch(s);		
		return false;
	});

	/*savelists*/
	//save to default list
	$('.orow a.ovault_add2list').live('click', function() {
		var oid = parseInt($(this).data('oid'));		
		doSave2List(ovault_currentSavelist, oid);
		return false;
	});
	
	//select list to save to (in offer rows)
	$('.orow a.ovault_add2list_select').live('click', function() {
					
		undoSave2listSelect(); //close others if any are open
		
		if(!$(this).hasClass('active'))
			insertSave2listSelect($(this));
		
		return false;
	});
	
	//save to sele list in offer row olay
	$('#ovault_olay_savelists_select tbody tr').live('click', function() {
		ovault_orow_ignoreClick = true;
		if(ovault_currentAdd2listSelectOid)
			doSave2List($(this).data('listid'), ovault_currentAdd2listSelectOid, true); //3rd param = is_select
		else {
			ajaxMessage('It seems that the offer you want to add is invalid. Please try again!',1);
			ovault_orow_ignoreClick = false;
		}
	});
	
	//save all of page
	$('#odial a.ovault_saveallpage').live('click', function() {
		/*LATERRRRRRRRRRRRRR*/
		return false;
	});
	
	//make default
	$('#ovault_olay_savelists tbody tr').live('click', function() {
		var	listid = $(this).data('listid'),
			listname = $(this).data('listname');
		makeSavelistDefault(listid, listname);	
	});

	//view list on savelistpage
	$('#ovault_olay_savelists tbody tr td.view, #ovault_olay_savelists tbody tr td.view a.btn').live('click', function() {
		var	listid = $(this).data('listid'),
			listname = $(this).data('listname');
		makeSavelistDefault(listid, listname, true); //3rd param suppresses any notifications and redirects to list management page
		return false;
	});
});
