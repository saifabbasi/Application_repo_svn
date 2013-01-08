<?php 

/*
** depreciated 101119. button "my account" in header now links directly to "ChangeProfile.html"
*/

/* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<?php /*<li><a class="active" href="/BevoMedia/User/AccountInformation.html">My Account<span></span></a></li> */ ?>
			<li><a title='Change your Bevo Profile' href='/BevoMedia/User/ChangeProfile.html'>My Profile<span></span></a></li>
			<li><a title='View PPC Accounts' href='<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/Index.html#PPC'>My PPC Accounts<span></span></a></li>
			<li><a rel="shadowbox;width=320;height=200;player=iframe" title='Change Bevo Password' href='ChangePassword.html'>My Password<span></span></a></li>
			<li><a rel="shadowbox;width=480;height=250;player=iframe" title='Cancel Bevo Account' href='CancelAccount.html'>Cancel Account<span></span></a></li>
			<li><a title='Billing' href='Invoice.html'>Billing<span></span></a></li>
		</ul>
		
		<ul class="floatright">
			<li><a title='Verfiy My Account' href='https://affportal.bevomedia.com/user/add-credit-card'><strong>Verify Account Now</strong><span></span></a></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page
	?>

<?php if(isset($_GET['SubmitTicket'])):?>
<script language='javascript'>
	window.onload = function(){
		Shadowbox.open({
			content:    'SubmitTicket.html',
			player:     "iframe",
			title:      "Submit Ticket",
			height:     250,
			width:      360
		});
	};
</script>
<?php endif ?>