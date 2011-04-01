 <?php
//*************************************************************************************************

require_once(PATH . "Legacy.Abstraction.class.php");

//*************************************************************************************************

		global $userId;
		$userId = $this->User->id;
		
		if(!isset($_GET['crNetworks']))
		{
			$_GET['crNetworks'] = false;
		}
		if(!isset($_GET['crRAnge']))
		{
			$_GET['crRange'] = '';
		}
		if(!isset($_GET['crStartDate']))
		{
			$_GET['crStartDate'] = $_GET['crEndDate'] = '';
		}
		
		$crNetworks		= is_array($_GET['crNetworks']) ? $_GET['crNetworks'] : array('CPA');
		$crRange		= $_GET['crRange'] == '' ? 'today' : $_GET['crRange'];
		
		$crStartDate	= $_GET['crStartDate'];
		$crEndDate		= $_GET['crEndDate'];
		
		$today = date('Y-m-d');

//*************************************************************************************************

		$isSubReportPage = true;

//*************************************************************************************************

		$arrNetworks = array();

		$sub = "";

		if ( $crRange == 'yesterday' )
		{
			$crStartDate	= LegacyAbstraction::addDays($today, -1);
			$crEndDate		= LegacyAbstraction::addDays($today, -1);
			$sub			= "AND S.statDate = '".$crStartDate."' ";
		}
		
		elseif ( $crRange == 'thisweek' )
		{
			$crStartDate	= LegacyAbstraction::addDays($today, -date('w', $tsToday));
			$crEndDate		= LegacyAbstraction::addDays($crStartDate, 6);
			$sub			= "AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' ";
		}
		
		elseif ( $crRange == 'thismonth' )
		{
			$crStartDate	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
			$crEndDate		= LegacyAbstraction::addDays($crStartDate, date('t', $tsToday)-1);
			$sub			= "AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' ";
		}
		
		elseif ( $crRange == 'thisyear' )
		{
			$crStartDate	= date('Y-01-01', $tsToday);
			$crEndDate		= date('Y-12-31', $tsToday);
			$sub			= "AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' ";
		}
		
		elseif ( $crRange == 'custom' )
		{
			$crStartDate	= LegacyAbstraction::handleSingleQuote($crStartDate);
			$crEndDate		= LegacyAbstraction::handleSingleQuote($crEndDate);
			$sub			= "AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' ";
		}
		
		else
		{
			$crStartDate	= $today;
			$crEndDate		= $today;
			$sub = "AND S.statDate = '".$crStartDate."' ";
		}

		$sql = "SELECT N.id, N.TITLE, N.MODEL, S.id, S.subId, S.CLICKS, S.CONVERSIONS, S.REVENUE*(100-N.adminCommission)/100 AS REVENUE, S.statDate FROM bevomedia_aff_network N, bevomedia_user_aff_network UAN, bevomedia_user_aff_network_subid S WHERE N.MODEL = 'CPA' AND N.ISVALid = 'Y' AND S.network__id = N.id AND S.user__id = '".$userId."' AND UAN.user__id = S.user__id AND UAN.network__id = N.id ".$sub." ORDER BY N.MODEL, N.TITLE, S.statDate DESC";
//echo "<font color=white>$sql</font>";
		$res = LegacyAbstraction::breakPages($sql);
		while ( $row = LegacyAbstraction::getRow($res) )
			$arrNetworks[] = $row;
		
		LegacyAbstraction::free($res);

//*************************************************************************************************

		$sql = "SELECT N.id, N.TITLE, N.MODEL FROM bevomedia_aff_network N WHERE N.MODEL = 'CPA' AND N.ISVALid = 'Y' ORDER BY N.MODEL, N.TITLE";
		$arrCRNetworks = array();
		$res = LegacyAbstraction::executeQuery($sql);
		while ( $row = LegacyAbstraction::getRow($res) )
			$arrCRNetworks[] = $row;
		LegacyAbstraction::free($res);

//*************************************************************************************************
      
		// Call template

//*************************************************************************************************
?>

<script language="JavaScript">
function W9TurnOff()
{
	postData = 'task=Publisher&action=W9Alert&alert=N';
	getContentFromUrl('controller_ajax.php', null, 'POST', postData, '');
	
	showPop("Removing W9 Alert... Please Wait...", 350, 100, "loading", "30");
}

function W9TurnOffComplete()
{
	showPopInfo('W9 alert turned off');
}

function SaveFailed(msg)
{
	showPopError(msg);
}

function customRepNetwork(objCheckbox, model)
{
	if ( objCheckbox.checked )
		document.getElementById('model_'+model).checked = false;
}

function customRepModel(objCheckbox, model)
{
	if ( !objCheckbox.checked )
		return;

	eval("networks = model_"+model+";");

	for ( i=0; i<networks.length; i++ )
		document.getElementById('network_'+networks[i]).checked = false;
}

function customRepRange(objCheckbox)
{
	document.getElementById('customRepRangeCustom').style.visibility = objCheckbox.value == 'custom' ? 'visible' : 'hidden';
}
</script>
<center>
	
	<div class="SkyBox"><div class="SkyBoxTopLeft"><div class="SkyBoxTopRight"><div class="SkyBoxBotLeft"><div class="SkyBoxBotRight">
		<table width="550" cellspacing="0" cellpadding="5" border="0">
			<tr valign="top">
				<td width="127"><img src="/Themes/BevoMedia/img/icon-sub.gif" width="127" height="116" border=0 alt=""></td>
				<td class="main">
					<h4>Subid Report</h4>
					<br>
Keep track of your sub ids for your campaigns from all of your ad networks. Use the Custom Reporting Tool to adjust the time frame of your report. For more information about how to automatically insert keywords into your sub id line, click the Google and Yahoo information boxes to the right.				</td>
			</tr>
		</table>
	</div></div></div></div></div>

	<br>

	<table width="100%" cellspacing="0" cellpadding="5" border="0">
		<tr valign="top">
			<td>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="btable">
					<tr class="table_header">
					    <td class="hhl">&nbsp;</td>
						<td style="text-align: center;">Subid</td>
						<td style="text-align: center;">Offer</td>
						<td style="text-align: center;">Network</td>
						<td style="text-align: center;">Revenue</td>
						<td style="text-align: center;">Date</td>
						<td class="hhr">&nbsp;</td>
					</tr>
<?
$model			= '';
$totTodayRev	= 0;
$totMTDRev		= 0;
$totImpression	= 0;
$totClicks		= 0;
$totConversions	= 0;
foreach ( $arrNetworks as $network )
{
	if ( !in_array('CPA', $crNetworks) && !in_array($network['id'], $crNetworks) )
		continue;

	$offer = getOfferRecord($userId, $network['id'], $network['id']);
	if ( is_array($offer) )
		$offer = $offer['TITLE'];
	else
		$offer = 'Campaign#'.$network['id'];
?>
					<tr>
					    <td class="border">&nbsp;</td>
						<td style="text-align: center"><span><?=$network['subId']?></span></td>
						<td style="text-align: center"><span><?=$offer?></span></td>
						<td style="text-align: center"><span><?=$network['TITLE']?></span></td>
						<td style="text-align: center"><span><?=LegacyAbstraction::getFormattedPrice($network['REVENUE'])?></span></td>
						<td style="text-align: center"><span><?=formatDate($network['statDate'])?></span></td>
						<td class="tail">&nbsp;</td>
					</tr>
<?
}
?>
					<tr class="table_footer">
						<td class="hhl">&nbsp;</td>
						<td colspan="5">&nbsp;</td>
						<td class="hhr">&nbsp;</td>
					</tr>
				</table>
				<?=LegacyAbstraction::makePagesEx($_SERVER['PHP_SELF'].'?'.LegacyAbstraction::removeQueryStringVar($_SERVER['QUERY_STRING'], 'x'))?>

				<br>

				<form action="" method="GET">
				<h2>Custom Subid Report</h2>
				<table align="center" cellspacing="0" cellpadding="5" border="0">
					<tr valign="top">
<?
$model = 'CPA';
?>
						<td width="33%" class="main">
							<input type="checkbox" name="crNetworks[]" id="model_<?=$model?>" value="<?=$model?>" <?=in_array($model, $crNetworks)?'checked':''?> onclick="customRepModel(this, '<?=$model?>')"><label for="model_<?=$model?>">All <?=$model?> Companies</label><br>
<?
	$modelNets = '';
	foreach ( $arrCRNetworks as $network )
	{
		if ( $network['MODEL'] != $model )
			continue;

		$modelNets .= ','.$network['id'];
?>
							<input type="checkbox" name="crNetworks[]" id="network_<?=$network['id']?>" value="<?=$network['id']?>" <?=in_array($network['id'], $crNetworks)?'checked':''?> onclick="customRepNetwork(this, '<?=$model?>')"><label for="network_<?=$network['id']?>"><?=$network['TITLE']?></label><br>
<?
	}
?>
							<script language="JavaScript">
							<!--
							var model_<?=$model?> = new Array(<?=substr($modelNets, 1)?>);
							//-->
							</script>
						</td>
						<td width="33%" class="main">
							<h3>&nbsp;</h3>
							<input type="radio" name="crRange" id="range_today" value="today" <?=$crRange=='today'?'checked':''?> onclick="customRepRange(this)"><label for="range_today">Today</label><br>
							<input type="radio" name="crRange" id="range_yesterday" value="yesterday" <?=$crRange=='yesterday'?'checked':''?> onclick="customRepRange(this)"><label for="range_yesterday">Yesterday</label><br>
							<input type="radio" name="crRange" id="range_thisweek" value="thisweek" <?=$crRange=='thisweek'?'checked':''?> onclick="customRepRange(this)"><label for="range_thisweek">This Week</label><br>
							<input type="radio" name="crRange" id="range_thismonth" value="thismonth" <?=$crRange=='thismonth'?'checked':''?> onclick="customRepRange(this)"><label for="range_thismonth">This Month</label><br>
							<input type="radio" name="crRange" id="range_thisyear" value="thisyear" <?=$crRange=='thisyear'?'checked':''?> onclick="customRepRange(this)"><label for="range_thisyear">This Year</label><br>
						</td>
						<td width="33%" class="main">
							<br>
							<input type="radio" name="crRange" id="range_custom" value="custom" <?=$crRange=='custom'?'checked':''?> onclick="customRepRange(this)"><label for="range_custom">Custom</label><br>

							<div id="customRepRangeCustom" style="visibility:<?=$crRange=='custom'?'visible':'hidden'?>;">
								<input id="crStartDate" name="crStartDate" value="<?=$crStartDate?>" size="8" type="text" class="smalleffect" maxlength="10"> - 
								<input id="crEndDate" name="crEndDate" value="<?=$crEndDate?>" size="8" type="text" class="smalleffect" maxlength="10">
								<script language="JavaScript">
								<!--
								Calendar.setup({
									inputField     :    "crStartDate",
									ifFormat       :    "%Y-%m-%d",
									range		   :    [<?=date('Y')-2?> , <?=(date('Y'))?>],
									weekNumbers    :    false,
									showsTime      :    false
								});

								Calendar.setup({
									inputField     :    "crEndDate",
									ifFormat       :    "%Y-%m-%d",
									range		   :    [<?=date('Y')-2?> , <?=(date('Y'))?>],
									weekNumbers    :    false,
									showsTime      :    false
								});
								//-->
								</script>
							</div>
							<br>
							<input type="image" name="btnSubmit" src="/Themes/BevoMedia/img/btn-submit.gif" border=0 alt="">
							<br>
							<a href="/Themes/BevoMedia/img/btn-default.gif" border=0 alt=""></a>
						</td>
					</tr>
				</table>
				</form>
			</td>
			<td>
			</td>
		</tr>
	</table>

</center>


