<div id="pagemenu"></div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>


<?php 
	$signedUp =  ( 
					(($this->User->vaultID==0) && !$this->User->IsSubscribed(User::PRODUCT_FREE_SELF_HOSTED)) || 
				 	(($this->User->vaultID!=0) && !$this->User->IsSubscribed(User::PRODUCT_SELF_HOSTED_YEARLY_CHARGE))
		   		);

	$freeSelfHosted = $this->User->IsSubscribed(User::PRODUCT_FREE_SELF_HOSTED);
	$verified = ($this->User->vaultID!=0);
	$paidSelfHosted = $this->User->IsSubscribed(User::PRODUCT_SELF_HOSTED_YEARLY_CHARGE); 
	
	$signedUp = ( 
					 !$freeSelfHosted && !$verified || 
				 	 $verified && !$paidSelfHosted  
		   		); 
?>

<div class="sh-wrapper">
	
	<div class="sh-box sh-selfhosted">
		<div class="sh-boxtop"></div>
		<div class="sh-content">
			<ul>
				<li>Entirely Open-Source and Self-Hosted<span></span></li>
				<li>All data stored in databases on your own server, ensuring 100% security<span></span></li>
				<li>Easily integrate custom features in our fully documented, open-source environment<span></span></li>
				<li>Daily updates to the newest Bevo interface edits<span></span></li>
				<li>On-Demand affiliate network stats update<span></span></li>
				<li>Perfect solution for high-volume publishers who are concerned about security<span></span></li>
			</ul>
			<a class="button sh-downloadnow" href="<?php if (!$signedUp) {?>/BevoMedia/User/RackspaceWizard.html<?php } ?>">Download now for $600</a>
		</div>
		<div class="sh-boxbutt"></div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
<?php 
	$freeSelfHosted = $this->User->IsSubscribed(User::PRODUCT_FREE_SELF_HOSTED);
	$verified = ($this->User->vaultID!=0);
	$paidSelfHosted = $this->User->IsSubscribed(User::PRODUCT_SELF_HOSTED_YEARLY_CHARGE); 
	
	if ( 
			 !$freeSelfHosted && !$verified || 
		 	 $verified && !$paidSelfHosted  
   		)
	{
?>
	$('.sh-downloadnow').click(function() {

		var a = document.createElement('a');
		a.href = '/BevoMedia/Publisher/VerifySelfHosted.html?ajax=true';
		a.rel = 'shadowbox;width=640;height=480;player=iframe';
		Shadowbox.open(a);

	    return false;
		
});
<?php 
	} //if not signed up for self hosted
?>
}); //doc ready
</script>
