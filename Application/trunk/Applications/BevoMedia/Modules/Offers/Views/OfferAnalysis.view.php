<?php
//*************************************************************************************************

require_once(PATH . "Legacy.Abstraction.class.php");

global $userId;
$userId = $this->User->ID;
//*************************************************************************************************

include PATH.'images/charts.php';
//*************************************************************************************************
		$isOffersPage = true;

//*************************************************************************************************
//*************************************************************************************************
//Get network name and model
  $res = LegacyAbstraction::executeQuery('SELECT TITLE,MODEL FROM '.PREFIX.'aff_network WHERE ID='.mysql_real_escape_string($_GET['network']));
  $row = LegacyAbstraction::getRow($res) ;
  $networkTitle = $row['TITLE'];
  $networkmodel= $row['MODEL'];
//**************************************************************************************************
$network=$_GET['network'];

                     $offers = isset($_POST['offers'])?$_POST['offers']:'ALL offers';
        $rA      = isset($_POST['offers'])?$_POST['offers']:'ALL offers';
        $regionB = isset($_POST['regionB'])?$_POST['regionB']:'Earnings';
        $rB      = isset($_POST['regionB'])?$_POST['regionB']:'Earnings';
        $timefram = isset($_POST['timefram'])?$_POST['timefram']:'Per Day';
        $rC      = isset($_POST['timefram'])?$_POST['timefram']:'Per Day';
		$categorie = isset($_POST['category'])?$_POST['category']:'Insurance';
        $rD      = isset($_POST['category'])?$_POST['category']:'Insurance';
        $networkselect = isset($_POST['network'])?$_POST['network']:'Azoogle';
        $rE      = isset($_POST['network'])?$_POST['network']:'Azoogle';


?>


<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-offer-detail.js.php?sriptRoot=<?=SCRIPT_ROOT?>&langFolder=<?=$langFolder?>"></script>

	<center>
	
	<?= @$info ?>
	
	<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu"></div>
	<div id="pagesubmenu">
		<ul>
			<li><a href="Stats.html?network=<?php echo $network_id?>">Main<span></span></a></li>
			
			<?php if($networkmodel == 'CPA')
				echo '<li><a href="SubReport.html?network='.$network_id.'">Sub Report<span></span></a></li>';
			else {
				echo '<li><a href="StatsIndustry.html?network='.$network_id.'">Stats Industry<span></span></a></li>';
				echo '<li><a class="active" href="OfferAnalysis.html?network='.$network_id.'">Offer Analysis<span></span></a></li>';
			} ?>			
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false,false,'/networkoffers/'.$network_id.'.png'); ?>

<?php /* this looks like it's depreciated? no way of testing. This can go if it's no longer used - the page menu above should work.

if($networkmodel=='CPA'):?>
<table cellspacing="5px">
<tbody><tr>
	<td><a href="Stats.html?network=1024"><img src="https://www.bevomedia.com/images/mainbutton.gif" style="border: medium none ;"></a></td>
	<td><a href="https://www.bevomedia.com/publisher-stats-subreport.php?network=1024"><img src="https://www.bevomedia.com/images/subreportbut.gif" style="border: medium none ;"></a></td>
	<td><a href="StatsIndustry.html?network=1024"><img src="https://www.bevomedia.com/images/industrybut.gif" style="border: medium none ;"></a></td>
</tr>
<tr><td height="25"></td></tr>
</tbody></table>
<?php else:?>
<?php $network_id = $_GET['network']?>
<table cellspacing="5px">
<tr>
	<td><a href="Stats.html?network=<?php echo $network_id?>"><img src="/Themes/BevoMedia/img/mainbutton.gif"  style="border:none;"/></a></td>
	<td><a href="StatsIndustry.html?network=<?php echo $network_id?>"><img src="/Themes/BevoMedia/img/industrybut.gif" style="border:none;"/></a></td>
	<td><a href="OfferAnalysis.html?network=<?php echo $network_id?>"><img src="/Themes/BevoMedia/img/offeranalysysbut.gif" style="border:none;"/></a></td>
</tr>
<tr><td height=25></td></tr>
</table>
<?php endif;
*/ ?>


<form  action="publisher-stats-offeranalysis.php?network=<?=$network ?>" method="post" id="fTs" name="fTs">
                <strong></strong><select name="regionA" onchange="submitf()">
                <option value='ALL Offers' <? if($rA=='ALL Offers')echo 'selected';?>>ALL Offers</option>
                <option value='Offer 1' <? if($rA=='Offer 1')echo 'selected';?>>Offer 1</option>
                <option value="Offer 2" <? if($rA=='Offer 2')echo 'selected';?>>Offer 2</option>
                <option value='Offer 3' <? if($rA=='Offer 3')echo 'selected';?>>Offer 3</option>
                </select>
              
                <strong></strong><select name="regionC" onchange="submitf()">
                <option value='Per Day' <? if($rC=='Per Day')echo 'selected';?>>Per Day</option>
                <option value='Per Week' <? if($rC=='Per Week')echo 'selected';?>>Per Week</option>
                <option value="Per Month" <? if($rC=='Per Month')echo 'selected';?>>Per Month</option>
                <option value='Per Year' <? if($rC=='Per Year')echo 'selected';?>>Per Year</option>
                </select>
                
                 <strong></strong><select name="regionB" onchange="submitf()">
                <option value='Earnings' <? if($rB=='Earnings')echo 'selected';?>>Earnings</option>
                <option value='Clicks' <? if($rB=='Clicks')echo 'selected';?>>Clicks</option>
                <option value="Conversions" <? if($rB=='Conversions')echo 'selected';?>>Conversions</option>
                <option value='EPC' <? if($rB=='EPC')echo 'selected';?>>EPC</option>
                </select>
                
                <strong></strong><select name="regionD" onchange="submitf()">
                <option value='Insurance' <? if($rB=='Insurance')echo 'selected';?>>Insurance</option>
                <option value='Automatives' <? if($rB=='Automatives')echo 'selected';?>>Automatives</option>
                <option value="Beauty" <? if($rB=='Beauty')echo 'selected';?>>Beauty</option>
                <option value='Dating' <? if($rB=='Dating')echo 'selected';?>>Dating</option>
                </select>
                
                <strong></strong><select name="regionE" onchange="submitf()">
                <option value='Azoogle' <? if($rC=='Azoogle')echo 'selected';?>>Azoogle</option>
                <option value='Copeac' <? if($rC=='Copeac')echo 'selected';?>>Copeac</option>
                <option value="CPAStorm" <? if($rC=='CPAStorm')echo 'selected';?>>CPAStorm</option>
                <option value='Maxbounty' <? if($rC=='Maxbounty')echo 'selected';?>>Maxbounty</option>
                <option value='NeverblueAds' <? if($rC=='NeverblueAds')echo 'selected';?>>NeverblueAds</option>
                </select>
                
                <strong></strong><select name="regionF" onchange="submitf()">
                <option value='ALL Offers' <? if($rA=='ALL Offers')echo 'selected';?>>ALL Offers</option>
                <option value='Offer 1' <? if($rA=='Offer 1')echo 'selected';?>>Offer 1</option>
                <option value="Offer 2" <? if($rA=='Offer 2')echo 'selected';?>>Offer 2</option>
                <option value='Offer 3' <? if($rA=='Offer 3')echo 'selected';?>>Offer 3</option>
                </select>
</form> 
 <?=InsertChart ( '/assets/graphs/stats-analysis.php?userId='.$userId.'&timefram='.$timefram.'&rB='.@$regionB.'&offers='.@$offers, 600, 380, 'ffffff' );?>           
<!-- start table !-->
<table cellspacing="0" cellpadding="3" border="0" class="btable">
       
       <tr class="table_header">
            <td class="hhl">&nbsp;</td>
            <td >&nbsp;</td>
             <td class="STYLE2" style="text-align: center;">Network</td>
            <td style="width: 50px; text-align: center;" class="STYLE2">Earnings</td>
            <td style="width: 90px; text-align: center;" class="STYLE2">Clicks</td>
            <td class="STYLE2" style=" width: 50px; text-align: center;">Conversions</td>
            <td style="width: 50px; text-align: center;">CTR</td>
            <td style="width: 50px; text-align: center;">EPC</td>
             <td style="width: 90px; text-align: center;" >BeVo EPC</td>
            <td class="hhr">&nbsp;</td>
        </tr>
      <? echo('<tr>
			<td class="border">&nbsp;</td>
			<td colspan="8" class="STYLE4" style="border-left: none;">Offers </td>
			<td class="tail">&nbsp;</td>
		</tr>'); ?>
        <!--     start the listing !-->
        <tr>
         <td class="border">&nbsp;</td> 
            <td ><a href="#">Win $1500 Ebay Shopping Spree!</a></td>
            <td style="text-align: center;"><?=$networkTitle?></td>
            <td style="text-align: center;">$81.20</td>
            <td style="text-align: center;">260</td>
            <td style="text-align: center;">28</td>
            <td style="text-align: center;">10.7%</td>
            <td style="text-align: center;">$0.31</td>
            <td style="text-align: center;">$0.28</td>
         <td class="tail">&nbsp;</td>
         </tr>
         <tr>
         <td class="border">&nbsp;</td>
            <td ><a href="#">Flycell - 25 Ringtones</a></td>
            <td style="text-align: center;"><?=$networkTitle?></td>
            <td style="text-align: center;">$812.00</td>
            <td style="text-align: center;">588</td>
            <td style="text-align: center;">56</td>
            <td style="text-align: center;">9.5%</td>
            <td style="text-align: center;">$1.38</td>
            <td style="text-align: center;">$2.12</td>
         <td class="tail">&nbsp;</td>
         </tr>
         <tr>
         <td class="border">&nbsp;</td>
            <td ><a href="#">Free Scholarship Guide</a></td>
            <td style="text-align: center;"><?=$networkTitle?></td>
            <td style="text-align: center;">$1,046.00</td>
            <td style="text-align: center;">1947</td>
            <td style="text-align: center;">523</td>
            <td style="text-align: center;">26.8%</td>
            <td style="text-align: center;">$0.54</td>
            <td style="text-align: center;">$0.40</td>
            
         <td class="tail">&nbsp;</td>
         </tr>
         <tr>
         <td class="border">&nbsp;</td>
            <td ><a href="#">ValuedOpinions.com - iPhone Sweeps</a></td>
            <td style="text-align: center;"><?=$networkTitle?></td>
            <td style="text-align: center;">$817.00</td>
            <td style="text-align: center;">980</td>
            <td style="text-align: center;">439</td>
            <td style="text-align: center;">44.7%</td>
            <td style="text-align: center;">$0.83</td>
            <td style="text-align: center;">$0.33</td>
         <td class="tail">&nbsp;</td>
         </tr>
         <tr>
         <td class="border">&nbsp;</td>
            <td ><a href="#">Win a Satellite Radio </a></td>
            <td style="text-align: center;"><?=$networkTitle?></td>
            <td style="text-align: center;">52.50</td>
            <td style="text-align: center;">175</td>
            <td style="text-align: center;">42</td>
            <td style="text-align: center;">25%</td>
            <td style="text-align: center;">$0.30</td>
            <td style="text-align: center;">0.35$</td>
         <td class="tail">&nbsp;</td>
         </tr>
        <!--     end the listing   !-->
       <tr class="table_footer">
			<td class="hhl">&nbsp;</td>
			<td colspan="8">&nbsp;</td>
			<td class="hhr">&nbsp;</td>
		</tr>
	</table><br>

<!-- end table !-->

     <form action="<?=SCRIPT_ROOT?>publisher-analytic-detail.php" method="GET" style="margin:0px; padding:0px;"> 
				<div class="right_box2">
					<div class="right_box2_top"></div>
					<div class="right_box2_middle">
						<div class="right_box2_middle2">
						<div class="right_box2_title">Custom Report</div>
										   <div class="right_box4_tr">
						    <div style="text-align: left;" class="right_redio"><label><input type="radio" name="crRange" id="range_today" value="today" <?=$crRange=='today'?'checked':''?> onclick="customRepRange(this)"></label>Today</div>
							<div style="text-align: left;" class="right_redio"><label><input type="radio" name="crRange" id="range_yesterday" value="yesterday" <?=$crRange=='yesterday'?'checked':''?> onclick="customRepRange(this)"></label>Yesterday</div>
						   </div>
						   <div class="right_box4_tr">
						  	<div class="right_redio" style="text-align: left;"><label><input type="radio" name="crRange" id="range_thisweek" value="thisweek" <?=$crRange=='thisweek'?'checked':''?> onclick="customRepRange(this)"></label>This Week</div>
							<div class="right_redio" style="text-align: left;"><label><input type="radio" name="crRange" id="range_thismonth" value="thismonth" <?=$crRange=='thismonth'?'checked':''?> onclick="customRepRange(this)"></label>This Month</div>
						   </div>
						   <div class="right_box4_tr">
						  	<div class="right_redio" style="text-align: left;"><label><input type="radio" name="crRange" id="range_thisyear" value="thisyear" <?=$crRange=='thisyear'?'checked':''?> onclick="customRepRange(this)"></label>This Year</div>
							<div class="right_redio" style="text-align: left;"><label><input type="radio" name="crRange" id="range_custom" value="custom" <?=$crRange=='custom'?'checked':''?> onclick="customRepRange(this)"></label>Custom</div>
                            <div id="customRepRangeCustom" style="display:<?=$crRange=='custom'?'block':'none'?>;">

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
						  </div>
						  <div class="right_box2_title">
						   
						   <input type="image" name="btnSubmit" src="<?=SCRIPT_ROOT?>images/<?=$langFolder?>/btn-submit.gif" border=0 alt="">
						   </div>
					  </div>
					</div>
					<div class="right_box2_bottom"></div>
				</div>
			  </form>
              
                    <br />



<script language="javascript">
function submitf(){
 document.fTs.submit();
}
</script>
