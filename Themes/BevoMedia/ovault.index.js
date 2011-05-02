/* it's soapdesigned.com */
$(document).ready(function() {
	//paginate
	$('#opagi .numbers a.j_num:not(.active), #opagi .numbers a.j_prevnext').live('click', function() {
		s = ovault_cache.current_searchstring + '&newpage='+$(this).data('page'); //newpage overrides page
		
		//alert('paginate s: '+s);
		doSearch(s);
		
		return false;
	});

	/*savelists*/
	//select list to save to (in offer rows)
	$('.orow a.ovault_add2list_select').live('click', function() {
					
		undoSave2listSelect(); //close others if any are open
		
		if(!$(this).hasClass('active'))
			insertSave2listSelect($(this));
		
		return false;
	});
	
	//save to selected list
	$('#ovault_olay_savelists_select tbody tr').live('click', function() {
		ovault_orow_ignoreClick = true;
		if(ovault_currentAdd2listSelectOid)
			doSave2List($(this).data('listid'), ovault_currentAdd2listSelectOid, true);
		else {
			ajaxMessage('It seems that the offer you want to add is invalid. Please try again!',1);
			ovault_orow_ignoreClick = false;
		}
	});
	
	//save to default list
	$('.orow a.ovault_add2list').live('click', function() {
		var oid = parseInt($(this).data('oid'));		
		doSave2List(ovault_currentSavelist, oid);				
	});
	
	//save all of page
	$('#odial a.ovault_saveallpage').live('click', function() {
		/*LATERRRRRRRRRRRRRR*/
	});
	
	//make default
	$('#ovault_olay_savelists tbody tr').live('click', function() {
		var	listid = $(this).data('listid'),
			listname = $(this).data('listname');
		makeSavelistDefault(listid, listname);	
	});	
});
