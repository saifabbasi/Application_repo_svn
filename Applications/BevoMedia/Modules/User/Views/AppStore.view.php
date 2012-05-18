<?php 
	
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
	
		<?php	/*featured app*/ 
			if($appOfTheWeek!=null)
			{		
				
		?>
	
			<div class="topfeat">
				<div class="box slteal noshadow top">
					<div class="pic">
						<a class="applogo" href="/BevoMedia/User/AppDetail.html?id=<?php echo $appOfTheWeek->ID; ?>"><img src="/Themes/BevoMedia/apps-layout/img/applogos/<?php echo $appOfTheWeek->ID; ?>.jpg" alt="" /></a>
						<div class="icon_appofweek"></div>
					</div>
				
					<h2><?php echo $appOfTheWeek->ProductName; ?></h2>
					<div class="floatright">
						<a class="tbtn big teal" href="/BevoMedia/User/AppDetail.html?id=<?php echo $appOfTheWeek->ID; ?>">view app</a>
					</div>
					<div class="clear"></div>
				</div><!--close box-->
				<div class="box teal noshadow butt">
					<?php 
						if ($appOfTheWeek->DescriptionTitle!='')
						{
							echo '<h3>'.$appOfTheWeek->DescriptionTitle.'</h3>';
						}
						
						if($appOfTheWeek->DescriptionText!='') {
							echo '<p>'.$appOfTheWeek->DescriptionText.'</p>';
						}
						
						if ($appOfTheWeek->DescriptionListText!='') {
							echo $appOfTheWeek->DescriptionListText;
						}
						
					?>
					<div class="clear"></div>					
				</div><!--close box-->			
			</div><!--close topfeat-->
			
		<?php
			}; //featured app
		?>
		
		
		<?php 
			$sql = "SELECT
						bevomedia_products.*
					FROM
						bevomedia_integerated_user_products,
						bevomedia_products
					WHERE
						(bevomedia_products.ID = bevomedia_integerated_user_products.productId) AND
						(bevomedia_integerated_user_products.userId = ?)
					";
			$userProducts = $this->db->fetchAll($sql, $_SESSION['User']['ID']);
			
			if (count($userProducts)>0)
			{
				echo '<h4 class="txtblack"><span>my apps</span></h4>';
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
			}
			
		?>
			
		
		
		<?php 
			$sql = "SELECT * FROM bevomedia_product_categories ORDER BY position";
			$categories = $this->db->fetchAll($sql);
			
			foreach ($categories as $category)
			{
				$totalCategoryProducts = $this->db->fetchOne("SELECT COUNT(*) FROM bevomedia_products_to_categories WHERE (bevomedia_products_to_categories.categoryId = ?)", $category->id); 
				
				$sql = "SELECT
								bevomedia_products.*
							FROM
								bevomedia_products,
								bevomedia_products_to_categories
							WHERE
								(bevomedia_products_to_categories.productId = bevomedia_products.ID) AND
								(bevomedia_products_to_categories.categoryId = ?)
							ORDER BY
									bevomedia_products_to_categories.position
							LIMIT 5
							";
				$products = $this->db->fetchAll($sql, $category->id);
				
				if (count($products)>0)
				{
					
					if ($category->name=='Featured Apps') {
						echo "<h4 class='txtred'><span>{$category->name}</span></h4>";
					} else {
						echo "<h4><span>{$category->name}</span></h4>";
					}
					
					echo "<div class='appwrap'>";
					
					foreach ($products as $key => $product)
					{
		?>
						<a class="box teal hover app" href="/BevoMedia/User/AppDetail.html?id=<?php echo $product->ID; ?>">
							<img src="/Themes/BevoMedia/apps-layout/img/applogos/<?php echo $product->ID; ?>.jpg" alt="" />
							<span class="desc">
								<span class="h3"><?php echo $product->ProductName; ?></span>
								<span class="p">
								<?php
									$productDescription = $product->DescriptionTitle;
									if (strlen($productDescription)>50) $productDescription = substr($productDescription, 0, 50).'...';  
									echo $productDescription; 
								?>
								</span>
			
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
					
					if ($totalCategoryProducts>5) {
		?>
						<div class="box app more">
							<a href="/BevoMedia/User/AppCategory.html?category=<?php echo $category->id; ?>">
								<span class="big"><?php echo $totalCategoryProducts; ?></span>
								<span class="right">
									<span class="top">apps total</span>
									<span class="small">&#x25B6; view all</span>
								</span>
							</a>
						</div>		
		<?php 
					}
					
					echo '<div class="clear"></div>';
					echo "</div>";
				}
			}
		?>
		
		
	
	</div><!--close main-->
	<div class="clear"></div>
	
</div><!--close pagecontent-->
