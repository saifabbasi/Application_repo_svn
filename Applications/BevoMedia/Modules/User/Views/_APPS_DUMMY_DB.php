<?php 

	/*temporary file, contains functions and dummy data, can be removed once backend is complete and functions have been moved to controller*/
	
	
	
	/*functions*/
	
	
	
	/** renderAppThumb()
	  * builds HTML for the small app buttons on the overview and category pages
	  * @param $app array the app array in $apps
	  * @return string HTML of small app thumbnail button 
	  */
	function renderAppThumb($app=false, $userApps=false) {
		if($app) {
		
			$out = '<a class="box slblue hover app" href="/BevoMedia/User/AppDetail.html?id='.$app['ID'].'">'
				.'<img src="'.$app['logoURL'].'" alt="" />'
				.'<span class="desc">'
					.'<span class="h3">'.$app['appName'].'</span>';
					
					if($app['descTitle']) {
						$out .= '<span class="p">'
							.(strlen($app['descTitle']) > 50 ? substr($app['descTitle'], 0, 46).'...' : $app['descTitle'])
							.'</span>';
					}
				
				$out .= '</span>'
				.'<span class="butt">'
					.($userApps && is_array($userApps) && in_array($app['ID'], $userApps) ? '<span class="ismy">&#x2714; my apps</span>' : '')
					.(!$app['price'] || $app['price'] == '' ? '<strong class="txtdgreen">&#x2714; free</strong>' : '')
				.'</span>'
			.'</a>';
			
			return $out;
			
		}//endif params
	}//renderAppThumb()
	
	
	
	/** renderAppCatMenu()
	  * builds HTML for the side navigation menu of app categories
	  * @param $appCategories array the array of categories
	  * @param $featuredApp bool id of the featured app of the week
	  * @param $currentCat str the catURL key in the $appCategories array, optional. highlights the current category if set
	  * @return string HTML of menu ul 
	  */
	function renderAppCatMenu($appCategories=false, $featuredApp=false, $currentCat=false) {
		$out = '<ul>'
			.'<li><a class="txtblack'
				.($currentCat && $currentCat == 'my' ? ' active' : '')
				.'" href="/BevoMedia/User/AppCategory.html?category=my">My Apps</a></li>'
			.'<li><a class="txtred" href="/BevoMedia/User/AppDetail.html?id='.$featuredApp.'">App of the Week</a></li>';
				
			if($appCategories && is_array($appCategories) && !empty($appCategories)) {
				for($i=0; $i<=count($appCategories)-1; $i++) {
					$out .= '<li><a class="'
						.($i==0 ? 'txtred ' : '')
						.($currentCat && $currentCat == $appCategories[$i]['catURL'] ? 'active' : '')
						.'" href="/BevoMedia/User/AppCategory.html?category='.$appCategories[$i]['catURL'].'">'.$appCategories[$i]['catName'].'</a></li>';
				}
			}
		$out .= '</ul>';

		return $out;
		
	}//renderAppCatMenu()
	
	
	
	/*data*/
	
	
	
	/** userApps array
	  * contains the IDs of the apps user has added to "My Apps"
	  */
	$userApps = array(1,3);
	
	/** featuredApp variable
	  * contains the IDs of the current "Featured App"
	  */
	$featuredApp = 1;
	
	
	/** app categories array
	  * app IDs reference the $apps array below
	  */
	$appCategories = array(
		array(
			'ID' => 1, //first category should be Featured Apps, this property is used in the view
			'catURL' => 'featured',
			'catName' => 'Featured Apps',
			'appIDs' => array(1,2,3)
		),array(		
			'ID' => 2,
			'catURL' => 'campaign',
			'catName' => 'Campaign Management',
			'appIDs' => array(2,3)
		),array(		
			'ID' => 3,
			'catURL' => 'education',
			'catName' => 'Education',
			'appIDs' => array(1,2,3)
		),array(		
			'ID' => 4,
			'catURL' => 'forums',
			'catName' => 'Forums',
			'appIDs' => array(1,2,3,1,2,3,1,2,3)
		),array(		
			'ID' => 5,
			'catURL' => 'research',
			'catName' => 'Research',
			'appIDs' => array(3)
		),array(		
			'ID' => 6,
			'catURL' => 'tracker',
			'catName' => 'Tracker Addons',
			'appIDs' => array()
		),array(		
			'ID' => 7,
			'catURL' => 'free',
			'catName' => 'Free Apps',
			'appIDs' => array(1,2,3,1)
		)
	);//$appCategories
	
	
	
	
	/** apps array
	  * only the basics for presentation have been added to this array.
	  * there might be more keys that have to be added to each app, like whether users need to be verified etc.
	  */
	$apps = array(
		1 => array(
			'ID' => '1',
			'appName' => 'MixRank',
			'price' => '12.50/mo', //empty if free. never use $
			'signupURL' => 'http://affportal.bevomedia.com',
			'launchURL' => 'http://affportal.bevomedia.com',
			'logoURL' => '/Themes/BevoMedia/apps-layout/img/applogos/mixrank.png', //on Bevo's server
			'descTitle' => 'Uncover any advertiser\'s ads and traffic sources',
			'descText' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor. .invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
			'descList' => array( //optional array of features, appears as a yellow list if set
				'Search across millions of ads',
				'Discover hot traffic sources',
				'Gain a competitive advantage'
			),
			'descDetail' => array( //optional array of details, usually about how to sign up or how to pay. only appears on the app's detail page below the box
				'This app features a Basic Plan that is free to use for Bevo Media users!'
			)
		),
		2 => array(
			'ID' => '2',
			'appName' => 'PPV Spy',
			'price' => '',
			'signupURL' => '#',
			'launchURL' => '#',
			'logoURL' => '/Themes/BevoMedia/apps-layout/img/applogos/ppvspy.png',
			'descTitle' => 'Thousands upon thousands of money-making Pay-Per-View Campaigns right at your fingertips',
			'descText' => 'PPV Spy is the only research tool for Pay-Per-View ads in existence. By using it, you will get unique insights into what works for your competitors - and what doesn\'t. It\'s all at your fingertips and ready to be replicated.',
			'descList' => array(
				'Browse and search in thousands of PPV campaigns, offers, and targets',
				'Learn what pops are seen the most',
				'Download full lists of target URLs'
			),
			'descDetail' => array(
				'Full access to the Bevo PPV Spy App for only $385/month or a $999 one-time payment.',
				'Requires a verified Bevo account. <a href="#">Verify Now</a>, then come back to this page!'
			)
		),
		3 => array(
			'ID' => '3',
			'appName' => 'Overnight Affiliate',
			'price' => '',
			'signupURL' => '#',
			'launchURL' => '#',
			'logoURL' => '/Themes/BevoMedia/img/pagedesc_overaff.png',
			'descTitle' => 'Learn to be profitable overnight',
			'descText' => 'Overnight Affiliate is a step-by-step walkthrough of every aspect a beginner affiliate needs to get a profitable campaign. It\'s packed with videos, step-by-step instructions, example campaigns, and weekly webinars where verifed users can get personalized one-on-one help specifically for their own campaigns.',
			'descList' => array(
				'Step By Step Videos',
				'Examples of Successful Campaigns',
				'Weekly Personalized Webinars'
			),
			'descDetail' => array(
				'A structured 18 video course, developed to make a beginner affiliate prepared with everything they need to get a profitable campaign.',
				'Get set up with example campaigns that have made big bucks. See exactly how the campaign were done, and use for your own campaigns.',
				'Weekly webinars to have successful internet marketers peronally help you with your campaigns. Get step by step directions on how to turn your campaigns profitable!',
				'The Overnight Affiliate is an premium package that is free to use for verified BevoMedia users!'
			)
		)		
	);//$apps
	
	
	
	
