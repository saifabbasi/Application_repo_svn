<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<title><?=$this->{'Application/Mode'} == 'Development' ? 'DEV ' :'' ?>BevoAdmin</title>
<meta name="keywords" content="<?=$this->{'Instance/PageKeywords'};?>" />
<meta name="description" content="<?=$this->{'Instance/PageDescription'};?>" />
<meta name="robots" content="noindex" />
<script language='javascript'>
var modToday = 'today';
var modYesterday = 'yesterday';
</script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Functions.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Ajax.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Lock.js" type="text/javascript"></script>
<script type="text/javascript" src="<?=$this->{'System/BaseURL'};?>JS/charts/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/Themes/BevoMedia/jquery.json-2.2.min.js"></script>
<link href="<?=$this->{'System/BaseURL'};?>CSS/Application.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/main.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/style.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/default.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/ui.daterangepicker.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/jquery-ui-1.7.1.custom.css" rel="stylesheet" type="text/css" />
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/LayoutAssist.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/jquery-ui-1.7.1.custom.min.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/daterangepicker.jQuery.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/shadowbox-source-3.0b/shadowbox.css">
<script type="text/javascript" src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/shadowbox-source-3.0b/shadowbox.js"></script>
<script type="text/javascript">
	Shadowbox.init({
	    language: 'en',
	    players:  ['html', 'iframe']
	});
</script>
	    

<?php /*?>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/Nextlines.js" type="text/javascript"></script>
<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=73e6ed73-10b8-4613-8d6b-680d01b68d3c&amp;type=website&amp;buttonText=Share%20It.&amp;linkfg=%23333333"></script>
<?php */?>

</head>

<body>
    <div id="divPopWin" class="PopWin" style="display:none;">
       <div class="PopWinLeft"><div class="PopWinRight"><div class="PopWinTop"><div class="PopWinBot"><div class="PopWinTopLeft"><div class="PopWinTopRight"><div class="PopWinBotLeft"><div class="PopWinBotRight">
       <div class="PopWinContainer">
           <table width="98%" height="92%" cellspacing="0" cellpadding="3" border="0">

               <tr>
                   <td colspan="2" align="right"><div id="divPopWinTopClose"><a href="javascript:hidePop();"><img src="/Themes/BevoMedia/img/close_window.gif" width="100" height="20" border=0 alt=""></a></div></td>
               </tr>
               <tr valign="top">
                   <td><div id="divPopWinIcon"></div></td>
                   <td width="100%"><div id="divPopWinData" style="font-weight:bold;"></div></td>
               </tr>
               <tr>
                   <td colspan="2" align="center">

                       <div id="divPopWinButtons"><input type="button" name="btnClosePopWin" value="Close" class="baseeffect" onClick="hidePop();"></div>
                   </td>
               </tr>
           </table>
       </div>
       </div></div></div></div></div></div></div></div>
   </div>

    <div id="pub_main">
       <div id="pub_header">

        	<div id="pub_logo">
        		<!-- &nbsp; -->
        	</div>
        	<div id="pub_headnav" style="top:160px;text-align:right;">
        		<?php if($this->Admin):?>
        		<a href="Logout.html" style='color:#ffffff;'>
          			Logout
           		</a>
           		<?php else:?>
        		<a href="Login.html" style='color:#ffffff;'>
          			Login
           		</a>
           		
           		<?php endif?>
          	</div>


        </div>
        
        <div class="TabBar">
            <div class="Tab<?php echo($this->PageHelper->Area == 'Index')?'Over':''?>" style="margin-left:10px;"><a href="/BevoMedia/Admin/Index.html">Index</a></div>
            <div class="Tab<?php echo($this->PageHelper->Area == 'Publishers')?'Over':''?>"><a href="/BevoMedia/Admin/Publishers.html">Publishers</a></div>
            <div class="Tab<?php echo($this->PageHelper->Area == 'NetworksPlatforms')?'Over':''?>"><a href="/BevoMedia/Admin/Networks.html">Networks Platform</a></div>
            <div class="Tab<?php echo($this->PageHelper->Area == 'Networks')?'Over':''?>"><a href="/BevoMedia/Admin/NetworkStats.html">Networks</a></div>
            <div class="Tab<?php echo($this->PageHelper->Area == 'Queue')?'Over':''?>"><a href="/BevoMedia/Admin/Queue.html">Queue</a></div>
            <div class="Tab<?php echo($this->PageHelper->Area == 'Settings')?'Over':''?>"><a href="/BevoMedia/Admin/Settings.html">Settings</a></div>
            <div class="Tab<?php echo($this->PageHelper->Area == 'Self Hosted')?'Over':''?>"><a href="/BevoMedia/Admin/SelfHostedPublishers.html">Self Hosted</a></div>
        </div>

        <div class="MainArea">

	<div class="AdminMainArea">



<!-- BEGIN ADMIN PAGEHELPER HEADINGS -->
<h3><?php echo $this->{'PageHelper'}->Heading?></h3>
<h5><?php echo $this->{'PageHelper'}->SubHeading?></h5>
<!-- ENDOF ADMIN PAGEHELPER HEADINGS -->



<!-- BEGIN ADMIN PUBLISHER SUBMENU -->
<?php if($this->PageHelper->Area == 'Publishers'):?>
<ul class='adminHeadLinks'>
	<li>
		<a href='Publishers.html'>Browse Publishers</a>
	</li>
	<li>
		<a href='NewApplications.html'>New Applications</a>
	</li>
	<li>
		<a href='AllPublishers.html'>View All</a>
	</li>
	<li>
		<a href='DeletedPublishers.html'>Deleted</a>
	</li>
	<li>
		<a href='SearchPublishers.html'>Search</a>
	</li>
	<li>
		<a href='EmailPublishers.html'>Email</a>
	</li>
	<li>
		<a href='Tickets.html'>Tickets</a>
	</li>
	<li>
		<a href='APIUsage.html'>API Usage</a>
	</li>
	<li>
		<a href='PublisherStats.html'>Publisher Stats</a>
	</li>
	<li>
		<a href='PublisherRatings.html'>Network Ratings</a>
	</li>
</ul>
<?php endif?>
<!-- ENDOF ADMIN PUBLISHER SUBMENU -->



<!-- BEGIN ADMIN MENTOR SUBMENU -->
<?php if($this->PageHelper->Area == 'Mentors'):?>
<ul class='adminHeadLinks'>
	<li>
		<a href='Mentors.html'>Browse Mentors</a>
	</li>

</ul>
<?php endif?>
<!-- ENDOF ADMIN MENTOR SUBMENU -->



<!-- BEGIN ADMIN NETWORKS SUBMENU -->
<?php if($this->PageHelper->Area == 'Networks'):?>
<ul class='adminHeadLinks'>
	<li>
		<a href='AffiliateNetworks.html'>Affiliate Networks</a>
	</li>
	<li>
		<a href='NetworkStats.html'>PPC Network Stats</a>
	</li>
</ul>
<?php endif?>
<!-- ENDOF ADMIN NETWORKS SUBMENU -->



<!-- BEGIN ADMIN CRONS/QUEUE SUBMENU -->
<?php if($this->PageHelper->Area == 'Queue'):?>
<ul class='adminHeadLinks'>
	<li>
		<a href='Queue.html'>Queue Status</a>
	</li>
</ul>
<?php endif?>
<!-- ENDOF ADMIN CRONS/QUEUE SUBMENU -->



<!-- BEGIN ADMIN SELF HOSTED SUBMENU -->
<?php if($this->PageHelper->Area == 'Self Hosted'):?>
<ul class='adminHeadLinks'>
	<li>
		<a href='SelfHostedPublishers.html'>Browse Self Hosted Publishers</a>
	</li>
	<li>
		<a href='SelfHostedAPIUse.html'>Self Hosted API Use</a>
	</li>
</ul>
<?php endif?>
<!-- ENDOF ADMIN SELF HOSTED SUBMENU -->



<br style='clear:both;'>



<!-- BEGIN VIEW CONTENT -->
<?=$this->{'Instance/ViewContent'};?>
<!-- ENDOF VIEW CONTENT -->

	</div>
	
		</div>

		<div class="Copyright">
			<!-- <a href="https://www.bevomedia.com/index.php">Home</a> -
			<a href="https://www.bevomedia.com/privacy.php">Privacy Policy</a> -
			<a href="https://www.bevomedia.com/terms.php">Terms Of Service</a> -
			<a href="https://www.bevomedia.com/sitemap.php">Site Map</a> -
			<a href="https://www.bevomedia.com/aboutus.php">About BeVo</a> -
			<a href="https://www.bevomedia.com/faqs.php">FAQs</a> -
			<a href="https://www.bevomedia.com/careers.php">Careers</a> -
			<a href="https://www.bevomedia.com/press.php">Press</a> -
			<a href="https://www.bevomedia.com/investors.php">Investors</a> -
			<a href="https://www.bevomedia.com/ad-networks.php">Ad Networks</a>
			<br>
			Copyright &copy; 2009 BeVo Media LLC. 721 University Ave. Syracuse, NY 13244
			| <a href="javascript:reportProblem();" class="linkWhite">Report an error on this page</a> -->
			<a href="index.php">Home</a> - <a href="privacy-policy-bevo-media.htm">Privacy Policy</a> - <a href="terms-of-service-bevo-media.htm">Terms Of Service</a> - <a href="sitemap.htm">Site Map</a> - <a href="about-bevo-media.htm">About BeVo</a> - <a href="faqs-bevo-media.htm">FAQs</a> - <a href="careers-bevo-media.htm">Careers</a> - <a href="press-bevo-media.htm">Press</a> - <a href="investors-bevo-media.htm">Investors</a> - <a href="ad-networks-bevo-media.htm">Ad Networks</a><a href="networks.htm"></a>

			<br>
			Copyright &copy; 2010 BeVo Media LLC. Beverly Hills, CA 90210
		</div>

		<script language="JavaScript" src="https://www.bevomedia.com/style/wz_tooltip.js"></script>

	</div>

</body>

</html>