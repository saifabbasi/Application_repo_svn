<?php
require_once(PATH . "Legacy.Abstraction.class.php");
require(PATH . 'classes/clsAdwordsAccounts.php');
require(PATH . 'classes/clsYahooAccounts.php');
require(PATH . 'classes/clsMSNAccounts.php');
require(PATH . 'classes/clsPPCKeywordStats.php');

//*************************************************************************************************

if(Zend_Registry::get('Application/Mode') != 'SelfHosted')
{
	include PATH.'images/charts.php';
	//include ("session.php");
}
        
global $userId, $isSelfHosted;
$userId = $this->User->id;
$isSelfHosted = $this->User->IsSelfHosted();

$isPpcManagerPage = true;
//******************************************************************************************



if(!isset($_GET['DateRange']))
	$_GET['DateRange'] = $this->DefaultDateRange;
require(PATH . 'inc_daterange.php');
 
function ListAdwordsAccounts() {
	global $userId;
	
	$objAdwords = new AdwordsAccounts();
	$objAdwords->GetListByUserID($userId);
	
	if ($objAdwords->RowCount == 0) {
?>
  <tr>
    <td class="border">&nbsp;</td>
    <td colspan="5">No accounts found.
					    <a class="tbtn" title="Google Adwords" href="Index.html#PPC">
						    Add Account...
						</a></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	
	$arrStats = LoadAccountStats(1);
	$blnAltRow = false;
	while ($arrThisRow = $objAdwords->GetRow()) {
		if(!isset($arrStats[$arrThisRow['ID']]))
			$arrThisStats = false;
		else
			$arrThisStats = $arrStats[$arrThisRow['ID']];
					
		if (!is_array($arrThisStats)) {
			$arrThisStats['NetImpr'] = 0;
			$arrThisStats['NetClicks'] = 0;
			$arrThisStats['AvgCPC'] = 0;
			$arrThisStats['AvgCPM'] = 0;
			$arrThisStats['NetCost'] = 0;
			$arrThisStats['AvgPos'] = 0;
		}
				
		if ($arrThisStats['NetImpr'] != 0) {
			$arrThisStats['NetCtr'] = round($arrThisStats['NetClicks'] / $arrThisStats['NetImpr'], 2);
		}
		else {
			$arrThisStats['NetCtr'] = 0;
		}
		
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td class="border">&nbsp;</td>
    <td><a href="AccountStatsPPC.html?Provider=Adwords&amp;ID=<?php echo $arrThisRow['ID']?><?=(LegacyAbstraction::$strDateRangeVal)?>"><?php echo htmlspecialchars($arrThisRow['AdwordsEmail']); ?></a></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetImpr']; ?></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetClicks']; ?></td>
	<td style="text-align: center;"><?php echo round($arrThisStats['NetCtr'] *100 , 2); ?>%</td>
	<td style="text-align: center;">$<?php echo number_format($arrThisStats['NetCost'],2); ?></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
	
	if ($objAdwords->RowCount > 0)
	{
?>
				  <tr>
					<td class="border">&nbsp;</td>
					<td colspan="5">
					    <a class="tbtn" title="Google Adwords" href="Index.html#PPC">
						    Add Account...
						</a></td>
					<td class="tail">&nbsp;</td>
				  </tr>

<?
	}
}

function ListYahooAccounts() {
	global $userId;
	
	$objYahoo = new YahooAccounts();
	$objYahoo->GetListByUserID($userId);
	
	
	if ($objYahoo->RowCount == 0) {
?>
  <tr>
    <td class="border">&nbsp;</td>
    <td colspan="5">No accounts found.
					    <a class="tbtn" title="Yahoo Search Marketing" href="Index.html#PPC">
						    Add Account...
						</a></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	$blnAltRow = false;
	$arrStats = LoadAccountStats(2);
		
	while ($arrThisRow = $objYahoo->GetRow()) {
		if(!isset($arrStats[$arrThisRow['ID']]))
			$arrThisStats = false;
		else
			$arrThisStats = $arrStats[$arrThisRow['ID']];
		
		if (!is_array($arrThisStats)) {
			$arrThisStats['NetImpr'] = 0;
			$arrThisStats['NetClicks'] = 0;
			$arrThisStats['AvgCPC'] = 0;
			$arrThisStats['AvgCPM'] = 0;
			$arrThisStats['NetCost'] = 0;
			$arrThisStats['AvgPos'] = 0;
		}
				
		if ($arrThisStats['NetImpr'] != 0) {
			$arrThisStats['NetCtr'] = round($arrThisStats['NetClicks'] / $arrThisStats['NetImpr'], 2);
		}
		else {
			$arrThisStats['NetCtr'] = 0;
		}
		
		
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td class="border">&nbsp;</td>
    <td><a href="AccountStatsPPC.html?Provider=Yahoo&amp;ID=<?php echo $arrThisRow['ID']?><?=(LegacyAbstraction::$strDateRangeVal)?>"><?php echo htmlspecialchars($arrThisRow['Username']); ?></a></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetImpr']; ?></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetClicks']; ?></td>
	<td style="text-align: center;"><?php echo round($arrThisStats['NetCtr'] * 100, 2); ?>%</td>
	<td style="text-align: center;">$<?php echo number_format($arrThisStats['NetCost'],2); ?></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
	
	if ($objYahoo->RowCount > 0)
	{
?>
				  <tr>
					<td class="border">&nbsp;</td>
					<td colspan="5">
					    <a class="tbtn" title="Yahoo Search Marketing" href="Index.html#PPC">
						    Add Account...
						</a></td>
					<td class="tail">&nbsp;</td>
				  </tr>

<?
	}
	
	
}

function ListMSNAccounts() {
	global $userId;
	
	$objMSN = new MSNAccounts();
	$objMSN->GetListByUserID($userId);
	
	if ($objMSN->RowCount == 0) {
?>
  <tr>
    <td class="border">&nbsp;</td>
    <td colspan="5">No accounts found.
					    <a class="tbtn" title="MSN Ad Center" href="Index.html#PPC">
						    Add Account...
						</a></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	
	$arrStats = LoadAccountStats(3);
	
	while ($arrThisRow = $objMSN->GetRow()) {
		if(!isset($arrStats[$arrThisRow['ID']]))
			$arrThisStats = false;
		else
			$arrThisStats = $arrStats[$arrThisRow['ID']];
		
		if (!is_array($arrThisStats)) {
			$arrThisStats['NetImpr'] = 0;
			$arrThisStats['NetClicks'] = 0;
			$arrThisStats['AvgCPC'] = 0;
			$arrThisStats['AvgCPM'] = 0;
			$arrThisStats['NetCost'] = number_format(0,2);
			$arrThisStats['AvgPos'] = 0;
		}
				
		if ($arrThisStats['NetImpr'] != 0) {
			$arrThisStats['NetCtr'] = round($arrThisStats['NetClicks'] / $arrThisStats['NetImpr'], 2);
		}
		else {
			$arrThisStats['NetCtr'] = 0;
		}
		

		$blnAltRow = true;
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td class="border">&nbsp;</td>
    <td><a href="AccountStatsPPC.html?Provider=MSN&amp;ID=<?php echo $arrThisRow['ID']?><?=(LegacyAbstraction::$strDateRangeVal)?>"><?php echo htmlspecialchars($arrThisRow['Name']); ?></a></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetImpr']; ?></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetClicks']; ?></td>
	<td style="text-align: center;"><?php echo round($arrThisStats['NetCtr'] * 100, 2); ?>%</td>
	<td style="text-align: center;">$<?php echo number_format($arrThisStats['NetCost'],2); ?></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
	
	
	if ($objMSN->RowCount > 0)
	{
?>
				  <tr>
					<td class="border">&nbsp;</td>
					<td colspan="5">
					
					    <a class="tbtn" title="MSN Ad Center" href="Index.html#PPC">
						    Add Account...
						</a></td>
					<td class="tail">&nbsp;</td>
				  </tr>

<?
	}
	
}

function LoadAccountStats($intInProvider) {
	global $userId;
	
	$objStats = new PPCKeywordStats();
	$objStats->GetStatsByProvider($userId, $intInProvider, LegacyAbstraction::$strStartDateVal, LegacyAbstraction::$strEndDateVal);
	
	if ($objStats->RowCount == 0) {
		return false;
	}
	
	$arrStats = array();
	
	while ($arrThisRow = $objStats->GetRow()) {
		$arrStats[$arrThisRow['accountId']] = $arrThisRow;
	}
	
	return $arrStats;
}

		// Include Date Range JS/CSS Includes
		$strPageHead = '<script type="text/javascript" src="' . SCRIPT_ROOT . 'js/jquery-1.3.1.min.js"></script>
		<script type="text/javascript" src="' . SCRIPT_ROOT . 'js/jquery-ui-1.7.1.custom.min.js"></script>
		<script type="text/javascript" src="' . SCRIPT_ROOT . 'js/daterangepicker.jQuery.js"></script>
		<link rel="stylesheet" href="' . SCRIPT_ROOT . 'css/ui.daterangepicker.css" type="text/css" />
		<link rel="stylesheet" href="' . SCRIPT_ROOT . 'css/redmond/jquery-ui-1.7.1.custom.css" type="text/css" title="ui-theme" />
		<script type="text/javascript">
			$(function(){
				  $(\'#datepicker\').daterangepicker();
			 });
		</script>';
        
//        include(PATH.'templates/publisher-ppcmanager.tpl.php');
?>
<?php if(!isset($_GET['DateRange'])){LegacyAbstraction::$strDateRangeVal = '';}else{LegacyAbstraction::$strDateRangeVal = '&amp;DateRange=' . LegacyAbstraction::$strDateRangeVal;}?>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<div id="pagemenu">
		<ul>
			<li><a class="active" href="/BevoMedia/Publisher/PPCManager.html">Overview<span></span></a></li>
			<li><a id="CampaignEditorLink" href="/BevoMedia/Publisher/CreatePPC.html">Campaign Editor<span></span></a></li>
		</ul>
	</div>

	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<script type="text/javascript">
	$(function(){
		//make some charts
		$('#JQueryChartData').visualize({type: 'line'}).appendTo('#JQueryChartDisplay');
	});
</script>

<?php
	$DateRange = date('m/j/Y', strtotime('TODAY -1 DAYS')) . '-' . date('m/j/Y', strtotime('TODAY -1 DAYS'));
	if(isset($_GET['DateRange']))
		$DateRange = $_GET['DateRange'];

	$ChartXML = new ChartXMLHelper();
	$ChartXML->SetDateRange($DateRange);
	
	$ChartXML->LoadPPCManagerStats($this->User->id);
	$Out = $ChartXML->getJQueryChartOutput('PPC Statistics', 'JQueryChartData', 'JQueryChartDisplay', '', '0');
	echo $Out;
?>

<?php //echo InsertChart ( '_PPCManagerStatsChartXML.html?ID='. $this->User->ID . '&DateRange=' . LegacyAbstraction::$strDateRangeVal, 600, 225, 'ffffff' );
?>

<div class="clear"></div>

<div align="right">
<form method="get" action="" name="frmRange">
<table align="right" cellspacing="0" cellpadding="0" class="datetable">
  <tr>
    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php print isset($_GET['DateRange'])?$_GET['DateRange']:$this->defaultDateRange; ; ?>" /></td>
	<td><input class="formsubmit" type="submit" /></td>
  </tr>
</table>
</form>
</div>

<div style="clear: both; height: 5px; overflow: clip;">&nbsp;</div>

<center>
<div class="left_box2_bg">
                 <table border="0" cellspacing="0" cellpadding="0" width="600" class="btable">
                    <tr class="table_header">
					  <td class="hhl"><!-- --></td>
                      <td style="text-align: center;">Account</td>
                      <td style="text-align: center;">Impressions</td>
					  <td style="text-align: center;">Clicks</td>
                      <td style="text-align: center;">CTR</td>
                      <td style="text-align: center;">Cost</td>
                      <td class="hhr"><!-- --></td>
                    </tr>
<!-- Google Accounts -->
                    <tr>
                    <td class="border" style=" background-color: #ffffff;">&nbsp;</td>
                     <td colspan="5" style=" background-color: #ffffff;" >
                     <span><img src="<?=SCRIPT_ROOT?>img/galogo.jpg" /></span></td>
                     <td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListAdwordsAccounts(); ?>
                    

<!-- Yahoo Accounts -->
                     <tr>
						<td class="border" style=" background-color: #ffffff;">&nbsp;</td>
						<td style="border-left: none; background-color: #ffffff;" colspan="5"  ><span><img style="margin-left: 5px;" src="<?=SCRIPT_ROOT?>img/ysmlogo.gif" /></span></td>
						<td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListYahooAccounts(); ?>
<!-- MSN Accounts -->
                    <tr>
                    <td class="border" style=" background-color: #ffffff;">&nbsp;</td>
                     <td colspan="5" style=" background-color: #ffffff;" >
                     <span><img src="<?=SCRIPT_ROOT?>img/adcentersmall.gif" /></span></td>
                     <td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListMSNAccounts(); ?>

<!-- Footer -->
					 <tr class="table_footer">
					  <td class="hhl">&nbsp;</td>
					  <td colspan="5">&nbsp;</td>
					  <td class="hhr">&nbsp;</td>
					 </tr>
                  </table>
</div>
				  

</center>
      
<script language="javascript">
function showcr(objSelect)
{
      document.getElementById('customRepRangeCustom').style.display = objSelect.value == 'custom' ? 'block':'none';
}
function showrows(id,num)
{
    var rows ;
    var i;
    for(i=0;i<num;i++){
    rows = document.getElementById(id+i);
    if(rows.style.visibility == 'collapse')rows.style.visibility='visible';
    else rows.style.visibility = 'collapse'
    }
}
</script>
