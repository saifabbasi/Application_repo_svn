<?php
//*************************************************************************************************
$Temp = (realpath(substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' ));
include($Temp . DIRECTORY_SEPARATOR . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');

require_once(PATH . "Legacy.Abstraction.class.php");
//*************************************************************************************************

//*************************************************************************************************

		include PATH.'images/charts.php';

//*************************************************************************************************
global $userId, $isSelfHosted;
		
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

		$sql		= "SELECT N.ID AS network__id, SUM(S.CONVERSIONS) AS CONVERSIONS, SUM(S.REVENUE)*(100-N.adminCommission)/100 AS REVENUE FROM bevomedia_user_aff_network UAN, bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network_subid S ON S.network__id = N.ID AND S.user__id = '".$userId."' AND S.statDate>= '".$crStartDate."' AND S.statDate<= '".$crEndDate."' WHERE N.ISVALID = 'Y' AND UAN.network__id = N.ID AND UAN.user__id = '".$userId."' ".$networkSql." GROUP BY N.ID";
		//echo $sql;die;
		$res		= LegacyAbstraction::executeQuery($sql);

		$arrNets	= array(null);
		$arrData1	= array("Conversions");
		$arrData2	= array("Revenue ".SCRIPT_DOLLAR);

		while ( $row = LegacyAbstraction::getRow($res) )
		{
			if ( ($row['CONVERSIONS']===null) || ($row['CONVERSIONS']==0) ) continue;
		
			
			$arrNets[]	= LegacyAbstraction::getColumn("aff_network", $row['network__id']);
			$arrData1[]	= $row['CONVERSIONS']===null?'0':$row['CONVERSIONS'];
			$arrData2[]	= $row['REVENUE']===null?'0.000000':$row['REVENUE'];
			
		}

		if ( count($arrNets) <= 1 )
		{
			$arrNets[]	= 'none';
			$arrData1[]	= '0';
			$arrData2[]	= '0';
		}
		
//*************************************************************************************************

		$chart['axis_category']		= array ( 'size'=>9, 'color'=>"333333", 'alpha'=>75, 'skip'=>0 ,'orientation'=>"diagonal_down" );
		$chart['axis_ticks']		= array ( 'value_ticks'=>false, 'category_ticks'=>true, 'major_thickness'=>2, 'minor_thickness'=>1, 'minor_count'=>1, 'major_color'=>"000000", 'minor_color'=>"222222" ,'position'=>"inside" );
		$chart['axis_value']		= array ( 'min'=>0, 'size'=>8, 'color'=>"333333", 'alpha'=>50, 'steps'=>6, 'prefix'=>"", 'suffix'=>"", 'decimals'=>2, 'separator'=>"", 'show_min'=>false );

		$chart['chart_data']		= array ( $arrNets, /*$arrData1,*/ $arrData2 );

		$chart['chart_grid_h']		= array ( 'alpha'=>10, 'color'=>"000000", 'thickness'=>1 );
		$chart['chart_grid_v']		= array ( 'alpha'=>10, 'color'=>"000000", 'thickness'=>1 );
		$chart['chart_pref']		= array ( 'line_thickness'=>1, 'point_shape'=>"circle", 'fill_shape'=>false );
		$chart['chart_rect']		= array ( 'x'=>30, 'y'=>0, 'width'=>250, 'height'=>155, 'positive_color'=>"ffffff", 'positive_alpha'=>90, 'negative_color'=>"ffffff", 'negative_alpha'=>90 );
		$chart['chart_transition']	= array ( 'type'=>"slide_left", 'delay'=>.5, 'duration'=>.5, 'order'=>"series" );
		$chart['chart_label']		= array ( 'position'=>"middle-up", 'size'=>10, 'color'=>"FFFFFF", 'prefix'=>"", 'suffix'=>"", 'decimals'=>2 );

		$chart['chart_type']		= 'column';

		$chart['legend']			= array ( 'layout'=>"horizontal", 'bullet'=>"line", 'font'=>"arial", 'bold'=>true, 'size'=>12, 'color'=>"222222", 'alpha'=>85, 'x'=>0, 'y'=>0, 'width'=>280, 'height'=>5, 'margin'=>5, 'fill_color'=>"ffffff", 'fill_alpha'=>7, 'line_color'=>"000000", 'line_alpha'=>0, 'line_thickness'=>0, 'transition'=>"dissolve", 'delay'=>0, 'duration'=>.5 );

		$chart['series_color']		= array ( "156199", "1989D3" );

		//print_r($chart);die;
		SendChartData ( $chart );

//*************************************************************************************************
?>
