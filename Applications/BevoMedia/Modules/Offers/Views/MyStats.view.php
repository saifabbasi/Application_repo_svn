<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Viewheader.include.php'; 

	$myNws = array(
		'cookie'=>'__bevoMyNwLast',
		'cookie_from'=>'__bevoMyNwLastFrom',
		'cookie_to'=>'__bevoMyNwLastTo'
	);
	
	//set dates
	$myNws['current_from'] = isset($_COOKIE[$myNws['cookie_from']]) ? date('Y-m-d', strtotime(trim($_COOKIE[$myNws['cookie_from']]))) : date('Y-m-d', time()-60*60*24); //yesterday 
	$myNws['current_to'] = isset($_COOKIE[$myNws['cookie_to']]) ? date('Y-m-d', strtotime(trim($_COOKIE[$myNws['cookie_to']]))) : date('Y-m-d'); //today
	
	//fetch all user networks
	$sql = "SELECT 	networks.*
		FROM	bevomedia_aff_network AS networks
			LEFT JOIN bevomedia_user_aff_network AS usernetworks
				ON usernetworks.network__id = networks.id
		WHERE	usernetworks.user__id = {$_SESSION['User']['ID']}
		ORDER BY usernetworks.id
	";
	$raw = mysql_query($sql);
	
	$myNws['networks'] = array();
	$myNws['lefttable'] = '';
	$myNws['righttable'] = new stdClass();
	$myNws['num_networks'] = mysql_num_rows($raw);
	
	if($myNws['num_networks'] > 0) {
		
		//get latest viewed network from cookie so that we can fetch the overview report for it
		if(isset($_COOKIE[$myNws['cookie']]) && is_numeric($_COOKIE[$myNws['cookie']]) && $_COOKIE[$myNws['cookie']] != 0) {
			$myNws['current'] = intval(trim($_COOKIE[$myNws['cookie']]));
			
		} else	$myNws['current'] = false;

		
		while($nw = mysql_fetch_object($raw)) {
			
			//set current if not yet set
			if(!$myNws['current'])
				$myNws['current'] = $nw->id; //set first one to current if none exists yet
			
			$myNws['networks'][] = $nw;
			
			//build left table
			$myNws['lefttable'] .= '<tr class="oleftrow j_network-'.$nw->id;
			$myNws['lefttable'] .= $myNws['current'] == $nw->id ? ' active' : '';
			$myNws['lefttable'] .= '" title="'.$nw->title.'">
				<td class="hhl">&nbsp;</td>
				<td class="td_oleft nwlogo">
					<img class="nwpic w120" src="/Themes/BevoMedia/img/networklogos/uni/'.$nw->id.'.png" alt="" />
					<div class="connector hide"></div>
				</td>
				<td class="hhr">&nbsp;</td></tr>';
			
			//fetch and build details for current
			if($myNws['current'] == $nw->id) {
				
				$myNws['righttable']->nw = $nw;
				
				//fetch stats for last viewed network
				$sql = "SELECT
						subids.offer__id as offer_id,
						offers.title AS offer_name,
						SUM(subids.clicks) as clicks,
						SUM(subids.conversions) AS conversions,
						SUM(subids.revenue) as revenue
					FROM
						bevomedia_user_aff_network_subid AS subids
						LEFT JOIN bevomedia_offers AS offers ON
							subids.offer__id = offers.offer__id
							AND subids.network__id = offers.network__id
					WHERE
						subids.user__id = {$_SESSION['User']['ID']}
						AND subids.network__id = {$nw->id}
						AND subids.statDate BETWEEN {$myNws['current_from']} AND {$myNws['current_to']}
					GROUP BY
						subids.offer__id,
						offers.title
				";
				
				$det = mysql_query($sql);
				
				//build chart and table
				$myNws['righttable']->chart = "<chart showBorder='0' bgAlpha='0,0' caption='Offers Overview' numberPrefix='$' formatNumberScale='0'>";
				$myNws['righttable']->table = '';
				
				$clicks = 0;
				$conversions = 0;
					
				while($details = mysql_fetch_object($det)) {
					//chart
					$offer_name = htmlspecialchars_decode(empty($details->offer_name) ? 'Unknown' : preg_replace('/[^a-z0-9\s]/i', '', $details->offer_name));
					$myNws['righttable']->chart .= "<set label='".htmlentities($offer_name)."' value='".$details->revenue."' />";
					
					//table
					$clicks += $details->clicks;
					$conversions += $details->conversions;
					@$revenue += $details->revenue;
					
					$myNws['righttable']->table .= '<tr>
						<td class="border">&nbsp;</td>
						<td>'.htmlentities($offer_name).'(';
					$myNws['righttable']->table .= @$details->offer_id ? $details->offer_id : 'No ID #';
					$myNws['righttable']->table .= ')</td>';
					$myNws['righttable']->table .= '<td class="number">'.number_format($details->clicks, 0).'</td>
									<td class="number">'.number_format($details->conversions, 0).'</td>
									<td class="number">';
							$myNws['righttable']->table .= number_format(($details->clicks != 0 ? $details->conversions / $details->clicks : 0) * 100, 2).'%';
							$myNws['righttable']->table .= '</td>
									<td class="number">$'.number_format($details->revenue, 2).'</td>
									<td class="number">$';
							$myNws['righttable']->table .= number_format(($details->clicks != 0 ? $details->revenue / $details->clicks : 0), 2);
							$myNws['righttable']->table .= '</td>
						<td class="tail">&nbsp;</td>
					</tr>';
				}
				
				//chart butt
				if(mysql_num_rows($det) == 0)
					$myNws['righttable']->chart .= "<set label='".htmlentities(str_replace("'","",$myNws['current_from']))."' value='".number_format(0, 2, '.', '')."' />";
				
				$myNws['righttable']->chart .= "</chart>";
				
				//table butt
				$myNws['righttable']->table .= '<tr class="total">
							<td class="border">&nbsp;</td>
							<td>Total</td>
							<td class="number">'.@number_format($clicks, 0).'</td>
							<td class="number">'.@number_format($conversions, 0).'</td>
							<td class="number">'.@number_format(($clicks != 0 ? $conversions / $clicks : 0) * 100, 2).'%</td>
							<td class="number">$'.@number_format($revenue, 2).'</td>
							<td class="number">$'.@number_format(($clicks != 0 ? $revenue / $clicks : 0), 2).'</td>
							<td class="tail">&nbsp;</td>
						</tr>';
					
			}//end current nw details
			
		}//endwhile network
		
	}//endif > 0 nws
	
?>
<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-offer-detail.js.php?sriptRoot=<?=SCRIPT_ROOT?>&langFolder=<?=$langFolder?>"></script>

<div id="pagemenu"></div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false, false, false, 'ovault'); //disable toggle, custom css class
?>
<?php 	$hideOdialExtras = true;
	include 'Applications/BevoMedia/Modules/Offers/Views/Ovault_Odial_include.view.php'; ?>

<div class="pagecontent secondarypage" id="ovault">

	<div id="oleft">
		<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable">
			<thead>
				<tr class="table_header">
					<td class="hhl">&nbsp;</td>
					<td style="text-align:center;">My Networks</td>
					<td class="hhr">&nbsp;</td>
				</tr>
				<tr>
					<td class="hhl">&nbsp;</td>
					<td class="td_oleft">
						<a class="btn ovault_yell_addnetworks" href="/BevoMedia/Publisher/Index.html">Add Networks</a>
					</td>
					<td class="hhr">&nbsp;</td>
				</tr>
			</thead>
			
			<tbody>
				<?php if($myNws['lefttable'] == '') { ?>
				
					<tr class="oleftrow disabled j_list-new active">
						<td class="hhl">&nbsp;</td>
						<td class="td_oleft">
							<p class="center">You haven't added any networks yet! You can apply to all networks directly from the Bevo interface. <a href="/BevoMedia/Publisher/Index.html">Click Here</a> to add your first network now!</p>
							<div class="connector hide"></div>
						</td>
						<td class="hhr">&nbsp;</td></tr>
					
				<?php } else echo $myNws['lefttable']; 	?>
			</tbody>
			
			<tfoot>
				<tr class="table_footer">
					<td class="hhl"></td>	
					<td style="border-left: none;"></td>
					<td class="hhr"></td>
				</tr>
			</tfoot>
		</table><!--close outer .btable-->
		<div class="footfeat">
			<div class="hilite first">
				<p>You're on</p>
				<h3 class="j_savelists_listnum"><?php echo $myNws['num_networks']; ?></h3>
				<p class="dark">Networks</p>
			</div>
			<div class="clear"></div>
		</div>
		
	</div><!--close #oleft-->
	<div id="oright">
		<?php if(!isset($myNws['righttable']->table) || $myNws['righttable']->table == '') { ?>
			
			<div class="tabs">
				<ul>
					<li><a class="active" href="#">Start<span></span></a></li>
				</ul>
			</div>
			<div class="content">
				<div class="conttop">
					<div class="top topfull">
						<h2>Welcome to your Network Stats!</h2>
						
						<p>Once you've signed up with a network, you will find all the clicks and conversions on this page. Bevo calculates your conversion rate, your earnings, and your earnings per click (EPC). You'll also be able to drill down by Sub ID so that you know exactly where your converting clicks came from.</p>
						
						<p><a href="/BevoMedia/Publisher/Index.html">Click Here</a> to add your first network now!</p>
					</div>
				</div>
			</div><!--close content-->
		
		<?php } else { //if we have a righttable
		?>
			<div class="tabs">
				<ul>
					<li><a class="active" href="#">Performance Report<span></span></a></li>
					<li><a href="#">Sub ID Report<span></span></a></li>
					<li><a href="#">Offers<span></span></a></li>
				</ul>
			</div><!--close tabs-->
			<div class="content">
				<div class="conttop">
					<div class="top topfull">
						<h2><?php echo $myNws['righttable']->nw->title; ?></h2>
						
						<form method="get" action="" name="frmRange" class="dateform">
						<input type="hidden" name="network" value="<?php echo $myNws['righttable']->nw->id ?>" />
						<table align="right" cellspacing="0" cellpadding="0" class="datetable">
						  <tr>
						    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>" /></td>
							<td><input class="formsubmit" type="submit" /></td>
						  </tr>
						</table>
						</form>
						
						<div class="clear"></div>						
						
					</div><!--close top-->
					
					<div class="clear"></div>
				</div><!--close conttop-->
				
				<div id="chartOverviewDiv" align="center"></div>
				<script type="text/javascript">
					//Instantiate the Chart
					var chart_chartOverview = new FusionCharts("/Themes/BevoMedia/chart_swf/Column2D.swf", "chartOverview", "600", "380", "0", "0");
					//Provide entire XML data using dataXML method
					chart_chartOverview.setDataXML("<?php echo $myNws['righttable']->chart; ?>");
					//Finally, render the chart.
					chart_chartOverview.render('chartOverviewDiv');
				</script>
				
				
				<h3><?php echo $myNws['righttable']->nw->title; ?> Offer Performance Report</h3>
				
				<table cellspacing="0" cellpadding="3" border="0" class="btable">
					<thead>
						<tr class="table_header">
							<td class="hhl">&nbsp;</td>
							<td width="30%">Offer</td>
							<td style="text-align: center;">Clicks</td>
							<td style="text-align: center;">Conversions</td>
							<td style="text-align: center;">Conv. Rate</td>
							<td style="text-align: center;">Earnings</td>
							<td style="text-align: center;">EPC</td>
							<td class="hhr">&nbsp;</td>
						</tr>
					</thead>
					<tbody>
						<?php echo $myNws['righttable']->table; ?>
					</tbody>
					<tfoot>
						<tr class="table_footer">
							<td class="hhl">&nbsp;</td>
							<td colspan="6">&nbsp;</td>
							<td class="hhr">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				
				<a class="tbtn floatright" href="#">Export to CSV</a>
				<div class="clear"></div>
			</div><!--close content-->
		
		<?php } //endif righttable
		?>
	
	</div><!--close #oright-->
	<div class="clear"></div>	
</div><!--close .pagecontent#ovault-->
