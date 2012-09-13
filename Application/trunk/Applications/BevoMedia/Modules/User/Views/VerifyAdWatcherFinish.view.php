<div class="wrap">
<script language="javascript" src="/Themes/BevoMedia/jquery.js"></script>
<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>
<style type="text/css">
li, div { font-family: Arial; font-size: 13px; }

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



<br /><br />


<div style="margin-left: 20px;">
Thank you for your payment.


Click <a href="/BevoMedia/User/App.html?id=18&l" target="_blank">here</a> to start using the AdScout tool.

<br /><br />

To access the Bevo Watcher in the future, go back to the Apps section and select Launch App.

<br /><br />

To receive an invoice for your item, please visit the
Billing section to Request an Invoice. Invoice will be sent within 24
hours via email.

<?php 
	if (isset($_GET['monthly'])) {
?>

<br /><br />

You can cancel, or upgrade your subscription anytime by going to the My Products section in your Account Information Section
<?php 
	}
?>

</div>


</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#VerifyLink').click(function() {
			parent.window.location = '/BevoMedia/User/AddCreditCard.html';
			return false;
		});
	});
</script>
