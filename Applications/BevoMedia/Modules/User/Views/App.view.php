<?php 
	##########TEMP include dummy db
//	include_once dirname(__FILE__).'/_APPS_DUMMY_DB.php';
	
	$backURL = '/BevoMedia/User/AppStore.html';
	
	$productId = $_GET['id'];
	$productInfo = $this->db->fetchRow("SELECT * FROM bevomedia_products WHERE ID = ?", $productId);
	
	 
	
	if ($productInfo!=null) {
		/*GET action*/
		if(isset($_GET['l'])) { //launch
			$frameURL = $productInfo->LaunchURL;
			
		} elseif(isset($_GET['s'])) {//signup
			$frameURL = $productInfo->SignupURL;
		
		} else { //else launch
			$frameURL = $productInfo->LaunchURL;
		}
		
		$backURL = '/BevoMedia/User/AppDetail.html?id='.$productInfo->ID;
	}//endif get action
	
	global $db;
	$db = $this->db;
	
	
	function isProductIntegrated($productId)
	{
		global $db;
		$productId = intval($productId);
		return ($db->fetchOne("SELECT id FROM bevomedia_integerated_user_products WHERE userId = ? AND productId = ?", array($_SESSION['User']['ID'], $productId))!=null);
	}
?>
<link rel="stylesheet" href="/Themes/BevoMedia/apps-layout/apps_iframe.css" type="text/css" />

<div id="appbar">
	<div class="inner">
		<div class="floatleft">
			<a class="logo" href="<?php echo $backURL; ?>" title="Back to Bevo"><img src="https://s3.amazonaws.com/bevomedia-media/public/images/header/btn_headlogo.png" alt="" /></a>
			<a class="tbtn lblue" href="<?php echo $backURL; ?>">&#x25C0; to appstore</a>
		</div>
		
		<?php if($productInfo!=null) { ?>
			<a class="tbtn trans floatright" href="<?php echo $frameURL; ?>" target="_blank">break out of frame &#x25E5;</a>
			
			<div class="middle">
				<img src="/Themes/BevoMedia/apps-layout/img/icon_apps_teal.png" alt="" />
				<h3><?php echo $productInfo->ProductName; ?></h3>
				<a id="appadd" class="chkbtn wide slim trans txtteal j_appadd <?php echo (isProductIntegrated($productInfo->ID) ? ' checked' : ''); ?>" data-id="<?php echo $productInfo->ID; ?>" href="#" title="Integrate this app with Bevo for quick access">
					<span class="check">&#x2714;</span>
					<span class="txtunchecked">ADD TO MY APPS</span>
					<span class="txtchecked">INTEGRATED <span class="small">(remove)</span></span>
				</a>
				<div class="clear"></div>
			</div>
			
		<?php } ?>
		
		<div class="clear"></div>
	</div><!--close inner-->
</div><!--close appbar-->

<div class="message"></div>

<div id="appframe">
	<?php if($productInfo!=null) { ?>
		<iframe src="<?php echo $frameURL; ?>" width="100%" height="93%" frameborder="0" scrolling="auto">test</iframe>
	<?php } else { ?>
		
		<div class="noframe">
			<h2>Ooops!</h2>
			<p>It looks like this is not a valid app! If this link has worked before, the app may have been removed from the Bevo Media Exchange.</p>
			<a class="tbtn lblue big" href="/BevoMedia/User/AppStore.html">to the appstore</a></p>
		</div>
		
	<?php } ?>
</div><!--close appframe-->

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script type="text/javascript">
//integrate app with bevo
$('.j_appadd').live('click', function() {
	var	action = $('#appadd').hasClass('checked') ? 'remove' : 'add',
		id = $('#appadd').attr('data-id'),
		proceed = false;
		
	if(action == 'remove') {
		var proceed = confirm("Are you sure you want to remove this app from your Bevo Media account? Doing this will only remove the app from your \"My Apps\" page. It will NOT cancel any subscriptions or accounts you may have with the app itself. Please refer to the app if you want to cancel a subscription.\n\nProceed if you want to remove this app from your Bevo account (you'll still be able to access it from the app store anytime).");
	} else {
		proceed = true;	
	}
	
	if(proceed) {
	
		
			$.get('/BevoMedia/User/MyAppsAction.html?id='+$(this).data('id')+'&action='+action, function(data) {

				appChangeMyApp(action);

			});

			
	}//endif proceed
	
	return false;
});//j_appadd

function appChangeMyApp(action) {
	if(action == 'add') {
		$('#appadd').addClass('checked');
		$('.message').html('<p><?php echo $productInfo->ProductName; ?> has been added to your "My Apps" page!</p>').slideDown(200).delay(5000).slideUp(200);
	} else {
		$('#appadd').removeClass('checked');
		$('.message').html('<p><?php echo $productInfo->ProductName; ?> has been removed your "My Apps" page!<br />You may still have an open account or a running subscription with the app itself. Please refer to the app itself if you\'d like to cancel that as well.</p>').slideDown(200).delay(5000).slideUp(200);
	}
}//appChangeMyApp()

</script>