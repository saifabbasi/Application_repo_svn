<?php 
	/*
	require 'soap_functions.php'; //outsource to controller or so later
	
	//hooks used in the markup
	$_db = Zend_Registry::get('Instance/DatabaseObj');
	
	//topdrop status
	$soap_topdrop_status = soap_topdrop_status();
	
	
	*/		
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<title><?= $this->{'Instance/PageTitle'}; ?> </title>
<meta name="keywords" content="<?=$this->{'Instance/PageKeywords'};?>" />
<meta name="description" content="<?=$this->{'Instance/PageDescription'};?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="/JS/Functions.js" type="text/javascript"></script>
<script src="/JS/Ajax.js" type="text/javascript"></script>
<script src="/JS/Lock.js" type="text/javascript"></script>
<script language="JavaScript">
//<![CDATA[
var AC_FL_RunContent = 0;
var DetectFlashVer = 0;
var requiredMajorVersion = 9;
var requiredMinorVersion = 0;
var requiredRevision = 45;

<?php
$TZ = new TimezoneHelper();
$Offset = (intval($TZ->getTimezoneByPHPTimezone($this->User->Timezone)->HourIntOffset)+5)*-1;

if($Offset+date('H') >= 24)
{
	echo 'var userTimezoneOffset = true;';
}else{
	echo 'var userTimezoneOffset = false;';
}
?>
if(userTimezoneOffset == false)
{
	var modToday = 'today';
	var modYesterday = 'yesterday';
}else{
	var modToday = 'tomorrow';
	var modYesterday = 'today';
}
//]]>
</script>

<script language="JavaScript" src="/Themes/BevoMedia/AC_RunActiveContent.js"></script>

<?php 
	if (Zend_Registry::get('Instance/Function')!='VisitorSpy')
	{
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<?php 
	} else
	{
?>
<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>
<?php 
	}
?>
<!--[if IE]><script type="text/javascript" src="/JS/charts/excanvas.compiled.js"></script><![endif]-->
<script type="text/javascript" src="/JS/charts/visualize.jQuery.js"></script>
<link type="text/css" rel="stylesheet" href="/JS/charts/visualize.jQuery.css" />
<link type="text/css" rel="stylesheet" href="/JS/charts/demopage.css" />
<!-- ENDOF New Chart System -->

<link href="/Themes/BevoMedia/style.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/ui.daterangepicker.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/jquery-ui-1.7.1.custom.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/Themes/BevoMedia/shadowbox-source-3.0b/shadowbox.css" />
<link href="/Themes/BevoMedia/soapystyle.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/soapy_newcontent.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/global.css" rel="stylesheet" type="text/css" />


<?php /*?>
<script src="/Themes/BevoMedia/jquery.js" type="text/javascript"></script>

<?php //*/?>
<script src="/Themes/BevoMedia/jquery-ui-1.7.1.custom.min.js" type="text/javascript"></script> 
<script src="/Themes/BevoMedia/jquery.validate.min.js" type="text/javascript"></script>
<script src="/Themes/BevoMedia/daterangepicker.jQuery.js" type="text/javascript"></script>
<script src="/Themes/BevoMedia/FusionCharts.js" type="text/javascript"></script>
<script src="/Themes/BevoMedia/RegisterForm.js" type="text/javascript"></script>
<script src="/Themes/BevoMedia/json2.js" type="text/javascript"></script>
<script language="JavaScript" src="/Themes/BevoMedia/firstlogin.js"></script>
<script type="text/javascript" src="/Themes/BevoMedia/shadowbox-source-3.0b/shadowbox.js"></script>
<script type="text/javascript" src="/Themes/BevoMedia/soapylayout.js"></script>
<!--[if lt IE 8]><link rel="stylesheet" href="/Themes/BevoMedia/ie7.css" type="text/css" media="screen" /><![endif]-->

<?php /*?>
<script src="/Themes/BevoMedia/Nextlines.js" type="text/javascript"></script>
<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=73e6ed73-10b8-4613-8d6b-680d01b68d3c&amp;type=website&amp;buttonText=Share%20It.&amp;linkfg=%23333333"></script>
<?php */?>


<link rel="stylesheet" href="/Themes/BevoMedia/apps-layout/layout.css" type="text/css" />
<link rel="stylesheet" href="/Themes/BevoMedia/apps-layout/global.css" type="text/css" />


</head>
<body><?php /* ################################################################# BODY */ ?>

	<script type="text/javascript">
	//<![CDATA[
		Shadowbox.init({
		    language: 'en',
		    players:  ['html', 'iframe', 'img']
		});
	
	$(document).ready(function() {
		//form input default
		$('form input.defaultvalue').focus(function() {
			if($(this).val() == $(this).attr('data-defaultvalue')) {
				$(this).val('');
			}
		}).blur(function() {
			if($(this).val() == '') {
				$(this).val($(this).attr('data-defaultvalue'));
			}
		});
		
		//expand
		$('.j_expand').live('click', function() {
			var target = $('#'+$(this).attr('data-target'));
				
			if(target.is(':visible')) {
				target.slideUp(200);	
			} else {
				target.slideDown(200);
			}
			return false;
		});
		
		//header offer search
		$('#topDropSearch').submit(function() {		
			window.location = '/offer/offerhub/search/'+$('#topsearchInput').val();
			return false;			
		});		
	});
		
	/*	DO WE NEED THIS IN APPS?
	
	
		$(document).ready(function() {
			//sapi tab switch
			$('a.sapi-tab').click(function() {
				if(!$(this).hasClass('active')) {
					var sapiTarget = $(this).attr('href');
					$('.sapi-box').each(function() {
						$(this).slideUp(300).delay(300);
					});
					
					//open target tab
					$(sapiTarget).hide().slideDown(300);
					
					//handle red box
					if($('.sapi-wrapper').hasClass('sapi-premium-user')) {}
					else { //if this is a standard user
						
						$('#sapi-upsell-butt').delay(300).slideDown(300);
						
						//if he is opening the premium tab
						if(sapiTarget == '#sapi-premium') {
							$('#sapi-upsell-top').delay(1600).slideDown(600);
							$('#sapi-preview').delay(3000).fadeIn(2000);
						}
					}
					
					$('a.sapi-tab.active').removeClass('active');
					$(this).addClass('active');
				}
			}); //end sapi tab switch click function
		});
	*/
	
	//]]>
	</script>



<div id="body">	 
	<div id="body">
		<div id="wrap">
			<div id="wrapbg" style="background: none;"></div>
			
			 <br /><br /><br />

			<div id="container">
				<div class="containertop"></div><div class="containerinside"></div>
				
				<div id="nav">
				
				</div><!--close nav-->
				
				
				
				<div id="content" class="content">
					<?=$this->{'Instance/ViewContent'};?>
					<div class="clear"></div>	
				</div><!--close content-->
				<div class="containerbutt"></div>
					
			</div><!--close container-->

			
			
			<div id="footer">
		    	<ul class="footlist txtblack">
					<li><a href="https://exchange.bevomedia.com/">The BevoMedia Exchange</a></li>
					<li><a href="https://networks.bevomedia.com/">Bevo for Networks</a></li>
					<li><a href="http://blog.bevomedia.com/">Official Blog</a></li>
				</ul>
				
				<br />
				
				<ul class="footlist txtmgray">
					<li><a href="http://<?php echo $_COOKIE['v3domain']; ?>/affiliate_network/list">Networks</a></li>
					<li><a href="http://<?php echo $_COOKIE['v3domain']; ?>/tracker_click/cumulative-stats">Tracker</a></li>
					<li><a href="http://<?php echo $_COOKIE['v3domain']; ?>/offer/offerhub">Offers</a></li>
					<li><a href="http://<?php echo $_COOKIE['v3domain']; ?>/classroom_section/list">Classroom</a></li>
					<li><a href="#">Apps</a></li>
					<li><a href="#">Tools</a></li>
				</ul>
				
				<ul class="footlist txtmgray">
					<li><a href="http://<?php echo $_COOKIE['v3domain']; ?>/tracker_click/dashboard">Dashboard</a></li>
					<li><a href="#">Help</a></li>
					<li><a href="#">My Bevo Account</a></li>
				</ul>
				
				<br />
				
				<ul class="footlist txtlgray">
					<li><a href="https://beta.bevomedia.com/TermsOfServiceBevoMedia.html">Terms &amp; Conditions</a></li>
					<li><a href="https://beta.bevomedia.com/PrivacyPolicyBevoMedia.html">Privacy Policy</a></li>
				</ul>
		    
		    	<p>&copy; <?php echo date('Y'); ?> by BevoMedia. All Rights Reserved.</p>
		    	
			</div><!--close footer-->
			

</div><!--close wrap-->
</div><!--close body-->


<script type="text/javascript">
//<![CDATA[
$(document).ready(function () {
	if ($('#datepicker').length)
	{
		$('#datepicker').daterangepicker();
	}

	$('#topDropSearch').submit(function() {

		window.location = 'https://<?php echo $_COOKIE["v3domain"]; ?>/offer/offerhub/search/'+$('#topsearchInput').val();

	});
});
//]]>
</script>

<?php 
	if (!isset($_SERVER['HTTPS'])) {
?>
<script type="text/javascript">
var sc_project=7778761; 
var sc_invisible=1; 
var sc_security="ce27f004"; 
</script>
<script type="text/javascript" src="http://www.statcounter.com/counter/counter.js"></script>
<noscript><div class="statcounter"><a title="tumblr pagecounter" href="http://statcounter.com/tumblr/" target="_blank"><img class="statcounter" src="http://c.statcounter.com/7778761/0/ce27f004/1/" alt="tumblr page counter"></a></div></noscript>
<?php 
	}
?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-6678573-1");
pageTracker._trackPageview();
</script>



</body>
</html>
