<?php
//*************************************************************************************************

require_once(PATH . "Legacy.Abstraction.class.php");

//*************************************************************************************************

		$isOffersPage = true;

//*************************************************************************************************

		global $pageSize;
		if(isset($_SESSION['pageSize']))
		{
			$pageSize = $_SESSION['pageSize'];
		}
		
		$categoryId		= (int)@$_GET['categoryId'];
		$countryId		= @$_GET['countryId'];
		$title			= @$_GET['title'];
		$trafficType	= is_array(@$_GET['trafficType'])	? $_GET['trafficType']	: array();
		$network		= is_array(@$_GET['network'])		? $_GET['network']		: array();
		$network		= array_diff($network, array(''));
		$isNetwork		= count($network) == 1;

//*************************************************************************************************

		$sql		= "";

		$sqlSelect	= "O.ID, O.title, O.DETAIL, O.PAYOUT, O.EPC";
		
		$sqlTables	= "bevomedia_offers O ";
		$sqlTables	.= ", bevomedia_aff_network N";
		$sqlWhere	= '1=1'; //"O.USERID = '".$userId."'"; // Offers are global to all users
		$sqlOrderBy	= "O.title ";

		/*if ( $trafficType )
			$sqlWhere	.= " AND O.network__id IN (".implode(",", $network)."0";*/
		//print_r($trafficType);
		
		if( in_array('S',$trafficType) )
		{
		$sqlWhere.= " AND ( O.search_allow ='Y' OR O.DETAIL LIKE '%Search: Yes%' ) AND  O.DETAIL NOT LIKE '%No search%'  ";
		}
		if( in_array('E',$trafficType) )
		{
		$sqlWhere.= " AND ( O.email_allow ='Y' OR O.DETAIL LIKE '%E-mail: Yes%' OR  O.DETAIL NOT LIKE '%To email this offer%' )  ";
		}
		if( in_array('I',$trafficType) )
		{
		$sqlWhere.= " AND ( O.incentives_allow ='Y' OR O.title LIKE '%Incentivized%' ) AND O.DETAIL NOT LIKE '%This offer CAN NOT be incentivized%'  AND  O.DETAIL NOT LIKE '%No Cash Incent Sites%'  ";
		}
			
		if ( $title != '' )
		{
			$sql="SELECT id FROM bevomedia_mcategorie WHERE MCATEGORIE = '".LegacyAbstraction::handleSingleQuote($title)."'";
			$req=LegacyAbstraction::executeQuery($sql);
			//$catids=array();
			if ( $row = LegacyAbstraction::getRow($req) )
						{
						$catid=$row['id'];
						
						}
					//$catidlist=implode(",",$catids);
			if( false && @$catid != '')
			{
			$categoryId=$catid;
			}
			else
			{
				
				$sqlWhere	.= " AND (";
				$num = 0;
				foreach(explode(" ", $title) as $t)
				{
					if($num != 0)
						$sqlWhere .= " OR ";
					$num += 1;
					$sqlWhere .= " ( O.DETAIL LIKE '%".LegacyAbstraction::handleSingleQuote($t)."%' OR O.title LIKE '%".LegacyAbstraction::handleSingleQuote($t)."%' ) ";
				}
				$sqlWhere .= " ) ";
		  	}

		}
/* catagorie filtre exemple :  SELECT of.ID, of.title, of.DETAIL, of.PAYOUT, of.EPC
FROM adpalace_offers of, ADPALACE_MCATEGORIE_OFFERS CA
WHERE of.USERID =1006
AND of.ID = CA.offer__id
AND CA.id =2
LIMIT 0 , 30
*/

		if ( $categoryId != '' )
		{
			$sqlWhere	.= " AND O.ID=C.offer__id AND C.id = '".$categoryId."'  ";
			$sqlTables	.= ", bevomedia_mcategorie_offers C ";
		}
		
		if ( $countryId != '' AND $categoryId=='' )
		{
			$sqlWhere	.= " AND O.ID=D.offer__id AND (D.ID_MCOUNTRY = '".$countryId."' OR D.ID_MCOUNTRY ='241')"; // the id 241 for all any country is alowed for this.
			$sqlTables	.= ", bevomedia_mcountry_offers D ";
		   
		}
		
		if ( $countryId != '' AND $categoryId!='' )
		{
			$sqlWhere	.= " AND C.offer__id = D.offer__id AND ( D.ID_MCOUNTRY = '".$countryId."' OR D.ID_MCOUNTRY ='241')";
			$sqlTables	.= ", bevomedia_mcountry_offers D ";
		}
		
		/*if ( !$isNetwork )
		{*/
			$sqlSelect	.= ", N.title AS NETWORK";
		
			$sqlWhere	.= " AND N.ID = O.network__id";
			
			// $sqlOrderBy	.= ", N.title";
		//}

		//echo '<pre>'; print_r($network); die;
		
		global $userId;
		$userId = $this->User->id;
		
		
		if ( (count($network)==1) && ($network[0]==-1) )
		{
			$arrNetsJoined = array();
			$res = LegacyAbstraction::executeQuery("SELECT N.MODEL, N.ID, N.title, UAN.STATUS FROM bevomedia_aff_network N, bevomedia_user_aff_network UAN WHERE UAN.USER__ID = '".$userId."' AND UAN.STATUS = '".APP_STATUS_ACCEPTED."' AND UAN.network__id = N.ID AND N.ISVALID = 'Y' ORDER BY N.MODEL, N.title");
			while ( $row = LegacyAbstraction::getRow($res) )
			{
				$arrNetsJoined[] = $row['ID'];
			}
			//print_r($arrNetsJoined);
			if (count($arrNetsJoined) > 0)
			{
				$sqlWhere	.= " AND N.ID IN (".implode(",", $arrNetsJoined).") ";
			}
		} else
		{
			if ( count($network) > 0 AND count($network)<5 )
				$sqlWhere	.= " AND N.ID IN (".implode(",", $network).") ";
		}


		$sql	= "SELECT DISTINCT ".$sqlSelect." FROM ".$sqlTables." WHERE ".$sqlWhere." ORDER BY O.title ";
		
//  ORDER BY ".$sqlOrderBy."
//*************************************************************************************************
		//echo($sql);
		
		//exit;
		$resOffers = LegacyAbstraction::breakPages($sql);
		
		

//*************************************************************************************************


//*************************************************************************************************
?>

	
	<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-offers-search.js.php?sriptRoot=<?=SCRIPT_ROOT?>&langFolder=<?=$langFolder?>"></script>
	
	<?= @$info ?>
	
	<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Offers/Index.html">New Search<span></span></a></li>
			<li><a class="active" href="#">Search Results<span></span></a></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

	<center>

<div class="smallItalics textAlignRight">
	Page Size:
	<?php foreach(array(20,50,100,200) as $k):?>
	<?php if($pageSize == $k):?>
		<b><u style='font-style:normal;'><?php echo $k?></u></b>
	<?php else:?>
	<a href='<?php print $this->{'System/BaseURL'}; ?>BevoMedia/Offers/_SetPageSize.html?size=<?php echo $k?>'>
		<?php echo $k?>
	</a>
	<?php endif?>
	<?php endforeach?>
</div>
	<table cellspacing="0" cellpadding="0" border="0" class="btable" >
		<tr class="table_header" >
         <td class="hhl">&nbsp;</td>
		<? if ( !$isNetwork ) { ?> <td style="text-align: center;">Network</td><? } ?>
		 <td style="text-align: center;" >Offer&nbsp;</td>
		 <td style="text-align: center;">Payout&nbsp;</td>
         <td class="hhr" >&nbsp;</td>
		</tr>
<?
$i=0;

	while ( $row = LegacyAbstraction::getRow($resOffers))
	{
		$i++;
		if ($row['title']=='') continue;
		
		$Price = $row['PAYOUT'];
		$Price = str_replace('$', '', $Price);
		$Price = str_replace('%', '', $Price);
		$Price = number_format(floatval($Price), 2);
		
		if (strstr($row['PAYOUT'], '%'))
			$row['PAYOUT'] = $Price.'%'; else
			$row['PAYOUT'] = '$'.$Price;
?>
		<tr>
			<? if ( !$isNetwork ) { ?><td class="border" style="padding: 3px;">&nbsp;</td><td  style="padding: 3px;"><?=$row['NETWORK']?></td><? } else { ?><td class="border" style="padding: 3px;">&nbsp;</td><? } ?>
            
			<td  style="padding: 3px;"><a href="Detail.html?id=<?=$row['ID']?>"><?=$row['title']?></a><br><?=html_entity_decode(LegacyAbstraction::getPartialString($row['DETAIL'], 200))?>&nbsp;</td>
			<td style="text-align: center; padding: 3px;" ><?=$row['PAYOUT']?>&nbsp;</td>
            <td class="tail" style="padding: 3px;">&nbsp;</td>
		</tr>
<?
	}
?>
		<tr>
           <td class="border">&nbsp;</td>
			<td style="text-align: center;" <? if ( !$isNetwork ) { ?>colspan="3"><? } else { ?>colspan="2"><? } ?><?=LegacyAbstraction::makePages("Search.html?categoryId=".$categoryId."&amp;countryId=".$countryId."&amp;title=".$title."".LegacyAbstraction::makeGetArray($trafficType, "trafficType[]").LegacyAbstraction::makeGetArray($network, "network[]")."&amp;")?></td>
		    
             <td class="tail">&nbsp;</td>
        </tr>
       
        <tr class="table_footer">
            <td class="hhl">&nbsp;</td>
            <td <? if ( !$isNetwork ) { ?>colspan="3" ><? } else { ?>colspan="2"><? } ?>&nbsp;</td>
            <td class="hhr">&nbsp;</td>
        </tr>
	</table>

	</center>
