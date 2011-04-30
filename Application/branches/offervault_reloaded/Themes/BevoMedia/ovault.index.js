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
	
	//create new list
	$('#ovault_createnewlistform').live('submit',function() {
			
		if(parseInt(ovault_existSavelistNum) < parseInt(ovault_maxSavelists)) {
			var field = $('#ovault_newlistname');
			
			if(field.val() == field.prev().html() || field.val() == '')
				ajaxMessage('Please enter a name for your list!');
			
			else	doCreateNewList(field.val());
		
		} else {			
			var msg = 'Sorry, but you can\'t have more than '+ovault_maxSavelists+' Offer Lists at the same time. You can delete old lists to make room for new ones.';
			ajaxMessage(msg, true);
		}
		
		return false;
	});
	
	//make default
	$('#ovault_olay_savelists tbody tr').live('click', function() {
		var	listid = $(this).data('listid'),
			listname = $(this).data('listname');
		makeSavelistDefault(listid, listname);	
	});
	
	//delete list
	$('#ovault_olay_savelists tbody tr td.delete, #ovault_olay_savelists tbody tr td.delete a').live('click', function() {
		var	parent = $(this).parents('tr'),
			listid = parent.data('listid'),
			listname = parent.data('listname');
		
		if(confirm("Are you sure you want to delete the\n\n*** "+listname+" ***\n\nOffer List?")) {
			doDeleteList(listid);
		}
		
		return false;
	});
	
	//delete all
	$('#olay_savedlists a.ovault_smallyell_deleteall').live('click', function() {
		if(confirm("Are you sure you want to delete ALL your Offer Lists now?")) {
			$.ajax({
				type: 'GET',
				url: ovault_ajaxPut+'?put=deletealllists',
				success: function(r) {					
					r = $.parseJSON(r);					
					if(r.error)
						ajaxMessage(r.error);					
					else {
						//remove html markup
						$('#olay_savedlists .j_olisttable, #olay_savedlists .j_oliststats').fadeOut(1000).remove();						
						makeSavelistDefault('new', 'New List');						
						ovault_existSavelistNum = 0;					
						ajaxMessage(r.message);
					}
				},
				error: function(r) {
					ajaxMessage('An error occured. Please try again!');
				}
			});
		}//endif confirm
		return false;
	});//delete all lists	
	
});
