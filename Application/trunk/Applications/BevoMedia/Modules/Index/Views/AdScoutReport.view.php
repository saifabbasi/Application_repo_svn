<?php 
$ip = (empty($_SERVER['HTTP_X_FORWARDED_FOR']))?$_SERVER['REMOTE_ADDR']:$_SERVER['HTTP_X_FORWARDED_FOR'];

if ( $ip != "72.53.144.53" && $ip != "66.75.25.49"  ) {
    // Access forbidden:
//    header('HTTP/1.1 403 Forbidden');
//    die;
}
?>

<div id="pageinfo" class="sub">
	<h2>AdScout Report</h2>	
</div>

<div class="clear"></div>


<br/><br/>

<div class='content'>

Today Sales: <?php echo $this->TodaySales; ?><br />
Today Revenue: $<?php echo $this->TodayRevenue; ?><br />

Total Sales: <?php echo $this->TotalSales; ?><br />
Total Revenue: $<?php echo $this->TotalRevenue; ?><br />
<br /><br />

<table width="100%">
	<tr style="text-align: left;">
		<th>E-mail</th>
		<th>Total Month Revenue</th>
		<th>All Time Revenue</th>
	</tr>
<?php 
	foreach ($this->Payments as $payment)
	{
?>
	<tr>
		<td><?php echo $payment->email; ?></td>
		<td>$<?php echo number_format($payment->TotalMonthRevenue, 2); ?></td>
		<td>$<?php echo number_format($payment->TotalRevenue, 2); ?></td>
	</tr>
<?php 
	}
?>
</table>

</div>