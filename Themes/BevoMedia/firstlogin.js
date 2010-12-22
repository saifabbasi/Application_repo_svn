var firstlogin = {
	init: function()
	{
		$(document).ready(function(){
			$('body').append("<div id='firstlogin'>x</div>");
			$('#firstlogin').load('/BevoMedia/User/LightboxFirst.html');
		});
	},
	loadstep: function(step)
	{
		$(document).ready(function(){
			$('body').append("<div id='firstlogin'>x</div>");
			$('#firstlogin').load('/BevoMedia/User/LightboxTemplate_Final.html?STEP='+step);
		});
	},
	
	close: function()
	{
		$('#firstlogin').hide();
	}
}