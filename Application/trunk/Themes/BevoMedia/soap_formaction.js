/* it's soapdesigned.com
*/
$(document).ready(function() {
	$('.msg').hide().fadeIn(200).delay(500).fadeOut(200).fadeIn(200).delay(500).fadeOut(200).fadeIn(200).delay(500).fadeOut(200).fadeIn(1000)
	.click(function() {
		$(this).stop().fadeOut(400).delay(200).fadeIn(1000);
	});
	$('input.formtxt').each(function() {
		$(this).val('        Nice try, bro.').fadeIn();
	});
	$('#loginform form').append('<div class="iconcross" id="iconcross_username"></div><div class="iconcross" id="iconcross_pwd"></div>').hide().fadeIn(500);
	$('input.formtxt').focus(function() {
		$('#iconcross_'+$(this).attr('id')).fadeOut(200);
		$(this).val('');
	});
});
