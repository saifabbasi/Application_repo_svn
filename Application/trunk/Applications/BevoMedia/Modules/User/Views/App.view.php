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
				<a id="appadd" class="chkbtn wide slim trans txtteal j_appadd<?php /*echo (in_array($currentID, $userApps) ? ' checked' : '');*/ ?>" data-id="<?php echo $currentID; ?>" href="#" title="Integrate this app with Bevo for quick access">
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
<div id="appframe">
	<?php if(!$error) { ?>
		<iframe src="http://affportal.bevomedia.com<?php /*echo $frameURL;*/ ?>" width="100%" height="100%" frameborder="0" scrolling="auto">test</iframe>
	<?php } else { ?>
		
		<div class="noframe">
			<h2>Ooops!</h2>
			<p>It looks like this is not a valid app! If this link worked before, the app may have been removed from the Bevo Media Exchange.</p>
			<a class="tbtn blue big" href="/BevoMedia/User/AppStore.html">to the appstore</a></p>
		</div>
		
	<?php } ?>
</div><!--close appframe-->

#########################
<div id="apps">
	<div class="sidebar">

		<h2 class="icon_apps_big">Bevo Apps</h2>
		<p>Explore some of the industry's best tools, right at your fingertips!</p>
		
		<a class="tbtn lblue" href="/BevoMedia/User/AppStore.html">&#x25C0; all apps</a>
		
		<h4><span>Categories</span></h4>
		<?php echo renderAppCatMenu($appCategories, $featuredApp); ?>
	
	</div><!--close sidebar-->
	<div class="colmain">
	
		<?php if($error) : ?>
		
			<h2>Ooops!</h2>
			<p>It looks like this is not a valid app! If this link worked before, the app may have been removed from the Bevo Media Exchange. Use the menu on the left to select a category, or <a class="tbtn trans" href="/BevoMedia/User/AppStore.html">click here</a> to view all apps.</p>
		
		<?php else : //if we have an app
		?>
	
			<div class="box slblue apptopnav">
				<a class="tbtn trans" href="/BevoMedia/User/AppStore.html">&#x25C0; back to all apps</a>
				<h2><?php echo $currentAppData['appName']; ?></h2>
			</div>
			
			<div class="box yell message hide"></div>
		
			<p>On this page, you can buy or launch this app. <?php
				if(in_array($currentID, $userApps)) {
					echo 'If you have already signed up with '.$currentAppData['appName'].' or bought it, <a class="j_appadd j_addonly" href="#">integrate it with Bevo now</a> and it will appear on your "My Apps" page.'; 
				} else {
					echo $currentAppData['appName'].' is currently integrated with your Bevo Media account. To remove it, click the remove button. Please note that any subscriptions or paid plans that you may have subscribed to from within the app will not be canceled when you remove the app from your Bevo Media account.';
				} ?>
			</p>
			
			<div class="topfeat">
				<div class="box slteal noshadow top">
					<div class="floatleft">
						<?php echo ($featuredApp == $currentID ? '<div class="icon_appofweek"></div>' : ''); ?>
						<img class="applogo" src="<?php echo $currentAppData['logoURL']; ?>" alt="" />
						<div class="clear"></div>
					</div>
					
					<div class="floatright">
						<?php 	//if free
						if($currentAppData['price'] == '') { ?>
							<span class="tbtn big doubleleft bordered"><strong class="txtdgreen">&#x2714; free</strong></span>
						<?php } else { ?>
							<span class="tbtn big doubleleft bordered">
								<img src="https://s3.amazonaws.com/bevomedia-media/public/images/header/formicon_dollar.png" alt="" /><?php echo $currentAppData['price']; ?>
							</span>								
						<?php }
						
						//if integrated
						if(in_array($currentID, $userApps)) { ?>
							<a class="tbtn big teal bold doubleright j_appframe" href="/BevoMedia/User/App.html?id=################################<?php echo $currentAppData['launchURL'] ?>">launch</a>
							
						<?php } else { 
							
							//if free
							if($currentAppData['price'] == '') { ?>								
								<a class="tbtn big dgreen bold doubleright j_appframe" href="<?php echo $currentAppData['signupURL']; ?>">sign up now</a>
							<?php } 
							//if paid
							else { ?>
								<a class="tbtn big dgreen bold doubleright j_appframe" href="<?php echo $currentAppData['signupURL']; ?>">buy now</a>
							<?php } ?>
							
						<?php }//endif integrated 
						?>
					</div>
					<div class="clear"></div>
				</div><!--close box-->
				<div class="box teal noshadow butt">
					<a id="appadd" class="chkbtn txtteal j_appadd<?php echo (in_array($currentID, $userApps) ? ' checked' : ''); ?>" data-id="<?php echo $currentID; ?>" data-launch="<?php echo $currentAppData['launchURL'] ?>" data-signup="<?php echo $currentAppData['signupURL'] ?>" href="#" title="Integrate this app with Bevo for quick access">
						<span class="check">&#x2714;</span>
						<span class="txtunchecked">ADD TO MY APPS</span>
						<span class="txtchecked">INTEGRATED <span class="small">(remove)</span></span>
					</a>
				
					<?php 
						if($currentAppData['descTitle']) {
							echo '<h3>'.$currentAppData['descTitle'].'</h3>';
						}
						
						if($currentAppData['descText']) {
							echo '<p>'.$currentAppData['descText'].'</p>';
						}
						
						if($currentAppData['descList'] && !empty($currentAppData['descList'])) {
							echo '<ul class="txtyell">';
							foreach($currentAppData['descList'] as $li) {
								echo '<li>&#x25B6; '.$li.'</li>';
							}
							echo '</ul>';
						}
					?>
					<div class="clear"></div>
					
				</div><!--close box-->			
			</div><!--close topfeat-->
			
			<?php if(!empty($currentAppData['descDetail'])) { ?>
				<h4><span>about <?php echo $currentAppData['appName']; ?></span></h4>
				
				<ul class="details">
					<?php foreach($currentAppData['descDetail'] as $li) { 
						echo '<li>'.$li.'</li>';	
					} ?>
				</ul>
				
			<?php }//if descdetail
			?>
		
		<?php endif; //error
		?>
	
	</div><!--close main-->
	<div class="clear"></div>
	
</div><!--close pagecontent-->

<script type="text/javascript">
//integrate app with bevo
$('.j_appadd').live('click', function() {
	var	action = $('#appadd').hasClass('checked') ? 'remove' : 'add',
		id = $('#appadd').attr('data-id'),
		proceed = false;
		
	if($(this).hasClass('j_addonly') && action == 'remove') {
		$('.message').html('<p><?php echo $currentAppData['appName']; ?> is already integrated with your Bevo Media account! To remove it, use the button below the "Launch" option.</p>').slideDown(200).delay(5000).slideUp(200);
		
	} else {
		
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
	}//endif addonly
	
	return false;
});//j_appadd

function appChangeMyApp(action) {
	if(action == 'add') {
		$('#appadd').addClass('checked');
		$('#apps .topfeat .box.top .floatright a.tbtn.doubleright').removeClass('dgreen').addClass('teal').attr('href', $('#appadd').attr('data-launch')).html('launch');
		$('.message').html('<p><?php echo $currentAppData['appName']; ?> has been added to your "My Apps" page!</p>').slideDown(200).delay(5000).slideUp(200);
	} else {
		$('#appadd').removeClass('checked');
		$('#apps .topfeat .box.top .floatright a.tbtn.doubleright').removeClass('teal').addClass('dgreen').attr('href', $('#appadd').attr('data-signup')).html('sign up');
		$('.message').html('<p><?php echo $currentAppData['appName']; ?> has been removed your "My Apps" page!<br />You may still have an open account or a running subscription with the app itself. Please refer to the app itself if you\'d like to cancel that as well.</p>').slideDown(200);
	}
}//appChangeMyApp()

</script>