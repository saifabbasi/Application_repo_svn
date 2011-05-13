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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="/JS/Functions.js" type="text/javascript"></script>
<script src="/JS/Ajax.js" type="text/javascript"></script>
<script src="/JS/Lock.js" type="text/javascript"></script>
<script language="JavaScript">
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
</script>

<script language="JavaScript" src="/Themes/BevoMedia/AC_RunActiveContent.js"></script>

<!--<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<!--[if IE]><script type="text/javascript" src="/JS/charts/excanvas.compiled.js"></script><![endif]-->
<script type="text/javascript" src="/JS/charts/visualize.jQuery.js"></script>
<link type="text/css" rel="stylesheet" href="/JS/charts/visualize.jQuery.css"/>
<link type="text/css" rel="stylesheet" href="/JS/charts/demopage.css"/>
<!-- ENDOF New Chart System -->

<!--  check these later, we may need some of the styles in here
<link href="/Themes/BevoMedia/main.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/default.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/market.css" rel="stylesheet" type="text/css" /> -->
<link href="/Themes/BevoMedia/style.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/ui.daterangepicker.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/jquery-ui-1.7.1.custom.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/Themes/BevoMedia/shadowbox-source-3.0b/shadowbox.css">
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
<script type="text/javascript">
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
</script>



<?php /*?>
<script src="/Themes/BevoMedia/Nextlines.js" type="text/javascript"></script>
<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=73e6ed73-10b8-4613-8d6b-680d01b68d3c&amp;type=website&amp;buttonText=Share%20It.&amp;linkfg=%23333333"></script>
<?php */?>

</head>
<body><?php /* ################################################################# BODY */ ?>

	<?php //financial + api data
	//financial
	$RevenueMonth = $RevenueToday = $ExpensesMonth = $ExpensesToday = 0;
	
	$Today = date('Y-m-d');
	$FirstOfMonth = date('Y-m-1');
	$Sql = "SELECT 
				SUM(S.revenue)*(100-N.adminCommission)/100 AS REVENUE,
			    SUM( S.clicks ) AS CLICKS,
			    SUM( S.conversions ) AS CONVERSIONS
			FROM 
				bevomedia_aff_network N 
				LEFT JOIN bevomedia_user_aff_network_stats S ON S.network__id = N.id AND S.statDate >= '$Today' AND S.statDate <= '$Today' 
			WHERE 
				S.user__id = {$this->User->id} AND 
				N.isValid = 'Y' GROUP BY S.user__id 
			";
	$Row = $_db->fetchRow($Sql);
	if($Row) {
		$RevenueToday = $Row->REVENUE;
		$ClicksToday = $Row->CLICKS;
		$ConversionsToday = $Row->CONVERSIONS;
	}
	
	$Sql = "SELECT
						S.id,
						SUM( S.clicks ) AS CLICKS,
						SUM( S.conversions ) AS CONVERSIONS,
						SUM(S.revenue) AS REVENUE,
						(SUM(S.REVENUE)*1000) AS ECPM
					FROM bevomedia_user_aff_network_subid S
					
					WHERE S.user__id = {$this->User->id}
					AND S.statDate >= '$FirstOfMonth'
					AND S.statDate <= '$Today'
					
					GROUP BY S.user__id";
	$Row = $_db->fetchRow($Sql);
	if($Row)
		$RevenueMonth = $Row->REVENUE;
		
	$Network = new Network_Stats();
	$ExpensesMonth = $Network->GetMonthToDateCostForUser($this->User->id);
	
	$ExpensesToday = 0;
	
	$NetToday = $RevenueToday - $ExpensesToday;
	$NetMonth = $RevenueMonth - $ExpensesMonth;
	
	$lun = ($this->User->lastNetworkUpdate);
	if($lun[0] == '-')
		$lun = 0;
	else	$lun = strtotime($lun);
	
	$lpn = ($this->User->lastPPCUpdate);
	if($lpn[0] == '-')
		$lpn = 0;
	else	$lpn = strtotime($lpn);
		
	 ?>
	


	<div id="topdrop" class="<?php if($this->User->vaultID != 0) echo 'topdrop_premium'; else echo 'topdrop_standard' ?>">
	<div id="topdroptop"<?php if($soap_topdrop_status) echo ' class="'.$soap_topdrop_status.'"'; ?>>
	
		<?php /*finance for veified*/
		if($this->User->vaultID != 0) : ?>
		
			<div class="topdropbox topdropbox_financial">
				<div class="topdropentry topdropentry_earnings">$<?php echo number_format($RevenueMonth, 2)?></div>
				<div class="topdropentry topdropentry_expenses">$<?php echo number_format($ExpensesMonth, 2)?></div>
				<div class="topdropentry topdropentry_profit">$<?php echo number_format($NetMonth, 2)?></div>
			</div>
			
		<?php endif;
		?>		
		
		<div class="topdropbox topdropbox_api">
			<div class="topdropentry topdropentry_networkupdate">
				<?php echo $lun ? date('m/d/y', $lun) : 'Never'; ?>
				<span><?php echo $lun > 0 ? date('h:i:s A', $lun) : ''; ?></span>
			</div>
			<div class="topdropentry topdropentry_ppcupdate">
				<?php echo $lpn ? date('m/d/y', $lpn) : 'Never'; ?>
				<span><?php echo $lpn > 0 ? date('h:i:s A', $lpn) : ''; ?></span>
			</div>
		</div>
		
		<div class="topdropbox topdropbox_mentorship_wide">
			<ul class="iconlist iconlist_mentorship">
				<li class="icon_phone">Unlimited access to your mentor</li>
				<li class="icon_people">Financial, technical, and legal support</li>
				<li class="icon_thumb">Invaluable benefits</li>
				<li class="icon_pen">Full resource pool at your service</li>
			</ul>
			<a class="btn topdrop_mentorship_learnmore" href="/BevoMedia/Marketplace/MentorshipProgram.html">Learn more about the Bevo Mentorship Program</a>
		</div>
		
		<?php /*upgrade*/
		if($this->User->vaultID == 0) : //standard account
		?>
			<div class="topdropbox topdropbox_accstatus">
				<div class="topdropentry topdropentry_upgradetopremium">
					<strong><em>Why should you be verified?</em></strong>
					<ul class="arrlist arrlist_yellow">
						<li>Access to entire interface</li>
						<li>On-Demand Support</li>
						<li>Sync up with auto updates</li>
					</ul>
				</div>
				<a class="btn topdrop_getpremium" href="/BevoMedia/User/AddCreditCard.html"></a>
			</div>
			
			<?php //depreciated slim mentorship box
			/*<div class="topdropbox topdropbox_mentorship_slim">
				<a class="btn topdrop_mentorship_icons" href="/BevoMedia/Marketplace/MentorshipProgram.html"></a>
			</div>*/
			?>
		
		<?php endif; //endif !$accstatus_premium
		
		/*offer of the month*/
		$offer_id = '20726'; //MAKE SURE THIS IS THE SAME AS IN Offers/BestPerformers.view line 6!
		
		$sql = "SELECT 	title, imageUrl, previewUrl
			FROM	bevomedia_offers
			WHERE	id = $offer_id
			LIMIT 1		
		";
		$ootmRaw = mysql_query($sql);
		$ootm = mysql_fetch_object($ootmRaw);

		if(!empty($ootm)) { ?>
			<div class="topdropbox topdropbox_ootm">
				<div class="ootmpic">
					<a class="picbtn" href="<?php echo $ootm->previewUrl; ?>" target="_blank" title="<?php echo htmlentities($ootm->title); ?> - Click to preview in a new tab">
						<img src="/Themes/BevoMedia/img/offers/<?php echo $offer_id; ?>.jpg" alt="" />
						<span class="picframe"></span>
						<span class="btn ovault_visiticon_transyell"></span>
					</a>
				</div>
				<a class="btn ovault_transyell_details" href="/BevoMedia/Offers/BestPerformers.html" title="View offer details in Bevo">View offer details in Bevo</a>
			</div>
			
		<?php } //endif $ootm
		?>	
		
		<div class="clear"></div>
		
	</div><!--close topdroptop-->
	<div id="topdropbutt">
		<?php if($this->User->vaultID != 0) echo '<div class="premium_hiddenfirst topdroptoggle"></div>'; ?>
		<div class="topdroplabel topdroplabel_financial">
			<div class="topdropentry">$<?php echo number_format($RevenueToday, 2)?></div>
		</div>
	
		<?php if($this->User->vaultID != 0) : //if premium
		?>
			<div class="topdroplabel topdroplabel_clickstooffer">
				<div class="topdropentry"><?php echo @intval($ClicksToday);?></div>
			</div>
			<div class="topdroplabel topdroplabel_conversions">
				<div class="topdropentry"><?php echo @intval($ConversionsToday);?></div>
			</div>
			<div class="topdroplabel topdroplabel_cr">
				<div class="topdropentry"><?php echo @number_format(($ClicksToday>0)?($ConversionsToday*100/$ClicksToday):0);?></div>
			</div>
			
		<?php else : //if standard
		?>
		
			<div class="topdroplabel topdroplabel_installdownload">
				<a class="btn btn_topdrop_installnetworks" href="/BevoMedia/Publisher/Index.html">Install Networks</a>
				<a class="btn btn_topdrop_downloadselfhosted" href="/BevoMedia/User/SelfHostedLogin.html">Download Self-Hosted</a>
				<div class="clear"></div>
			</div>
			
			<div class="topdroplabel topdroplabel_discoverpremium topdroptoggle"></div>
			
		<?php endif; //endif premium
		?>
		
		<div class="topdroplabel topdroplabel_quicksearch">
			<?php 	if(	$this->PageHelper->Controller == 'Offers'
				&&	$this->PageHelper->Function != 'NameYourPayout'	
				) { ?>
			
				<p>Use the Bevo Search Sphere to search for offers!</p>
			
			<?php } else { ?>
				
				<form method="get" action="" id="topdrop_osearchform">
					<label class="hide">type any offer name or vertical</label>
					<input type="text" class="formtxt" id="topdrop_osearch" name="topdrop_osearch" value="type any offer name or vertical" />
					<input type="submit" class="btn formsubmit odial_go_small" name="topdrop_quicksearch_submit" value="Go" />
				</form>	
				<script type="text/javascript">
				$('#topdrop_osearch').live('focus', function() {
					if($(this).val() == $(this).prev().html())
						$(this).val('');					
				}).live('blur', function() {
					if($(this).val() == '')
						$(this).val($(this).prev().html());				
				})
				$('#topdrop_osearchform').submit(function() {
					var field = $('#topdrop_osearch');
					if(field.val() == '' || field.val() == field.prev().html()) {
						alert('Please enter a search term!');
						return false;
					} else {
						<?php /* TEMPORARY - POST or GET does not work right now, not sure why. will revisit when HASH/GET search navi is implemented. */?>						
						var s = 'get=searchresults&search='+field.val()+'&type=lead&include_mysaved=1&include_networks=ALL&numresults=100';
						soap_cookCreate('__bevoOLSearch',s,365);
						window.location = '/BevoMedia/Offers/Index.html';
					}
					return false;
				});
				</script>
				
			<?php } ?>			
		</div><!--close topdroplabel_quicksearch-->
		
		<?php //depreciated 110511 in favor of quick offer search
		/*<div class="topdroplabel topdroplabel_mentorship">
			<div class="topdroptoggle"></div>
			<a class="btn topdrop_mentorship_clickhere" href="/BevoMedia/Marketplace/MentorshipProgram.html">Click Here</a>
		</div>*/ ?>
		<div class="clear"></div>
	
	</div><!--close topdropbutt-->	
</div><!--close topdrop-->


<div id="body">
	<div id="wrap">
		<div id="header">
			<h1><a href="/BevoMedia/User/Index.html">BevoMedia - Your Internet Marketing Homebase</a></h1>
			
			<a class="btn headfeatapp_ppvspy" href="<?php //featured app of the month: if user is subscribed to it, link to it directly, else to appstore
			
								if(	
								(	$this->User->vaultID != 0
								&& 	(	$this->User->IsSubscribed(User::PRODUCT_PPVSPY_MONTHLY)
									||	$this->User->IsSubscribed(User::PRODUCT_PPVSPY_YEARLY)
									)
								)
								||	$freePPVSpy = $this->User->IsSubscribed(User::PRODUCT_FREE_PPVSPY)
								)
									echo '/BevoMedia/PPVSpy/Index.html';
								else	echo '/BevoMedia/User/AppStore.html'; 
							?>">PPV Spy</a>
			
			<div id="topmenu">
				<a class="btn topmenu_nyp<?php echo($this->PageHelper->Function == 'NameYourPayout'
									|| $this->PageHelper->Function == 'NameYourPayoutResult')?' active':''?>" href="/BevoMedia/Offers/NameYourPayout.html">Name Your Payout</a>
				<a class="btn topmenu_marketplace<?php echo($this->PageHelper->Area == 'Marketplace')?' active':''?>" href="/BevoMedia/Marketplace/">Marketplace</a>
				<a class="btn topmenu_topdroptoggle topdroptoggle<?php if($soap_topdrop_status) echo ' '.$soap_topdrop_status; ?>" href="#">Toggle Bevo Topdrop</a>
				<a class="btn topmenu_classroom<?php echo($this->PageHelper->Area == 'Classroom'
									|| $this->PageHelper->Function == 'KB')?' active':''?>" href="/BevoMedia/Publisher/Classroom.html">Classroom</a>
				<a class="btn topmenu_help topmenu_hassub<?php echo($this->PageHelper->Area == 'PPCTutorials')?' active':''?>" href="#topmenu_help">Help</a>
				<a class="btn topmenu_tools asa topmenu_hassub<?php echo($this->PageHelper->Function == 'ApiCalls'
									|| $this->PageHelper->Function == 'ManageStats')?' active':''?>" href="#topmenu_tools">Tools</a>
				<a class="btn topmenu_dashboard<?php echo($this->PageHelper->Area == 'Overview')?' active':''?>" href="/BevoMedia/User/Index.html">Dashboard</a>
				<a class="btn topmenu_myaccount<?php echo($this->PageHelper->Function == 'ChangeProfile'
									|| $this->PageHelper->Function == 'AccountInformation'
									|| $this->PageHelper->Function == 'AddCreditCard'
									|| $this->PageHelper->Function == 'CreditCard'
									|| $this->PageHelper->Function == 'Invoice'
									|| $this->PageHelper->Function == 'MyProducts')?' active':'';?>" href="/BevoMedia/User/ChangeProfile.html">My Account</a>
				<a class="btn topmenu_logout" href="/BevoMedia/User/Logout.html">Logout</a>
			</div>
			<div id="offersmenu">
				<a class="btn offersmenu_networks<?php echo(($this->PageHelper->Area == 'MyNetworks' && $this->PageHelper->Function == 'Index')
									|| $this->PageHelper->Function == 'Reviews')?' active':''?>" href="/BevoMedia/Publisher/Index.html">Networks</a>
				<a class="btn offersmenu_offervault<?php echo($this->PageHelper->Function == 'BestPerformers'
									|| ($this->PageHelper->Area == 'Offers' && $this->PageHelper->Function == 'Index') 
									|| $this->PageHelper->Function == 'MySavedLists')?' active':''?>" href="/BevoMedia/Offers/BestPerformers.html">Offers</a>
				<a class="btn offersmenu_stats<?php echo($this->PageHelper->Function == 'MyStats')?' active':''?>" href="/BevoMedia/Offers/MyStats.html">My Network Stats</a>
			</div>
			<!--<div id="setupmenu">
				<a class="btn setupmenu_networks<?php echo(($this->PageHelper->Area == 'MyNetworks' && $this->PageHelper->Function == 'Index')
									|| $this->PageHelper->Function == 'Reviews')?' active':''?>" href="/BevoMedia/Publisher/Index.html">Networks</a>
				<a class="btn setupmenu_offers<?php echo($this->PageHelper->Area == 'Offers')?' active':''?>" href="/BevoMedia/Offers/Index.html">Offers</a>
				<a class="btn setupmenu_selfhosted<?php echo($this->PageHelper->Function == 'SelfHostedLogin'
									|| $this->PageHelper->Function == 'RackspaceWizard'
									|| $this->PageHelper->Function == 'ServerScript'
									|| $this->PageHelper->Function == 'SelfHostedLoginDownload')?' active':''?>" href="/BevoMedia/User/SelfHostedLogin.html">Self-Hosted</a>
			</div>-->
			<div id="coremenu">
				<a class="btn coremenu_ppc<?php 
					echo($this->PageHelper->Function == 'PPCManager' //gotta find a better way to do all this, including the redundant containerclass below
					|| $this->PageHelper->Function == 'CreatePPC' 
					|| $this->PageHelper->Function == 'CreatePPCSubmit'
					|| $this->PageHelper->Function == 'CreatePPCSaved'
					|| $this->PageHelper->Function == 'PPCQueueProgress'
					|| $this->PageHelper->Function == 'AccountStatsPPC'
					|| $this->PageHelper->Function == 'AdwordsManualUpload'
					|| $this->PageHelper->Function == 'CampaignStatsPPC'
					|| $this->PageHelper->Function == 'AdGroupStatsPPC'
					|| $this->PageHelper->Function == 'AdGroupAdVariationsPPC')?' active':''?>" href="/BevoMedia/Publisher/PPCManager.html">PPC</a>
				<a class="btn coremenu_tracker<?php echo($this->PageHelper->Area == 'KeywordTracker')?' active':''?>" href="/BevoMedia/KeywordTracker/Overview.html">Tracker</a>
				<a class="btn coremenu_analytics<?php echo($this->PageHelper->Area == 'Analytics')?' active':''?>" href="/BevoMedia/Analytics/AnalyticsDetail.html">Analytics</a>
				<a class="btn coremenu_apps<?php echo($this->PageHelper->Controller == 'PPVTools' //this button is active for all tools
								|| $this->PageHelper->Controller == 'PPVSpy'
								|| $this->PageHelper->Controller == 'Geotargeting'
								|| $this->PageHelper->Controller == 'Timetargeting'
								|| $this->PageHelper->Function == 'AppStore') ?' active':''?>" href="/BevoMedia/User/AppStore.html">Apps</a>
				<div class="clear"></div>
			</div>
			
			
			<?php 
			//change nw of the month id here
			$NWotMid = 1052;
			
			$db = Zend_Registry::get('Instance/DatabaseObj');
			$query = "SELECT n.*, u.status FROM bevomedia_aff_network AS n LEFT JOIN bevomedia_user_aff_network AS u ON n.id = u.network__id AND u.user__id = {$this->User->id} WHERE n.model = 'CPA' AND n.isValid = 'Y' AND n.id = '$NWotMid' ORDER BY n.title";
			$NWotM = $db->fetchAll($query);
			$NWotM = $NWotM[0];
			
			$soap_sideadImg = $this->{'System/BaseURL'}.'Themes/'.$this->{'Application/Theme'}.'/sideads/'.$NWotM->id.'.png'; //check extension!
			
			$bhbutton_networkofmonth = array(
				'id' => $NWotM->id, 				//ID that is passed on to publisher-networkofmonth.php
				'anchor' => $NWotM->title,		//Anchor text of the link, also appears under the logo
				'img' => 'img/networklogos/uni/' . $NWotM->id .'.png',		//path-to-network-logo.jpg
				'signup_url' => $NWotM->signupUrl //not used anymore in the new layout
				);
			//end nwotm
			?>
			
			<div id="networkotm">
				<a href="/BevoMedia/Publisher/Index.html?ID=<?php echo $bhbutton_networkofmonth['id']; ?>" title="Click here to visit <?php echo $bhbutton_networkofmonth['anchor']; ?>"><img class="nwpic" src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/<?php echo $bhbutton_networkofmonth['img']; ?>" alt="<?php echo $bhbutton_networkofmonth['anchor']; ?>" /></a>
			</div>
			
			<ul id="topmenu_help" class="topmenusub">
				<li><a href="/BevoMedia/User/Forum.html">Support Forum</a></li>
				<li><a href="/BevoMedia/Publisher/PPCTutorials.html">Tutorials</a></li>
				<li><a href="/BevoMedia/KeywordTracker/Import202.html">Prosper202 Import</a></li>
				<li class="last"></li>
			</ul>
			<ul id="topmenu_tools" class="topmenusub">
				<li><a href="/BevoMedia/User/ApiCalls.html">API Call History</a></li>
				<li><a href="/BevoMedia/User/Referrals.html">Referrals</a></li>
				<li><a href="/BevoMedia/Publisher/PPCQueueProgress.html">Campaign Editor Queue</a></li>
				<li><a href="/BevoMedia/User/ManageStats.html">Delete Stats</a></li>
				<li><a href="http://ryanbuke.com/" target="_blank">Official Blog</a></li>
				<li class="last"></li>
			</ul>
		
		</div><!--close header-->

		
		
		
<?php /* ############################################################## CONTAINER */ ?>
		

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
	
	<?php if(isset($_GET['STEP'])):?>
	<script language="javascript">
		firstlogin.loadstep('<?php echo $_GET['STEP']?>');
	</script>
	<?php endif?>
	
	<?php if($this->PageHelper->Controller == 'KeywordTracker' && $this->User->vaultID == 0): ?>
	<script language="javascript">
	    $(document).ready(function() {
	        $('#pagemenu .li_geotargeting').click(function(){
		        var a = document.createElement('a');
		        a.href = '/BevoMedia/Publisher/VerifySelfHosted.html?ajax=true';
		        a.rel = 'shadowbox;width=640;height=480;player=iframe';
		        Shadowbox.open(a);

	            return false;
    	    });
        });
	</script>
	<?php endif; ?>
	
	<div class="content">
		<?=$this->{'Instance/ViewContent'};?>
		<div class="clear"></div>	
	</div><!--close content-->
	<div class="containerbutt"></div>
	
		<?php 
		
		//SIDE ADS: change NW ids here
		$sideAdIDright = 1052; //EWA
		$sideAdIDleft = 1059; //CPA Staxx
		
		//110401: added override to place a manual ad (BLAMads). if active, should be an array with 'img' (abspath to img location) and 'url' (http://).
		//only for "left" right now... $sideAds['left'] seems to have been removed from the remapping below, so we'll just refill that for now as a temp solution.
		$sideAdOverrideLeft = array(
			'img' => $this->{'System/BaseURL'}.'Themes/'.$this->{'Application/Theme'}.'/sideads/override/blamads_left.jpg',
			'url' => 'http://blamads.com/'
		);
		
		$db = Zend_Registry::get('Instance/DatabaseObj');
		$query = "SELECT n.*, u.status FROM bevomedia_aff_network AS n LEFT JOIN bevomedia_user_aff_network AS u ON n.id = u.network__id AND u.user__id = {$this->User->id} WHERE n.model = 'CPA' AND n.isValid = 'Y' AND n.id IN ('$sideAdIDleft','$sideAdIDright') ORDER BY n.title";
		$sideAds_raw = $db->fetchAll($query);
		
		//remap
		$sideAds = array();
		foreach($sideAds_raw as $k => $v) {
			if((int)$v->id == $sideAdIDright) //right
				$sideAds['right'] = $sideAds_raw[$k];
			
		}
		
		//temp: add $sideAds['left']
		if($sideAdOverrideLeft && is_array($sideAdOverrideLeft) && !empty($sideAdOverrideLeft)) {
			$sideAds['left'] = $sideAdOverrideLeft;	
		}
		
		if(!empty($sideAds)) {
			foreach($sideAds as $side => $ad) {
				
				/*TEMP 110401 start (remove later in favor of a scalable / dynamic solution)*/
				if($side == 'left' && !empty($ad)) {
					echo '<div class="sidead left">
					<a href="'.$ad['url'].'" title="Visit BLAM!Ads.com" target="_blank"><img src="'.$ad['img'].'" alt="" /></a>
					</div>';
				} else {
				/*END TEMP 110401. be sure to remove the } on line 652! */
				
				//add ad banner image
				$sideAds[$side]->ad_image = $this->{'System/BaseURL'}.'Themes/'.$this->{'Application/Theme'}.'/sideads/'.$ad->id.'.png';
				
				echo '<div class="sidead '.$side.'">';
				
				if(empty($ad->status) || $ad->status != 3) {
					if(isset($_GET['ID'])) {
						if($_GET['ID'] == $ad->id) {
						?>
						
						<script language='javascript'>
							window.onload = function(){
								// open a welcome message as soon as the window loads
								Shadowbox.open({
									content:    '/BevoMedia/Publisher/ApplyAdd.html?network=<?php print $ad->id; ?>',
									player:     "iframe",
									title:      "<?php print htmlentities($ad->title)?>",
									height:     480,
									width:      640
								});
							};
						</script>
						
						<?php
						}
					} ?>
					
					<a href="/BevoMedia/Publisher/ApplyAdd.html?network=<?php print $ad->id?>" title="<?php print htmlentities($ad->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe"><img src="<?php echo $ad->ad_image; ?>" alt="" /></a>
					
				<?php } else { //if ad status
					
					if(isset($_GET['ID'])) {
						if($_GET['ID'] == $ad->id) {
						?>
						
						<script language='javascript'>
							window.onload = function(){
								// open a welcome message as soon as the window loads
								Shadowbox.open({
									content:    '/BevoMedia/Publisher/EditNetwork.html?network=<?php print $ad->id; ?>',
									player:     "iframe",
									title:      "<?php print htmlentities($ad->title); ; ?>",
									height:     480,
									width:      640
								});
							};
						</script>
						
						<?php
						}
					} ?>
					
					<a href="/BevoMedia/Publisher/EditNetwork.html?network=<?php print $ad->id; ; ?>" title="Edit account details for <?php print htmlentities($ad->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe"><img src="<?php echo $ad->ad_image; ?>" alt="" /></a>
				
				<?php } //endif status
				
				echo '</div>'; //close div.sidead.right/left
				
				/*TEMP 110401*/
				}
				/*END TEMP 110401*/
				
			} //endforeach sideads
		} //endif sideads exist
		?>
		
</div><!--close container-->

<div id="footer">
	<ul id="footermenu">
		<li><a href="/BevoMedia/User/Index.html">Home</a></li>
		<li><a href="/PrivacyPolicyBevoMedia.html">Privacy Policy</a></li>
		<li><a href="/TermsOfServiceBevoMedia.html">Terms Of Service</a></li>
		<li><a href="http://beta.bevomedia.com/CopyrightPolicy.html">Copyright Policy</a></li>
		<li><a href="/SitemapBevoMedia.html">Sitemap</a></li>
		<li><a href="/AboutBevoMedia.html">About Bevo</a></li>
		<li><a href="/FaqsBevoMedia.html">FAQ</a></li>
		<li><a href="/CareersBevoMedia.html">Careers</a></li>
		<li><a href="/PressBevoMedia.html">Press</a></li>
		<li><a href="/InvestorsBevoMedia.html">Investors</a></li>
		<li><a href="/AdNetworksBevoMedia.html">Affiliate Networks</a></li>
	</ul>
	<p>Copyright &copy; <?php echo date('Y'); ?> Bevo Media LLC. San Diego, CA 92130<p>

</div><!--close footer-->
</div><!--close wrap-->
</div><!--close body-->


<script type="text/javascript">
$(document).ready(function () {
	if ($('#datepicker').length)
	{
		$('#datepicker').daterangepicker();
	}
});
</script>

<!--[if lt IE 7]><div id="srykthxbai">We're sorry, but your browser is too old for BevoMedia.<br />This application requires a modern browser to run smoothly and safely.<br />Please upgrade your browser.<br />Thank you.</div><![endif]-->
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
