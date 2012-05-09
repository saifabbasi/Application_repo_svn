<?php 
	##########TEMP include dummy db
	include_once dirname(__FILE__).'/_APPS_DUMMY_DB.php';
	
	
	/* GET app category */
	$currentCat = '';
	if(isset($_GET['category']) && $_GET['category'] != '') {
	
		$currentCat = str_replace(array('\'','"'), '', strip_tags(trim($_GET['category'])));
		
		//check if it's a valid category
		if($currentCat == 'my') {
		
			$currentCatData = array(
				'catURL' => 'my',
				'catName' => 'My Apps',
				'appIDs' => $userApps
			);
		
		} else {
			
			$error = true;
			foreach($appCategories as $cat) {
				if($currentCat == $cat['catURL']) {
					$currentCatData = $cat;
					$error = false;
					break;
				}
			}
			
			if($error) {
				header('Location: /BevoMedia/User/AppStore.html');
			}
		}
		
	}//endif GET
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
			)
				echo '<li><a href="/BevoMedia/User/MyProducts.html">Manage My Subscriptions</a></li>';
		?>
	</ul>
</div>


<div id="apps">
	<div class="sidebar">

		<h2 class="icon_apps_big">Bevo Apps</h2>
		<p>Explore some of the industry's best tools, right at your fingertips!</p>
		
		<h4><span>Categories</span></h4>
		<?php echo renderAppCatMenu($appCategories, $featuredApp, $currentCat); ?>
	
	</div><!--close sidebar-->
	<div class="colmain">
	
		<?php if(!$currentCat || $currentCat == '') : ?>
		
			<h2>Ooops!</h2>
			<p>It looks like you haven't selected a category! Use the menu on the left to select a category, or <a class="tbtn trans" href="/BevoMedia/User/AppStore.html">click here</a> to view all apps.</p>
		
		<?php else : //if we have a currentCat
		?>
	
			<div class="box slblue apptopnav">
				<a class="tbtn trans" href="/BevoMedia/User/AppStore.html">&#x25C0; back to all apps</a>
				<h2><?php echo $currentCatData['catName']; ?></h2>
			</div>
		
			<?php if($currentCat == 'my') { ?>
				<p>These are the apps that you've integrated with your Bevo Media account. To launch an app, click on it to go to the app's Detail page, and use the Launch button. From that page, you can also remove the app from this list. If you are currently subscribing to a paid app or are on a payment plan for an app, and you'd like to cancel your subscription, please refer to the app itself. Removing an app from "My Apps" will not cancel any existing subscriptions.</p>
			<?php } else { ?>
				<p>You're viewing all apps in the <?php echo $currentCatData['catName']; ?> category. Click on an app to view its detail page, from where you may launch it, buy it (if it's a paid app), or add it to your "My Apps" page.</p>
			<?php } ?>
		
		
			<?php 
				/*my apps*/
				if($currentCat == 'my') { ?>
					
					<h4 class="txtblack"><span>my apps</span></h4>
					
					<?php if($userApps && is_array($userApps) && !empty($userApps)) { ?>
				
						<div class="appwrap">
						
							<?php foreach($userApps as $id) {
								if(array_key_exists($id, $apps) && !empty($apps[$id])) {
									echo renderAppThumb($apps[$id], $userApps);		
								}
							} ?>
						
							<div class="clear"></div>
						</div><!--close appwrap-->
						
					<?php } else { ?>
							
						<h3>No apps integrated with Bevo yet!</h3>
						<p>You don't have any apps that you've integrated with your Bevo Media account yet. To integrate an app, click the "Add to my apps" button on the app's detail page. Once you've integrated an app, it will appear on this page.</p>
						
						<a class="tbtn big lblue" href="/BevoMedia/User/AppStore.html">go to all apps</a>
						
					<?php }//if no apps
					
				} else {//normal category
				?>
				
					<h4<?php echo ($currentCat == 'featured' ? ' class="txtred"' : ''); ?>><span><?php echo $currentCatData['catName']; ?></span></h4>
				
					<?php if(!empty($currentCatData['appIDs'])) { ?>
							
						<div class="appwrap">
						
							<?php foreach($currentCatData['appIDs'] as $id) {
								if(array_key_exists($id, $apps) && !empty($apps[$id])) {
									echo renderAppThumb($apps[$id], $userApps);		
								}
							} ?>
						
							<div class="clear"></div>
						</div><!--close appwrap-->
							
					<?php } else { //if currentCat has apps
					?>
					
						<h3>No apps in this category!</h3>
						<p>But stay tuned, we're adding new apps all the time. Chances are, the next time you check back, we'll have some apps in here for you.</p>
						
						<a class="tbtn big lblue" href="/BevoMedia/User/AppStore.html">go to all apps</a>
					
					<?php }//endif currentCat has apps
					?>
				
				<?php }//endif my apps or normal category
			
		endif; //currentCat
		?>
	
	</div><!--close main-->
	<div class="clear"></div>
	
</div><!--close pagecontent-->