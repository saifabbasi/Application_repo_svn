<?php

//require(PATH . 'include/include.php');
//require(PATH . 'include/include_parseyahoo.php');
require(PATH . 'classes/clsYahooAccounts.php');

global $strAction, $userId, $intAccountID;
$userId = $this->User->id;
$strAction = false;


if(isset($_POST['Action']))
	$strAction = $_POST['Action'];
if(isset($_POST['AccountID']))
	$intAccountID = $_POST['AccountID'];

	

if (isset($_POST['Action']) && $_POST['Action']=='Upload')
{
	UploadReport();

}


function UploadReport() 
{
	global $userId, $intAccountID, $strErr, $blnParsed; 
	$intAccountID = $_POST['AccountID'];
	$FileName = $_FILES['report']['tmp_name'];
	
	if (strtolower(substr($_FILES['report']['name'], strlen($_FILES['report']['name'])-3, 3)) == 'zip') 
	{
		$strReport = ExtractReport($_FILES['report']['tmp_name']);
		$FileName = str_replace("\\", "/", sys_get_temp_dir())."/".md5(time().date("u"));
		file_put_contents($FileName, $strReport);
	}
	else 
	{
		$strReport = file_get_contents($_FILES['report']['tmp_name']);
	}
	
	global $DateImported;
	global $intAccountID;
	
	$DateImported = ImportYahooAdwords($FileName, $intAccountID);
	
	return true;

	// $arrUser = array('ID' => $intAccountID,
						// 'user__id' => $userId);

	// if (strlen($strReport) < 1) {
		// return false;
	// }
	
	// if (!CheckDateRange($strReport)) {
		// $strErr = 'Error: You must choose a date range of only one date to accurately record stats.';
		// return false;
	// }
	
	// echo $strReport;

	// ParseReport($arrUser, $strReport);
	// $blnParsed = true;
}

function ExtractReport($strInFile) 
{
	$objZip = zip_open($strInFile);
	
	if (!$objZip) 
	{
		return false;
	}
	
	$strContents = '';
	
	while ($objEntry = zip_read($objZip)) 
	{
		$strName =  zip_entry_name($objEntry);
		
		// Look for the Keyword Performance XML 
		if (strpos($strName, 'Keyword-Performance') !== false) 
		{
			// Ignore Non XML Reports
			if (substr($strName, strlen($strName)-3, 3) != 'xml') 
			{
				continue;
			}
			
			if (zip_entry_open($objZip, $objEntry)) 
			{
				$strContents = zip_entry_read($objEntry, zip_entry_filesize($objEntry)); 
				zip_entry_close($objEntry);
			}
		}
	}
	
	zip_close($objZip);
	return $strContents;
}

function CheckDateRange($strInXML) {
	if (strlen($strInXML) < 1) {
		return false;
	}
	
	$objXML = simplexml_load_string($strInXML);
		
	if (!$objXML) {
		return false;
	}
		
	// Get Report Date, Assume 1 Day and use StartDate as StatDate
	$arrReportAtts = $objXML->attributes();
		
	$strDateStart = (string) $arrReportAtts['dateStart'];
	$strDateEnd = (string) $arrReportAtts['dateEnd'];
	
	if (date('Y-m-d', strtotime($strDateStart)) != date('Y-m-d', strtotime($strDateEnd))) {
		return false;
	}
	else {
		return true;
	}
}

function ImportYahooAdwords($FileName, $AccountID)
{
	//$Sql = "SELECT ID, user__id FROM adwords_accounts WHERE ID = '{$AccountID}'; ";
	$Sql = "SELECT id, user__id FROM bevomedia_accounts_yahoo WHERE id = '{$AccountID}'; ";

	$user__id = mysql_query($Sql) or die(mysql_error());
	if (mysql_num_rows($user__id)==0)
	{
		die ("The e-mail is not registered.");
	} else
	{
		$user__id = mysql_fetch_assoc($user__id);
		$AccountID = $user__id['id'];
		$user__id = $user__id['user__id'];		
	}
	
	$XML = simplexml_load_file($FileName);
	
	$d = preg_replace('/(-|\+)\d{4}$/', '', (string)$XML->attributes()->dateStart);
	$StatDate = date("Y-m-d", strtotime($d));
	
	$DeletedCombinations = array();
	foreach ($XML->row as $Row)
	{
		$Analytics = $Row->analytics;
		if($Row->attributes()->tacticName == 'Content Match')
			continue;
			
		//campaign
		$CampaignName = (string)$Row->attributes()->cmpgnName;
		$CampaignName = mysql_real_escape_string($CampaignName);
		if(empty($CampaignName))
		  break;
		$Sql = "SELECT ID FROM bevomedia_ppc_campaigns WHERE (user__id = {$user__id}) AND (Name = '{$CampaignName}') AND (ProviderType = 2) AND (accountId = {$AccountID})";
		$CampaignID = mysql_query($Sql) or die(mysql_error());
		if (mysql_num_rows($CampaignID)==0)
		{		
			$Sql = "INSERT INTO bevomedia_ppc_campaigns (user__id, ProviderType, AccountID, Name, Updated) VALUES ({$user__id}, 2, {$AccountID}, '{$CampaignName}', 0 ); ";
			mysql_query($Sql) or die(mysql_error());
			$CampaignID = mysql_insert_id();
		} else
		{
			$CampaignID = mysql_fetch_assoc($CampaignID);
			$CampaignID = $CampaignID['ID'];
		}
		//campaign

		//adgroup
		$AdGroupName = (string)$Row->attributes()->adGrpName;
		$AdGroupName = mysql_real_escape_string($AdGroupName);
		
		$Sql = "SELECT ID FROM bevomedia_ppc_adgroups WHERE (CampaignID = {$CampaignID} ) AND (Name = '{$AdGroupName}') ; ";
		$AdGroupID = mysql_query($Sql) or die(mysql_error());
		if (mysql_num_rows($AdGroupID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_adgroups (CampaignID, Name, Updated) VALUES ({$CampaignID}, '{$AdGroupName}', 0)  ";
			mysql_query($Sql) or die(mysql_error());
			$AdGroupID = mysql_insert_id();
		} else
		{
			$AdGroupID = mysql_fetch_assoc($AdGroupID);
			$AdGroupID = $AdGroupID['ID'];
		}
		//adgroup
		
		
		//keyword
		$Keyword = strtolower((string)$Row->attributes()->keywordName);
		$Keyword = mysql_real_escape_string($Keyword);
		
		
		$Sql = "SELECT ID FROM bevomedia_keyword_tracker_keywords WHERE (Keyword = '{$Keyword}'); ";
		$KeywordID = mysql_query($Sql) or die(mysql_error());
		if (mysql_num_rows($KeywordID)==0)
		{
			$Sql = "INSERT INTO bevomedia_keyword_tracker_keywords (Keyword) VALUES ('{$Keyword}'); ";
			mysql_query($Sql) or die(mysql_error());
			$KeywordID = mysql_insert_id();
		} else
		{
			$KeywordID = mysql_fetch_assoc($KeywordID);
			$KeywordID = $KeywordID['ID'];
		}
		//keyword
		
		//bevomedia_ppc_keywords
		$MaxCPC = 0;
		$DestURL = (string)$Row->attributes()->url;
		$Updated = 0;
		$Status = 1;
		
		if ($DestURL=='default URL') $DestURL = '';
		
		$Sql = "SELECT ID FROM bevomedia_ppc_keywords WHERE (AdGroupID = {$AdGroupID}) AND (KeywordID = {$KeywordID})"; 
		$PPCKeywordID = mysql_query($Sql) or die(mysql_error());
		if (mysql_num_rows($PPCKeywordID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_keywords (AdGroupID, KeywordID, MaxCPC, DestURL, Updated, Status) 
									  VALUES ({$AdGroupID}, {$KeywordID}, {$MaxCPC}, '{$DestURL}', {$Updated}, {$Status}); ";
			mysql_query($Sql) or die(mysql_error());
			$PPCKeywordID = mysql_insert_id();
		} else
		{
			$PPCKeywordID = mysql_fetch_assoc($PPCKeywordID);
			$PPCKeywordID = $PPCKeywordID['ID'];
			
			$Sql = "UPDATE bevomedia_ppc_keywords SET MaxCPC = {$MaxCPC}, DestURL = '{$DestURL}', Updated = 1, Status = {$Status} WHERE ID = {$PPCKeywordID} ";
			mysql_query($Sql) or die(mysql_error());
		}
		//bevomedia_ppc_keywords

		
		
		//bevomedia_ppc_keywords_stats
		if (!in_array(array($StatDate, $AdGroupID, $CampaignID), $DeletedCombinations))
		{
			$Sql = "SELECT 
						bevomedia_ppc_keywords_stats.ID
					FROM 
						`bevomedia_ppc_keywords_stats`,
						`bevomedia_ppc_keywords`,
						`bevomedia_ppc_campaigns`,
						`bevomedia_ppc_adgroups`,
						`bevomedia_keyword_tracker_keywords`
					WHERE 
						(bevomedia_ppc_keywords.id = bevomedia_ppc_keywords_stats.KeywordID) AND
						(bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.ID) AND
						(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
						(bevomedia_ppc_keywords.KeywordID = bevomedia_keyword_tracker_keywords.ID) AND
						(bevomedia_ppc_keywords_stats.StatDate = '$StatDate') AND
						(bevomedia_ppc_keywords.AdGroupID = {$AdGroupID}) AND
						
						(bevomedia_ppc_campaigns.ID = {$CampaignID})
					"; 
			$DeleteResults = mysql_query($Sql) or die(mysql_error());
			while ($DeleteRow = mysql_fetch_assoc($DeleteResults))
			{
				$Sql = "DELETE FROM bevomedia_ppc_keywords_stats WHERE ID = {$DeleteRow['ID']} ";
				mysql_query($Sql) or die(mysql_error());
			}
			
			$DeletedCombinations[] = array($StatDate, $AdGroupID, $CampaignID);
		}

		
		
		
		$Impressions = (string)$Analytics->attributes()->numImpr;
		$Clicks = (int)$Analytics->attributes()->numClick;
		$CPC = (float)$Analytics->attributes()->cpc;
		$CPM = 0;
		$Cost = (float)$Analytics->attributes()->cost;
		$Pos = (float)$Analytics->attributes()->averagePosition;
		
		
		$Sql = "SELECT ID, Cost FROM bevomedia_ppc_keywords_stats WHERE KeywordID = {$PPCKeywordID} AND StatDate = '{$StatDate}'";
		$PPCStateKeywordID = mysql_query($Sql) or die(mysql_error());
		if (mysql_num_rows($PPCStateKeywordID)==0)
		{
			$Sql = "INSERT INTO bevomedia_ppc_keywords_stats (KeywordID, Impressions, Clicks, CPC, CPM, Cost, Pos, StatDate) 
									  VALUES ({$PPCKeywordID}, {$Impressions}, {$Clicks}, {$CPC}, {$CPM}, {$Cost}, {$Pos}, '{$StatDate}'); ";
			mysql_query($Sql) or die(mysql_error());
			$PPCStateKeywordID = mysql_insert_id();
		} else
		{
			$PPCStateKeywordID = mysql_fetch_assoc($PPCStateKeywordID);
			
			$PPCStateKeywordID = $PPCStateKeywordID['ID'];
			
			$Sql = "UPDATE bevomedia_ppc_keywords_stats SET KeywordID = {$PPCKeywordID}, Impressions = {$Impressions}, Clicks = {$Clicks}, 
											CPC = {$CPC}, CPM = {$CPM}, Cost = {$Cost}, Pos = {$Pos}, StatDate = '{$StatDate}'
										WHERE ID = {$PPCStateKeywordID} ";
			mysql_query($Sql) or die(mysql_error());
		}
		//bevomedia_ppc_keywords_stats
	}
	return $StatDate;
}


function ListReportDates($AccountID)
{
	global $userId;
	$Sql = "SELECT
				DISTINCT(bevomedia_ppc_keywords_stats.StatDate) AS `Date`
			FROM
				bevomedia_accounts_yahoo,
				bevomedia_ppc_campaigns,
				bevomedia_ppc_adgroups,
				bevomedia_ppc_keywords,
				bevomedia_ppc_keywords_stats
			WHERE
				(bevomedia_ppc_campaigns.user__id = $userId) AND
				(bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID) AND
				(bevomedia_ppc_keywords.AdGroupID = bevomedia_ppc_adgroups.ID) AND
				(bevomedia_ppc_keywords_stats.KeywordID = bevomedia_ppc_keywords.ID) AND
				(bevomedia_ppc_campaigns.ProviderType = 2) AND
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


?>


<style>

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

<?php /* ##################################################### OUTPUT ############### */ ?>
<div id="pagemenu">
		<ul>
			<li><a class="active" href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
			<li><a href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<?php 
	global $DateImported;
	if ($DateImported!="") 
	{ 
		
?>
	<br />
	<p align="center">Your stats for <?=date("m/d/Y", strtotime($DateImported))?> has been uploaded and should now appear on your Bevo interface.</p>
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

	<form action="?ID=<?=$_GET['ID']?>" method="post" enctype="multipart/form-data">
		<div align="center">
			<input type="hidden" name="AccountID" value="<?=$_GET['ID']?>" />
			<input type="hidden" name="Action" value="Upload" />
			<table>
			  <tr>
				<td><label for="report">Report:</label></td>
				<td><input type="file" name="report" id="report"/></td>
			  </tr>
			  <tr>
				<td colspan="2" style="text-align: center;"><input class="formsubmit ppc_uploadreport" type="submit" name="submit" value="Upload Report"/></td>
			  </tr>
			</table>
		</div>
	</form>
</div>