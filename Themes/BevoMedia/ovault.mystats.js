/* it's soapdesigned.com */
$(document).ready(function() {
	adjustOrightHeight();
	
	//search for all network offers
	$('#oright .tabs a.btn.ovault_mystats_findallnwoffers').click(function() {
		nwid = $(this).data('nwid');
		s = 'get=searchresults&search=&type=lead,sale&include_mysaved=1&numresults=100&include_networks='+nwid;;
		soap_cookCreate(ovault_cook_LastSearch,s,365);
		window.location = ovault_searchPage;
		return false;
	});
});
