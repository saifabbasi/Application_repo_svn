<script language="javascript" src="/Themes/BevoMedia/jquery.js"></script>
<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>

<link href="/Themes/BevoMedia/lightbox_style.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/global.css" rel="stylesheet" type="text/css" />

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



<div class="lpop lpop_verify<?php echo $lpopWV = $lpopVid ? ' lpop_withvid' : ''; ?>">
	<div class="lpop_veriquired">
		<p>
		<?php 
			if (!isset($_GET['FirstLogin'])) {
		?>
			Account Status: Unverified<br />
			<span style="color: #f00;">LAST STEP: Verify your Bevo Media Account</span>
		<?php 
			} else {
		?>
			<span style="color: #f00;">Your network stats are not being pulled!</span>
		<?php 
			}
		?>
		</p>
	</div>                                                                                          
	
	<?php if($lpopVid)
		echo ShowMovie($lpopVid, 300, 220); ?>
	
	<ul class="iconlist iconlist_qmark">
		<li><p>What is account verification?</p> 
			<span>
			An unverified account gives you access to the most basic Bevo Media
			features, and offers limited functionality. To take full advantage of the
			Bevo Interface, please verify your account. It only takes a minute!
			</span>
		</li>
		<li><p>Why is it better to be verified?</p>
			<span>With a verified account, users can track keyword and campaign performance with exact revenue and expense data. Verified Bevo users can auto-sync all of their Network Stats, access the Premium Research Tools and view and retrieve their specific network offers. Also, users gain optimum use of the analytics and PPC management pages as all stats will sync automatically.</span>
		</li>
	</ul>
	
	<a class="btn btn_lpop_verify" id="VerifyLink" href="#">Verify Now!</a>	
	
	<a href="#" class="btn_lpop_no_verify" onclick="parent.Shadowbox.close(); return false;">No Thanks, I'd like to continue with an unverified account</a>

</div><!--close lpop_verify-->



<script type="text/javascript">
	$(document).ready(function() {
		$('#VerifyLink').click(function() {
			parent.window.location = '/BevoMedia/User/AddCreditCard.html';
			return false;
		});
	});
</script>
