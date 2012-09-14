<?php 
$ip = (empty($_SERVER['HTTP_X_FORWARDED_FOR']))?$_SERVER['REMOTE_ADDR']:$_SERVER['HTTP_X_FORWARDED_FOR'];

if ( $ip != "72.53.144.53" && $ip != "64.150.157.16" && $ip != "64.138.200.174" ) {
    // Access forbidden:
    header('HTTP/1.1 403 Forbidden');
    die;
}
?>

<div id="pageinfo" class="sub">
	<h2>AdScout Report</h2>	
</div>

<div class="clear"></div>


<br/><br/>

<div class='content'>

<table width="100%">
	<tr style="text-align: left;">
		<th>E-mail</th>
		<th>Amount</th>
		<th>Deleted</th>
	</tr>
<?php 
	foreach ($this->Payments as $payment)
	{
?>
	<tr>
		<td><?php echo $payment->email; ?></td>
		<td><?php echo $payment->Price; ?></td>
		<td><?php echo ($payment->Deleted==1)?'Yes':'No'; ?></td>
	</tr>
<?php 
	}
?>
</table>

</div>