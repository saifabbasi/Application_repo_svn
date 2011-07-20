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


<?php 
	if ($this->User->vaultID>0)
	{
		//youtube video ID if we are showing a video, else false
		//$lpopVid = 'UO7mV9_RUww'; // <-- TEMPORARY - this is an old video from the tutorials section!
?>

<div class="lpop lpop_license<?php echo $lpopWV = $lpopVid ? ' lpop_withvid' : ''; ?>">
	<div class="lpop_title">
		<h2>Bevo PPVSpy</h2>
		<p>Get full access to Bevo PPVSpy for only $385 /month or $999 /year!</p>
	</div>
	
	<?php if($lpopVid)
		echo ShowMovie($lpopVid, 380, 290); ?>
	
	<ul class="soapchecklist">
		<li><p>Open-Source and Self-Hosted</p>
			<span>Ensure 100% security with data stored on your own server and easily integrate customized features in our fully documented, open sourced environment.</span>
		</li>
		<li><p>On-Demand Network Stats Updates</p>
			<span>Get the most current stats with Bevo Self-Hosted by updating them as often as you want.</span>
		</li>
		<li><p>Perfect Solution for High Volume Publishers</p>
			<span>High volume publishers can monitor and sync their stats and simplify their entire process. Bevo Media Self-Hosted is built to scale to optimize campaigns for serious publishers.</span>
		</li>
	</ul>
	
	<a class="btn btn_lpop_continuetopay" href="/BevoMedia/Publisher/VerifyPPVSpyConfirm.html?ajax=true">Contine to Payment Page</a>	

</div><!--close lpop_license-->

<?php 
	}
	$lpopVid = false; //reset
?>


<?php 
	if ($this->User->vaultID==0) {
		
		//youtube video ID if we are showing a video, else false
		//$lpopVid = 'UO7mV9_RUww'; // <-- TEMPORARY - this is an old video from the tutorials section!
?>

<div class="lpop lpop_verify<?php echo $lpopWV = $lpopVid ? ' lpop_withvid' : ''; ?>">
	<div class="lpop_veriquired">
		<p>This feature requires your Bevo Media account<br />
		to be verified.</p>
	</div>     
	
	<div class="top">
		<p class="title">What is account verification?</p>
		<p>Right now, you're a free Bevo user with access to basic features. But Bevo is more than that: as soon as you verify your account, you'll be able to use all of the following features that are reserved for verified users:</p>
	</div>
	
	<?php if($lpopVid)
		echo ShowMovie($lpopVid, 300, 220); 
		else { ?>
		
		<div class="box">
			<ul class="soapchecklist">
				<li>Auto-sync your stats from affiliate networks</li>
				<li>Retrieve your affiliate links right from the interface</li>
				<li>Geotargeting and Day Targeting</li>
				<li>Geoparting and Day Parting</li>
				<li>Manage your PPC accounts</li>
			</ul>
			<ul class="soapchecklist nomargin">
				<li>Access to Premium Research Tools</li>
				<li>Access to list building technology</li>
				<li>Overnight Affiliate Course</li>
				<li>FREE COACHING</li>
				<li>...and much, much more! </li>
			</ul>
			<div class="clear"></div>
		</div>
			
	<?php } ?>	
	
	<a class="btn btn_lpop_verify" id="VerifyLink" href="#">Verify Now!</a>
	<a class="nolink" href="#" onclick="parent.Shadowbox.close();">No thanks, I'll continue with the limited, free version.</a>	
	

</div><!--close lpop_verify-->

<?php 
	}
?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#VerifyLink').click(function() {
			parent.window.location = '/BevoMedia/User/AddCreditCard.html';
			return false;
		});
	});
</script>
