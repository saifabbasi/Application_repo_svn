<?php
require_once(PATH . "Legacy.Abstraction.class.php");



global $filter_keyword;
global $filter_visitorip;
global $filter_ppcaccount;
global $filter_ppcprovider;
global $filter_ppccampaign;
global $filter_ppcadgroup;
global $filter_ppcadvar;

$request = array_merge($_COOKIE, $_GET);


$filter_ppcprovider = $this->provider;
$filter_ppccampaign = $this->campaign;

/* HTML */
global $userId;
$userId = $this->User->id;
$stDate = $this->StartDate;
$enDate = $this->EndDate;
$dateRange = ($stDate == $enDate) ? date('m/d/Y', strtotime($stDate)) : date('m/d/Y', strtotime($stDate)) . ' - ' . date('m/d/Y', strtotime($enDate));
$filtering_sql = $this->filter->getSql();
$filtering_rev_sql = $filtering_sql[0];
$filtering_cost_sql = $filtering_sql[1];


?>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','ppv'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
<?php


global $sort, $dir;
$sort = 'data';
if(isset($_GET['sort']))
	$sort = $_GET['sort'];
$dir = 1;
if(isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'desc')
	$dir = -1;

function custom_sort($a, $b)
{
	global $sort, $dir;
    if ($a->{$sort} == $b->{$sort}) {
        return 0;
    }
    return ($a->{$sort} < $b->{$sort}) ? ($dir*-1) : ($dir*1);
}

uasort($this->StatRows, 'custom_sort');

function get_column_sort_link($a, $b)
{
	$img = '';
	$tg = array();
	$tg = $_GET;
	$qs = '?';
	if(!isset($tg['sort']))
		$tg['sort'] = '';
	if(!isset($tg['sort_dir']))
		$tg['sort_dir'] = '';
		
	if($tg['sort'] == $b)
	{
		if($tg['sort_dir'] == 'desc')
		{
			$tg['sort_dir'] = '';
			$img = '<img src="/assets/images/sort_desc.gif"/>';
		}else{
			$tg['sort_dir'] = 'desc';
			$img = '<img src="/assets/images/sort_asc.gif"/>';
		}
	}
	$tg['sort'] = $b;
	foreach($tg as $k=>$g)
	{
		$qs .= $k . '=' . $g . '&';
	}
	echo "<a href='$qs'>$a</a>" . $img;
}

?>

<?php
$this->filter->show_ppv_filtering_table();
?>


<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
	    <td class="hhl">&nbsp;</td>
		<td><?php get_column_sort_link('SubID', 'data'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Clicks', 'sumClick'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Click Throughs', 'sumClicks'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('CTR', 'sumClicks'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Conv', 'sumConv'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Conv %', 'sumConv'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Earnings', 'revenue'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Cost', 'cost'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Profit', 'profit'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Avg CPC', 'cpc'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Avg EPC', 'epc'); ?></td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
		<?php
		$i = 0;
		$total_clicks = 0; $total_conversions = 0; $total_revenue = 0; $total_cost = 0; $total_profit = 0; $total_sumClicks = 0;
		if(sizeof($this->StatRows)>0)
		{
		foreach($this->StatRows as $key=>$row):?>
		<?php
			$row->profit = $row->sumRevenue - $row->sumCost;
			$row->cpc = $row->epc = $row->convPercent = 0;
			if($row->sumClick > 0) {
				$row->cpc = $row->sumCost / $row->sumClick;
				$row->epc = $row->sumRevenue / $row->sumClick;
				$row->ctr = $row->sumClicks / $row->sumClick;
			}
			if ($row->sumClicks > 0) {
				$row->convPercent = $row->sumConv / $row->sumClicks;
			}
		?>
				<tr class='show row_id'>
					<td class="border">&nbsp;</td>
					<td>
						<?php print $row->data; ?>&nbsp;
					</td>
					<td class="number"><?php print number_format($row->sumClick, 0); ; ?></td>
					<td class="number"><?php print number_format($row->sumClicks, 0); ; ?></td>
					<td class="number"><?php print number_format($row->ctr*100, 2); ; ?>%</td>
					<td class="number"><?php print number_format($row->sumConv, 0); ; ?></td>
					<td class="number"><?php print number_format($row->convPercent*100, 2);?>%</td>
					<td class="number">$<?php print number_format($row->sumRevenue, 2); ; ?></td>
					<td class="number">$<?php print number_format($row->sumCost, 5); ; ?></td>
					<td class="number">$<?php print number_format($row->profit, 2); ; ?></td>
					<td class="number">$<?php print number_format($row->cpc, 2); ; ?></td>
					<td class="number">$<?php print number_format($row->epc, 2); ; ?></td>
					<td class="tail">&nbsp;</td>
				</tr>
				
				<?php
				$total_clicks += $row->sumClick;
				$total_sumClicks += $row->sumClicks;
				$total_conversions += $row->sumConv;
				$total_revenue += $row->sumRevenue;
				$total_cost += $row->sumCost;
				$total_profit += $row->profit;
		endforeach;
		}
		else
		{
			?>
			<tr>
				<td class="border">&nbsp;</td>
				<td colspan="9">No keywords found for the selected time frame.</td>
				<td class="tail">&nbsp;</td>
			</tr>
			<?php
		}
		
		$total_convPercent = ($total_sumClicks == 0) ? 0 : $total_conversions / $total_sumClicks * 100;
		$total_ctr = ($total_clicks == 0) ? 0 : $total_sumClicks / $total_clicks * 100;
		$total_cpc = ($total_clicks == 0) ? 0 : $total_cost / $total_clicks;
		$total_epc = ($total_clicks == 0) ? 0 : $total_revenue / $total_clicks;
		?>
		<tr class="total">
			<td class="border">&nbsp;</td>
			<td>Total</td>
			<td class="number"><?php echo number_format($total_clicks, 0); ?></td>
			<td class="number"><?php echo number_format($total_sumClicks, 0); ?></td>
			<td class="number"><?php echo @number_format($total_ctr, 2); ?>%</td>
			<td class="number"><?php echo @number_format($total_conversions, 0); ?></td>
			<td class="number"><?php echo @number_format($total_convPercent, 2); ?>%</td>
			<td class="number">$<?php echo @number_format($total_revenue, 2); ?></td>
			<td class="number">$<?php echo @number_format($total_cost, 5); ?></td>
			<td class="number">$<?php echo @number_format($total_profit, 2); ?></td>
			<td class="number">$<?php echo @number_format($total_cpc, 2); ?></td>
			<td class="number">$<?php echo @number_format($total_epc, 2); ?></td>
			<td class="tail">&nbsp;</td>
		</tr>
	</tbody>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="11">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>

