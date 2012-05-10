<?php 
	##########TEMP include dummy db
	include_once dirname(__FILE__).'/_APPS_DUMMY_DB.php';
	
	$currentID = '';
	$error = true;
	$backURL = '/BevoMedia/User/AppStore.html';
	
	/* GET app id */
	if(isset($_GET['id']) && $_GET['id'] != '' && is_numeric($_GET['id'])) {
	
		$currentID = trim($_GET['id']);
		
		//check if it's a valid id
		foreach($apps as $app) {
			if($currentID == $app['ID']) {
				$currentAppData = $app;
				$backURL = '/BevoMedia/User/AppDetail.html?id='.$currentID;
				$error = false;
				break;
			}
		}
			
	}//endif get app id
	
	if(!$error) {
		/*GET action*/
		if(isset($_GET['l'])) { //launch
			$frameURL = $currentAppData['launchURL'];
			
		} elseif(isset($_GET['s'])) {//signup
			$frameURL = $currentAppData['signupURL'];
		
		} else { //else launch
			$frameURL = $currentAppData['launchURL'];
		}
	}//endif get action
	
?>
<link rel="stylesheet" href="/Themes/BevoMedia/apps-layout/apps_iframe.css" type="text/css" />

<div id="appbar">
	<div class="inner">
		<div class="floatleft">
			<a class="logo" href="<?php echo $backURL; ?>" title="Back to Bevo"><img src="https://s3.amazonaws.com/bevomedia-media/public/images/header/btn_headlogo.png" alt="" /></a>
			<a class="tbtn lblue" href="<?php echo $backURL; ?>">&#x25C0; to appstore</a>
		</div>
		
		<?php if(!$error) { ?>
			<a class="tbtn trans floatright" href="<?php echo $frameURL; ?>" target="_blank">break out of frame &#x25E5;</a>
			
			<div class="middle">
				<img src="/Themes/BevoMedia/apps-layout/img/icon_apps_teal.png" alt="" />
				<h3><?php echo $currentAppData['appName']; ?></h3>
				<a id="appadd" class="chkbtn wide slim trans txtteal j_appadd<?php echo (in_array($currentID, $userApps) ? ' checked' : ''); ?>" data-id="<?php echo $currentID; ?>" href="#" title="Integrate this app with Bevo for quick access">
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
	<?php if(!$error) { ?>
		<iframe src="<?php echo $frameURL; ?>" width="100%" height="100%" frameborder="0" scrolling="auto">test</iframe>
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
	
		/*	AJAX function not developed yet - all it needs to do is call appChangeMyApp(action) on success.
			you should be able to just fill in the correct php response script url and uncomment the function.
			remove the following line when implementing ajax*/
			
		appChangeMyApp(action);
		
		/*
		$.ajax({
			type: 'GET',
			url: php-response-script.php?id='+id+'&action='+action,
			success: function(r) {
				appChangeMyApp(action);
			},
			error: function(r) {
				$('.message').html('<p>Something went wrong! The app could not be integrated with your Bevo Media account. Please refresh the page and try again.</p>').slideDown(200);
			}
		});	
		*/
	}//endif proceed
	
	return false;
});//j_appadd

function appChangeMyApp(action) {
	if(action == 'add') {
		$('#appadd').addClass('checked');
		$('.message').html('<p><?php echo $currentAppData['appName']; ?> has been added to your "My Apps" page!</p>').slideDown(200).delay(5000).slideUp(200);
	} else {
		$('#appadd').removeClass('checked');
		$('.message').html('<p><?php echo $currentAppData['appName']; ?> has been removed your "My Apps" page!<br />You may still have an open account or a running subscription with the app itself. Please refer to the app itself if you\'d like to cancel that as well.</p>').slideDown(200).delay(5000).slideUp(200);
	}
}//appChangeMyApp()

</script>