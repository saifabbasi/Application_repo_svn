/* it's soapdesigned.com */
$(document).ready(function() {		
	
	adjustSavelistOrightHeight();
	
	//switch list view
	$('#oleft tbody tr.oleftrow:not(.active)').live('click', function() {
		var listdata = {
			listid: $(this).data('listid'),
			name: $(this).data('listname'),
			listcount: $(this).data('listcount'),
			num_offers: $(this).data('num_offers'),
			created: $(this).data('created')
		};
			
		$.ajax({
			type: 'GET',
			url: ovault_ajaxGet+'?get=savelistoffers&list='+listdata.listid,
			success: function(r) {
				r = $.parseJSON(r);
				
				if(r.error) {
					ajaxMessage(r.error);
					
				} else {
					makeSavelistDefault(listdata.listid, listdata.name);
					rebuildSavelistOrowrightContent(r.resultarr, listdata);				
				}
			},
			error: function(r) {
				ajaxMessage(r);
			}
		});//ajax
		
		return false;
		
	});//switch list view
});
