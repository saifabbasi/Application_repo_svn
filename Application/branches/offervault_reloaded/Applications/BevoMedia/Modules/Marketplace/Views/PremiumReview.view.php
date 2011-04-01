<!--


	100808 depreciated


-->
<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Marketplace/">Marketplace<span></span></a></li>
		<li><a href="/BevoMedia/Marketplace/MentorshipProgram.html">Mentorship Program<span></span></a></li>
		<li><a class="active" href="/BevoMedia/Marketplace/Premium.html">Get Bevo Premium<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false); ?>

<div class="pbs-wrapper pbs-step2">

	<div class="pbs-crumbs"></div>
	
	<div class="pbs-box">		
		<form method="post" action="PremiumSubmit.html">
		<input type='hidden' name='user__id' value='<?=$this->User->id?>'/>
			<div class="row">
				<label for="Email">Your Email Address:</label>
				<div class="formtxtwrap formtxtwrap-disabled">
					<input disabled="disabled" type="text" class="formtxt formtxt-disabled" id="Email" name="email" maxlength="255" value="<?php echo $_POST['Email']?>" />
					<input type="hidden" name="Email" value="<?php echo $_POST['Email']?>" />
				</div>
			</div>
			<div class="row">
				<label for="Phone">Your Phone Number:</label>
				<div class="formtxtwrap formtxtwrap-disabled">
					<input disabled="disabled" type="text" class="formtxt formtxt-disabled" id="Phone" name="phone" maxlength="255" value="<?php echo $_POST['Phone']?>" />
					<input type="hidden" name="Phone" value="<?php echo $_POST['Phone']?>" />
				</div>
			</div>
			<div class="row">
				<p><a href="PremiumSignup.html">Go back</a> to edit, or <input type="submit" class="formsubmit continue-pbs" value="Continue" /></p>
			</div>
		</form>
	</div>
	
</div>