<?php 
	##########TEMP include dummy db
//	include_once dirname(__FILE__).'/_APPS_DUMMY_DB.php';
	
	global $db;
	$db = $this->db;
	
	function getAppOfTheWeek()
	{
		global $db;
		$productId = $db->fetchOne("SELECT value FROM bevomedia_settings WHERE name = 'APP_OF_THE_WEEK' ");
		
		return $db->fetchRow("SELECT * FROM bevomedia_products WHERE (ID = ?)", $productId);
	}
	
	$appOfTheWeek = getAppOfTheWeek();
	
	function isProductIntegrated($productId)
	{
		global $db;
		$productId = intval($productId);
		return ($db->fetchOne("SELECT id FROM bevomedia_integerated_user_products WHERE userId = ? AND productId = ?", array($_SESSION['User']['ID'], $productId))!=null);
	}
	
	
	if (!isset($_GET['category'])) {
		header('Location: /BevoMedia/User/AppStore.html');
		die;
	}
	
	$categoryId = $_GET['category'];
	
	if ($categoryId=='my')
	{
		$categoryInfo = new stdClass();
		$categoryInfo->id = 0;
		$categoryInfo->name = 'My Apps';
	} else 
	{
		$categoryInfo = $this->db->fetchRow("SELECT * FROM bevomedia_product_categories WHERE id = ? ", $categoryId);
	}
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
	
	
			<div class="box slblue apptopnav">
				<a class="tbtn trans" href="/BevoMedia/User/AppStore.html">&#x25C0; back to all apps</a>
				<h2><?php echo $categoryInfo->name; ?></h2>
			</div>
		
			<?php if($categoryInfo->name == 'My Apps') { ?>
				<p>These are the apps that you've integrated with your Bevo Media account. To launch an app, click on it to go to the app's Detail page, and use the Launch button. From that page, you can also remove the app from this list. If you are currently subscribing to a paid app or are on a payment plan for an app, and you'd like to cancel your subscription, please refer to the app itself. Removing an app from "My Apps" will not cancel any existing subscriptions.</p>
			<?php } else { ?>
				<p>You're viewing all apps in the <?php echo $categoryInfo->name; ?> category. Click on an app to view its detail page, from where you may launch it, buy it (if it's a paid app), or add it to your "My Apps" page.</p>
			<?php } ?>
		
		
			<?php 
				/*my apps*/
				if($categoryInfo->name == 'My Apps') { ?>
					
					<h4 class="txtblack"><span>my apps</span></h4>
					
					<?php 
						$sql = "SELECT
									bevomedia_products.*
								FROM
									bevomedia_integerated_user_products,
									bevomedia_products
								WHERE
									(bevomedia_products.ID = bevomedia_integerated_user_products.productId) AND
									(bevomedia_integerated_user_products.userId = ?)
								ORDER BY
									bevomedia_products.ProductName
								";
						$userProducts = $this->db->fetchAll($sql, $_SESSION['User']['ID']);
						
						if (count($userProducts)>0)
						{
							echo '<div class="appwrap">';
							
							foreach ($userProducts as $userProduct)
							{
					?>
							<a class="box teal hover app" href="/BevoMedia/User/AppDetail.html?id=<?php echo $userProduct->ID; ?>">
								<img src="/Themes/BevoMedia/apps-layout/img/applogos/<?php echo $userProduct->ID; ?>.jpg" alt="" />
								<span class="desc">
									<span class="h3"><?php echo $userProduct->ProductName; ?></span>
									<span class="p">
									<?php
										$productDescription = $userProduct->DescriptionTitle;
										if (strlen($productDescription)>50) $productDescription = substr($productDescription, 0, 50).'...';  
										echo $productDescription; 
									?>
									</span>
				
								</span>
								<span class="butt">
									<span class="ismy">&#x2714; my apps</span>
									
									<?php 
										if ($userProduct->Price==0) {
									?>
									<strong class="txtlgreen">&#x2714; free</strong>
									<?php 
										}
									?>
				
								</span>
							</a>
					<?php 	
							}
							
							echo '<div class="clear"></div>';
							echo '</div><!--close appwrap-->';
						} else
						{
					?>
						<h3>No apps integrated with Bevo yet!</h3>
						<p>You don't have any apps that you've integrated with your Bevo Media account yet. To integrate an app, click the "Add to my apps" button on the app's detail page. Once you've integrated an app, it will appear on this page.</p>
						
						<a class="tbtn big lblue" href="/BevoMedia/User/AppStore.html">go to all apps</a>
					<?php 
						}
					
				/*normal category*/		
				} else {
				?>
				
				<?php 
					$sql = "SELECT
								bevomedia_products.*
							FROM
								bevomedia_products,
								bevomedia_products_to_categories
							WHERE
								(bevomedia_products_to_categories.productId = bevomedia_products.ID) AND
								(bevomedia_products_to_categories.categoryId = ?)
							ORDER BY
									bevomedia_products.ProductName
							";
					$products = $this->db->fetchAll($sql, $categoryId);
				?>
				
					<h4<?php echo ($categoryInfo->name == 'Featured Apps' ? ' class="txtred"' : ''); ?>><span><?php echo $categoryInfo->name; ?></span></h4>
				
					<?php if(count($products)>0) { ?>
						
						<div class="appwrap">
						
							<?php 
								foreach ($products as $product)
								{
							?>
								<a class="box teal hover app" href="/BevoMedia/User/AppDetail.html?id=<?php echo $product->ID; ?>">
									<img src="/Themes/BevoMedia/apps-layout/img/applogos/<?php echo $product->ID; ?>.jpg" alt="" />
									<span class="desc">
										<span class="h3"><?php echo $product->ProductName; ?></span>
										<span class="p"></span>
					
									</span>
									<span class="butt">
										<?php 
											if (isProductIntegrated($product->ID))
											{
										?>
										<span class="ismy">&#x2714; my apps</span>
										<?php 
											}
										?>
										
										<?php 
											if ($product->Price==0) {
										?>
										<strong class="txtlgreen">&#x2714; free</strong>
										<?php 
											}
										?>
					
									</span>
								</a>
							<?php 	
								}
							?>
						
							
						
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
			
		?>
	
	</div><!--close main-->
	<div class="clear"></div>
	
</div><!--close pagecontent-->