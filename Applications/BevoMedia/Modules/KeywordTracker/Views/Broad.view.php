<?php
require_once(PATH . "Legacy.Abstraction.class.php");
global $user__id, $isSelfHosted;
$user__id = $this->User->id;
$isSelfHosted = $this->User->IsSelfHosted();

if(empty($sort_field))
	$sort_field = 'keyword';
$data = $this->data;

foreach($data as $Key=>$Value)
{
	$data[$Key]['profit'] = $data[$Key]['revenue'] - $data[$Key]['cost'];
	$data[$Key]['ctr'] = $Value['clicks'] > 0 ? $data[$Key]['conversions'] / $data[$Key]['clicks'] : 0;
	$data[$Key]['cpc'] = $Value['clicks'] > 0 ? $data[$Key]['cost'] / $data[$Key]['clicks'] : 0;
	$data[$Key]['epc'] = $Value['clicks'] > 0 ? $data[$Key]['revenue'] / $data[$Key]['clicks'] : 0;
}
$rawData = $data;
$aggData = array();

foreach($rawData as $row)
{
	$kw = str_replace(array('"', '[', ']'), '', $row['keyword']);
	$found = findExistAgg($aggData, $kw);
	
	if($found === false)
	{
		$temp = $row;
		$temp['raw'] = array($row);
		$temp['keyword'] = $kw;
		$aggData[] = $temp;
	}else{
		foreach($row as $k=>$v)
		{
			if($k != 'keyword' && $k != 'raw' && $k != 'ctr')
				$aggData[$found][$k] += $v;  	
		} 
		$aggData[$found]['ctr'] = ($aggData[$found]['clicks']>0)?$aggData[$found]['conversions']/$aggData[$found]['clicks']:0;
		$aggData[$found]['cpc'] = ($aggData[$found]['clicks']>0)?$aggData[$found]['cost']/$aggData[$found]['clicks']:0;
				
		$aggData[$found]['raw'][] = $row;
	}
}
//echo '<pre>'; print_r($aggData);
$data = $aggData;
global $sort, $dir;
$sort = 'keyword';
if(isset($_GET['sort']))
	$sort = $_GET['sort'];
$dir = 1;
if(isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'desc')
	$dir = -1;

function custom_sort($a, $b)
{
	global $sort, $dir;
    if ($a[$sort] == $b[$sort]) {
        return 0;
    }
    return ($a[$sort] < $b[$sort]) ? ($dir*-1) : ($dir*1);
}



//$data = sort_by_user_selection($data);



if(isset($_GET['ExportCSV']) && $_GET['ExportCSV'] == 'FILE')
{
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=Broad.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	print '"Keyword","Clicks","Conversions","CTR","Earnings","Cost","Profit","Avg CPC","Avg EPC"' . "\r\n";
	foreach($data as $row)
	{
		$temp = array();
		$temp['keyword'] = $row['keyword'];
		$temp['clicks'] = (isset($row['clicks'])?($row['clicks']):'0'); $row['clicks'] = $temp['clicks'];
		$temp['conversions'] = (isset($row['conversions'])?($row['conversions']):'0');
		$temp['ctr'] = ($row['clicks'] == 0) ? 0 : $row['conversions'] / $row['clicks'] * 100;
		$temp['revenue'] = (isset($row['revenue'])?($row['revenue']):'0'); $row['revenue'] = $temp['revenue'];
		$temp['cost'] = (isset($row['cost'])?($row['cost']):'0'); $row['cost'] = $temp['cost'];
		$temp['profit'] = (isset($row['profit'])?($row['profit']):'0');
		$temp['avgcpc'] = ($row['clicks'] == 0) ? 0 : $row['cost'] / $row['clicks'];
		$temp['avgepc'] = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];

		$roi = ($row['cost'] == 0) ? 0 : $row['profit'] / $row['cost'] * 100;
		$First = true;
		foreach($temp as $v)
		{
			echo ($First)?'':",";
			$First = false;
			print '"' . $v .'"';
		}
		print "\r\n";
	}
	exit;
}
?>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','ppc','bidded'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<?php $this->filter->show_filtering_table(true); ?>

<?php
function get_column_sort_link($a, $b)
{
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
		}else{
			$tg['sort_dir'] = 'desc';
		}
	}
	$tg['sort'] = $b;
	foreach($tg as $k=>$g)
	{
		$qs .= $k . '=' . $g . '&';
	}
	echo "<a href='$qs'>$a</a>";
}

function findExistAgg($aggArr, $srch)
{
	for($i=0; $i<sizeOf($aggArr); $i++)
	{
		if($aggArr[$i]['keyword'] == $srch)
		{
			return $i;
		}
	}
	return false;
}

uasort($data, 'custom_sort');

?>

<style type="text/css">
.show {cursor: pointer;}
.hide {display: none;}
</style>

<script language="javascript">
$(document).ready(function(){
	$("tr.show").click(function(e){
		var classname = '.hide.'+this.className.split(' ')[1];
		var elem = $(classname)[0];
		if(elem.style.display == '' || elem.style.display == 'none')
		     $(classname).show();
		else
		{
		     $(classname).hide();
		}
		//$('.hide.'+this.className.split(' ')[1]).toggle();
		return false;
    });
});
</script>
<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
	    <td class="hhl">&nbsp;</td>
		<td><?php get_column_sort_link('Keyword', 'keyword'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Clicks', 'clicks'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Conv', 'conversions'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Conv %', 'ctr'); ?></td>
		<td style="text-align: center;"><?php get_column_sort_link('Earnings', 'revenue'); ?></td>
		<?php if($this->CostView != 'none'){?>
			<td style="text-align: center;"><?php get_column_sort_link('Cost', 'cost'); ?></td>
			<td style="text-align: center;"><?php get_column_sort_link('Profit', 'profit'); ?></td>
		<?php  }  ?>
		
		<?php if($this->CostView != 'none'){?>
			<td style="text-align: center;"><?php get_column_sort_link('Avg CPC', 'cpc'); ?></td>
			<td style="text-align: center;"><?php get_column_sort_link('Avg EPC', 'epc'); ?></td>
		<?php  } ?>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
		<?php
		$i = 0;
		$total_clicks = 0; $total_conversions = 0; $total_revenue = 0; $total_cost = 0; $total_profit = 0;
		if(count($data) > 0)
		{
			foreach($data as $key=>$row)
			{
				if(!isset($row['cost']))
					$row['cost'] = 0;
					
				?>
				<tr class='show row_id<?php echo $key?>'>
					<td class="border">&nbsp;</td>
					<td>
						<?php if(false && sizeOf($row['raw'])>1):?>
							<div style='position: absolute;'><span style='position:relative; left:-20px;'>+</span></div>
						<?php endif?>
						<?php echo htmlentities($row['keyword']) ?>
					</td>
					<td class="number"><?php echo number_format(@$row['clicks'], 0); ?></td>
					<td class="number"><?php echo number_format(@$row['conversions'], 0); ?></td>
					<td class="number"><?php echo number_format(@$row['ctr'], 2); ?>%</td>
					<td class="number">$<?php echo number_format(@$row['revenue'], 2); ?></td>
					<?php if($this->CostView != 'none'){?>
						<td class="number">$<?php echo number_format(@$row['cost'], 2); ?></td>
						<td class="number">$<?php echo number_format(@$row['profit'], 2); ?></td>
					<?php  } ?>
					
					
					<?php if($this->CostView != 'none'){?>
						<td class="number">$<?php echo number_format(@$row['cpc'], 2); ?></td>
						<td class="number">$<?php echo number_format(@$row['epc'], 2); ?></td>
					<?php } ?>
					<td class="tail">&nbsp;</td>
				</tr>
				<?php foreach($row['raw'] as $rawRow):?>
					<tr class='hide row_id<?php echo $key?>' style="color: #888888;">
						<td class="border">&nbsp;</td>
						<td>&nbsp;<?php echo htmlentities($rawRow['keyword']); ?></td>
						<td class="number"><?php echo number_format($rawRow['clicks'], 0); ?></td>
						<td class="number"><?php echo number_format($rawRow['conversions'], 0); ?></td>
						<td class="number"><?php echo number_format($rawRow['ctr'], 2); ?>%</td>
						<td class="number">$<?php echo number_format($rawRow['revenue'], 2); ?></td>
						<?php if($this->CostView != 'none'){?>
    						<td class="number">$<?php echo number_format($rawRow['cost'], 2); ?></td>
    						<td class="number">$<?php echo number_format($rawRow['profit'], 2); ?></td>
						<?php } ?>
						
						<?php if($this->CostView != 'none'){?>
							<td class="number">$<?php echo number_format($rawRow['cpc'], 2); ?></td>
							<td class="number">$<?php echo number_format($rawRow['epc'], 2); ?></td>
						<?php } ?>
						<td class="tail">&nbsp;</td>
					</tr>
				<?php endforeach?>
				
				<?php
				$total_clicks += @$row['clicks'];
				$total_conversions += @$row['conversions'];
				$total_revenue += @$row['revenue'];
				$total_cost += $row['cost'];
				$total_profit += $row['profit'];
			}
		}
		else
		{
			?>
			<tr>
				<td class="border">&nbsp;</td>
				<td colspan="<?= ($this->CostView != 'none') ? 9 : 6 ?>">No keywords found for the selected time frame.</td>
				<td class="tail">&nbsp;</td>
			</tr>
			<?php
		}
		
		$total_ctr = ($total_clicks == 0) ? 0 : $total_conversions / $total_clicks * 100;
		$total_cpc = ($total_clicks == 0) ? 0 : $total_cost / $total_clicks;
		$total_epc = ($total_clicks == 0) ? 0 : $total_revenue / $total_clicks;
		?>
		<tr class="total">
			<td class="border">&nbsp;</td>
			<td>Total</td>
			<td class="number"><?php echo number_format($total_clicks, 0); ?></td>
			<td class="number"><?php echo @number_format($total_conversions, 0); ?></td>
			<td class="number"><?php echo @number_format($total_ctr, 2); ?>%</td>
			<td class="number">$<?php echo @number_format($total_revenue, 2); ?></td>
			<?php if($this->CostView != 'none'){?>
    			<td class="number">$<?php echo @number_format($total_cost, 2); ?></td>
    			<td class="number">$<?php echo @number_format($total_profit, 2); ?></td>
			<?php } ?>
			
			
			<?php if($this->CostView != 'none'){?>
				<td class="number">$<?php echo @number_format($total_cpc, 2); ?></td>
				<td class="number">$<?php echo @number_format($total_epc, 2); ?></td>
			<?php } ?>
			<td class="tail">&nbsp;</td>
		</tr>
	</tbody>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="<?= ($this->CostView != 'none') ? 9 : 6 ?>">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>
<? if($this->CostView=='smart') { ?>
If you created your campaign today, Smart View won't work until tomorrow.
<? } ?>

<a class="tbtn floatright" href='?<?php echo $_SERVER['QUERY_STRING']?>&ExportCSV=FILE' >Export to CSV</a>

<br/><br/>
