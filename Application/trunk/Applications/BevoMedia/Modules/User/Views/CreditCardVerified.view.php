
	<div id="pagemenu"></div>

	
	<div id="pageinfo" class="sub">
		<h2>Add Payment Method</h2>	
	</div>
	
	<div class="clear"></div>

	<br /><br /><br />
	
	<div class="content" align="center">
		
		<?php 
			if (isset($_GET['adscout']))
			{
				echo 'Your credit card has been successfully verified and purchased AdScout. You are now being redirected to the AdScout Interface.';
			} else 
			{
				echo 'Your credit card has been successfully verified. You may continue to use BevoMedia.';
			}
		?>
		
		<br />
			
	</div>
	
	
	<script type="text/javascript">
	
		setTimeout('window.location = "/BevoMedia/User/AppDetail.html?id=18";', 3000);
	
	</script>