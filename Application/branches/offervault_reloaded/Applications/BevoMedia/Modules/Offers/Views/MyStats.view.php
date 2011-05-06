<?php 
	$myNws = array(
		'cookie_lastnw'=>'__bevoMyNwLast',	//network ID last viewed
		'cookie_from'=>'__bevoMyNwLastFrom',	//date-from last selected
		'cookie_to'=>'__bevoMyNwLastTo',	//date-to
		'cookie_page' => '__bevoMyNwPage'	//last tab viewed (performance or subids)
	);
	
	require_once(PATH . "Legacy.Abstraction.class.php");
	require_once(PATH.'inc_daterange.php');
	
	/*get/set page (tab)*/
	if(isset($_GET['page']) && !empty($_GET['page'])
		&& ($_GET['page'] == 'performance' || $_GET['page'] == 'subids')) {
	
		$myNws['page'] = trim($_GET['page']);
	
	} elseif(isset($_COOKIE[$myNws['cookie_page']]) && !empty($_COOKIE[$myNws['cookie_page']])
		&& ($_COOKIE[$myNws['cookie_page']] == 'performance' || $_COOKIE[$myNws['cookie_page']] == 'subids')) {
	
		$myNws['page'] = trim($_COOKIE[$myNws['cookie_page']]);
	
	} else	$myNws['page'] = 'performance';
	
	//set cookie
	setcookie($myNws['cookie_page'], $myNws['page'], time()+60*60*24*30*12);//1y
	
	
	/*get/set dates*/
	if(isset($_GET['DateRange'])) {
		$myNws['current_from'] = LegacyAbstraction::$strStartDateVal;
		$myNws['current_to'] = LegacyAbstraction::$strEndDateVal;
		$myNws['current_range'] = LegacyAbstraction::$strDateRangeVal;
	} else {
		$myNws['current_from'] = isset($_COOKIE[$myNws['cookie_from']]) ? date('Y-m-d', strtotime(trim($_COOKIE[$myNws['cookie_from']]))) : date('Y-m-d', time()-60*60*24); //yesterday 
		$myNws['current_to'] = isset($_COOKIE[$myNws['cookie_to']]) ? date('Y-m-d', strtotime(trim($_COOKIE[$myNws['cookie_to']]))) : date('Y-m-d'); //today
		$myNws['current_range'] = $myNws['current_from'].' - '.$myNws['current_to'];
	}
	
	//set cookie
	setcookie($myNws['cookie_from'], $myNws['current_from'], time()+60*60*24*30*12);//1y
	setcookie($myNws['cookie_to'], $myNws['current_to'], time()+60*60*24*30*12);//1y
	
	/*get/set current network*/
	if(isset($_GET['network']) && is_numeric($_GET['network']) && !empty($_GET['network']) && $_GET['network'] != 0) {
		$myNws['current'] = intval(trim($_GET['network']));
	
	} elseif(isset($_COOKIE[$myNws['cookie_lastnw']]) && is_numeric($_COOKIE[$myNws['cookie_lastnw']]) && $_COOKIE[$myNws['cookie_lastnw']] != 0) {
		$myNws['current'] = intval(trim($_COOKIE[$myNws['cookie_lastnw']]));
		
	} else	$myNws['current'] = 0;
	
	//set cookie
	if($myNws['current'] != 0)
		setcookie($myNws['cookie_lastnw'], $myNws['current'], time()+60*60*24*30*12);
	
	/*get all user networks*/
	$sql = "SELECT 	networks.*,
			COUNT(offers.id) AS offercount
		FROM	bevomedia_aff_network AS networks
			LEFT JOIN bevomedia_offers AS offers
				ON (offers.network__id = networks.id)
			LEFT JOIN bevomedia_user_aff_network AS usernetworks
				ON (usernetworks.network__id = networks.id)
		WHERE	usernetworks.user__id = {$_SESSION['User']['ID']}
			AND offers.archived = 0
		GROUP BY networks.id	
		ORDER BY networks.title
	";
	$raw = mysql_query($sql);
	
	$myNws['networks'] = array();
	$myNws['lefttable'] = '';
	$myNws['righttable'] = new stdClass();
	$myNws['num_networks'] = mysql_num_rows($raw);
	
	if($myNws['num_networks'] > 0) {		
		while($nw = mysql_fetch_object($raw)) {
			
			//set current network to the first one if not yet set
			if($myNws['current'] == 0) {
				$myNws['current'] = $nw->id; //set first one to current if none exists yet
				setcookie($myNws['cookie_lastnw'], $myNws['current'], time()+60*60*24*30*12);
			}
			
			$myNws['networks'][] = $nw;
			
			//build left table
			$myNws['lefttable'] .= '<tr class="oleftrow j_network-'.$nw->id;
			$myNws['lefttable'] .= $myNws['current'] == $nw->id ? ' active' : '';
			$myNws['lefttable'] .= '" title="'.$nw->title.'">
				<td class="hhl">&nbsp;</td>
				<td class="td_oleft nwlogo">
					<a href="?network='.$nw->id.'"><img class="nwpic w120" src="/Themes/BevoMedia/img/networklogos/uni/'.$nw->id.'.png" alt="" /></a>
					<div class="connector hide"></div>
				</td>
				<td class="hhr">&nbsp;</td></tr>';
			
			//fetch and build details for current
			if($myNws['current'] == $nw->id) {
				
				$myNws['righttable']->nw = $nw;
				
				//fetch stats for last viewed network
				if($myNws['page'] == 'performance') {
					
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
					
				} elseif($myNws['page'] == 'subids') {
					
					$sql = "
						SELECT
							subids.subId AS sub_id,
							offers.offer__id AS offer_id,
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
							subids.subId,
							offers.offer__id,
							offers.title
						ORDER BY
							offers.title
					";
				
				}//endif page (sql)
				
				$det = mysql_query($sql);
				
				//build chart and table
				if($myNws['page'] == 'performance')
					$myNws['righttable']->chart = "<chart showBorder='0' bgAlpha='0,0' caption='Offers Overview' numberPrefix='$' formatNumberScale='0'>";
				
				$myNws['righttable']->table = '';
				$myNws['righttable']->csv = ''; //csv content
				
				$totalClicks = 0;
				$totalConversions = 0;
				$totalRevenue = 0;
				
				$righttablerows = 0;
				$previousOffer = null;
					
				/*content loop*/
				while($details = mysql_fetch_object($det)) {
					$righttablerows++;
					
					$offer_name = htmlspecialchars_decode(empty($details->offer_name) ? 'Unknown' : preg_replace('/[^a-z0-9\s]/i', '', $details->offer_name));
					
					//chart
					if($myNws['page'] == 'performance')
						$myNws['righttable']->chart .= "<set label='".htmlentities($offer_name)."' value='".$details->revenue."' />";
					
					//table
					$totalClicks += $details->clicks;
					$totalConversions += $details->conversions;
					@$totalRevenue += $details->revenue;
					
					if($myNws['page'] == 'subids' && $previousOffer != $details->offer_id) {
						$myNws['righttable']->table .= '<tr>
								<td class="border">&nbsp;</td>
								<td colspan="6" class="STYLE4" style="border-left: none;">'.htmlentities($details->offer_name).'</td>
								<td class="tail">&nbsp;</td>
							</tr>';
							
						$myNws['righttable']->csv = '"'.$details->offer_name.'","","","","",""'."\r\n";
					}
					
					$previousOffer = $details->offer_id;
					
					$myNws['righttable']->table .= '<tr>
						<td class="border">&nbsp;</td>
						<td>';
						
					if($myNws['page'] == 'performance') {
						$myNws['righttable']->table .= htmlentities($offer_name);
						$myNws['righttable']->table .= @$details->offer_id ? '(ID '.$details->offer_id.')' : '(No ID #)';
						
						$myNws['righttable']->csv .= '"'.$details->offer_name;
						$myNws['righttable']->csv .= @$details->offer_id ? '(ID '.$details->offer_id.')' : '(No ID #)';
						$myNws['righttable']->csv .= '",';
						
					} elseif($myNws['page'] == 'subids') {
						$myNws['righttable']->table .= htmlentities($details->sub_id);
						
						$myNws['righttable']->csv .= '"'.$details->sub_id.'",';
					}

					$clicks = @number_format($details->clicks, 0);
					$conversions = @number_format($details->conversions, 0);
					$cvr = @number_format(($details->clicks != 0 ? $details->conversions / $details->clicks : 0) * 100, 2).'%';
					$earnings = '$'.@number_format($details->revenue, 2);
					$epc = '$'.@number_format(($details->clicks != 0 ? $details->revenue / $details->clicks : 0), 2);
						
					$myNws['righttable']->table .= '</td>
									<td class="number">'.$clicks.'</td>
									<td class="number">'.$conversions.'</td>
									<td class="number">'.$cvr.'</td>
									<td class="number">'.$earnings.'</td>
									<td class="number">'.$epc.'</td>
								<td class="tail">&nbsp;</td>
								</tr>';
								
					$myNws['righttable']->csv .= '"'.$clicks.'",';
					$myNws['righttable']->csv .= '"'.$conversions.'",';
					$myNws['righttable']->csv .= '"'.$cvr.'",';
					$myNws['righttable']->csv .= '"'.$earnings.'",';
					$myNws['righttable']->csv .= '"'.$epc.'"'."\r\n";
					
				} //endwhile details
				
				//chart butt
				if($myNws['page'] == 'performance') {
					if(mysql_num_rows($det) == 0)
						$myNws['righttable']->chart .= "<set label='".htmlentities(str_replace("'","",$myNws['current_from']))."' value='".number_format(0, 2, '.', '')."' />";
					
					$myNws['righttable']->chart .= "</chart>";
				}
				
				$totalClicks = @number_format($totalClicks, 0);
				$totalConversions = @number_format($totalConversions, 0);
				$totalCVR = @number_format(($totalClicks != 0 ? $totalConversions / $totalClicks : 0) * 100, 2).'%';
				$totalEarnings = '$'.@number_format($totalRevenue, 2);
				$totalEPC = '$'.@number_format(($totalClicks != 0 ? $totalRevenue / $totalClicks : 0), 2);
				
				//table butt
				$myNws['righttable']->table .= '<tr class="total">
							<td class="border">&nbsp;</td>
							<td>Total</td>
							<td class="number">'.$totalClicks.'</td>
							<td class="number">'.$totalConversions.'</td>
							<td class="number">'.$totalCVR.'</td>
							<td class="number">'.$totalEarnings.'</td>
							<td class="number">'.$totalEPC.'</td>
							<td class="tail">&nbsp;</td>
						</tr>';
						
				$myNws['righttable']->csv .= '"Total",';
				$myNws['righttable']->csv .= '"'.$totalClicks.'",';
				$myNws['righttable']->csv .= '"'.$totalConversions.'",';
				$myNws['righttable']->csv .= '"'.$totalCVR.'",';
				$myNws['righttable']->csv .= '"'.$totalEarnings.'",';
				$myNws['righttable']->csv .= '"'.$totalEPC.'",'."\r\n";
				
						
				//thead
				$myNws['righttable']->thead = '
					<thead>
						<tr class="table_header">
							<td class="hhl">&nbsp;</td>
							<td width="30%">';							
				$myNws['righttable']->thead .= $myNws['page'] == 'performance' ? 'Offer' : 'Sub ID';				
				$myNws['righttable']->thead .= '</td>
							<td style="text-align: center;">Clicks</td>
							<td style="text-align: center;">Conversions</td>
							<td style="text-align: center;">Conv. Rate</td>
							<td style="text-align: center;">Earnings</td>
							<td style="text-align: center;">EPC</td>
							<td class="hhr">&nbsp;</td>
						</tr>
					</thead>';
					
				//tfoot
				$myNws['righttable']->tfoot = '
					<tfoot>
						<tr class="table_footer">
							<td class="hhl">&nbsp;</td>
							<td colspan="6">&nbsp;</td>
							<td class="hhr">&nbsp;</td>
						</tr>
					</tfoot>';
					
			}//end current nw details
		}//endwhile all networks
	}//endif > 0 nws
	
	/*CSV*/
	if(isset($_GET['ExportTo']) && $_GET['ExportTo'] == 'CSV' && isset($righttablerows) && $righttablerows > 0) {
		
		$viewheaderCSV = false;
		
		$csv_filename = 'Bevo-Stats-'
			.str_replace(' ', '.', $myNws['righttable']->nw->title)
			.'-'.ucfirst($myNws['page'])
			.'---'.str_replace(' ', '-', $myNws['current_range'])
			.'.csv';
			
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$csv_filename");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		print '"Bevo '.ucfirst($myNws['page']).' Report for '
			.$myNws['righttable']->nw->title.': '
			.$myNws['current_range'].'","","","","",""' . "\r\n";
			
		print $myNws['page'] == 'performance' ? '"Offer Name",' : '"SubID",';
		print '"Clicks","Conversions","Conversion Rate","Earnings","EPC"' . "\r\n";
		print $myNws['righttable']->csv;
		exit;
	}//csv
	
	include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Viewheader.include.php'; //this sends headers
?>
<script src="/Themes/BevoMedia/ovault.mystats.js" type="text/javascript"></script>

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
					<li><a<?php echo ($myNws['page'] == 'performance' ? ' class="active"' : ''); ?> href="?page=performance">Performance Report<span></span></a></li>
					<li><a<?php echo ($myNws['page'] == 'subids' ? ' class="active"' : ''); ?> href="?page=subids">Sub ID Report<span></span></a></li>
					<?php if($myNws['righttable']->nw->offercount > 0)
						echo '<li><a class="btn ovault_mystats_findallnwoffers" href="#" data-nwid="'.$myNws['righttable']->nw->id.'" title="View all '.$myNws['righttable']->nw->offercount.' offers on this network">Offers</a></li>';
					?>
				</ul>
			</div><!--close tabs-->
			<div class="content">
				<div class="conttop">
					<div class="top topfull">
						<h2><?php echo $myNws['righttable']->nw->title; echo ($myNws['page'] == 'subids' ? ' SubID Report' : ''); ?></h2>
						
						<form method="get" action="" name="frmRange" class="datetable">
							<input type="hidden" name="network" value="<?php echo $myNws['righttable']->nw->id ?>" />
							<input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo $myNws['current_range']; ?>" />
							<input class="formsubmit" type="submit" />
						  </form>
						
						<div class="clear"></div>						
						
					</div><!--close top-->
					
					<div class="clear"></div>
				</div><!--close conttop-->
				
				<?php if($myNws['page'] == 'performance') { ?>
					
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
				
				<?php } //endif performance page 
				?>
				
					<table cellspacing="0" cellpadding="3" border="0" class="btable">
						<?php echo $myNws['righttable']->thead.'<tbody>'.$myNws['righttable']->table.'</tbody>'.$myNws['righttable']->tfoot; ?>
					</table>
					
				<?php if($righttablerows > 0) 
					echo '<a class="tbtn floatright" href="?ExportTo=CSV">Export to CSV</a>'; //should work without params since we use cookiezz
				?>
					
				<div class="clear"></div>
			</div><!--close content-->
		
		<?php } //endif righttable
		?>
	
	</div><!--close #oright-->
	<div class="clear"></div>	
</div><!--close .pagecontent#ovault-->
