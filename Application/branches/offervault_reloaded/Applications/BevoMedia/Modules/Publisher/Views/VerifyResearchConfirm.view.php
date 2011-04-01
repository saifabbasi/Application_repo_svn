<script language="javascript" src="/Themes/BevoMedia/jquery.js"></script>
<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>

<link href="/Themes/BevoMedia/lightbox_style.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/global.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#tooltip{
	line-height: 1.231; font-family: Arial; font-size: 13px;
	position:absolute;
	border:1px solid #333;
	background:#f7f5d1;
	padding:2px 5px;
	display:none;
	width:285px;
	margin-left: -330px;
	}
.tooltip {
	color: #ffffff;
	text-decoration: none !important;
	font-weight: bold;
	font-size: 12pt;
	}
.tooltip.defaultLink {
	color: maroon;
	font-size: 12px;
	font-style: normal;
	font-weight: normal;
	font-size: 12px;
	}
.successInstall {
	background-color: #008800;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
.failInstall {
	background-color: #880000;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
.wrap {
	background-color: #fff;
	height: 100%;
}
</style>


<?php if(isset($this->message)): ?>
<p class="updated"><?php print htmlentities($this->message); ; ?></p>
<script type="text/javascript">
	window.setTimeout('closeThis()', 1500);

	function closeThis()
	{
		parent.Shadowbox.close();
	}
</script>
<?php endif; ?>

<?php 
	if (isset($_GET['Error']))
	{
?>
	<div style="border: 1px #E85163 solid; background-color: #F5AEB6; width: 560px; height: 45px; line-height: 45px; color: #000; text-align: center; margin-left: 40px;">
		<?php echo $_GET['Error'];?>
	</div>
	<br />
<?php 
	}
?>

<div class="lpop lpop_pay">
	<div class="lpop_title">
		<h2>Bevo Research</h2>
		<p>The Research tool is a premium feature that has a nominal licensing fee, for which you are free to use the feature for a full year, 24/7.</p>
	</div>
	
	<div class="lpop_content">
		<h2>Your Cart</h2>
		
		<div class="lpop_cart">
			<div class="lpop_cartbox">
				<h3 class="floatright">$120.00</h3>
				
				<h3>Bevo PPC Campaign Editor</h3>
				<p>1 year 24/7 access from tomorrow, <?php 
					echo date('l, F j Y',time()+60*60*24); /*tomorrow*/ ?> thru <?php echo date('F j, Y',time()+60*60*24*365); ?>.</p>
					
				<div class="clear"></div>				
			</div>
			<div class="lpop_cartbox lpop_securicons">
				<a class="btn btn_lpop_paynow" href="/BevoMedia/User/PayResearchYearly.html">Pay Now</a>
				<div class="clear"></div>
			</div>
			<div class="lpop_cartbox">
				<ul>
					<li>Your card on file will be billed for the above noted amount. You will receive immediate access to the feature.</li>
					<li>We will only bill your card once for the above mentioned license duration. You will receive notification when it’s about to run out, at which point you may choose to extend or cancel your license.</li>
				</ul>
				
				<div class="lpop_tos">
					<h2>Terms of Service</h2>
					
					<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
					
					<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
					
					<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
				</div>
			</div>
			<div class="lpop_cartbox last">
				<p>You agree to the terms of service b clicking the PayNow button.</p>
				<a class="btn btn_lpop_paynow" href="/BevoMedia/User/PayResearchYearly.html">Pay Now</a>
				<div class="clear"></div>
			</div>
		</div><!--close lpop_cart-->
	</div><!--close lpop_content-->
</div><!--close lpop_license-->

<script type="text/javascript">
	$(document).ready(function() {
		$('#VerifyLink').click(function() {
			parent.window.location = '/BevoMedia/User/CreditCard.html';
			return false;
		});
	});
</script>
