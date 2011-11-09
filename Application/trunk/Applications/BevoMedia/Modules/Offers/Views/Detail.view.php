<?php
//*************************************************************************************************

require_once(PATH . "Legacy.Abstraction.class.php");

		global $userId;
		$userId = $this->User->id;

//*************************************************************************************************

		$isOffersPage = true;

//*************************************************************************************************

		$id		= (int)$_GET['id'];
		if ( $id == 0 )
		{
			header('Location: /BevoMedia/Publisher/Index.html?ID='.$NetworkID);
			//header("Location: index.html?info=1");
			die;
		}
//*************************************************************************************************

		if ( !($offer = LegacyAbstraction::getRecord("offers", $id)) )
		{
			header('Location: /BevoMedia/Publisher/Index.html?ID='.$NetworkID);
			//header("Location: index.html?info=5");
			die;
		}


//*************************************************************************************************
/*
		// Call template
		if( $offer['NETWORK_ID'] == NETWORK_MAXBOUNTY_ID )
		{
		include(PATH.'templates/maxbounty_offer_detail.tpl.php');
		}
		elseif( $offer['NETWORK_ID'] == NETWORK_NEVERBLUEADS_ID )
		{
		include(PATH.'templates/neverblue_offer_detail.tpl.php');
		}
		elseif( $offer['NETWORK_ID'] == NETWORK_CPASTORM_ID || $offer['NETWORK_ID'] == NETWORK_COPEAC_ID )
		{
		include(PATH.'templates/cpastorm_offer_detail.tpl.php');
		}
        elseif( $offer['NETWORK_ID'] == NETWORK_CLICKBANK_ID )
        {
        include(PATH.'templates/clickbank_offer_detail.tpl.php');
        }
         elseif( $offer['NETWORK_ID'] == NETWORK_COMMISIONJUNCTION_ID )
        {
        include(PATH.'templates/cj_offer_detail.tpl.php');
        }
		else
		{
		include(PATH.'templates/publisher-offer-detail.tpl.php');
		}
*/
//*************************************************************************************************

//		LegacyAbstraction::free($resOffers);

//*************************************************************************************************
?>
<?
	$NetworkID = $offer['network__id'];

	$Sql = "SELECT COUNT(id) as `Total` FROM bevomedia_user_aff_network WHERE (user__id = {$userId}) AND (network__id = {$NetworkID}) ";
	$Row = mysql_query($Sql);
	$Row = mysql_fetch_assoc($Row);
	
	if ($Row['Total']==0)
	{
		header('Location: /BevoMedia/Publisher/Index.html?ID='.$NetworkID);
		die;
	}
	
	$OfferID = $offer['id'];
	$Sql = "SELECT signupUrl, offerUrl FROM bevomedia_aff_network WHERE (id = {$NetworkID}) ";
	
	$Row = mysql_query($Sql);
	$Row = mysql_fetch_assoc($Row);
	$SignupUrl = $Row['signupUrl'];
	$OfferUrl = $Row['offerUrl'];
	
	if ($OfferUrl!='')
	{
		$OfferUrl = str_replace('{$OfferID}', $offer['offer__id'], $OfferUrl);
	} else
	{
		$OfferUrl = $SignupUrl;
	}
	

?>
	<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-offer-detail.js.php?sriptRoot=<?=SCRIPT_ROOT?>&langFolder=<?=$langFolder?>"></script>

	<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Offers/Index.html">New Search<span></span></a></li>
			<li><a class="active" href="#">Search Results<span></span></a></li>
		</ul>
	</div>
	<?php 	
	$customPDImage = 'networkoffers/'.$offer['network__id'].'.png';
	echo $this->PageDesc->ShowDesc($this->PageHelper,false,false,$customPDImage); ?>

	<center>	
	<?= @$info ?>

	<table cellspacing="0"  border="0" align="center"  cellpadding="5px" class="btable" >
		<tr class="table_header" >
			<td class="hhl">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="hhr" >&nbsp;</td>
		</tr>
		
        <tr>
            <td class="border">&nbsp;</td>
			<td class="GridRowHead">Offer&nbsp;</td>
			<td class="GridRowCol"><?=htmlentities($offer['title'])?>&nbsp;</td>
            <td class="tail">&nbsp;</td>
		</tr>
		<tr>
            <td class="border">&nbsp;</td>
			<td class="GridRowHead">Detail&nbsp;</td>
			<td class="GridRowCol"><?=htmlentities($offer['detail'])?>&nbsp;</td>
            <td class="tail">&nbsp;</td>
		</tr>
		<tr>
            <td class="border">&nbsp;</td>
			<td class="GridRowHead">Offer ID&nbsp;</td>
			<td class="GridRowCol"><?=$offer['id']?>&nbsp;</td>
            <td class="tail">&nbsp;</td>
		</tr>
		<tr>
            <td class="border">&nbsp;</td>
			<td class="GridRowHead">Payout&nbsp;</td>
			<td class="GridRowCol">
			<?
				$Price = $offer['payout'];
				$Price = str_replace('$', '', $Price);
				$Price = str_replace('%', '', $Price);
				$Price = number_format($Price, 2);
				
				if (strstr($offer['payout'], '%'))
					$offer['payout'] = $Price.'%'; else
					$offer['payout'] = '$'.$Price; 
					
				echo $offer['payout'];
			?>
			&nbsp;
			</td>
            <td class="tail">&nbsp;</td>
		</tr>
		<tr>
            <td class="border">&nbsp;</td>
			<td class="GridRowHead"><?='Allowed country'?>&nbsp;</td>
			<td class="GridRowCol"><?=LegacyAbstraction::getcountrylist($offer['id'])?>&nbsp;</td>
            <td class="tail">&nbsp;</td>
		</tr>
		<tr>
            <td class="border">&nbsp;</td>
			<td class="GridRowHead">Category&nbsp;</td>
			<td class="GridRowCol"><?=LegacyAbstraction::getcategorylist($offer['category__id'])?>&nbsp;</td>
            <td class="tail">&nbsp;</td>
		</tr>
		<tr>
            <td class="border">&nbsp;</td>
			<td class="GridRowHead" colspan="2" style="text-align:center;"><a href="<?=htmlentities($OfferUrl)?>" target="_blank">Click Here To Retreive Tracking Codes</a></td>
            <td class="tail">&nbsp;</td>
		</tr>

		<tr> <td class="border">&nbsp;</td>
			<td class="GridRowHead" colspan="2">
				<input type="button" value="Back" class="baseeffect" onclick="history.back(1);" style="color: #fff;" />
			</td>
            <td class="tail">&nbsp;</td>
		</tr>
		<tr class="table_footer">
           <td class="hhl">&nbsp;</td>
		   <td >&nbsp;</td>
           <td style="border-left: none;">&nbsp;</td>
           <td class="hhr">&nbsp;</td>
		</tr>
	</table>

	</center>
