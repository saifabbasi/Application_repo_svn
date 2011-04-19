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
<div id="pagemenu">
	<ul>
		<li><a class="active" href="/BevoMedia/Offers/BestPerformers.html">Best Performing Offers<span></span></a></li>
		<li><a href="/BevoMedia/Offers/Index.html">Search<span></span></a></li>
		<li><a href="/BevoMedia/Offers/MySavedLists.html">My Saved Lists<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false, false, false, 'ovault'); //disable toggle, custom css class
?>

<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Odial.include.php'; ?>

<div class="pagecontent" id="ovault">

	<div class="icon icon_ovault_ootm_tabletop"></div>
	<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable" id="ootm">
	
		<tr class="orow expanded" title="Click to expand/collapse this offer">
			<td class="border">&nbsp;</td>
			<td class="td_saved2list">
				<div class="icon icon_ovault_added2list" title="You have already saved this offer"></div>
			</td>
			<td class="td_savelist">
				<a class="btn ovault_add2list" href="#" data-offerid="1000" title="Add this offer to the active list">Add</a>
				<a class="btn ovault_add2list_select" href="#" data-offerid="1000" title="Select a list to add this offer to...">Select</a>
			</td>
			<td class="td_offername">
				<p>Shield Deluxe<span>12/12/2011</span></p>
			</td>
			<td class="td_payout">
				<p>$12.50</p>
			</td>
			<td class="td_type">
				<p>Lead</p>
			</td>
			<td class="td_vertical">
				<p>Shields &amp; Daggers</p>
			</td>
			<td class="td_network" colspan="2">
				<p class="icon icon_nwmember">CPA Empire</p>
			</td>
		</tr>
	
		<tr class="orowbig">
			<td class="td_info" colspan="4">
				<div class="floatleft">
					<a class="ovault_othumb" href="#" title="Click to view large">
						<img src="/Themes/BevoMedia/img_new/othumb_default.gif" alt="" /><!-- 245x125px -->
						<span></span>
					</a>
					<a class="btn ovault_importoffer" href="#">Import this offer into my network</a>
					<div class="clear"></div>
				</div>
				<div class="floatright">
					<h3>Shield Deluxe</h3>
					<small>Added 12/12/2011</small>
					
					<div class="otitle otitle_offerdesc"></div>
					<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
					<p>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
					
					<div class="olink">
						<input type="text" class="formtxt" readonly value="http://google.com/" />
						<a class="btn ovault_visiticon" href="http://google.com/" title="Open link in a new tab" target="_blank">Visit</a>
					</div>
				</div>
				<div class="clear"></div>
			</td>
			<td class="td_nw" colspan="2">
				<div class="td_inner">
				
				<div class="otitle otitle_network noborder"></div>
				<div class="onwpic">
					<img class="nwpic w120" src="/Themes/BevoMedia/img/networklogos/uni/1068.png" alt="" title="Dadingo" />
					
					<p class="bordertop aligncenter">Publisher's Rating:<br />
						<?php 	for($i=1; $i<=5; $i++) { ?>
							<img src="/Themes/BevoMedia/img/star-on.gif" id="img_rating_top_month_1068_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?php } ?> 
						
						<?php /* UNCOMMENT ONCE IMPLEMENTED, AND DELETE THE ABOVE!
							$this->Network1->rating = 5;
							for($i=1; $i<6; $i++) :
							if($this->Network1->rating >= $i){ $state = 'on'; }else{ $state = 'off'; } ?>
							
								
								
							<?	
								if (!$SelfHosted)
								{
							?>
								<a href='/BevoMedia/Publisher/NetworkRating.html?Rating=<?php print $i?>&ID=<?php print $this->Network1->id; ; ?>' rel='shadowbox;width=400;height=400;options={animate:true,showOverlay:true};player=iframe;'>
									<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_top_month_<?php print $this->Network1->id; ; ?>_<?php print $i?>" onmouseover="ratingTill('img_rating_top_month_<?php print $this->Network1->id; ; ?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_top_month_<?php print $this->Network1->id; ; ?>', <?php print $this->Network1->rating; ?>)" style="" align="absbottom" border="0" />
								</a>
							<?
								} else {
							?>
								<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_top_month_<?php print $this->Network1->id; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
							<?
								} 
							?>
							
						<?php endfor 
						?>
						
						<a href="/BevoMedia/Publisher/Reviews.html?NetworkID=<?=$this->Network1->id?>">Network Reviews</a>
						*/?>
					</p><!--close publisher's rating-->
				</div><!--close div.onwpic-->
				
				<p>You're already a member of this network!</p>
				<div class="icon icon_ovault_nwmember_bigwhite"></div>
				<a class="btn ovault_gotomystats_trans" href="/BevoMedia/Offers/MyStats.html">Go to my stats</a>
				
				</div><!--close td_inner-->
			</td>
			<td class="td_nwdesc" colspan="3">
				<div class="td_inner">
	
				<div class="otitle otitle_networkdesc"></div>
				<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
				<p>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
				
				<div class="otitle otitle_latestnwreviews noborder"></div>
				<ul class="ovault_boxlist hastitle">
					<li>not a fan</li>
					<li>famous network, tons of offers</li>
					<li>The amount of help and solid advice they give out is awesome</li>
				</ul>
				</div><!--close td_inner-->
			</td>
		</tr><!--close .orowbig-->
	</table><!--close btable-->

	<?php /*
	<div class="icon icon_ovault_bevoalsorecommends"></div> 
	
	later: add 3 offers in a row here
	
	*/?>

	<div class="icon icon_ovault_hotnewestoffers"></div>	

	<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Pagecontent.include.php'; ?>
</div><!--close pagecontent#ovault-->



<?php /* 
#############################
#############################
#############################
#############################
#############################
<form method=get action="Search.html" name="frm" class="appform" id="offmain_searchform">
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
							<input type="reset" value="Default" class="baseeffect default" style="color: white"> * / ################ ?>
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
				<table class="btable floatleft" cellspacing="0" cellpadding="5" border="0" style="float: left; width: 270px;">
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
				</table> */ ?>