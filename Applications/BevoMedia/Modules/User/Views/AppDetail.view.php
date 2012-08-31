<?php 
	##########TEMP include dummy db
//	include_once dirname(__FILE__).'/_APPS_DUMMY_DB.php';
	
	$productId = $_GET['id'];
	$productInfo = $this->db->fetchRow("SELECT * FROM bevomedia_products WHERE ID = ?", $productId);
	
	
	
	/* GET app id */
//	$currentID = '';
//	$error = true;
//	if(isset($_GET['id']) && $_GET['id'] != '' && is_numeric($_GET['id'])) {
//	
//		$currentID = trim($_GET['id']);
//		
//		//check if it's a valid id
//		foreach($apps as $app) {
//			if($currentID == $app['ID']) {
//				$currentAppData = $app;
//				$error = false;
//				break;
//			}
//		}
//			
//	}//endif GET

	
	global $db;
	$db = $this->db;
	
	
	function isProductIntegrated($productId)
	{
		global $db;
		$productId = intval($productId);
		return ($db->fetchOne("SELECT id FROM bevomedia_integerated_user_products WHERE userId = ? AND productId = ?", array($_SESSION['User']['ID'], $productId))!=null);
	}
	
	function integrateProduct($productId)
	{
		global $db;
		
		$productId = intval($productId);
		
		if (!isProductIntegrated($productId))
		{
			$sql = "INSERT INTO bevomedia_integerated_user_products (userId, productId) VALUES ({$_SESSION['User']['ID']}, {$productId}); ";
			$db->exec($sql);
		}
	}
	
	function unintegrateProduct($productId)
	{
		global $db;
		$productId = intval($productId);
		$db->exec("DELETE FROM bevomedia_integerated_user_products WHERE (userId = {$_SESSION['User']['ID']}) AND (productId = {$productId}) ");
	}
	
	function getAppOfTheWeek()
	{
		global $db;
		$productId = $db->fetchOne("SELECT value FROM bevomedia_settings WHERE name = 'APP_OF_THE_WEEK' ");
		
		return $db->fetchRow("SELECT * FROM bevomedia_products WHERE (ID = ?)", $productId);
	}
	
	$appOfTheWeek = getAppOfTheWeek();
?>
<link rel="stylesheet" href="/Themes/BevoMedia/apps-layout/apps.css" type="text/css" />

<div id="pagemenu">
	<ul>
		<li><a class="active" href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/User/AppStore.html">App Store<span></span></a></li>
		<?php	//if user is subscribed to any paid app, show link
			if(	$this->User->IsSubscribed(User::PRODUCT_PPVSPY_MONTHLY)
			||	$this->User->IsSubscribed(User::PRODUCT_PPVSPY_YEARLY)
			||	$this->User->IsSubscribed(User::PRODUCT_FREE_PPVSPY)
			||	$this->User->IsSubscribed(User::PRODUCT_FREE_SELF_HOSTED)
			||	$this->User->IsSubscribed(User::PRODUCT_FREE_PPC)
			||	$this->User->IsSubscribed(User::PRODUCT_ADWATCHER_MONTHLY)
			||	$this->User->IsSubscribed(User::PRODUCT_ADWATCHER_YEARLY)
			)
				echo '<li><a href="/BevoMedia/User/MyProducts.html">Manage My Subscriptions</a></li>';
		?>
	</ul>
</div>


<div id="apps">
	<div class="sidebar">

		<h2 class="icon_apps_big">Bevo Apps</h2>
		<p>Explore some of the industry's best tools, right at your fingertips!</p>
		
		<a class="tbtn lblue" href="/BevoMedia/User/AppStore.html">&#x25C0; all apps</a>
		
		<h4><span>Categories</span></h4>
		
		<ul>
			<li><a href="/BevoMedia/User/AppCategory.html?category=my" class="txtblack">My Apps</a></li>
			<li><a class="txtred" href="/BevoMedia/User/AppDetail.html?id=<?php echo $appOfTheWeek->ID; ?>">App of the Week</a></li>
		<?php 
			$sql = "SELECT
						*
					FROM
						bevomedia_product_categories
					ORDER BY
						position
					";
			$rows = $this->db->fetchAll($sql);
			
			foreach ($rows as $row) {
				$class = "";
				if ($row->name=='Featured Apps') {
					$class = "txtred";
				}
		?>
			<li><a class="<?php echo $class;?>" href="/BevoMedia/User/AppCategory.html?category=<?php echo $row->id; ?>"><?php echo $row->name; ?></a></li>
		<?php 
			}
		?>
		</ul>
	
	
	</div><!--close sidebar-->
	<div class="colmain">
	
		<?php if($productInfo==null) : ?>
		
			<h2>Ooops!</h2>
			<p>It looks like this is not a valid app! If this link has worked before, the app may have been removed from the Bevo Media Exchange. Use the menu on the left to select a category, or <a class="tbtn trans" href="/BevoMedia/User/AppStore.html">click here</a> to view all apps.</p>
		
		<?php else : //if we have an app
		?>
	
			<div class="box slblue apptopnav">
				<a class="tbtn trans" href="/BevoMedia/User/AppStore.html">&#x25C0; back to all apps</a>
				<h2><?php echo $productInfo->ProductName; ?></h2>
			</div>
			
			<div class="box yell message hide"></div>
		
			<p>On this page, you can buy or launch this app. 
			
			<?php 
				if (!isProductIntegrated($productInfo->ID))
				{
					echo 'If you have already signed up with '.$productInfo->ProductName.' or bought it, <a class="j_appadd j_addonly" action="add" data-id="'.$productInfo->ID.'" href="#">integrate it with Bevo now</a> and it will appear on your "My Apps" page.';
				} else 
				{
					echo $productInfo->ProductName.' is currently integrated with your Bevo Media account. To remove it, click the remove button. Please note that any subscriptions or paid plans that you may have subscribed to from within the app will not be canceled when you remove the app from your Bevo Media account.';					
				}
			?>
			</p>
			
			<div class="topfeat">
				<div class="box slteal noshadow top">
				
					<div class="pic">
						<div class="applogo"><img src="/Themes/BevoMedia/apps-layout/img/applogos/<?php echo $productInfo->ID; ?>.jpg" alt="" /></div>
						<?php echo ($appOfTheWeek->ID == $productInfo->ID ? '<div class="icon_appofweek"></div>' : ''); ?>
					</div>
				
					<h2><?php echo $productInfo->ProductName; ?></h2>				
					
					<div class="floatright">
						<?php 	//if free
							if($productInfo->Price == 0) 
							{
						?>
							<span class="tbtn big doubleleft bordered"><strong class="txtdgreen">&#x2714; free</strong></span>
						<?php
							} else 
							{
						?>
							<span class="tbtn big doubleleft bordered">
								<img src="https://s3.amazonaws.com/bevomedia-media/public/images/header/formicon_dollar.png" alt="" />
								<?php
									$price = number_format($productInfo->Price, 2);
									$termLength = $productInfo->TermLength;
									
									if ($termLength==30) {
										$price = $price.'/mo';
									}
									
									echo $price;
								?>
							</span>								
						<?php 
							}
							echo '<pre>';
						
							$isProductPPVSpy = ($productInfo->ProductName=='PPVSpy');
							$isProductAdWatcher = ($productInfo->ProductName=='AdWatcher');
							$isUserSubscribedToPPVSpy = ( $this->User->IsSubscribed(User::PRODUCT_PPVSPY_MONTHLY) || $this->User->IsSubscribed(User::PRODUCT_PPVSPY_YEARLY) || $this->User->IsSubscribed(User::PRODUCT_FREE_PPVSPY) );
							$isUserSubscribedToAdWatcher = ( $this->User->IsSubscribed(User::PRODUCT_ADWATCHER_MONTHLY) || $this->User->IsSubscribed(User::PRODUCT_ADWATCHER_YEARLY) );
							 
							
						//if integrated
							if ( ($productInfo->SignupURL=='') && ($productInfo->LaunchURL!='') && !$isProductPPVSpy && !$isProductAdWatcher )
							{
						?>
							<a class="tbtn big teal bold doubleright j_appframe" href="/BevoMedia/User/App.html?id=<?php echo $productInfo->ID; ?>&l">launch</a>
						<?php 
							} else
							if (isProductIntegrated($productInfo->ID) && !$isProductPPVSpy && !$isProductAdWatcher) 
							{ 
						?>
							<a class="tbtn big teal bold doubleright j_appframe" href="/BevoMedia/User/App.html?id=<?php echo $productInfo->ID; ?>&l">launch</a>
							
						<?php
							} else 
							if (!$isProductPPVSpy && !$isProductAdWatcher)
							{
								//if free
								if($productInfo->Price == 0) 
								{
						?>								
								<a class="tbtn big dgreen bold doubleright j_appframe" href="/BevoMedia/User/App.html?id=<?php echo $productInfo->ID; ?>&s">sign up now</a>
						<?php 
								} else //if paid 
								{
						?>
								<a class="tbtn big dgreen bold doubleright j_appframe" href="/BevoMedia/User/App.html?id=<?php echo $productInfo->ID; ?>&s">buy now</a>
						<?php 
								} 
						?>
							
						<?php 
							} else //endif integrated
							if ($isProductPPVSpy)
							{
								if ($isUserSubscribedToPPVSpy)
								{
						?>
								<a class="tbtn big teal bold doubleright j_appframe" href="/BevoMedia/User/App.html?id=<?php echo $productInfo->ID; ?>&l">launch</a>
						<?php 
								} else 
								{
						?>
								<a class="tbtn big dgreen bold doubleright j_appframe j_add2cart" href="/BevoMedia/Publisher/VerifyPPVSpyConfirm.html">buy now</a>
						<?php 
								}
							} else //endif ppvspy
							if ($isProductAdWatcher)
							{ 
								if ($isUserSubscribedToAdWatcher)
								{
						?>
								<a class="tbtn big teal bold doubleright j_appframe" href="/BevoMedia/User/App.html?id=<?php echo $productInfo->ID; ?>&l">launch</a>
						<?php 
								} else 
								{
						?>
								<a class="tbtn big dgreen bold doubleright j_appframe j_add2cart" href="/BevoMedia/Publisher/VerifyAdWatcherConfirm.html">buy now</a>
						<?php 
								}
							} 
						?>
					</div>
					<div class="clear"></div>
				</div><!--close box-->
				<div class="box teal noshadow butt">
				
										
					<a id="appadd" class="chkbtn txtteal j_appadd<?php echo (isProductIntegrated($productInfo->ID) ? ' checked' : ''); ?>" data-id="<?php echo $productInfo->ID; ?>" data-launch="/BevoMedia/User/App.html?id=<?php echo $productInfo->ID; ?>&l" data-signup="/BevoMedia/User/App.html?id=<?php echo $productInfo->ID; ?>&s" href="#" title="Integrate this app with Bevo for quick access">
						<span class="check">&#x2714;</span>
						<span class="txtunchecked">ADD TO MY APPS</span>
						<span class="txtchecked">INTEGRATED <span class="small">(remove)</span></span>
					</a>
				
					<?php 
						if ($productInfo->DescriptionTitle!='')
						{
							echo '<h3>'.$productInfo->DescriptionTitle.'</h3>';
						}
						
						if($productInfo->DescriptionText!='') {
							echo '<p>'.$productInfo->DescriptionText.'</p>';
						}
						
						if ($productInfo->DescriptionListText!='') {
							echo $productInfo->DescriptionListText;
						}
						
					?>
					<div class="clear"></div>
					
					
				</div><!--close box-->			
			</div><!--close topfeat-->
			
			<?php if(!empty($productInfo->DescriptionDetail)) { ?>
				<h4><span>about <?php echo $productInfo->ProductName; ?></span></h4>
				
				<?php echo $productInfo->DescriptionDetail; ?>
				
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

	var actionLink = $(this).attr('action');
	if (typeof actionLink !== 'undefined' && actionLink !== false) {
		action = $(this).attr('action');
	}
		console.info(action);
	if($(this).hasClass('j_addonly') && action == 'remove') {
		$('.message').html('<p><?php echo $productInfo->ProductName; ?> is already integrated with your Bevo Media account! To remove it, use the button below the "Launch" option.</p>').slideDown(200).delay(5000).slideUp(200);
		
	} else {
		
		if(action == 'remove') {
			var proceed = confirm("Are you sure you want to remove this app from your Bevo Media account? Doing this will only remove the app from your \"My Apps\" page. It will NOT cancel any subscriptions or accounts you may have with the app itself. Please refer to the app if you want to cancel a subscription.\n\nProceed if you want to remove this app from your Bevo account (you'll still be able to access it from the app store anytime).");
		} else {
			proceed = true;	
		}
		
		if(proceed) {

			var appId = $(this).data('id'); 
			$.get('/BevoMedia/User/MyAppsAction.html?id='+$(this).data('id')+'&action='+action, function(data) {

				if (appId=='13') {
					window.location.reload();
				}
				
				appChangeMyApp(action);

			});
			
		}//endif proceed
	}//endif addonly
	
	return false;
});//j_appadd

$(document).ready(function() {
	$('a.j_add2cart').click(function() {
		var a = document.createElement('a');
		a.href = $(this).attr('href')+'?ajax=true';
		a.rel = 'shadowbox;width=640;height=480;player=iframe';
		Shadowbox.open(a);
		return false;
	});
});

function appChangeMyApp(action) {
	if(action == 'add') {
		$('#appadd').addClass('checked');
		$('#apps .topfeat .box.top .floatright a.tbtn.doubleright').removeClass('dgreen').addClass('teal').attr('href', $('#appadd').attr('data-launch')).html('launch');
		$('.message').html('<p><?php echo $productInfo->ProductName; ?> has been added to your "My Apps" page!</p>').slideDown(200).delay(5000).slideUp(200);
	} else {
		$('#appadd').removeClass('checked');
		$('#apps .topfeat .box.top .floatright a.tbtn.doubleright').removeClass('teal').addClass('dgreen').attr('href', $('#appadd').attr('data-signup')).html('sign up');
		$('.message').html('<p><?php echo $productInfo->ProductName; ?> has been removed your "My Apps" page!<br />You may still have an open account or a running subscription with the app itself. Please refer to the app itself if you\'d like to cancel that as well.</p>').slideDown(200);
	}
}//appChangeMyApp()

</script>