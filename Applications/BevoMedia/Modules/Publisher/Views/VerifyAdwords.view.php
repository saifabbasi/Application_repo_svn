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
		<h2>Google API Fees</h2>
		<p>Google charges API fees to sync stats to a third party interface. Bevo Media publishers are responsible for their own API charges.</p>
	</div>
	
	<?php if($lpopVid)
		echo ShowMovie($lpopVid, 380, 290); ?>
	
	<ul class="soapchecklist">
		<li><p>Auto Sync'ed stats directly from Google</p>
			<span>Google will send your campaign stats directly to your Bevo Interface, allowing you to optimize your campaigns using exact campaign data.</span>
		</li>
		<li><p>Edit Campaigns on the Fly</p>
			<span>Having the Adwords API enabled allows users to upload campaigns instantly to Adwords using the Bevo Campaign Editor. Create and edit campaigns via API with the campaign editor.</span>
		</li>
		<li><p>Your API fees vary based on the size of your account</p>
			<span>The average total API fee for publishers is $35 a month.</span>
		</li>
	</ul>
	
	<a class="btn btn_lpop_continuetopay" href="/BevoMedia/Publisher/VerifyAdwordsConfirm.html?ajax=true">Contine to Payment Page</a>	

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
	
	<?php if($lpopVid)
		echo ShowMovie($lpopVid, 300, 220); 
		else { ?>
			
		<div class="overaff">
			<p>Overnight Affiliate is a step-by-step walkthrough of every aspect a beginner affiliate needs to get a profitable campaign. It's packed with videos, step-by-step instructions, example campaigns, and weekly webinars where verifed users can get personalized one-on-one help specifically for their own campaigns.</p>
		</div>
			
	<?php } ?>	
	
	<a class="btn btn_lpop_verify" id="VerifyLink" href="#">Verify Now!</a>

	<div class="clear"></div>
	
	<div class="butt">
		<p class="title">What is account verification?</p>
		<p>As soon as you become a verified user, you can start tracking keyword and campaign performance with exact revenue and expense data, use the Geo- and Day-Targeting features, access the Premium Research Tools, instantly retrieve offer tracking links from your affiliate networks, and all stats will sync automatically.<br />
		<small><a href="#" onclick="parent.Shadowbox.close();">No thanks, I'll continue with the limited, free version of Bevo Media.</a></small></p>
	</div>

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
