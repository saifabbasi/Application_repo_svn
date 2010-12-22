<?php
//*************************************************************************************************
$Temp = (realpath(substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' ));
include($Temp . DIRECTORY_SEPARATOR . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');

require_once(PATH . "Legacy.Abstraction.class.php");
//*************************************************************************************************

		include PATH.'images/charts.php';

//*************************************************************************************************

		if (!isset($_GET['crNetworks'])) $_GET['crNetworks'] = '';
		$crNetworks		= is_array($_GET['crNetworks']) ? $_GET['crNetworks'] : array();
		$crStartDate	= $_GET['crStartDate'];
		$crEndDate		= $_GET['crEndDate'];
		$userId			= $_GET['userId'];

//*************************************************************************************************

		if ( $crStartDate == "" || $crEndDate == "" )
		{
			echo "Report parameters not found";
			die;
		}

//*************************************************************************************************

		function ksOnlyNums($var) { return ((int)$var > 0); }
		$networks		= array_filter($crNetworks, 'ksOnlyNums');
		
		function ksOnlyNotNums($var) { return ((int)$var <= 0); }
		$models			= array_filter($crNetworks, 'ksOnlyNotNums');

		$networkSql		= "";
		if ( count($networks) > 0 )
			$networkSql	.= "AND N.ID IN (".implode(',', $networks).") ";

		if ( count($models) > 0 )
			$networkSql	.= "AND N.MODEL IN ('".implode("','", $models)."') ";

//*************************************************************************************************

		$fromY			= substr($crStartDate, 0, 4);
		$fromM			= substr($crStartDate, 5, 2);
		$fromD			= substr($crStartDate, 8, 2);

		$toY			= substr($crEndDate, 0, 4);
		$toM			= substr($crEndDate, 5, 2);
		$toD			= substr($crEndDate, 8, 2);

		$totDays		= mktime(0, 0, 0, $toM, $toD, $toY) - mktime(0, 0, 0, $fromM, $fromD, $fromY);
		$totDays		= $totDays / (60 * 60 * 24) + 1;
		$incDays		= floor($totDays / 10);
		$incDays		= $incDays == 0 ? 1 : $incDays;

		$arrDays		= array("");
		$arrData		= array("");

		for ( $i=1; $i<=$totDays; $i++ )
		{
			$thisDate = date("Y-m-d", mktime(0, 0, 0, $fromM, $fromD+$i, $fromY));
			$showDate = date("M d", mktime(0, 0, 0, $fromM, $fromD+$i, $fromY));
			$sql = "SELECT SUM(S.CONVERSIONS) AS CONVERSIONS, SUM(S.REVENUE)*(100-N.adminCommission)/100 AS REVENUE FROM bevomedia_aff_network N, bevomedia_user_aff_network UAN, bevomedia_user_aff_network_subid S WHERE S.user__id = '".$userId."' AND UAN.user__id = '".$userId."' AND UAN.network__id = N.ID AND UAN.network__id = S.network__id ".$networkSql." AND S.statDate = '".$thisDate."'";

			$sumConv	= 0;
			$sumRev		= 0;
			$result = LegacyAbstraction::executeQuery($sql);
			if ( $row = LegacyAbstraction::getRow($result) )
			{
				$sumConv	= (int)$row['CONVERSIONS'];
				$sumRev		= (float)$row['REVENUE'];
			}

			$arrDays[$i]	= $i % $incDays == 0 ? $showDate : "";
			$arrData[$i]	= $sumRev;
		}

//*************************************************************************************************

		$chart['axis_category']		= array ( 'size'=>9, 'color'=>"333333", 'alpha'=>75, 'skip'=>0 ,'orientation'=>"diagonal_down" );
		$chart['axis_ticks']		= array ( 'value_ticks'=>false, 'category_ticks'=>true, 'major_thickness'=>2, 'minor_thickness'=>1, 'minor_count'=>1, 'major_color'=>"000000", 'minor_color'=>"222222" ,'position'=>"inside" );
		$chart['axis_value']		= array ( 'min'=>0, 'size'=>8, 'color'=>"333333", 'alpha'=>50, 'steps'=>6, 'prefix'=>"", 'suffix'=>"", 'decimals'=>2, 'separator'=>"", 'show_min'=>false );

		$chart['chart_data']		= array ( $arrDays, $arrData );

		$chart['chart_grid_h']		= array ( 'alpha'=>10, 'color'=>"000000", 'thickness'=>1 );
		$chart['chart_grid_v']		= array ( 'alpha'=>10, 'color'=>"000000", 'thickness'=>1 );
		$chart['chart_pref']		= array ( 'line_thickness'=>1, 'point_shape'=>"circle", 'fill_shape'=>false );
		$chart['chart_rect']		= array ( 'x'=>30, 'y'=>0, 'width'=>250, 'height'=>155, 'positive_color'=>"ffffff", 'positive_alpha'=>90, 'negative_color'=>"ffffff", 'negative_alpha'=>90 );
		$chart['chart_transition']	= array ( 'type'=>"slide_left", 'delay'=>.5, 'duration'=>.5, 'order'=>"series" );

		$chart['chart_type']		= 'line';

		$chart['legend']			= array ( 'layout'=>"horizontal", 'bullet'=>"line", 'font'=>"arial", 'bold'=>true, 'size'=>12, 'color'=>"222222", 'alpha'=>85, 'x'=>-1000, 'y'=>0, 'width'=>280, 'height'=>5, 'margin'=>5, 'fill_color'=>"ffffff", 'fill_alpha'=>7, 'line_color'=>"000000", 'line_alpha'=>0, 'line_thickness'=>0 );

		$chart['series_color']		= array ( "222222", "1989D3" );

		//print_r($chart);die;
		SendChartData ( $chart );

//*************************************************************************************************

		function closePopupWindow($msg)
		{
			echo "<script>alert('".str_replace("'", "`", $msg)."');</script>";
		}

//*************************************************************************************************
?>
