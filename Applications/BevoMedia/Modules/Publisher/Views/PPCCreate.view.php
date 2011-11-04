<?php
require_once(PATH . "Legacy.Abstraction.class.php");
		require(PATH . 'classes/clsAdwordsAccounts.php');
		require(PATH . 'classes/clsYahooAccounts.php');
		require(PATH . 'classes/clsMSNAccounts.php');
		require(PATH . 'classes/clsPPCKeywordStats.php');

//*************************************************************************************************
        $isPpcManagerPage = true;
//******************************************************************************************        
global $userId;
$userId = $this->User->id;
        
function ListAdwordsAccounts() {
	global $userId;
	
	$objAdwords = new AdwordsAccounts();
	$objAdwords->GetListByUserID($userId);
	
	if ($objAdwords->RowCount == 0) {
?>
  <tr>
    <td class="border">&nbsp;</td>
    <td colspan="1">No accounts found.
    <a title="Google Adwords" href="GoogleAdwordsAPI.html" rel="shadowbox;width=640;height=480;player=iframe">
	    Add Account...
	</a>
	</td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	
	$arrStats = LoadAccountStats(1);
	$blnAltRow = false;
	
	while ($arrThisRow = $objAdwords->GetRow()) {
		$arrThisStats = false;
		
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
    <td><a href="AdwordsCreate.html?AccountID=<?php echo $arrThisRow['ID'];?>"><?php echo htmlspecialchars($arrThisRow['AdwordsEmail']); ?></a></td>
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
					<td colspan="1">
					    <a title="Google Adwords" href="GoogleAdwordsAPI.html" rel="shadowbox;width=640;height=480;player=iframe">
						    Add Account...
						</a>
					</td>
					
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
    
					<td colspan="1">
					    <a title="Yahoo Search Marketing" href="YahooAPI.html" rel="shadowbox;width=640;height=480;player=iframe">
						    Add Account...
						</a>
					</td>
					<td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	
	$arrStats = LoadAccountStats(2);
	
	$blnAltRow = false;
	while ($arrThisRow = $objYahoo->GetRow()) {
		$arrThisStats = false; //$arrStats[$arrThisRow['ID']];
		
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
    <td><a href="YahooCreate.html?ID=<?php echo $arrThisRow['ID']; ?>"><?php echo htmlspecialchars($arrThisRow['Username']); ?></a></td>
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
					
					<td colspan="1">
					    <a title="Yahoo Search Marketing" href="YahooAPI.html" rel="shadowbox;width=640;height=480;player=iframe">
						    Add Account...
						</a>
					</td><td class="tail">&nbsp;</td>
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
    
					<td colspan="1">
					    <a title="MSN Ad Center" href="MSNAdCenterAPI.html" rel="shadowbox;width=640;height=480;player=iframe">
						    Add Account...
						</a>
					</td><td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	
	$arrStats = LoadAccountStats(3);
	
	$blnAltRow = false;
	while ($arrThisRow = $objMSN->GetRow()) {
		$arrThisStats = false; //$arrStats[$arrThisRow['ID']];
		
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
    <td><a href="MSNCreate.html?AccountID=<?php echo $arrThisRow['ID']; ?>"><?php echo htmlspecialchars($arrThisRow['Name']); ?></a></td>
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
					
					<td colspan="1">
					    <a title="MSN Ad Center" href="MSNAdCenterAPI.html" rel="shadowbox;width=640;height=480;player=iframe">
						    Add Account...
						</a>
					</td><td class="tail">&nbsp;</td>
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
		$arrStats[$arrThisRow['AccountID']] = $arrThisRow;
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
?>

<div class="SkyBox"><div class="SkyBoxTopLeft"><div class="SkyBoxTopRight"><div class="SkyBoxBotLeft"><div class="SkyBoxBotRight">
        <table width="550" cellspacing="0" cellpadding="5" border="0">
            <tr valign="top">
                <td width="127"><img src="<?=SCRIPT_ROOT?>img/ppcicon.gif" border=0 alt=""></td>
                <td class="main">
                    <h4>Pay Per Click Management</h4>
                    <br/>
                    <p>BeVo's PPC Management gives our publishers the opportunity not only to examine their search marketing expenses,
                    but also to edit and create all of their campaigns.<br />  Check out your search campaign stats, 
                    create or edit a campaign and gain an in-depth view of exactly where your money is going. </p>
                </td>
            </tr>
        </table>
    </div></div></div></div></div>
    <br />

<table align="center" style="margin-left: auto; margin-right: auto;">
  <tr>
<td><a href="PPCManager.html"><img src="<?php echo SCRIPT_ROOT;?>img/overview_big.jpg" style="border:none"/></a></td>
<td><a href="PPCCreate.html"><img src="<?php echo SCRIPT_ROOT;?>img/newcampaign_big.jpg" style="border:none" /></a></td>
<td><a href="PPCTutorials.html"><img src="<?php echo SCRIPT_ROOT;?>img/tutorials_big.jpg" style="border:none" /></a></td>
  </tr>
</table>


<center>
<h3>Select an Account to Create a New Campaign To:</h3>
<br>
<div class="left_box2_bg">
                 <table border="0" cellspacing="0" cellpadding="0" width="600" class="btable">
                    <tr class="table_header">
					  <td class="hhl"><!-- --></td>
                      <td style="text-align: center;">Account</td>
                      <td class="hhr"><!-- --></td>
                    </tr>
<!-- Google Accounts -->
                    <tr>
                    <td class="border" style=" background-color: #ffffff;">&nbsp;</td>
                     <td colspan="1" style=" background-color: #ffffff;" >
                     <span><img src="<?=SCRIPT_ROOT?>img/galogo.jpg"></span><a name='adwordscreate'/></td>
                     <td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListAdwordsAccounts(); ?>
                    

<!-- Yahoo Accounts -->
                     <tr>
						<td class="border" style=" background-color: #ffffff;">&nbsp;</td>
						<td style="border-left: none; background-color: #ffffff;" colspan="1"  >
						<span><img style="margin-left: 5px;" src="<?=SCRIPT_ROOT?>img/ysmlogo.gif"></span><a name='yahoocreate'/></td>
						<td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListYahooAccounts(); ?>
<!-- MSN Accounts -->
                    <tr>
                    <td class="border" style=" background-color: #ffffff;">&nbsp;</td>
                     <td colspan="1" style=" background-color: #ffffff;" >
                     <span><img src="<?=SCRIPT_ROOT?>img/adcentersmall.gif"><a name='msncreate'/></span></td>
                     <td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListMSNAccounts(); ?>

<!-- Footer -->
					 <tr class="table_footer">
					  <td class="hhl">&nbsp;</td>
					  <td colspan="1">&nbsp;</td>
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
