<?php 
	
	if (!isset($_GET['basicRequired']))
	{
?>
		<div id="pagemenu">
			<ul>
				<li><a href="/BevoMedia/Marketplace/">Marketplace<span></span></a></li>
				<li><a href="/BevoMedia/Marketplace/MentorshipProgram.html">Mentorship Program<span></span></a></li>
				<li><a class="active" href="/BevoMedia/Marketplace/Premium.html">Get Bevo Premium<span></span></a></li>
			</ul>
		</div>
<?
		echo $this->PageDesc->ShowDesc($this->PageHelper,false); 
	} else
	{
		echo '<div align="center" style="color: #f00;">Research Tools are only active for premium members.</div><br />';
	}
	
?>

<? if(@$_GET['from']=='selfhost') { ?>
<center><h1 style="color: red" id="selfhosterror">You need a Premium account to download Bevo Self Hosted</h1></center>
<? } ?>
<div class="sh-wrapper presa-page">
	<div class="sh-box sh-premium">
		<div class="sh-boxtop">
		</div>
		<div class="sh-content">
			
			<div class="presa-box">
				<h3><strong>Access</strong> to Bevo Self-Hosted</h3>
				<p>
					The Self-Hosted version of Bevo Media is designed for high volume publishers and allows all data to

be stored on their own servers to ensure 100% security. Bevo Self-Hosted is open source which allows

experienced publishers to integrate custom features. Get on demand stat updates and stay updated with

the latest version of Bevo Media.
				</p>		
			</div>	
			<div class="presa-box">
				<h3><strong>Premium</strong> Research Tools</h3>
				<p>
					Bevo Premium PPC,PPV and Spy tools make building keyword and URLS lists easy! A fast way of doing

your campaign research, that easily integrates in our campaign editor. Whether your an affiliate marketer

or local business, our PPC, PPV and Spy tools are huge time savers in building properly structured

campaigns!
				</p>		
			</div>	
			<div class="presa-box">
				<h3><strong>Unlimited</strong> API calls</h3>
				<p>With unlimited API calls, users can do as much volume as they want. This is especially useful for the campaign editor and publishers who take advantage of "Smart Mode" within their Bevo Tracker.</p>		
			</div>
			<div class="presa-box">
				<h3><strong>Designated</strong> Technical Contact</h3>
				<p>Bevo Premium users will have access to designated technical support contact. With the help of Bevo Technical Support, Bevo becomes maintenance free, whether its installation help or a extremely specific problem. Technical support is valuable in every technology industry, but as internet marketers, it takes a lot of pressure off and allows users to focus on optimization and expansion, the key to this industry.</p>
			</div>
			
			<div class="presa-box presa-fin">
				<h3><strong>all</strong> for only $200 a month!</h3>
			</div>
			
			<p>Enjoy all the features of Bevo Media for one flat fee! With features being added all the time, you can enjoy all of your internet marketing needs within one all inclusive platform!</p>
			<a class="button sh-upgradenow" href="/BevoMedia/Marketplace/PremiumSignup.html">Upgrade now for $200 per month</a>
		</div>
		<div class="sh-boxbutt presa-paymentoptions"></div>
	</div>
</div>