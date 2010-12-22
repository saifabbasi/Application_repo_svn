jQuery(document).ready(function($){
	//$('#tabs div').hide(); // Hide all divs
	
	
	if (strAction == 'NEXT') {
		$('#tabs #OutputTab').addClass('active'); // Set the class for active state
		$('#tabs #Output').show();
	}
	else {
		$('#tabs ul li:first').addClass('active'); // Set the class for active state
		$('#tabs div:first').show(); // Show the first div
	}
	
	$('#tabs ul li a').click(function(){ // When link is clicked
		$('#tabs ul li').removeClass('active'); // Remove active class from links
		$(this).parent().addClass('active'); //Set parent of clicked link class to active
		var currentTab = $(this).attr('href'); // Set currentTab to value of href attribute
		$('#tabs div').hide(); // Hide all divs
		$(currentTab).show(); // Show div with id equal to variable currentTab
		return false;
	});
});

function PickTab(strInTab) {
	var $ = jQuery;
	$('#tabs ul li').removeClass('active'); // Remove active class from links
	$('#tabs ul li#' + strInTab + 'Tab').addClass('active'); // Set the class for active state
	$('#tabs div').hide(); // Hide all divs
	$('#tabs #' + strInTab).show();
}