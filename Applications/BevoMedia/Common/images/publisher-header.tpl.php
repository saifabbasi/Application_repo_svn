<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
	<head>
		<title><?=$pageTitle==""?SCRIPT_COMPANY_NAME." - Affiliate Network Consolidation and Affiliate Education - CPA, CPC and CPM Networks":$pageTitle?></title>
		<meta name="keywords"			content="<?=$pageKeywords==""?" ads network script, adbrite clone, adengage clone ":$pageKeywords?>">
		<meta name="description"		content="<?=$pageDescription==""?"Start your own ads network script. Supports text ads, photo ads and banners.":$pageDescription?>">
		<meta name="ROBOTS"				content="ALL">
		<meta name="GOOGLEBOT"			content="INDEX, FOLLOW">
		<meta name="revisit-after"		content="2 weeks">

		<meta http-equiv="Content-Type" content="text/html; charset=<? define('DEFAULT_CHARSET', 'iso-8859-1'); echo DEFAULT_CHARSET; ?>">
		<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/ajax_content.js"></script>
		<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/kValidate.js"></script>

		<? include PATH.'header-charts-include.php'; ?>
		
		<script language="Javascript" src="<?=SCRIPT_ROOT?>style/ColorPicker2.js"></script>
		<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/commons.js.php?scriptRoot=<?=urlencode(SCRIPT_ROOT)?>"></script>
		<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/popwin.js"></script>
		<link href="<?=SCRIPT_ROOT?>style/popwin.css" rel="stylesheet" type="text/css">
		
		<script language="JavaScript">
		<!--
		//***********************************************
		function getConfirmation()
		{ 
			var temp; 
			
			temp = confirm("<?=ARE_YOU_SURE?>");
			return temp;
		}
		//***********************************************
		function getConfirmation2(msg)
		{ 
			var temp; 
			
			temp = confirm(msg);
			return temp;
		}
		//***********************************************
		function mOver()
		{
			this.className = 'TabOver';
		}
		//***********************************************
		function mOut()
		{
			this.className = 'Tab';
		}
		//***********************************************
		//-->
		</script>
		<link rel="shortcut icon" href="<?=SCRIPT_ROOT?>images/favicon.ico">
		<link href="<?=SCRIPT_ROOT?>style/style.css" rel="stylesheet" type="text/css">
		<link href="<?=SCRIPT_ROOT?>style/main.css" rel="stylesheet" type="text/css">

		<!-- calendar start -->
		<link rel="stylesheet" type="text/css" media="all" href="<?=SCRIPT_ROOT?>style/calendar-blue.css" />
		<script type="text/javascript" src="<?=SCRIPT_ROOT?>style/calendar.js"></script>
		<script type="text/javascript" src="<?=SCRIPT_ROOT?>style/calendar-en.js"></script>
		<script type="text/javascript" src="<?=SCRIPT_ROOT?>style/calendar-setup.js"></script>
		<!-- calendar end -->
        <script language="javascript">
function check(){
var e = document.getElementById("nwAll");
e.checked = false;
}
function un_check(){
  for (var i = 0; i < document.frm.elements.length; i++) {
    var e = document.frm.elements[i];
    if ((e.id != 'nwAll' && e.id != 'ttAll'&&e.id != 'ttWeb'&&e.id != 'ttSrh'&&e.id != 'ttEml'&&e.id != 'ttInc') && (e.type == 'checkbox')) {
e.checked = false;
    }
  }
}
function check1(){
var e = document.getElementById("ttAll");
e.checked = false;
}
function un_check1(){
  for (var i = 0; i < document.frm.elements.length; i++) {
    var e = document.frm.elements[i];
    if (e.id == 'ttWeb'||e.id == 'ttSrh' || e.id == 'ttEml' || e.id == 'ttInc') {
e.checked = false;
    }
  }
}
</script>

	</head>

	<body>

	<div id="divPopWin" class="PopWin" style="display:none;">
		<div class="PopWinLeft"><div class="PopWinRight"><div class="PopWinTop"><div class="PopWinBot"><div class="PopWinTopLeft"><div class="PopWinTopRight"><div class="PopWinBotLeft"><div class="PopWinBotRight">
		<div class="PopWinContainer">
			<table width="98%" height="92%" cellspacing="0" cellpadding="3" border="0">
				<tr>
					<td colspan="2" align="right"><div id="divPopWinTopClose"><a href="javascript:hidePop();"><img src="<?=SCRIPT_ROOT?>images/close_window.gif" width="100" height="20" border=0 alt=""></a></div></td>
				</tr>
				<tr valign="top">
					<td><div id="divPopWinIcon"></div></td>
					<td width="100%"><div id="divPopWinData" style="font-weight:bold;"></div></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<div id="divPopWinButtons"><input type="button" name="btnClosePopWin" value="<?=_CLOSE?>" class="baseeffect" onClick="hidePop();"></div>
					</td>
				</tr>
			</table>
		</div>
		</div></div></div></div></div></div></div></div>
	</div>

	<div id="divFullBody" style="width:100%; min-height:100%; min_height:100%; text-align:center;">

		<div class="Header">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
				<tr valign="top">
					<td><img src="<?=SCRIPT_ROOT?>images/logo.gif" width="359" height="131" border=0 alt="" style="margin:20px; margin-left:100px;"></td>
					<td>
						<br>
						<a href="<?=SCRIPT_ROOT?>"><img src="<?=SCRIPT_ROOT?>images/home_icon.jpg" height="17" border="0" width="16"></a>
						<img src="<?=SCRIPT_ROOT?>images/spacer.gif" width="10" height="10" border=0 alt="">
						<a href="<?=SCRIPT_ROOT?>contactus.php"><img src="<?=SCRIPT_ROOT?>images/contact_icon.jpg" height="17" border="0" width="17"></a>
						<img src="<?=SCRIPT_ROOT?>images/spacer.gif" width="10" height="10" border=0 alt="">
						<a href="#"><img src="<?=SCRIPT_ROOT?>images/sitemap_icon.jpg" height="15" border="0" width="16"></a>
						<img src="<?=SCRIPT_ROOT?>images/spacer.gif" width="10" height="10" border=0 alt="">
						<a href="logout.php"><img src="<?=SCRIPT_ROOT?>images/logout_icon.jpg" height="20" border="0" width="24" title="Logout" align="absbottom"></a>
					</td>
				</tr>
			</table>
		</div>
		
		<div class="TabBar">
			<!--<div style="float:left; width:50px;">&nbsp;</div>--> 
			<div class="Tab<?=$isOverviewPage?"Over":""?>"><a href="<?=SCRIPT_ROOT?>welcome.php"><?=OVERVIEW?></a></div>
			<div class="Tab<?=$isMyNetworksPage?"Over":""?>"><a href="<?=SCRIPT_ROOT?>publisher-mynetworks.php"><?=MY_NETWORKS?></a></div>
			<div class="Tab<?=$isOffersPage?"Over":""?>"><a href="<?=SCRIPT_ROOT?>publisher-offers.php"><?=CODES_OFFERS?></a></div>
			<div class="Tab<?=$isClassroomPage?"Over":""?>"><a href="<?=SCRIPT_ROOT?>publisher-classroom.php"><?=CLASSROOM?></a></div>
			<div class="Tab<?=$isSubReportPage?"Over":""?>"><a href="<?=SCRIPT_ROOT?>publisher-subreport.php"><?=SUB_REPORT?></a></div>
			<div class="Tab<?=$isAccInfoPage?"Over":""?>"><a href="<?=SCRIPT_ROOT?>publisher-acc-info.php"><?=ACCOUNT_INFO?></a></div>
            <div class="Tab<?=$isTrackerPage?"Over":""?>"><a href="<?=SCRIPT_ROOT?>publisher-tracker.php">Tracker</a></div>
			<div class="Tab<?=$isSupportPage?"Over":""?>"><a href="<?=SCRIPT_ROOT?>support.php"><?=SUPPORT?></a></div>
		</div>

		<div class="MainArea">
		
			<div class="Stack">
			
				<div class="StackRight">

				<script type="text/javascript">
				function sch()
				{
					var ht='';
					<?php
									$oDate = new DateTime($recMentor['AVAILABILITY']);
									$sDate = $oDate->format("m/d/y g:i A");
									$ta    = (time()<strtotime($sDate))?true:false;
									$ma	= (!$ta)?'Unavailable':'Available';
									echo "ht+='<div style=\"text-align:center;\"><h2>Schedule a One on One Consulting Session</h2>';";
									echo "ht+='<br><br>Price per Session:<br><font class=\"main\"><b>".getFormattedPrice(((float)$recMentor['PRICE_SESSION']))."</b></font><br>';";
									echo "ht+='<br>Availability:<br><font class=\"main\"><h2 style=\"color:".((!$ta)?'red':'#95EF97')."\">". $oDate->format("F j, Y, g:i a")."</h2></font><br>';";									
									echo "ht+='Status:<h3>{$ma}</h3></div><br>';";
									echo "ht+='<div style=\"text-align:center;\"><input type=\"button\" onclick=\"doUpdate(this.form,\"\", 0)\" class=\"baseeffect\" value=\"Accept\" name=\"Apply\"/><input type=\"button\" onclick=\"hidePop();return false;\" class=\"baseeffect\" value=\"Cancel\"/></div>';";
										/*echo "<h3>Schedule a One on One Consulting Session</h3>";
										echo "<br><br>Price per Session:<br><font class=''main'><b>".getFormattedPrice(((float)$recMentor['PRICE_SESSION']))."</b></font><br>";
										echo "<br>Availability:<br><font class=''main'><a href='#'><h2 style='color:".((!$ta)?'red':'#95EF97')."'>".$sDate."</h2></a></font><br>";
										echo "Status:<h3>{$ma}</h3>"*/
								?>
					//showPop(ht,300,300);					
					var h = '<iframe src="scheduler.php?a=<?=$recMentor['ID']?>&c=<?=$_SESSION['userId']?>" noresize="noresize" frameborder="0" border="0" cellspacing="0" width="100%" marginwidth="0" marginheight="0" height="600"></iframe>';
						h+='<br><br><div style=\"text-align:center;\"><a href="#" onclick="hidePop();return false;">Close</a></div>';
					showPop(h,850,650);
				}
				</script>
					<table cellspacing="5" cellpadding="0" border="0">
						<tr>
							<td>
								<div class="BlueBox">
									<? if ( is_array($recMentor) ) {
										//print_r($recMentor);										
										?>
									<h3>Your Mentor Contact Info:</h3>
									<br><br>
									<h5>
										<b><?=$recMentor['FIRST_NAME'].' '.$recMentor['LAST_NAME']?></b>
										<br><br>
										AIM: <?=$recMentor['AIM']?><br>
										Email: <?=$recMentor['EMAIL']?><br>
										<?=$recMentor['PHONE']?>
									</h5>
									<br>
									Earnings:<br>
									<font class="main">Today's: <?=LegacyAbstraction::getFormattedPrice($userTodays)?></font>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<font class="main">MTD: <?=LegacyAbstraction::getFormattedPrice($userMTD)?></font><br><br>
									<a style="color:white;" href="#" onclick="sch();return false;"><b>Schedule a One on One Consulting Session</b></a>
									<? 
																	
									
									} else { ?>
									<? } ?>
								</div>								
							</td>
						</tr>
						<? if ( $showSearchOffers ) { ?>
						<tr>
							<td>
				<form method=get action="<?=SCRIPT_ROOT?>publisher-offers-search.php" name="frm">
				<table width="100%" cellspacing="1" cellpadding="5" border="0" class="table">
					<tr>
						<th colspan="2" class="GridBlankRow"></th>
					</tr>
					<tr>
						<td class="GridSubHead" colspan="2" style="text-align:center;"><?=OFFER_SEARCH_TOOL?></td>
					</tr>
					<tr>
						<td class="GridRowCol"><?=CATEGORY?>: </td>
					</tr>
					<tr>
						<td class="GridRowCol">
							<?=LegacyAbstraction::getCombobox("categoryId", "MCATEGORIE", "ID_MCATEGORIE", "MCATEGORIE", $categoryId, " MCATEGORIE<>'N/A' ", "MCATEGORIE", ALL_CATEGORIES, "style='width:210px;'")?>
						</td>
					</tr>
					<tr>
						<td class="GridRowCol"><?=COUNTRIES_ALLOWED?>: </td>
					</tr>
					<tr>
						<td class="GridRowCol">
							<?=LegacyAbstraction::getCombobox("countryId", "MCOUNTRY", "ID_MCOUNTRY", "MCOUNTRY", $countryId, " MCOUNTRY NOT IN ('N/A','ALL') ", "", 'All countries')?>
						</td>
					</tr>
					<tr>
						<td class="GridRowCol"><?=SEARCH?>: </td>
					</tr>
					<tr>
						<td class="GridRowCol">
							<input type="text" size="30" name="title" class="effect">
						</td>
					</tr>
					<tr valign="top">
						<td class="GridRowCol"><?=TRAFFIC_TYPE?>: </td>
					</tr>
					<tr>
						<td class="GridRowCol">
							<input type="checkbox" id="ttAll" name="trafficType[]" value="" checked onclick="un_check1()"><label for="ttAll"><?=SHOW_ALL?></label>
							<br>
							<input type="checkbox" id="ttWeb" name="trafficType[]" value="W" onclick="check1()"><label for="ttWeb"><?=WEB_BANNER?></label>
							<input type="checkbox" id="ttSrh" name="trafficType[]" value="S" onClick="check1()"><label for="ttSrh"><?=SEARCH?></label>
							<br>
							<input type="checkbox" id="ttEml" name="trafficType[]" value="E" onClick="check1()"><label for="ttEml"><?=EMAIL?></label>
							<input type="checkbox" id="ttInc" name="trafficType[]" value="I" onClick="check1()"><label for="ttInc"><?=INCENTIVE?></label>
						</td>
					</tr>
					<tr valign="top">
						<td class="GridRowCol"><?=NETWORK?>: </td>
					</tr>
					<tr>
						<td class="GridRowCol">
							<input type="checkbox" id="nwAll" name="network[]" value="" checked onclick="un_check()"><label for="nwAll"><?=ALL_NETWORKS?></label>
<?
				$count = 0;
				$idArray = '';
				foreach ( $arrNetsJoined as $network )
				{
					if ( $network['MODEL'] != 'CPA' )
						continue;

					$idArray .= ', "'.$network['ID'].'"';
					if ( $count++%2 == 0 )
						echo '<br>';
?>
							<input type="checkbox" id="nw<?=$network['ID']?>" name="network[]" value="<?=$network['ID']?>" onclick="check()"><label for="nw<?=$network['ID']?>"><?=$network['TITLE']?></label>
<?
				}
?>
							<script language="JavaScript">
							<!--
							var nwIds = new Array(<?=substr($idArray, 2)?>);
							//-->
							</script>
						</td>
					</tr>

					<tr valign="top">
						<td class="GridRowHead" colspan="2">
							<input type="submit" value="Search" class="baseeffect">
							<input type="reset" value="Default" class="baseeffect">
						</td>
					</tr>

					<tr>
						<td class="GridFoot" colspan="2"></td>
					</tr>
				</table>
				</form>
							</td>
						</tr>
						<? } ?>
						<? if ( $showNewNetworkBox ) { ?>
						<tr>
							<td>
								<div class="LightBlueBoxTop">
									<h5>
										<br>
										<br>
										To Add a network not listed
										<br>
										<br>
										<br>
									</h5>
									<a href="javascript:showAddNewNetwork();"><img src="<?=SCRIPT_ROOT?>images/<?=$langFolder?>/btn-click-here.gif" width="73" height="29" border=0 alt=""></a>
								</div>
								<div class="LightBlueBoxBot"></div>
							</td>
						</tr>
						<? } ?>
						<? if ( $showRightPanes ) { ?>
						<tr>
							<td>
								<div class="LightBlueBoxTop">
									<h5>
										<br>
										Need Programming Help On<br>Your Site?
										<br><br>
										Ask Our On Staff Tech<br>Advisor�s
									</h5>
									<a href="javascript:showSendNeedProgHelp();"><img src="<?=SCRIPT_ROOT?>images/<?=$langFolder?>/btn-click-here.gif" width="73" height="29" border=0 alt=""></a>
								</div>
								<div class="LightBlueBoxBot"></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="LightBlueBoxTop">
									<h5>
										<br>
										Free SEO Service
										<br><br>
										Apply for our FREE SEO<br>service. 
									</h5>
									<a href="free-seo.php"><img src="<?=SCRIPT_ROOT?>images/<?=$langFolder?>/btn-click-here.gif" width="73" height="29" border=0 alt=""></a>
								</div>
								<div class="LightBlueBoxBot"></div>
							</td>
						</tr>
						<? } ?>
						<? if ( $showOffersPanes ) { ?>
						<tr>
							<td>
								<div class="LightBlueBoxTop">
									<h5>
										<br>
										<br>
										To learn more about the AdOptimizer
										<br>
										<br>
										<br>
									</h5>
									<a href="<?=SCRIPT_ROOT?>publisher-adoptimizer-desc.php"><img src="<?=SCRIPT_ROOT?>images/<?=$langFolder?>/btn-click-here.gif" width="73" height="29" border=0 alt=""></a>
								</div>
								<div class="LightBlueBoxBot"></div>
							</td>
						</tr>
						<? if ( $hideTipsPane == '' ) { ?>
						<tr>
							<td>
								<div class="LightBlueBoxTop">
									<h5>
										<br>
										<br>
										Tips on finding a good offer
										<br>
										<br>
										<br>
									</h5>
									<a href="<?=SCRIPT_ROOT?>publisher-tips-on-finding-good-offers.php"><img src="<?=SCRIPT_ROOT?>images/<?=$langFolder?>/btn-click-here.gif" width="73" height="29" border=0 alt=""></a>
								</div>
								<div class="LightBlueBoxBot"></div>
							</td>
						</tr>
						<? } ?>
						<tr>
							<td>
								<div class="LightBlueBoxTop">
									<h5>
										<br>
										<br>
										Update your default ad
										<br>
										<br>
										<br>
									</h5>
									<a href="javascript:showUpdateDefaultAds();"><img src="<?=SCRIPT_ROOT?>images/<?=$langFolder?>/btn-click-here.gif" width="73" height="29" border=0 alt=""></a>
								</div>
								<div class="LightBlueBoxBot"></div>
							</td>
						</tr>
						<? } ?>
					</table>
					
				</div>

				<div class="StackLeft">

