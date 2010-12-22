<div id="pagemenu">
	<ul>
		<li><a class="active" href="/BevoMedia/Marketplace/">Marketplace<span></span></a></li>
		<li><a href="/BevoMedia/Marketplace/MentorshipProgram.html">Mentorship Program<span></span></a></li>
		<?php if($this->User->membershipType != 'premium')
			echo '<li><a href="/BevoMedia/Marketplace/Premium.html">Get Bevo Premium<span></span></a></li>';?>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false); ?>

<div class="pagecontent pbs-wrapper">
					
	<div class="soapyell">
		<div class="soapyelltop"></div>
		<p class="soapyell-exmark">Your account status has not been upgraded, you're still on a <img src="/Themes/BevoMedia/img/pbs_badge_accstandard.gif" alt="" /> account.</p>
		<div class="soapyellbutt"></div>
	</div>
		
	<a class="button tryagain-pbs" href="PremiumSignup.html">Try Again</a>
	
</div><!-- end premium buy steps wrapper -->