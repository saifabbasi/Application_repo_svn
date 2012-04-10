<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=$this->{'Instance/PageTitle'};?></title>
<meta name="keywords" content="<?=$this->{'Instance/PageKeywords'};?>" />
<meta name="description" content="<?=$this->{'Instance/PageDescription'};?>" />
<script src="<?=$this->{'System/BaseURL'};?>JS/Functions.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Ajax.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Lock.js" type="text/javascript"></script>
<link href="<?=$this->{'System/BaseURL'};?>CSS/Application.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/bob.style.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/default.css" rel="stylesheet" type="text/css" />
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/RegisterForm.js" type="text/javascript"></script>
<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>

<link rel="stylesheet" type="text/css" href="/Themes/BevoMedia/shadowbox-source-3.0b/shadowbox.css" />
<script type="text/javascript" src="/Themes/BevoMedia/shadowbox-source-3.0b/shadowbox.js"></script>

<script type="text/javascript">
Shadowbox.init({
    language: 'en',
    players:  ['html', 'iframe', 'img']
});
</script>

</head>

<div id='selfHostedUpdateBar'>
    <span class='info'>
    	This is the old version of Bevo Media. Please visit our new version at <a href="http://affportal.bevomedia.com">http://affportal.bevomedia.com</a>.
    </span>
</div>

<body>
<div id="wrap">
<div id="header">
	<h1><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/Index.html">Bevo Media - Affiliate Network Consolidation and Affiliate Classroom - CPA, CPC and CPM Networks</a></h1>

<!-- START EDIT -->
<a class="button opencode_nonlogin" href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/SelfHosted.html" title="Click here for the Bevo Media Self-Hosted feature list">Self-Hosted and Open Code Version available!</a>
<!-- END EDIT -->

</div>



<div id="container">
	<div id="toprite">
		<a class="signup" href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/User/Register.html">Sign Up Free Today</a>

		<form name="loginForm" method="post" action="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/User/ProcessLogin.html" onSubmit="return validateForm(this);">
		<?php if(isset($_SESSION['User']['ID'])):?>
		<div class="row">
			You are logged in as:
		</div>
		<div class="row">
			<b>
			<?php $User = new User($_SESSION['User']['ID'])?>
			<?php echo $User->firstName?>
			<?php echo $User->lastName?>
			</b>
		</div>
		<div class="row textAlignRight">
			<br/>
			<a href='<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/User/AccountInformation.html'>Member Section</a>
		</div>
		<div class="row textAlignRight">
			<a href='<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/User/Logout.html'>Log out</a>
		</div>
		<?php else:?>
			<div class="row">
				<label>Email</label>
				<input type="text" class="formtxt" name="Email" alt="email" tabindex="1" />
			</div>
			<div class="row">
				<label>Password<span style="padding-top: 5px; padding-left: 50px; font-size:.8em; display: inline"><a href="<?php /*=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};*/?>/BevoMedia/Index/ForgotPassword.html">Forgot it?</a></span></label>
				<input type="password" class="formtxt" name="Password" alt="blank" tabindex="2" />
			</div>
			<div class="row" style="padding-top:5px;">
				<input type="checkbox" name="Remember" alt="blank" id="RememberMeCheck" tabindex="4" checked="checked" />
				<label for="RememberMeCheck">Remember me</label>

			</div>
			<div class="row">
				<input type="submit" class="formsubmit" name="loginFormSubmit" value="Login" tabindex="3" />
			</div>
		<?php endif?>
		</form>
		
		<a class="button whyfree" href="<?=$this->{'System/BaseURL'};?>Whyfree.html" title="Why is BevoMedia free?">Why is BevoMedia free?</a>
		
	</div>

	<div id="topleft">
		<a class="home" href="<?=$this->{'System/BaseURL'};?>">Bevo Media</a>
		<ul id="pagemenu">
			<li><a class="<?php echo ($this->page == 'Networks')?('active'):('')?> networks" href="<?=$this->{'System/BaseURL'};?>Networks.html">Consolidate your Networks</a></li>
			<li><a class="<?php echo ($this->page == 'Offers')?('active'):('')?> offers" href="<?=$this->{'System/BaseURL'};?>Offers.html">Retrieve your Offers</a></li>
			<li><a class="<?php echo ($this->page == 'PPC')?('active'):('')?> ppc" href="<?=$this->{'System/BaseURL'};?>PPC.html">Manage your PPC Platforms</a></li>
			<li><a class="<?php echo ($this->page == 'Keywords')?('active'):('')?> keywords" href="<?=$this->{'System/BaseURL'};?>Keywords.html">Track your Keywords</a></li>
			<li><a class="<?php echo ($this->page == 'Analytics')?('active'):('')?> analytics" href="<?=$this->{'System/BaseURL'};?>Analytics.html">Examine your Analytics</a></li>
			<li><a class="<?php echo ($this->page == 'Classroom')?('active'):('')?> classroom" href="<?=$this->{'System/BaseURL'};?>Classroom.html">Learn in the Classroom</a></li>
		</ul>
	</div>
	



	<?=$this->{'Instance/ViewContent'};?>
	
	
	
		
	</div><!--close container-->
	<div id="footer">
		<div id="nwscroller" >
			<object	wmode="opaque" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" type="application/x-shockwave-flash" data="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/nwscroller.swf" width="880" height="70">
				<param name="movie" value="<?=$this->{'System/BaseURL'};?>/Themes/<?=$this->{'Application/Theme'};?>/nwscroller.swf" />
				<param name="wmode" value="opaque" />
				<embed wmode="opaque" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/nwscroller.swf" width="880" height="70">
				</embed>
			</object>
			
		</div>
		
		<ul>
			<?php
			/* USE STATIC HTML PAGES
			?>
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/Index.html">Home</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/PrivacyPolicyBevoMedia.html">Privacy Policy</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/TermsOfServiceBevoMedia.html">Terms of Service</a></li>
	
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/SitemapBevoMedia.html">Site Map</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/AboutBevoMedia.html">About Bevo</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/FaqsBevoMedia.html">FAQs</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/CareersBevoMedia.html">Careers</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/PressBevoMedia.html">Press</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/InvestorsBevoMedia.html">Investors</a></li>
	
			<li><a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Index/AdNetworksBevoMedia.html">Affiliate Networks</a></li>
			<?php
			*/
			?>
			<li><a href="<?=$this->{'System/BaseURL'};?>">Home</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?>PrivacyPolicyBevoMedia.html">Privacy Policy</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?>TermsOfServiceBevoMedia.html">Terms of Service</a></li>
	
			<li><a href="<?=$this->{'System/BaseURL'};?>SitemapBevoMedia.html">Site Map</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?>AboutBevoMedia.html">About Bevo</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?>FaqsBevoMedia.html">FAQs</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?>CareersBevoMedia.html">Careers</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?>PressBevoMedia.html">Press</a></li>
			<li><a href="<?=$this->{'System/BaseURL'};?>InvestorsBevoMedia.html">Investors</a></li>
	
			<li><a href="<?=$this->{'System/BaseURL'};?>AdNetworksBevoMedia.html">Affiliate Networks</a></li>
			
		</ul>
		
		<div id="footercopy">
			Copyright &copy; <?php echo date('Y'); ?> Bevo Media LLC. San Diego, CA 92130
		</div>
	</div>
</div>

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