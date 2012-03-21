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
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //no toggling
?>

<div class="pagecontent" id="appstore">
	<div class="feat featapp_ppvspy">
		<div class="desc">
			<h3 class="apptitle">Bevo PPV Spy</h3>
			<p>PPV Spy is the only research tool for Pay-Per-View ads in existence. By using it, you will get unique insights into what works for your competitors - and what doesn't. It's all at your fingertips and ready to be replicated.</p>
			
			<a class="btn btn_appstore_watchvideo" rel="shadowbox;width=900;height=506;" href="https://player.vimeo.com/video/17655071?byline=0&amp;portrait=0&amp;color=ff9933&amp;autoplay=1">Watch Video</a>
			
			<h3>Features</h3>
			<ul class="soapchecklist checkwhite">
				<li>Browse and search in thousands of PPV campaigns, offers, and targets</li>
				<li>Data set updated hourly</li>
				<li>Learn what pops are seen the most</li>
				<li>Download full lists of target URLs</li>
				<li>Suggest target URLs you'd like us to add</li>
			</ul>			
		</div>
		
		<div class="testi">
			<h3>What our Beta Testers are saying...</h3>
			<ul>
				<li>I used to spend weeks figuring out what sites to target, how to design my pops, and what offers performed the best. With PPV Spy, I got all that at my fingertips in no time at all. It's amazing.</li>
				<li>I used the PPV Spy last week for the first time. In about 30 minutes, I got a solid new campaign plan and launched it later that day. Right now, my running profit for that campaign is $12,770, and it's my most profitable campaign ever.<br />Thanks Bevo!</li>
				<li>PPV Spy is a total game-changer. Please don't let everyone and their mother use it. It's just too easy now.</li>
				<li>You guys are putting the money in my lap with this tool. I just have to pick it up.</li>
			</ul>
		</div>
		<div class="add2cart">
			<div class="cartdesc">
				<h3>Bevo PPV Spy</h3>
				<p>Full access to the Bevo PPV Spy App for only $385 /month or a $999 one-time payment.<br /> 
				
				<?php
				
					$paidPPVSpyMonthly = $this->User->IsSubscribed(User::PRODUCT_PPVSPY_MONTHLY);
					$paidPPVSpyYearly = $this->User->IsSubscribed(User::PRODUCT_PPVSPY_YEARLY);
					$verified = ($this->User->vaultID!=0);
					$freePPVSpy = $this->User->IsSubscribed(User::PRODUCT_FREE_PPVSPY);
					
					$ppvSpyAccess = false;
					
					if ($verified && ($paidPPVSpyMonthly || $paidPPVSpyYearly)) {
						$ppvSpyAccess = true;
					}
					
					if ($freePPVSpy) {
						$ppvSpyAccess = true;
					}
					 
					if ( $this->User->vaultID == 0) { //if user is unverified
				?>
					<strong>Requires a verified Bevo account.</strong> <a href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=1">Verify Now</a>, then come back to this page!
				</p>
				
			</div><!--close cartdesc-->
			<div class="cartaction buy">
				<a class="btn btn_appstore_verify_wide" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=1">Verify</a>
				<small>It's free and only takes 30 seconds!</small>
			</div>
									
				<?php } else { //if user is verified
					
					if ($ppvSpyAccess) { //if user is subscribed to this app
						
						?>You have a valid license for this app! <a href="/BevoMedia/User/MyProducts.html">Manage Subscription</a></p>
					
			</div><!--close cartdesc-->
			<div class="cartaction check">
				<a class="btn btn_appstore_launchapp_wide" href="/BevoMedia/PPVSpy/Index.html">Launch App</a>
			</div>
			
					<?php } else { //if user is NOT subscribed to this app
					?>
					
				</p>
			</div><!--close cartdesc-->
			<div class="cartaction buy">
				<a class="btn j_add2cart btn_appstore_add2cart_wide" href="#" rel="/BevoMedia/Publisher/VerifyPPVSpyConfirm.html">Add To Cart</a>
				<small>You'll be able to review your order on the next page</small>
			</div>
			
					<?php }//endif user may use the app
				}//endif user is verified
				?>
				
		</div><!--close add2cart-->
	</div><!--close featapp-->
	
	<div class="moreapps"></div>
	
	
	
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
	<?php
	/*
	
	
	appbox selfhosted
	
	
	*/ 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>Bevo Self-Hosted Version</h3>
			<div class="apptitleright"></div>
		</div>
		
		<div class="appboxinside">		
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_selfhost_simple.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>The self-hosted version of Bevo Media is geared towards high volume publishers who want to absolutely guarantee security by hosting all of their data on their own servers. Bevo Self-Host is also open source, which allows for customization by more intense publishers.</p>
				</div>
				
				<ul class="soapchecklist">
					<li><p>Open-Source and Self-Hosted</p>
						<span>Ensure 100% security with data stored on your own server and easily integrate customized features in our fully documented, open sourced environment.</span>
					</li>
					<li><p>On-Demand Network Stats Updates</p>
						<span>Get the most current stats with Bevo Self-Hosted by updating them as often as you want.</span>
					</li>
					<li><p>Perfect Solution for High Volume Publishers</p>
						<span>High volume publishers can monitor and sync their stats and simplify their entire process. Bevo Media Self-Hosted is built to scale to optimize campaigns for serious publishers.</span>
					</li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>Bevo Self-Hosted Version</h3>
						<p>The Bevo Self-Hosted version is a premium app that has a nominal one-time licensing fee.</p>
					</div>
					
					<div class="cartaction">				
						<?php if($this->User->vaultID == 0) { //if user is unverified
						?>
							<div class="icon icon_appstore_add2cart_buy"></div>
							<a class="btn btn_appstore_verify" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=1">Verify</a>
							
							<p>This App requires a verified Bevo account.</p>
						
						<?php } else { //if user is verified
							
							if($this->User->IsSubscribed(User::PRODUCT_FREE_SELF_HOSTED)) { //if user is subscribed to app
							?>
								<div class="icon icon_appstore_add2cart_check"></div>
								<a class="btn btn_appstore_launchapp" href="/BevoMedia/User/SelfHostedLogin.html">Launch App</a>
							
								<p>You have a valid license to use this app<?php /* ################ TODO!*/
													//1) if this is a one-time fee, and user has already paid it:
													//if(#### ONE-TIME FEE PAID ###)
														//echo ' forever';
														
													//2) if user is on a MONTHLY recurring payment model for this app, and has already paid for it
													//elseif(### MONTHLY RECURRING FEE PAID ###)
														//#do nothing
														
													//2) if user is on a YEARLY recurring payment model for this app, and has paid for it
													//elseif(### YEARLY RECURRING FEE PAID ###)
														//echo ' until DAY-BEFORE-LICENSE-EXPIRES'; //e.g. April 12, 2011
													?>!</p>
							
							<?php } else { //if user is NOT subscribed to this app
							?>
								<div class="icon icon_appstore_add2cart_buy"></div>
								<a class="btn j_add2cart btn_appstore_add2cart" href="#" rel="/BevoMedia/Publisher/VerifySelfHostedConfirm.html">Add to Cart</a>
							
								<h3>$600</h3>
								<p>One-time license fee</p>					
							
							<?php	} //endif user may use app 
						} //endif user is verified
						?>
					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->

<?php 
	}
?>

	<?php
	/*
	
	
	geotargeting
	
	
	*/	 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>Geotargeting</h3>
			<div class="apptitleright"></div>
			
		</div>
		
		<div class="appboxinside">
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_geo.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>Geotarget your landing pages and offers. This feature allows you to show different pages based on where the viewer is coming from in the world. Target by any city, state, or country in the world!</p>
				</div>
				
				<ul class="soapchecklist">
					<li>Show different landing pages based on where the viewer is coming from in the world</li>
					<li>Target users by country, state, or down to the city level</li>
					<li>Very easy to be integrated into the tracking system of your choice</li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>Geotargeting</h3>
						<p>The Geotargeting App is a premium app that is free to use for verified BevoMedia users!</p>
					</div>
					
					<div class="cartaction">
					
						<?php if($this->User->vaultID == 0) { //if user is not verified
						?>
							<div class="icon icon_appstore_add2cart_buy"></div>
							<a class="btn btn_appstore_verify" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=2">Verify</a>
							
							<p>This app is FREE to use for verified Bevo users.</p>
							
						<?php } else { //if user is verified
						?>
							<div class="icon icon_appstore_add2cart_check"></div>
							
						<?php 
							if (!isset($_COOKIE['v3apps']))
							{
						?>
							<a class="btn btn_appstore_launchapp" href="/BevoMedia/Geotargeting/Index.html">Launch App</a>
						<?php 
							} else 
							{
						?>
							<a class="btn btn_appstore_launchapp" href="https://<?php echo $_COOKIE['v3domain']; ?>/geotarget/list/" target="_blank">Launch App</a>
						<?php 	
							}
						?>
							<h3>FREE</h3>
							<p>This app is free to use!</p>
							
						<?php }
						?>
					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
	
	
	<?php
	/*
	
	
	daytargeting
	
	
	*/	 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>Daytargeting</h3>
			<div class="apptitleright"></div>
			
		</div>
		
		<div class="appboxinside">
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_dayparting.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>
						Target your landing pages and offers by both day and hour! This feature allows you to see the time period your campaign converts the best at, and allows you to optimize and target accordingly.
					</p>
				</div>
				
				<ul class="soapchecklist">
					<li>See what days and time of day that your campaign converts best at!</li>
					<li>Show different landing pages or offers depending on the time of day.</li>
					<li>Easily combine with the Geoparting feature to bring campaign optimization to a whole new level!</li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>Daytargeting</h3>
						<p>The Day Targeting App is a premium app that is free to use for verified BevoMedia users!</p>
					</div>
					
					<div class="cartaction">
					
						<?php if($this->User->vaultID == 0) { //if user is not verified
						?>
							<div class="icon icon_appstore_add2cart_buy"></div>
							<a class="btn btn_appstore_verify" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=2">Verify</a>
							
							<p>This app is FREE to use for verified Bevo users.</p>
							
						<?php } else { //if user is verified
						?>
							<div class="icon icon_appstore_add2cart_check"></div>
							
						<?php 
							if (!isset($_COOKIE['v3apps']))
							{
						?>
							<a class="btn btn_appstore_launchapp" href="/BevoMedia/Timetargeting/Index.html">Launch App</a>
						<?php 
							} else 
							{
						?>
							<a class="btn btn_appstore_launchapp" href="https://<?php echo $_COOKIE['v3domain']; ?>/daytarget/list/" target="_blank">Launch App</a>						
						<?php 
							}
						?>
							
							<h3>FREE</h3>
							<p>This app is free to use!</p>
							
						<?php }
						?>
					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
	
	
	<?php
	/*
	
	
	appbox keyword research (PPVTools)
	
	
	*/ 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>Bevo Keyword List Builder</h3>
			<div class="apptitleright"></div>
			<?php /*<a class="btn btn_appstore_watchvideo" rel="shadowbox;width=900;height=620;" href="http://www.youtube.com/v/aen5cQn4qEM&hl=en_US&fs=1&autoplay=1">Watch Video</a>*/ ?>
		</div>
		
		<div class="appboxinside">
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_ppv.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>The Bevo Keyword List Builder is a premium keyword research tool designed to save time during the crucial research stages of building a campaign. This tool is a huge leg up on the competition whether you have a search, PPV or media buy campaign. </p>
				</div>
				
				<ul class="soapchecklist">
					<li>Save time and stay ahead of the competition</li>
					<li>Find exact data on keywords and URLs</li>
					<li>Find out exactly where URLs rank on Alexa.com</li>					
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>Bevo Keyword List Builder</h3>
						<p>The Bevo Keyword List Builder is a premium app that is free to use for verified BevoMedia users!</p>
					</div>
					
					<div class="cartaction">
					
						<?php if($this->User->vaultID == 0) { //if user is not verified
						?>
							<div class="icon icon_appstore_add2cart_buy"></div>
							<a class="btn btn_appstore_verify" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=2">Verify</a>
							
							<p>This app is FREE to use for verified Bevo users.</p>
							
						<?php } else { //if user is verified
						?>
							<div class="icon icon_appstore_add2cart_check"></div>
							<a class="btn btn_appstore_launchapp" href="/BevoMedia/PPVTools/PageSniper.html">Launch App</a>
							
							<h3>FREE</h3>
							<p>This app is free to use!</p>
							
						<?php }
						?>
					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
	
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
	<?php
	/*
	
	
	appbox ppc campaign editor
	
	
	*/ 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>PPC Campaign Editor</h3>
			<div class="apptitleright"></div>
			<?php /*<a class="btn btn_appstore_watchvideo" rel="shadowbox;width=900;height=620;" href="http://www.youtube.com/v/aen5cQn4qEM&hl=en_US&fs=1&autoplay=1">Watch Video</a>*/ ?>
		</div>
		
		<div class="appboxinside">		
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_ppccampeditor.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>Bevo's PPC Management gives you the opportunity not only to examine your search marketing expenses, but also to edit and create all of your campaigns within a single interface. Check out your search campaign stats, create or edit a campaign and gain an in-depth view of exactly where your money is going.</p>
				</div>
				
				<ul class="soapchecklist">
					<li><p>Create Campaigns Faster than Ever</p>
						<span>The Bevo Campaign Editor allows users to upload multiple campaigns, adgroups, keywords and ad variations all at once, requiring the least amout of time possible!</span>
					</li>
					<li><p>Edit Campaigns on the Fly</p>
						<span>Edit your campaigns while browsing through your campaign performance, all on the Bevo interface.</span>
					</li>
					<li><p>Cross-post to Multiple PPC Accounts</p>
						<span>Create a campaigns once and post to multiple Google, Yahoo and Bing accounts in the click of a button. Clone campaigns instantly!</span>
					</li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>PPC Campaign Editor</h3>
						<p>The Bevo PPC Campaign Editor is a premium feature that has a nominal one-time licensing fee.</p>
					</div>
					
					<div class="cartaction">
						<?php if($this->User->vaultID == 0) { //if user is unverified
						?>
							<div class="icon icon_appstore_add2cart_buy"></div>
							<a class="btn btn_appstore_verify" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=1">Verify</a>
							
							<p>This App requires a verified Bevo account.</p>
						
						<?php } else { //if user is verified
							
							if($this->User->IsSubscribed(User::PRODUCT_FREE_PPC) || $this->User->IsSubscribed(User::PRODUCT_PPC_YEARLY_CHARGE)) { //if user is subscribed to app
							?>
						
								<div class="icon icon_appstore_add2cart_check"></div>
								<a class="btn btn_appstore_launchapp" href="/BevoMedia/Publisher/CreatePPC.html">Launch App</a>
								
								<p>You have a valid license to use this app<?php /* ################ TODO!*/
														//same logic as above for Selfhosted
														?>!</p>							
							<?php } else { //if user is not subscribed to this app
							?>
								<div class="icon icon_appstore_add2cart_buy"></div>
								<a class="btn j_add2cart btn_appstore_add2cart" href="#" rel="/BevoMedia/Publisher/VerifyPPCConfirm.html">Add to Cart</a>
								
								<h3>$160</h3>
								<p>One-time license fee</p>				
							
							<?php	} //endif user may use app 
						} //endif user is verified
						?>
					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
<?php 
	}
?>
	
	<?php
	/*
	
	
	appbox nyp
	
	
	*/ 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>Name Your Payout</h3>
			<div class="apptitleright"></div>
			<?php /*<a class="btn btn_appstore_watchvideo" rel="shadowbox;width=900;height=620;" href="http://www.youtube.com/v/aen5cQn4qEM&hl=en_US&fs=1&autoplay=1">Watch Video</a>*/ ?>
		</div>
		
		<div class="appboxinside">
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_nyp.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>Get the payout you want! Enter your requested payout for a a specific offer or niche and have the Bevo networks bid for you to run with them.</p>
				</div>
				
				<ul class="soapchecklist">
					<li><p>Enter your desired offer payout and EPC for any niche...</p>
						<span>...to get networks to bid on them.</span></li>
					<li><p>Add a specific offer name to your request...</p>
						<span>...if you prefer to run only that offer.</span></li>
					<li><p>29 niches currently supported...</p>
						<span>...and growing!</span></li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>Name Your Payout App</h3>
						<p>The Name Your Payout App is a premium app that is free to use for all BevoMedia users!</p>
					</div>
					
					<div class="cartaction">
							<div class="icon icon_appstore_add2cart_check"></div>
							<a class="btn btn_appstore_launchapp" href="/BevoMedia/Offers/NameYourPayout.html">Launch App</a>
							
							<h3>FREE</h3>
							<p>This app is free to use!</p>

					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
	<?php
	/*
	
	
	appbox peformance connector
	
	
	*/ 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>Performance Connector</h3>
			<div class="apptitleright"></div>
		</div>
		
		<div class="appboxinside">
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_perfconn.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>
						The Bevo Performance Connector is a free service connecting networks with publishers who want the best opportunity of promoting an offer. Bevo Performance Connector members receive periodic introductory emails connecting them with their best fit partnered networks.
					</p>
				</div>
				
				<ul class="soapchecklist">
					<li><p>Personalized help</p>
						<span>Have a dedicated rep at your network personally help you with whatever you need to get your campaign going in the right direction.</span></li>
					<li><p>Top payouts and guarenteed EPC's</p>
						<span>Get access to exclusive offers, top payouts, and even guarenteed EPC's from your best fit networks.</span></li>
					<li><p>Whatever it takes</p>
						<span>Our partnered networks will do whatever it takes going above and beyond to get you running on their networks giving you extra benefits because you are a Bevo publisher.</span></li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>Bevo Performance Connector</h3>
						<p>The Bevo Performance Connector is feature that is free to use for all BevoMedia users!</p>
					</div>
					
					<div class="cartaction">
							<div class="icon icon_appstore_add2cart_check"></div>
							<?php $userHasNiche = (count($this->User->getPerformanceConnectorNiches()) > 0);?>

							<?php if(!$userHasNiche):?>
							<a class="btn btn_appstore_subscribe" href="#" rel="/BevoMedia/User/PerfConn.html" id="PerfConn">Subscribe</a>
							<?php else:?>
							<a class="btn btn_appstore_unsubscribe" href="#" rel="/BevoMedia/User/PerfConn.html?unsubscribe=true" id="PerfConn">Unsubscribe</a>
							<?php endif;?>
							
							<h3>FREE</h3>
							<p>This app is free to use!</p>

					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
	<?php
	/*
	
	
	overnight affiliate
	
	
	*/	 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>Overnight Affiliate</h3>
			<div class="apptitleright"></div>
			
		</div>
		
		<div class="appboxinside">
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_overaff.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>Overnight Affiliate is a step-by-step walkthrough of every aspect a beginner affiliate needs to get a profitable campaign. It\'s packed with videos, step-by-step instructions, example campaigns, and weekly webinars where verifed users can get personalized one-on-one help specifically for their own campaigns.</p>
				</div>
				
				<ul class="soapchecklist">
					<li><p>Step By Step Videos</p>
						<span>A structured 18 video course, developed to make a beginner affiliate prepared with everything they need to get a profitable campaign.</span>
					</li>
					<li><p>Examples of Successful Campaigns</p>
						<span>Get set up with example campaigns that have made big bucks. See exactly how the campaign were done, and use for your own campaigns.</span>
					</li>
					<li><p>Weekly Personalized Webinars</p>
						<span>Weekly webinars to have successful internet marketers peronally help you with your campaigns. Get step by step directions on how to turn your campaigns profitable!</span>
					</li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>Overnight Affiliate</h3>
						<p>The Overnight Affiliate is an premium package that is free to use for verified BevoMedia users!</p>
					</div>
					
					<div class="cartaction">
					
						<?php if($this->User->vaultID == 0) { //if user is not verified
						?>
							<div class="icon icon_appstore_add2cart_buy"></div>
							<a class="btn btn_appstore_verify" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=2">Verify</a>
							
							<p>This app is FREE to use for verified Bevo users.</p>
							
						<?php } else { //if user is verified
						?>
							<div class="icon icon_appstore_add2cart_check"></div>
							<a class="btn btn_appstore_launchapp" href="/BevoMedia/Publisher/OvernightAffiliate.html">Launch App</a>
							
							<h3>FREE</h3>
							<p>This app is free to use!</p>
							
						<?php }
						?>
					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
	<?php
	/*
	
	
	coaching webinars
	
	
	*/	 
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>Coaching Webinars</h3>
			<div class="apptitleright"></div>
			
		</div>
		
		<div class="appboxinside">
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_coachingwebinars.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>Bevo Verified users have the ability to get personalized one-on-one help in the weekly Coaching webinars. Feel free to ask any questions you may have and have our trained professionals take you through your campaigns!</p>
				</div>
				
				<ul class="soapchecklist">
					<li><p>Personalized Help</p>
						<span>Have one of our experienced internet marketers peronally help you with your campaigns, and help turn them into profit.</span>
					</li>
					<li><p>Campaigns Broken Down To Baby Steps</p>
						<span>See step by step, every thing that must be done to your campaign to be turned successful.</span>
					</li>
					<li><p>Ask anything!</p>
						<span>Ask your coach anything at all. No question too small! The Coaches will break down the most basic concepts so anyone can understand them.</span>
					</li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>Coaching Webinars</h3>
						<p>The Coaching Webinars are regularly held personalized webinar sessions that are free to join in to for verified BevoMedia users!</p>
					</div>
					
					<div class="cartaction">
					
						<?php if($this->User->vaultID == 0) { //if user is not verified
						?>
							<div class="icon icon_appstore_add2cart_buy"></div>
							<a class="btn btn_appstore_verify" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=2">Verify</a>
							
							<p>This app is FREE to use for verified Bevo users.</p>
							
						<?php } else { //if user is verified
						?>
							<div class="icon icon_appstore_add2cart_check"></div>							
								<a class="btn btn_appstore_getinfo" rel="shadowbox;width=400;height=400;" href="/BevoMedia/User/Webinar.html">Get Webinar Info</a>
							
							<h3>FREE</h3>
							<p>This app is free to use!</p>
							
						<?php }
						?>
					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
	
	
	<?php
	/*
	
	
	appbox adwatcher
	
	
	?>
	<div class="item">
		<div class="apptitle">
			<div class="apptitleleft"></div>
			<h3>AdWatcher</h3>
			<div class="apptitleright"></div>
		</div>
		
		<div class="appboxinside">		
			<div class="img"><img src="<?php echo SCRIPT_ROOT; ?>img/pagedesc_ppccampeditor.png" alt="" /></div>
			<div class="cont">			
				<div class="desc">
					<p>
						Some info about AdWatcher....
					</p>
				</div>
				
				<ul class="soapchecklist">
					<li><p>Create Campaigns Faster than Ever</p>
						<span>The Bevo Campaign Editor allows users to upload multiple campaigns, adgroups, keywords and ad variations all at once, requiring the least amout of time possible!</span>
					</li>
					<li><p>Edit Campaigns on the Fly</p>
						<span>Edit your campaigns while browsing through your campaign performance, all on the Bevo interface.</span>
					</li>
					<li><p>Cross-post to Multiple PPC Accounts</p>
						<span>Create a campaigns once and post to multiple Google, Yahoo and Bing accounts in the click of a button. Clone campaigns instantly!</span>
					</li>
				</ul>
				
				<div class="add2cart">
					<div class="cartdesc">
						<h3>AdWatcher</h3>
						<p>
							Full access to the Bevo AdWatcher App for only $200 /month or a $699 one-time payment.
						</p>
					</div>
					
					<div class="cartaction">
						<?php if($this->User->vaultID == 0) { //if user is unverified
						?>
							<div class="icon icon_appstore_add2cart_buy"></div>
							<a class="btn btn_appstore_verify" href="/BevoMedia/User/AddCreditCard.html?notifyNotPaying=1">Verify</a>
							
							<p>This App requires a verified Bevo account.</p>
						
						<?php } else { //if user is verified
							
							if($this->User->IsSubscribed(User::PRODUCT_ADWATCHER_MONTHLY) || $this->User->IsSubscribed(User::PRODUCT_ADWATCHER_YEARLY)) { //if user is subscribed to app
							?>
						
								<div class="icon icon_appstore_add2cart_check"></div>
								<a class="btn btn_appstore_launchapp" href="/BevoMedia/User/OpenAdWatcher.html">Launch App</a>
								
								<p>You have a valid license to use this app!</p>							
							<?php } else { //if user is not subscribed to this app
							?>
								<div class="icon icon_appstore_add2cart_buy"></div>
								<a class="btn j_add2cart btn_appstore_add2cart" href="#" rel="/BevoMedia/Publisher/VerifyAdWatcher.html">Add to Cart</a>
								
								<h3>$699</h3>
								<p>One-time license fee</p>				
							
							<?php	} //endif user may use app 
						} //endif user is verified
						?>
					</div><!--close cartaction-->
				</div><!--close add2cart-->
			</div><!--close cont-->
			<div class="clear"></div>
		</div><!--close appboxinside-->
	</div><!--close appbox-->
	
	<?php 
	*/
	?>
	
	
	
</div><!--close pagecontent-->

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