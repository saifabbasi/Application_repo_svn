<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<title><?=$this->{'Instance/PageTitle'};?></title>
<meta name="keywords" content="<?=$this->{'Instance/PageKeywords'};?>" />
<meta name="description" content="<?=$this->{'Instance/PageDescription'};?>" />
<script src="<?=$this->{'System/BaseURL'};?>JS/Functions.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Ajax.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Lock.js" type="text/javascript"></script>

<script language="JavaScript" src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/AC_RunActiveContent.js"></script>


<link href="<?=$this->{'System/BaseURL'};?>CSS/Application.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/main.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/style.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/default.css" rel="stylesheet" type="text/css" />
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/jquery.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/FusionCharts.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/RegisterForm.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/shadowbox-source-3.0b/shadowbox.css" />
<script type="text/javascript" src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/shadowbox-source-3.0b/shadowbox.js"></script>
    
</head>

<script type="text/javascript">
//<![CDATA[
	Shadowbox.init({
	    language: 'en',
	    players:  ['html', 'iframe']
	}); 
//]]>
</script>

<body>
    <div id="divPopWin" class="PopWin" style="display:none;">
       <div class="PopWinLeft"><div class="PopWinRight"><div class="PopWinTop"><div class="PopWinBot"><div class="PopWinTopLeft"><div class="PopWinTopRight"><div class="PopWinBotLeft"><div class="PopWinBotRight">
       <div class="PopWinContainer">
           <table width="98%" height="92%" cellspacing="0" cellpadding="3" border="0">

               <tr>
                   <td colspan="2" align="right"><div id="divPopWinTopClose"><a href="javascript:hidePop();"><img src="/Themes/BevoMedia/img/close_window.gif" width="100" height="20" border="0" alt="" /></a></div></td>
               </tr>
               <tr valign="top">
                   <td><div id="divPopWinIcon"></div></td>
                   <td width="100%"><div id="divPopWinData" style="font-weight:bold;"></div></td>
               </tr>
               <tr>
                   <td colspan="2" align="center">

                       <div id="divPopWinButtons"><input type="button" name="btnClosePopWin" value="Close" class="baseeffect" onClick="hidePop();" /></div>
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
        	<div id="pub_headnav">
        		<a href="/BevoMedia/User/Index.html">
        			<img src="/Themes/BevoMedia/img/home.gif" />
        		</a> 
          		
          		<a href="https://www.bevomedia.com/contactus.php">
          			<img src="/Themes/BevoMedia/img/mail.gif" />

          		</a> 
          		
          		<a href="#">
          			<img src="/Themes/BevoMedia/img/setwap.gif" />
          		</a> 
          		
          		<a href="Logout.html">
          			<img src="/Themes/BevoMedia/img/logout_icon.jpg" height="20" border="0" width="24" title="Logout" align="absbottom" />
          		</a>
          	</div>
<!-- Network of Month -->
<div id="pub_headclass">
	<h1>Network of the Month</h1>

<div style="text-align: center; padding-top: 30px;"><a href="publisher-networkofmonth.php?ID=7" style="size:0;color:#FFFFFF"><img src="/Themes/BevoMedia/img/networkoffers/1020.png" alt="Blue Lithium" style="background-color: #FFFFFF;" /><br />
			Blue Lithium</a></div></div>
<!-- End Network of Month -->
        </div>
        
        <div class="TabBar">
           
            <div class="Tab<?php echo($this->PageHelper->Area == 'Overview')?'Over':''?>"><a href="/BevoMedia/User/Index.html">Overview</a></div>
            <div class="Tab<?php echo($this->PageHelper->Area == 'MyNetworks')?'Over':''?>"><a href="/BevoMedia/Publisher/Index.html">My Networks</a></div>
            <div class="Tab"><a href="https://www.bevomedia.com/publisher-offers.php">Codes/Offers</a></div>

            <div class="Tab<?php echo($this->PageHelper->Area == 'PPCManager')?'Over':''?>"><a href="/BevoMedia/Publisher/PPCManager.html">PPC Management</a></div>  
            <div class="Tab"><a href="https://www.bevomedia.com/publisher-tracker.php">Keyword Tracker</a></div>
            <div class="Tab"><a href="https://www.bevomedia.com/publisher-analytic-detail.php">Analytics</a></div>
            <div class="Tab"><a href="https://www.bevomedia.com/publisher-market.php">MarketPlace</a></div>
            <div class="Tab"><a href="https://www.bevomedia.com/publisher-classroom.php">Classroom</a></div>
                       
        </div>

        <div class="MainArea">
        
            <div class="Stack">
            
                <div class="StackRight">
                    <table cellspacing="5" cellpadding="0" border="0">
                        <tr>
                            <td>
                                <div class="BlueBox">
                                    <h3>Your Mentor Contact Info:</h3>
									
                                    
                                    Earnings:<br />
                                    <font class="main">Today's: $0.00</font>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <font class="main">MTD: $0.00</font><br />
                                    Expenses:<br />
                                    <font class="main">Today's: $0.00</font>

                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <font class="main">MTD: $0.00</font><br /><br />
                                    
									<br /><br />
                                    
                                    <p><a style="color:white;" href="publisher-market.php"><strong>Schedule a One on One Consulting Session</strong></a></p>
                                </div>                                
                            </td>
                        </tr>
						                                                <tr>

                            <td>
                            	<div class="BlueBox" style='height:30px; background: url(/Themes/BevoMedia/img/blue-box2.gif)'>
	                            	<a href='publisher-adwords-api-usage.php'>
	                                	<b>Remaining AdWords API Credit</b>
	                                	<br />
	                                	$65.00                                
	                                </a>
                                </div>                              
                            </td>
                        </tr>

                        <tr>
                        <td>

                            </td>
							</tr>
                            <tr>
                            <td>

                            <div class="r3">
                                <div class="rr3d">Resources</div>
                                <div class="rr3m">
    								<ul>
    									<li class="rr3i"><a href="AccountInformation.html">Account Information</a></li>
    									<li class="rr3i"><a>AdOptimizer</a>
    										<ul>

    											<li><a href="publisher-adoptimizer-desc.php">About</a></li>
    											<li><a href="publisher-adoptimizer.php">New/Edit AdOptimizer Campaign</a></li>
    										</ul>
    									</li>
    									<li class="rr3i"><a>Classroom Resources</a>
    										<ul>
    											<li><a href="first-campaign.php">My First Campaign</a></li>

   												<li><a href="industry-reports.php">Industry Reports</a></li>
												<li><a href="case-studies.php">Case Studies</a></li>
												<li><a href="#">Article Database</a></li>
    										</ul>
    									</li>
                                    	<li class="rr3i"><a>Keyword Resources</a>
                                    		<ul>

                                    			<li><a href="https://adwords.google.com/select/KeywordToolExternal" target="_blank">Google Keyword Tool</a></li>
												<li><a href="http://bevomedia.bryxen4.hop.clickbank.net/?tid=KWELITE" target="_blank">Keyword Elite</a></li>
												<li><a href="http://www.shareasale.com/r.cfm?b=100730&amp;u=316208&amp;m=14754&amp;urllink=&amp;afftrack=" target="_blank">SpyFu</a></li>
                                    		</ul>
                                    	</li>
                                    	
                                    	<li class="rr3i"><a href="publisher-market.php?ServiceID=4">Programming Help</a></li>

                                    	<li class="rr3i"><a href="publisher-market.php?ServiceID=1">Article Writers</a></li>
                                    	<li class="rr3i"><a href="publisher-market.php?ServiceID=2">Landing Page Design</a></li>
                                    	<li class="rr3i"><a href="publisher-market.php?ServiceID=5">SEO Resources</a>

                                    	</li>
                                    	<li class="rr3i"><a>Web Hosting</a>
                                    		<ul>

                                    			<li><a href="http://www.jdoqocy.com/click-3266927-10378494">Godaddy</a></li>
                                    			<li><a href="http://www.tkqlhce.com/click-3266927-10410811">Hostgator</a></li>
                                    		</ul>
                                    	</li>
                                    	<li class="rr3i"><a href="publisher-referrals.php">Referrals</a></li>
                                    	<li class="rr3i"><a href="publisher-market.php?ServiceID=3">One on One Consulting</a></li>
                                    	<li class="rr3i"><a href="publisher-subreport.php">SubID Report</a></li>

                                    	<li class="rr3i"><a href="support.php">Support</a></li>
    								</ul>
    							</div>
                                <div class="rr3db"></div>
                            </div>
                          
                            </td>
                            </tr>        
                                                                        <tr>

                            <td>
                                <div class="LightBlueBoxTop">
                                    <h5>
                                        <br />
                                        <br />
                                        To Add a network not listed
                                        <br />
                                        <br />
                                        <br />

                                    </h5>
                                    <a href="javascript:showAddNewNetwork();"><img src="/Themes/BevoMedia/img/eng/btn-click-here.gif" width="73" height="29" border="0" alt="" /></a>
                                </div>
                                <div class="LightBlueBoxBot"></div>
                            </td>
                        </tr>
                                            </table>
                    
                </div>

                <div class="StackLeft">

 
	<center>
	
	<?php if($this->{'PageHelper'}->Heading):?>
	<div class="SkyBox"><div class="SkyBoxTopLeft"><div class="SkyBoxTopRight"><div class="SkyBoxBotLeft"><div class="SkyBoxBotRight">
		<table width="550" cellspacing="0" cellpadding="5" border="0">
			<tr valign="top">

				<td width="127"><img src="/Themes/BevoMedia/img/<?php echo $this->{'PageHelper'}->HeadingImage?>" width="118" height="127" border="0" alt=""></td>
				<td class="main">
					<h4><?php echo $this->{'PageHelper'}->Heading?></h4>
					<br />
					<?php echo $this->{'PageHelper'}->SubHeading?>
				</td>
			</tr>
		</table>
	</div></div></div></div></div>
	<?php endif?>
	</center>
	<br/>

    
    
    
	<?=$this->{'Instance/ViewContent'};?>
	
	
	
	
	

	
	
	
	

				
				</div>

			</div>

		</div>

		<div class="Copyright">
		
			<a href="index.php">Home</a> - <a href="privacy-policy-bevo-media.htm">Privacy Policy</a> - <a href="terms-of-service-bevo-media.htm">Terms Of Service</a> - <a href="sitemap.htm">Site Map</a> - <a href="about-bevo-media.htm">About BeVo</a> - <a href="faqs-bevo-media.htm">FAQs</a> - <a href="careers-bevo-media.htm">Careers</a> - <a href="press-bevo-media.htm">Press</a> - <a href="investors-bevo-media.htm">Investors</a> - <a href="ad-networks-bevo-media.htm">Ad Networks</a><a href="networks.htm"></a>

			<br />
			Copyright &copy; 2009 BeVo Media LLC. 721 University Ave. Syracuse, NY 13244
		</div>

		<script language="JavaScript" src="https://www.bevomedia.com/style/wz_tooltip.js"></script>

	</div>

</body>

</html>