<?php
//*************************************************************************************************

require_once(PATH . "Legacy.Abstraction.class.php");

		global $userId;
		$userId = $this->User->id;
//*************************************************************************************************

		$isOffersPage		= true;
		$showOffersPanes	= true;

//*************************************************************************************************

		//$arrModels		= array('CPA', 'CPM', 'CPC');
		$arrModels		= array('CPA');
		
//*************************************************************************************************

		$today = date('Y-m-d');
		
		$arrNetworks = array();
		$res = LegacyAbstraction::executeQuery("SELECT N.ID, N.TITLE, N.MODEL, S.IMPRESSIONS, S.CLICKS, S.CONVERSIONS, S.REVENUE FROM bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network_stats S ON S.user__id = '".$userId."' AND S.network__id = N.ID AND S.statDate = '".$today."' WHERE ISVALID = 'Y' ORDER BY N.MODEL, N.TITLE");
		while ( $row = LegacyAbstraction::getRow($res) )
			$arrNetworks[] = $row;
		LegacyAbstraction::free($res);

//*************************************************************************************************

		$arrNetsJoined = array();
		$res = LegacyAbstraction::executeQuery("SELECT N.MODEL, N.ID, N.TITLE, UAN.STATUS FROM bevomedia_aff_network N, bevomedia_user_aff_network UAN WHERE UAN.user__id = '".$userId."' AND UAN.STATUS = '".APP_STATUS_ACCEPTED."' AND UAN.network__id = N.ID AND N.ISVALID = 'Y' ORDER BY N.MODEL, N.TITLE");
		while ( $row = LegacyAbstraction::getRow($res) )
		{
			$row['ISUSER']	= false;

			$arrNetsJoined[] = $row;
		}
		LegacyAbstraction::free($res);

//*************************************************************************************************

		// Call template

//*************************************************************************************************
?>
	
	<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-offers.js.php?sriptRoot=<?=SCRIPT_ROOT?>&langFolder=<?=$langFolder?>"></script>
<script language="javascript">
function check(){
var e = document.getElementById("nwAll");
e.checked = false;
var e = document.getElementById("nwMy");
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
function un_check_my(){
  for (var i = 0; i < document.frm.elements.length; i++) {
    var e = document.frm.elements[i];
    if ((e.id != 'nwMy' && e.id != 'ttMy'&&e.id != 'ttWeb'&&e.id != 'ttSrh'&&e.id != 'ttEml'&&e.id != 'ttInc') && (e.type == 'checkbox')) {
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
	
	<?= @$info ?>
	
	<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu"></div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
	
	<center>

				<form method=get action="Search.html" name="frm" class="appform">
				<table class="btable wide2x3" cellspacing="0" cellpadding="5">
					<tr class="table_header">
						<td class="hhl"></td>
						<td colspan="2" style="text-align: center;">
						</td>
						<td class="hhr"></td>
					</tr>
                    <tr>
                    <td class="border">&nbsp;</td>
                    
                    <td colspan="2" width="222"><span><center><img src="<?=SCRIPT_ROOT?>img/pagedesc_bevovault.png" border=0 alt=""></center></span></td>
                    <td class="tail">&nbsp;</td>
                    </tr>
					<tr>
						<td class="border">&nbsp;</td>
						<td>Search: </td>
						<td>
							<input class="formtxt" type="text" size="30" name="title" class="effect">
						</td>
						<td class="tail">&nbsp;</td>
					</tr>
					<tr valign="top">
						<td class="border">&nbsp;</td>
						<td>Network: </td>
						<td>
							<input class="formcheck" type="checkbox" id="nwAll" name="network[]" value="" checked onclick="un_check()">All Networks
							<input class="formcheck" type="checkbox" id="nwMy" name="network[]" value="-1" onclick="un_check_my()">My Networks
<?
				$count = 0;
				$idArray = '';
				foreach ( $arrNetsJoined as $network )
				{
					if ( $network['STATUS'] != APP_STATUS_ACCEPTED || $network['MODEL'] != 'CPA' )
						continue;

					$idArray .= ', "'.$network['ID'].'"';
						echo '<br>';
?>
							<input type="checkbox" id="nw<?=$network['ID']?>" name="network[]" value="<?=$network['ID']?>" onclick="check()"><label style="display: inline-block" for="nw<?=$network['ID']?>"><?=$network['TITLE']?></label>
<?
				}
?>
							<script language="JavaScript">
							<!--
							var nwIds = new Array(<?=substr($idArray, 2)?>);
							//-->
							</script>
						</td>
						<td class="tail">&nbsp;</td>
					</tr>

					<tr valign="top">
						<td class="border">&nbsp;</td>
						<td colspan="2"  style="text-align: right; padding-top: 2px;">
							<input class="formsubmit off_search baseeffect search" type="submit" value="Search" />
							<?php /* this looks like it could confuse people
							<input type="reset" value="Default" class="baseeffect default" style="color: white"> */ ?>
						</td>
						<td class="tail">&nbsp;</td>
					</tr>

					<tr class="table_footer">
						<td class="hhl"></td>
						<td colspan="2">&nbsp;</td>
						<td class="hhr"></td>
					</tr>
				</table>
				</form>
				<table class="btable" cellspacing="0" cellpadding="5" border="0" style="float: left; width: 270px;">
					<tr class="table_header_small">
						<td class="hhls" style="border: none;"></td>
						<td style="border: none;" colspan="2">&nbsp;</td>
						<td class="hhrs" style="border: none;"></td>
					</tr>
			<?
				$count = 0;
				foreach ( $arrModels as $model )
				{
			?>
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="2"><span><?=$model?></span></td>
						<td class="tail">&nbsp;</td>
					</tr>
			<?
					foreach ( $arrNetsJoined as $network )
					{
						if ( $model != $network['MODEL'] )
							continue;
						$count++;
			?>
					<tr>
						<td class="border">&nbsp;</td>
						<td style="width: 140px;"><span><?=$network['TITLE']?></span></td>
						<td style="width:130px;">
                        <input class="statsBut formsubmit off_stats" type="submit" onclick="location.href='Stats.html?network=<?=$network['ID']?>'">
						<? if ( $network['MODEL'] == 'CPA' ) { ?>
							<input class="offersBut formsubmit off_offers" type="submit" onclick="location.href='Search.html?network[]=<?=$network['ID']?>'" />
						<? } elseif ( $network['ISUSER'] ) { ?>
							<input class="codesBut formsubmit off_codes" type="submit" value="" onclick="location.href='http://www.bevomedia.com/publisher-new-network-code.php?networkId=<?=$network['ID']?>'" />
						<? } else { ?>
							<input class="codesBut formsubmit off_codes" type="submit" value="" onclick="location.href='http://www.bevomedia.com/publisher-network-code.php?networkId=<?=$network['ID']?>'" />
						<? } ?>
						
                        </td>
                        
                        
                        
						<td class="tail">&nbsp;</td>
					</tr>
			<?
					}
				}
			?>
			
			<?php if($count == 0):?>
			
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="2">
							<center>
								<a class="tbtn" href="/BevoMedia/Publisher/Index.html">
    		                    	You do not currently have any networks installed. Please click here to install them.
								</a>
							</center>
                        </td>
                        
                        
                        
						<td class="tail">&nbsp;</td>
					</tr>
			<?php endif?>
					<tr class="table_footer">
						<td class="hhl"></td>
						<td colspan="2">&nbsp;</td>
						<td class="hhr"></td>
					</tr>
				</table>

	</center>