<?php 
	##########TEMP include dummy db
	include_once dirname(__FILE__).'/_APPS_DUMMY_DB.php';
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
<?php //echo $this->PageDesc->ShowDesc($this->PageHelper, false); //no toggling
?>



<div id="apps">
	<div class="sidebar">

		<h2 class="icon_apps_big">Bevo Apps</h2>
		<p>Explore some of the industry's best tools, right at your fingertips!</p>
		
		<h4><span>Categories</span></h4>
		
		<ul>
			<li><a class="txtblack" href="/BevoMedia/User/AppCategory.html?category=my">My Apps</a></li>
			<li><a class="txtred" href="/BevoMedia/User/AppDetail.html?id=">App of the Week</a></li>
			<?php
				if($appCategories && is_array($appCategories) && !empty($appCategories)) {
					for($i=0; $i<=count($appCategories)-1; $i++) {
						echo 	'<li><a'
							.($i==0 ? ' class="txtred"' : '')
							.' href="/BevoMedia/User/AppCategory.html?category='.$appCategories[$i]['catURL'].'">'.$appCategories[$i]['catName'].'</a></li>';
					}
				}
			?>
		</ul>
	
	</div><!--close sidebar-->
	<div class="colmain">
	
		<?php	/*featured app*/ 
			if($featuredApp) :		
		?>
	
			<div class="topfeat">
				<div class="box slteal noshadow top">
					<div class="floatleft">
						<div class="icon_appofweek"></div>
						<a href="/BevoMedia/User/AppDetail.html?id=<?php echo $featuredApp; ?>"><img class="applogo" src="<?php echo $apps[$featuredApp]['logoURL']; ?>" alt="" /></a>
						<div class="clear"></div>
					</div>
					
					<div class="floatright">
						<a class="tbtn big teal" href="/BevoMedia/User/AppDetail.html?id=<?php echo $featuredApp; ?>">view app</a>
					</div>
					<div class="clear"></div>
				</div><!--close box-->
				<div class="box teal noshadow butt">
					<a class="chkbtn txtteal" href="#" title="Integrate your app's account with Bevo for quick access">
						<span class="check">&#x2714;</span>
						<span class="txtunchecked">ADD TO MY APPS</span>
						<span class="txtchecked">INTEGRATED <span class="small">(remove)</span></span>
					</a>
				
					<?php 
						if($apps[$featuredApp]['descTitle']) {
							echo '<h3>'.$apps[$featuredApp]['descTitle'].'</h3>';
						}
						
						if($apps[$featuredApp]['descText']) {
							echo '<p>'.$apps[$featuredApp]['descText'].'</p>';
						}
						
						if($apps[$featuredApp]['descList'] && !empty($apps[$featuredApp]['descList'])) {
							echo '<ul class="txtyell">';
							foreach($apps[$featuredApp]['descList'] as $li) {
								echo '<li>&#x25B6; '.$li.'</li>';
							}
							echo '</ul>';
						}
					?>
					<div class="clear"></div>
					
				</div><!--close box-->			
			</div><!--close topfeat-->
			
		<?php
			endif; //featured app
		?>
		
		
		<?php 
			/*my apps*/
			if($userApps && is_array($userApps) && !empty($userApps)) :
		?>
			
			<h4 class="txtblack"><span>my apps</span></h4>
			<div class="appwrap">
			
				<?php foreach($userApps as $id) {
					if(array_key_exists($id, $apps) && !empty($apps[$id])) {
						echo renderAppThumb($apps[$id], $userApps);		
					}
				} ?>
			
				<div class="clear"></div>
			</div><!--close appwrap-->
				
		<?php	
			endif; //my apps
		?>
		
		<?php 
			/*categories*/
			if($appCategories && is_array($appCategories) && !empty($appCategories)) {
				
				for($i=0; $i<=count($appCategories)-1; $i++) {
					
					if(is_array($appCategories[$i]['appIDs']) && !empty($appCategories[$i]['appIDs'])) {
						echo '<h4'
							.($i==0 ? ' class="txtred"' : '')
							.'><span>'.$appCategories[$i]['catName'].'</span></h4>';
							
						echo '<div class="appwrap">';
							$thumbcount = 1;
							foreach($appCategories[$i]['appIDs'] as $id) {
								echo renderAppThumb($apps[$id], $userApps);
								$thumbcount++;
								
								//show max 5 on this page
								if($thumbcount == 6) { ?>
									<div class="box app more">
										<a href="/BevoMedia/User/AppCategory.html?category=<?php echo $appCategories[$i]['catURL']; ?>">
											<span class="big"><?php echo count($appCategories[$i]['appIDs']); ?></span>
											<span class="right">
												<span class="top">apps total</span>
												<span class="small">&#x25B6; view all</span>
											</span>
										</a>
									</div>
								<?php break;
								}//endif thumbcount
								
							}//endforeach
						
						echo '<div class="clear"></div>'
							.'</div>';
					}//if cat has apps
				}//for
			}//if categories
		?>

	<?php /*
		<h4 class="txtblack"><span>my apps</span></h4>
		<div class="appwrap">
	
			<?php	for($i=1; $i<=4; $i++) {
					echo $app;
				}
			?>
			
		<div class="clear"></div>
		</div><!--close appwrap-->
	
		<h4 class="txtred"><span>featured apps</span></h4>
		<div class="appwrap">
	
			<?php	for($i=1; $i<=3; $i++) {
					echo $app;
				}
			?>
			
		<div class="clear"></div>
		</div><!--close appwrap-->
		
		
		<h4><span>campaign management</span></h4>	
		<div class="appwrap">
			<?php
				for($i=1; $i<=5; $i++) {
					echo $app;
				}
			?>
			
			<div class="box app more">
				<a href="#">
					<span class="big">24</span>
					<span class="right">
						<span class="top">apps total</span>
						<span class="small">&#x25B6; view all</span>
					</span>
				</a>
			</div>
			
			<div class="clear"></div>		
		</div><!--close appwrap-->
		
		<h4><span>education</span></h4>	
		<div class="appwrap">
			<?php
				for($i=1; $i<=3; $i++) {
					echo $app;
				}
			?>
			<div class="clear"></div>
		</div><!--close appwrap-->
		*/ ?>
	
	</div><!--close main-->
	<div class="clear"></div>
	
</div><!--close pagecontent-->

<?php /*
<script type="text/javascript">
$(document).ready(function() {
	$('a.j_add2cart').click(function() {
		var a = document.createElement('a');
		a.href = $(this).attr('rel')+'?ajax=true';
		a.rel = 'shadowbox;width=640;height=480;player=iframe';
		Shadowbox.open(a);
		return false;
	});
	$('a#PerfConn').click(function() {
		var a = document.createElement('a');
		a.href = $(this).attr('rel')+'?ajax=true';
		a.rel = 'shadowbox;width=400;height=460;player=iframe';
		Shadowbox.open(a);
		return false;
	});
});
</script>
*/ ?>