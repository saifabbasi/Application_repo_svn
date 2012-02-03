<?php 
	require 'soap_functions.php'; //outsource to controller or so later
	
	//hooks used in the markup
	$_db = Zend_Registry::get('Instance/DatabaseObj');
	
	//topdrop status
	$soap_topdrop_status = soap_topdrop_status();
	
	
//	if ( ($this->User->getVaultID()==0) && (Zend_Registry::get('Instance/Function')!='AddCreditCard') )
//	{
//		header('Location: /BevoMedia/User/AddCreditCard.html');
//		die;
//	}
	
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

</head>
<body><?php /* ################################################################# BODY */ ?>

	<script type="text/javascript">
	//<![CDATA[
		Shadowbox.init({
		    language: 'en',
		    players:  ['html', 'iframe', 'img']
		});
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
	//]]>
	</script>



<div id="body">
	<div id="wrap">
		

<?php 	/** container class
	  * allowed values:
	  * cont_ppc		==> PPC
	  * cont_tracker	==> Tracker
	  * cont_analytics	==> Analytics
	  * cont_setup		==> Networks, Offers, and Self-Hosted (setup menu)
	  * cont_top		==> Classroom, Tutorials, Support, Dashboard, and My Account (top menu)
	  * cont_market		==> Marketplace
	  */
	  
	if(	$this->PageHelper->Area == 'Classroom'
	||	$this->PageHelper->Function == 'KB'
	||	$this->PageHelper->Area == 'PPCTutorials' //tutorials
	||	$this->PageHelper->Area == 'Overview' //dashboard
	||	$this->PageHelper->Function == 'AccountInformation' //my account
	||	$this->PageHelper->Function == 'AddCreditCard'
	||	$this->PageHelper->Function == 'ChangeProfile'
	||	$this->PageHelper->Function == 'ApiCalls'
	||	$this->PageHelper->Function == 'ManageStats'
	||	$this->PageHelper->Function == 'CreditCard'
	||	$this->PageHelper->Function == 'Invoice'
	||	$this->PageHelper->Function == 'Referrals'
	||	$this->PageHelper->Function == 'MyProducts'
	)
		$soap_containerclass = 'cont_top';
	
	elseif($this->PageHelper->Area == 'Marketplace')
		$soap_containerclass = 'cont_market';
	
	elseif(	$this->PageHelper->Controller == 'PPVTools'
	|| 	$this->PageHelper->Controller == 'PPVSpy'
	|| 	$this->PageHelper->Controller == 'Geotargeting'
	|| 	$this->PageHelper->Controller == 'Timetargeting'
	||	$this->PageHelper->Function == 'AppStore'
	)
		$soap_containerclass = 'cont_apps';
		
	elseif(	$this->PageHelper->Area == 'PPCManager'
	||	$this->PageHelper->Function == 'CreatePPC'
	||	$this->PageHelper->Function == 'CreatePPCSubmit'
	||	$this->PageHelper->Function == 'CreatePPCSaved'
	||	$this->PageHelper->Function == 'PPCQueueProgress'
	||	$this->PageHelper->Function == 'AccountStatsPPC'
	||	$this->PageHelper->Function == 'AdwordsManualUpload'
	||	$this->PageHelper->Function == 'CampaignStatsPPC'
	||	$this->PageHelper->Function == 'AdGroupStatsPPC'
	||	$this->PageHelper->Function == 'AdGroupAdVariationsPPC'
	)
		$soap_containerclass = 'cont_ppc';
	
	elseif($this->PageHelper->Area == 'KeywordTracker')
		$soap_containerclass = 'cont_tracker';
	
	elseif($this->PageHelper->Area == 'Analytics')
		$soap_containerclass = 'cont_analytics';
	
	elseif(	$this->PageHelper->Function == 'SelfHostedLogin'
	||	$this->PageHelper->Area == 'Offers'
	||	$this->PageHelper->Area == 'MyNetworks'
	||	$this->PageHelper->Function == 'Reviews'
	||	$this->PageHelper->Function == 'RackspaceWizard'
	||	$this->PageHelper->Function == 'ServerScript'
	||	$this->PageHelper->Function == 'SelfHostedLoginDownload'
	||	$this->PageHelper->Function == 'NameYourPrice'
	||	$this->PageHelper->Function == 'NameYourPriceResult'
	)
		$soap_containerclass = 'cont_setup';

?>


<div id="container" class="<?php echo $soap_containerclass; ?>">
	<div class="containertop"></div><div class="containerinside"></div>
	
	
	
	<div class="content">
		<?=$this->{'Instance/ViewContent'};?>
		<div class="clear"></div>	
	</div><!--close content-->
	<div class="containerbutt"></div>
		
</div><!--close container-->

<div id="footer">
	
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
});
//]]>
</script>

<!--[if lt IE 7]><div id="srykthxbai">We're sorry, but your browser is too old for BevoMedia.<br />This application requires a modern browser to run smoothly and safely.<br />Please upgrade your browser.<br />Thank you.</div><![endif]-->
<script type="text/javascript">
//<![CDATA[
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
var pageTracker = _gat._getTracker("UA-6678573-1");
pageTracker._trackPageview();
//]]>
</script>



</body>
</html>
