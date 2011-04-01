<?php

//require(PATH . 'include/include.php');
//require(PATH . 'include/include_parseadwordsreport.php');
require(PATH . 'classes/clsAdwordsAccounts.php');

global $strAction;
global $intAccountID;
global $userId;

$userId = $this->User->id;

$strAction = $intAccountID = false;
if(isset($_GET['Action']))
	$strAction = $_GET['Action'];
	
if(isset($_GET['ID']))
	$intAccountID = $_GET['ID'];

if (!is_numeric($intAccountID)) {
	$intAccountID = 0;
}

if (strtoupper($strAction) == 'UPLOAD') {
	UploadReport();
}

if (isset($_POST['submit']))
{
	global $ReportDateRange, $userId;
	$ReportDateRange = ImportGoogleAdwords($_FILES['report']['tmp_name'], $userId, $_POST['AccountID']);
}

	function ImportGoogleAdwords($FileName, $UserID, $AccountID)
	{

			$XML = simplexml_load_file($FileName);

			$ReportDateRange = (string)$XML->{'date-range'}->attributes()->date;
			
			
			$count = 0;
			$UpdatedStatIDs = array();
			$DeletedDates = array();
			$DeletedAdGroup = array();
			$DeletedCampaigns = array();
			$DeletedCombinations = array();
			foreach ($XML->table->row as $Row)
			{
				//campaign
				$CampaignName = (string)$Row->attributes()->campaign;
				$CampaignName = mysql_real_escape_string($CampaignName);
				
				$Sql = "SELECT id FROM bevomedia_ppc_campaigns WHERE (user__id = {$UserID}) AND (AccountID = {$AccountID}) AND (Name = '{$CampaignName}')  AND (ProviderType = 1) ";
				$CampaignID = mysql_query($Sql);
				if (mysql_num_rows($CampaignID)==0)
				{
					$Sql = "INSERT INTO bevomedia_ppc_campaigns (user__id, ProviderType, AccountID, Name, Updated) VALUES ({$UserID}, 1, {$AccountID}, '{$CampaignName}', 0 ); ";
					mysql_query($Sql);
					$CampaignID = mysql_insert_id();
				} else
				{
					$CampaignID = mysql_fetch_assoc($CampaignID);
					$CampaignID = $CampaignID['id'];
				}
				//campaign
				
				//adgroup
				$AdGroupName = (string)$Row->attributes()->adGroup;
				$AdGroupName = mysql_real_escape_string($AdGroupName);
				
				$Sql = "SELECT id FROM bevomedia_ppc_adgroups WHERE (CampaignID = {$CampaignID} ) AND (Name = '{$AdGroupName}') ; ";
				$AdGroupID = mysql_query($Sql);
				if (mysql_num_rows($AdGroupID)==0)
				{
					$Sql = "INSERT INTO bevomedia_ppc_adgroups (CampaignID, Name, Updated) VALUES ({$CampaignID}, '{$AdGroupName}', 0)  ";
					mysql_query($Sql);
					$AdGroupID = mysql_insert_id();
				} else
				{
					$AdGroupID = mysql_fetch_assoc($AdGroupID);
					$AdGroupID = $AdGroupID['id'];
				}
				//adgroup
				
				//keyword
				$Keyword = strtolower((string)$Row->attributes()->kwSite);
				if ($Keyword=="")
				{
					$Keyword = strtolower((string)$Row->attributes()->keyword);
				}
				
				if (strstr($Keyword, '[') && strstr($Keyword, ']'))
				{
					$MatchType = 2;
				} else
				if (strstr($Keyword, '&quot;') || strstr($Keyword, '"'))
				{
					$MatchType = 1;
				} else
				{
					$MatchType = 0;
				}
				
				$Keyword = str_replace('&quot;', '', $Keyword);
				$Keyword = str_replace('"', '', $Keyword);
				$Keyword = str_replace('[', '', $Keyword);
				$Keyword = str_replace(']', '', $Keyword);
				
				
				$Keyword = mysql_real_escape_string($Keyword);
				
				$Sql = "SELECT id FROM bevomedia_keyword_tracker_keywords WHERE (Keyword = '{$Keyword}'); ";
				$KeywordID = mysql_query($Sql);
				if (mysql_num_rows($KeywordID)==0)
				{
					$Sql = "INSERT INTO bevomedia_keyword_tracker_keywords (Keyword) VALUES ('{$Keyword}'); ";
					mysql_query($Sql);
					$KeywordID = mysql_insert_id();
				} else
				{
					$KeywordID = mysql_fetch_assoc($KeywordID);
					$KeywordID = $KeywordID['id'];
				}
				
				
				
				
				
				//keyword
				
				//ppc_keywords
				
				
//				$MatchType = (string)$Row->attributes()->kwSiteType;
//				if ($MatchType=="")
//				{
//					$MatchType = (string)$Row->attributes()->kwType;
//				}
//				if ($MatchType=="Phrase") $MatchType = 1; else
//				if ($MatchType=="Exact") $MatchType = 2; else
//				$MatchType = 0;
				
				$Status = (string)$Row->attributes()->status;
				
				if (stristr($Status, "Active")) $Status = 1; else
				if (stristr($Status, "Paused")) $Status = 2; else
				$Status = 0;
				
				$MaxCPC = ((string)$Row->attributes()->maxCPC)/1000000;
				$DestURL = '';
				$Updated = 0;
				
				
				
				$Sql = "SELECT id FROM bevomedia_ppc_keywords WHERE (AdGroupID = {$AdGroupID}) AND (KeywordID = {$KeywordID})";
				$PPCKeywordID = mysql_query($Sql);
				if (mysql_num_rows($PPCKeywordID)==0)
				{
					$Sql = "INSERT INTO bevomedia_ppc_keywords (AdGroupID, KeywordID, MatchType, Status, MaxCPC, DestURL, Updated)
											  VALUES ({$AdGroupID}, {$KeywordID}, {$MatchType}, {$Status}, {$MaxCPC}, '{$DestURL}', {$Updated}); ";
											  
					mysql_query($Sql) or die(mysql_error());
					$PPCKeywordID = mysql_insert_id();
				} else
				{
					$PPCKeywordID = mysql_fetch_assoc($PPCKeywordID);
					$PPCKeywordID = $PPCKeywordID['id'];
					
					$Sql = "UPDATE bevomedia_ppc_keywords SET MatchType = {$MatchType}, Status = {$Status}, MaxCPC = {$MaxCPC}, DestURL = '{$DestURL}', Updated = 1 WHERE ID = {$PPCKeywordID} ";
					mysql_query($Sql);
				}
				//ppc_keywords
				
								

				
				
				//ppc_keywords_stats
				$Impressions = (string)$Row->attributes()->impressions;
				$Clicks = (int)$Row->attributes()->clicks;
				$CPC = (float)$Row->attributes()->avgCPC; ///1000000;
				$CPM = (float)$Row->attributes()->avgCPC/1000000;
				$Cost = round((float)$Row->attributes()->cost, 2);
				$Cost = number_format($Cost, 2, ".", "");
				$Pos = (float)$Row->attributes()->avgPosition;
				$StatDate = (string)$Row->attributes()->day;
				$StatDate = date("Y-m-d", strtotime($StatDate));

//				$StatDate = date("Y-m-d");
				
				if ($StatDate=="1969-12-31")
				{
					print_r($Row);
					die("Wrong date");
				}
				
				//if ( (!in_array($StatDate, $DeletedDates)) || (!in_array($AdGroupID, $DeletedAdGroup)) || (!in_array($CampaignID, $DeletedCampaigns)) )
				if (!in_array(array($StatDate, $AdGroupID, $CampaignID), $DeletedCombinations))
				{
					$Sql = "SELECT
								bevomedia_ppc_keywords_stats.id as id
							FROM
								`bevomedia_ppc_keywords_stats`,
								`bevomedia_ppc_keywords`,
								`bevomedia_ppc_campaigns`,
								`bevomedia_ppc_adgroups`,
								`bevomedia_keyword_tracker_keywords`
							WHERE
								(bevomedia_ppc_keywords.ID = bevomedia_ppc_keywords_stats.KeywordID) AND
								(bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.ID) AND
								(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
								(bevomedia_ppc_keywords.KeywordID = bevomedia_keyword_tracker_keywords.ID) AND
								(bevomedia_ppc_keywords_stats.StatDate = '$StatDate') AND
								(bevomedia_ppc_keywords.AdGroupID = {$AdGroupID}) AND
								
								(bevomedia_ppc_campaigns.ID = {$CampaignID})
							";
					$DeleteResults = mysql_query($Sql); //if (mysql_num_rows($DeleteResults)>0) echo "Deleted Rows: ".mysql_num_rows($DeleteResults)."<br />";
					while ($DeleteRow = mysql_fetch_assoc($DeleteResults))
					{
						$Sql = "DELETE FROM bevomedia_ppc_keywords_stats WHERE id = {$DeleteRow['id']} ";
						mysql_query($Sql);
					}
					
					$DeletedDates[] = $StatDate;
					$DeletedAdGroup[] = $AdGroupID;
					$DeletedCampaigns[] = $CampaignID;
					$DeletedCombinations[] = array($StatDate, $AdGroupID, $CampaignID);
				}
				
				

				$Sql = "SELECT
							bevomedia_ppc_keywords_stats.id as id
						FROM
							`bevomedia_ppc_keywords_stats`,
							`bevomedia_ppc_keywords`,
							`bevomedia_ppc_campaigns`,
							`bevomedia_ppc_adgroups`,
							`bevomedia_keyword_tracker_keywords`
						WHERE
							(bevomedia_ppc_keywords.ID = bevomedia_ppc_keywords_stats.KeywordID) AND
							(bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.ID) AND
							(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
							(bevomedia_ppc_keywords.KeywordID = bevomedia_keyword_tracker_keywords.ID) AND
							(bevomedia_ppc_keywords_stats.StatDate = '$StatDate') AND
							(bevomedia_ppc_keywords.AdGroupID = {$AdGroupID}) AND
							(bevomedia_ppc_keywords.KeywordID = {$KeywordID}) AND
							(bevomedia_ppc_campaigns.ID = {$CampaignID}) AND
							(bevomedia_ppc_keywords_stats.KeywordID = {$PPCKeywordID}) AND
							(bevomedia_ppc_campaigns.AccountID = {$AccountID})
						";
				$PPCStateKeywordID = mysql_query($Sql);
				if ( (mysql_num_rows($PPCStateKeywordID)==0)  )
				{
					$Sql = "INSERT INTO bevomedia_ppc_keywords_stats (KeywordID, Impressions, Clicks, CPC, CPM, Cost, Pos, StatDate)
											  VALUES ({$PPCKeywordID}, {$Impressions}, {$Clicks}, {$CPC}, {$CPM}, {$Cost}, {$Pos}, '{$StatDate}'); ";

					mysql_query($Sql);
					$PPCStateKeywordID = mysql_insert_id();
					$UpdatedStatIDs[] = $PPCStateKeywordID;
				} else
				{
					$PPCStateKeywordID = mysql_fetch_assoc($PPCStateKeywordID);
					$PPCStateKeywordID = $PPCStateKeywordID['id'];
					
					if (in_array($PPCStateKeywordID, $UpdatedStatIDs))
					{
						$Sql = "INSERT INTO bevomedia_ppc_keywords_stats (KeywordID, Impressions, Clicks, CPC, CPM, Cost, Pos, StatDate)
												  VALUES ({$PPCKeywordID}, {$Impressions}, {$Clicks}, {$CPC}, {$CPM}, {$Cost}, {$Pos}, '{$StatDate}'); ";

						mysql_query($Sql);
						$PPCStateKeywordID = mysql_insert_id();
						$UpdatedStatIDs[] = $PPCStateKeywordID;
					} else
					{
						$Sql = "UPDATE bevomedia_ppc_keywords_stats SET KeywordID = {$PPCKeywordID}, Impressions = {$Impressions}, Clicks = {$Clicks},
														CPC = {$CPC}, CPM = {$CPM}, Cost = {$Cost}, Pos = {$Pos}, StatDate = '{$StatDate}'
													WHERE ID = {$PPCStateKeywordID} ";
						mysql_query($Sql);
						$UpdatedStatIDs[] = $PPCStateKeywordID;
					}
				}
				//ppc_keywords_stats
				
				$count++;
			}
			
			return $ReportDateRange;
	}


function ListReportDates($AccountID)
{
	global $userId;
	$Sql = "SELECT
				DISTINCT(bevomedia_ppc_keywords_stats.StatDate) AS `Date`
			FROM
				bevomedia_accounts_adwords,
				bevomedia_ppc_campaigns,
				bevomedia_ppc_adgroups,
				bevomedia_ppc_keywords,
				bevomedia_ppc_keywords_stats
			WHERE
				(bevomedia_ppc_campaigns.user__id = $userId) AND
				(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
				(bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.ID) AND
				(bevomedia_ppc_keywords_stats.KeywordID = bevomedia_ppc_keywords.ID) AND
				(bevomedia_ppc_campaigns.ProviderType = 1) AND
				(bevomedia_ppc_campaigns.AccountID = $AccountID)
			";
	$Rows = mysql_query($Sql);
	$Dates = array();
	
	if (mysql_num_rows($Rows))
	{
		while ($Row = mysql_fetch_assoc($Rows))
			$Dates[] = date("Y/m/d", strtotime($Row['Date']));
	}
	
	return $Dates;
}

function UploadReport() {
	global $userId, $intAccountID;
	$intAccountID = $_POST['AccountID'];
	
	if (!is_numeric($userId) || !is_numeric($intAccountID)) {
		return false;
	}

	if ($_FILES['report']['error'] > 0) {
		return false;
	}
	
	// Only Accept Text/XML Uploads
	$arrAcceptedTypes = array('text/xml', 'text/plain');
	if (!in_array($_FILES['report']['type'], $arrAcceptedTypes)) {
		return false;
	}
	
	if ($_FILES['report']['size'] > 50000) {
//		return false;
	}

	$arrAdwordsUser = array('ID' => $intAccountID,
						'UserID' => $userId);
	
	$strReport = file_get_contents($_FILES['report']['tmp_name']);

	if (strlen($strReport) < 1) {
		return false;
	}

	ParseReport($arrAdwordsUser, $strReport);
	$blnParsed = true;
}
?>

<!-- calendar start -->
		<link rel="stylesheet" type="text/css" media="all" href="style/calendar-blue.css" />

		<!-- calendar end -->
<style>
#instructions img {
	border: 1px solid grey;
	margin: 2px;
	padding: 2px;
}
#calendar table
{
	border-collapse:collapse;
}
#calendar td
{
	border:1px solid #CCC;
	font:normal 11px sans-serif;
	padding:2px;
	text-align:center;
}
.a
{
	font:normal 11px sans-serif;
}
.t1, .t2
{
	padding:5px;
	border:1px solid #9EB9C6;
}
.t1
{
	width:40%;
	text-align:right;
	background-color:#EEEEEE;
	font-weight:bold;
}
.t2
{
	width:60%;
	text-align:left;
}
.ti
{
	background:transparent url(/images/th.gif) repeat-x scroll left top;
	height:17px;
}
.r1, .r2
{
	padding:7px;
}
.r1{

}
.r2{
	background-color:#EEE;
}
.r2 input{
	/*border-width:0px;*/
}
.m_link
{
	text-align:left;
	border:1px solid transparent;
	margin-left:-2px;
	padding-left:10px;
	cursor:pointer;
}
.m_link_sel
{
	background-color:CornflowerBlue;
	color:white;
}
.cal_calendar {
				font-size:8pt;
				font-family:sans-serif;padding:0px;margin:0px;border:none; border-collapse:collapse;
				border:1px solid #EEE;
			}
			.cal_header {
			/*	background-color:#CCCCCC;padding:0px;margin:0px;border:none; border-collapse:collapse;*/
			}
			.cal_cell {
				/*padding:2px;margin:1px;border:2px groove;text-align:center;width:3ex*/
				
			}
			.cal_cell, .cal_labelcell {
				/*padding:2px;margin:1px;border:2px groove;text-align:center;*/
				background-color:#CCCCCC;
				border:1px solid #EEEEEE;
				color:black;
				font-size:8pt;
				text-align:center;
			}
			.cal_oddweek {
				background-color:#FFF;padding:0px;margin:0px;border:none; border-collapse:collapse;
			}
			.cal_evenweek{
				background-color:#EEE;padding:0px;margin:0px;border:none; border-collapse:collapse;
			}

			.cal_day {
				cursor:default;
				width:3ex;text-align:center;
				padding:10px;
				margin:0px;border:none; border-collapse:collapse;
				/*cursor:pointer;*/
			}
			.day{
				background-color:MediumSeaGreen;
				/*font-weight:bold;*/
				text-align:center;
				cursor:pointer;
				color:white;
				border:1px solid green;
			}
			.cal_today {color:black;font-weight:bold;width:3ex;padding:0px;margin:0px;border:none; border-collapse:collapse;}
			.cal_disabled {color:#999999;width:3ex;padding:0px;margin:0px;border:none; border-collapse:collapse;}
			.cal_common {color:black;width:3ex;padding:0px;margin:0px;border:none; border-collapse:collapse;}
			.cal_holiday {color:red;width:3ex;padding:0px;margin:0px;border:none; border-collapse:collapse;}
			.cal_event {
				background-color:yellow;color:red;width:3ex;padding:0px;margin:0px;border:none; border-collapse:collapse;
			}
			.cal_b,.cal_f
			{
				cursor:pointer;
				font-weight:bold;
				font-size:15pt;
			}
			.day .cal_event
			{
				background-color:green;
			}
			.ces
			{
				background-color:green;
				font-weight:bold;
				font-weidht:17px;
			}
			.pd{
				padding:3px;
			}
</style>
<script type="text/javascript">
var c = false;
var hide_m=function()
{
	if(c!==false)
	{
		var a = 'mentor_id_'+c;
		var b = 'mentor_link_'+c;
		var x=$(a);
		x.style.display='none';
		var y=$(b);
		y.className='m_link';
	}
	
}
var oe=false;
var show_m=function(m)
{
	hide_m();
	var a = 'mentor_id_'+m;
	var b = 'mentor_link_'+m;
	var x=$(a);
	x.innerHTML = 'dadada';
	x.style.display='';
	var y=$(b);
	y.innerHTML = 'dadada';
	y.className='m_link m_link_sel';
	c=m;
	shh(1);
};
var $=function(i)
{
	return document.getElementById(i);
};
var ssh_hide=function()
{
	if(oe!==false)
	{
		var x=$(oe);
		x.style.display='none';
	}
}
var shh=function(id)
{
	ssh_hide();
	var i='mentor_'+c+'_'+id;
	if($(i))
	{
		var g = $(i);
		g.style.display='';
		oe=i;
	}
}
window.onload=function()
{
	show_m(0);
};
</script>

<?php /* ##################################################### OUTPUT ############### */ ?>
<div id="pagemenu">
		<ul>
			<li><a class="active" href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
			<li><a href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
<?php
	global $ReportDateRange;
	if ($ReportDateRange!="")
	{
		
?>
	<br />
	<p align="center">Your stats for <?=$ReportDateRange?> have been uploaded and should now appear on your Bevo interface.</p>
<?php
	}
?>


<br />


<script language='JavaScript'>
var tb = 't'; // top or bottom (t or b)
var headbg = '#cccccc';  // table heading background colour
var todaybg = '#99cccc'; // current selected date background colour
var textclr = '#000000'; // text colour
var linkclr = '#ff9999'; // link text colour
var noMessage =  'No report for today'; // message to display when no entry in array

var dA = new Array(); var x = 0;
// first 8 characters in ccyymmdd format for single date events
// first 8 characters in 0000mmdd format for every year events

<?
	$Dates = ListReportDates($_GET['ID']);
	$i = 1;
	foreach ($Dates as $Date)
	{
?>
	dA[x++] = "<?=date("Ymd", strtotime($Date))?> Report for <?=date("m/d/Y", strtotime($Date))?>";
<?
	}
?>



// permission is granted to use this javascript provided that the below code is not altered
var pageLoaded = 0; window.onload = function() {pageLoaded = 1;}
function loaded(i,f) {if (document.getElementById && document.getElementById(i) != null) f(); else if (!pageLoaded) setTimeout('loaded(\''+i+'\','+f+')',100);
}
function monthlength(month,year) {var dd = new Date(year, month, 0);return dd.getDate();}
var moy = ['January','February','March','April','May','June','July','August','September','October','November','December'];var today = new Date();var selDate = today.getFullYear()+getmmdd(today.getMonth()+1,today.getDate());
function dispCal(yy,mm) {if (mm < 0 || mm > 12) {alert('month must be between 1 and 12'); return false;} if (yy != 0 && (yy < 1901 || yy > 2100)) {alert('year must be after 1900 and before 2101'); return false;} var dow = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; var calendar = new Date();  var today = calendar.getDate(); calendar.setDate(1); if (yy > 1900) calendar.setFullYear(yy); if (mm > 0) calendar.setMonth(mm - 1); var yy = year = calendar.getFullYear(); var month = calendar.getMonth(); calendar.setDate(today); var weekday = calendar.getDay(); var daysInMonth = monthlength(month+1,year); var hilite_start = '<td width="30" style="background:' + todaybg + '" align="center"><b>'; var td_start = '<td width="30" align="center">'; var cal = '<div id="cal"><div style="border:1pt solid #cccccc;height:150px;width:238px"><table border="0" cellspacing="0" cellpadding="2" align="center"><tr><td colspan="7" style="background:' + headbg + '" align="center"><b>' + moy[month]  + ' ' + year + '<\/b><\/td><\/tr><tr>'; for(dex=0; dex < 7; dex++) {cal += td_start + dow[dex] + '</td>';} cal += '<\/tr><tr>'; var day2 = today; for (dex = today; dex > 6; dex -=7) day2 = dex; weekday -= day2 - 1; while (weekday < 0) weekday += 7; for(dex=0; dex < weekday; dex++) cal += td_start + ' <\/td>'; for(dex=1; dex <= daysInMonth; dex++) {if(weekday == 7) {cal += '</tr><tr>'; weekday = 0;} if(selDate==year+getmmdd(month+1,dex)) cal += hilite_start +'<span '+clickDate(dex,month,year) + '>'+ dex + '<\/span><\/b><\/td>'; else cal += td_start + '<span '+clickDate(dex,month,year) + '>' + dex + '<\/span><\/td>'; weekday += 1;} for(dex=weekday; dex < 7; dex++) cal += td_start + ' <\/td>'; cal += '<\/tr><\/table><\/div>';if (document.getElementById) {var mmb = month;  mm = month + 1; var yya = yyb = yy; if (mmb <1) {mmb += 12; yyb--;} var mma = month + 2; if (mma > 12) {mma -= 12; yya++;} var yb = yy -1; var ya = yy +1; cal += '<table border="0" cellspacing="0" cellpadding="2" width="210"><tr><td><a href="#" onclick="if (cala = dispCal('+yb+','+mm+')) {document.getElementById(\'cal\').innerHTML = cala; return false;}"><<</a></td><td><a href="#" onclick="if (cala = dispCal('+yyb+','+mmb+')) {document.getElementById(\'cal\').innerHTML = cala; return false;}"><</a></td><td align="right"><a href="#" onclick="if (cala = dispCal('+yya+','+mma+')) {document.getElementById(\'cal\').innerHTML = cala; return false;}">></a></td><td align="right"><a href="#" onclick="if (cala = dispCal('+ya+','+mm+')) {document.getElementById(\'cal\').innerHTML = cala; return false;}">>></a></td></tr></table>';} else {cal += '<div> </div>';} cal += '</div>'; return cal;}
function start() {var x = '<div id="calDate" style="border:1pt solid #cccccc;width:238px"><\/div>'; var y = ''; if (tb == 't') y = x + dispCal(0,0); else y = dispCal(0,0) + x; document.getElementById('calendar').innerHTML = y; ev();}
loaded('calendar',start);
function clickDate(day, month, year) {var ct = nextDate(year + getmmdd(month+1,day));if (ct == '') ct = nextDate('0000' + getmmdd(month+1,day));if (ct == '') return 'style="color:'+textclr+'"'; else return 'style="cursor:pointer;color:'+linkclr+'" onclick="selDate = '+year+ getmmdd(month+1,day)+'; isDate(' + day + ',' + month + ',' + year + ');return false;"';}function isDate(dayVal,monthVal,yearVal) {var ct = nextDate(yearVal + getmmdd(monthVal+1,dayVal));if (ct == '') ct = nextDate('0000' + getmmdd(monthVal+1,dayVal));if (ct == '') ct = noMessage;document.getElementById('calDate').innerHTML = selDate + ':<br \/>' +ct; return false;}function nextDate(yymmdd) {var x = dA.length;for (var i = 0; i < x; i++) {if (dA[i].substr(0,8) == yymmdd) return dA[i].substr(8);}return '';}function getmmdd(mm,dd) {return (mm > 9 ? '' + mm : '0' + mm) + (dd > 9 ? dd : '0' + dd);}
function ev() {var ct = nextDate(selDate);var ct = nextDate(selDate);if (ct == '') ct = nextDate('0000' + selDate.substr(4));if (ct == '') ct = noMessage; document.getElementById('calDate').innerHTML = selDate + ':<br \/> ' +ct;}


</script>

<div align="center">
	<div id="calendar"></div>


	<br /><br />

	<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="AccountID" value="<?=$_GET['ID']?>"/>
	<table border="0" style="border: 0px;">
	  <tr style="border: 0px;">
		<td style="border: 0px;"><label for="report">Report:</label></td>
		<td style="border: 0px;"><input type="file" name="report" id="report"/></td>
	  </tr>
	  <tr style="border: 0px;">
		<td colspan="2" style="text-align: center; border: 0px;"><input class="formsubmit ppc_uploadreport" type="submit" name="submit" value="Upload Report"/></td>
	  </tr>
	</table>
	</form>

</div>