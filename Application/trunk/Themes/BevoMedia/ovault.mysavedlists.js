/* it's soapdesigned.com */
$(document).ready(function() {		
	
	adjustOrightHeight();
	
	//switch list view
	$('#oleft tbody tr.oleftrow:not(.active)').live('click', function() {
		var listdata = {
			listid: $(this).data('listid'),
			name: $(this).data('listname'),
			listcount: $(this).data('listcount'),
			num_offers: $(this).data('num_offers'),
			created: $(this).data('created')
		};
		
		doSavelistFetchOffers(listdata);
		
		return false;
		
	});//switch list view
	
	//delete offer from list
	$('#j_otable tr.orow.j_oright td.td_delete, #j_otable tr.orow.j_oright td.td_delete a').live('click', function() {
		ovault_orow_ignoreClick = true;		
		var oid = $(this).parents('tr.orow').data('oid');			
		doSavelistDeleteOffer(oid, ovault_currentSavelist);		
		return false;
			
	});//delete offer from list
	
	//rename list
	$('#oright .top2 form.ovault_mysaved_renamelistform').live('submit', function() {
		var 	thisform = $(this),
			field = $('input.ovault_renamelistname', this),
			oldname = $(this).data('listname'),
			listid = $(this).data('listid'),
			val = field.val().replace(/[^A-z0-9-_.,:\s]/g,'');
			
		if(val == '' || val.length < 3 || val.length > 55) {
			ajaxMessage('Please enter a valid name for your list! List names should be between 3 and 55 characters long and may not contain any exotic characters.',1);
		
		} else {
			if(val == oldname) {//no change
				$(this).fadeOut(300, function() {
					$(this).removeClass('active');
				});
					
			} else {
				$.ajax({
					type: 'GET',
					url: ovault_ajaxPut+'?put=renamelist&listid='+listid+'&newlistname='+val,
					success: function(r) {				
						r = $.parseJSON(r);
						
						if(r.error)
							ajaxMessage(r.error,1);
						
						else {
							$('#oright .conttop .top2 h2').html(r.newlistname);
							
							thisform.fadeOut(300, function() {//hide form
								thisform.removeClass('active');
							});
							
							//update other name fields
							var listcount = $('#oleft tbody tr.oleftrow.j_list-'+listid+' td.td_oleft h3 span.no').html();
							$('#oleft tbody tr.oleftrow.j_list-'+listid+' td.td_oleft h3').html('<span class="no">'+listcount+'.</span>'+soap_truncTxt(r.newlistname,27));
							$('#oright .conttop .top2 form.ovault_mysaved_renamelistform').data('listname',r.newlistname);
							$('#oright .conttop .top3 a.btn.ovault_transgray_delete').data('listname',r.newlistname);
							
							ajaxMessage(r.message);
						}
					},
					error: function(r) {
						ajaxMessage('An error occured. Please try again!',1);
					}
				});//ajax
			}//endif same name
		}//name accepted
		
		return false;
		
	});//rename list
});
