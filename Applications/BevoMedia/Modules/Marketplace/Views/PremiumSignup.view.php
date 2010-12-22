<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Marketplace/">Marketplace<span></span></a></li>
		<li><a href="/BevoMedia/Marketplace/MentorshipProgram.html">Mentorship Program<span></span></a></li>
		<li><a class="active" href="/BevoMedia/Marketplace/Premium.html">Get Bevo Premium<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false); ?>

<div class="pagecontent pbs-wrapper">
	<form method="post" action="PremiumSubmit.html" class="appform">
		<input type='hidden' name='user__id' value='<?=$this->User->id?>'/>
		<label>
			<span>Your Email Address:</span>
			<input type="text" class="formtxt" id="Email" name="Email" value='<?php print $this->User->email; ?>' maxlength="255" />
		</label>
		<label>
			<span>Your Phone Number:</span>
			<input type="text" class="formtxt" id="Phone" name="Phone" value='<?php print $this->User->phone; ?>' maxlength="255" />
		</label>
		<label>
			<span></span>
			<input type="submit" class="formsubmit continue-pbs" value="Continue" />
		</label>
	</form>
</div><!--close pagecontent-->

<?php /*

<div class="pbs-wrapper pbs-step1">

	<div class="pbs-crumbs"></div>
	
	<div class="pbs-box">		
		<form method="post" action="PremiumReview.html">
			<div class="row">
				<label for="Email">Your Email Address:</label>
				<div class="formtxtwrap">
					<input type="text" class="formtxt" id="Email" name="Email" value='<?php print $this->User->email; ?>' maxlength="255" />
				</div>
			</div>
			<div class="row">
				<label for="Phone">Your Phone Number:</label>
				<div class="formtxtwrap">
					<input type="text" class="formtxt" id="Phone" name="Phone" value='<?php print $this->User->phone; ?>' maxlength="255" />
				</div>
			</div>
			<div class="row">
				<input type="submit" class="formsubmit continue-pbs" value="Continue" />
			</div>
		</form>
	</div>
	
</div>*/ ?>