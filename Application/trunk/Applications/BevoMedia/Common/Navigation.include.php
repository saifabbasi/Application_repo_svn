<?php	//goddammit Zend

	function SoapPageMenu($module=false,$firstActive=false,$secondActive=false,$leaveSubMenuOpen=false) {
		global $hasSubMenu;
		
		$menuItemsLeft = GetMenuItems($module);
		$menuItemsRight = GetMenuItems($module,'right');
	
		$out = '<div id="pagemenu">'.SoapMakeMenu($module,$menuItemsLeft,$firstActive,$secondActive);
		$out .= $menuItemsRight ? SoapMakeMenu($module,$menuItemsRight,$firstActive,$secondActive,'floatright') : '';
		$out .= '</div>';
		
		if($hasSubMenu) {
			$out .= '<div id="pagesubmenu"><ul>'.$hasSubMenu.'</ul>';
			$out .= $leaveSubMenuOpen ? '' : '</div>';
		}
		
		return $out;
	}//end soapPageMenu()
	
	
	function SoapMakeMenu($module,$array,$firstActive,$secondActive,$class=false,$nospan=false) {
		global $hasSubMenu;
		$modulesWithSubMenuOnSamePage = array('tut');
		
		$imageDir = SCRIPT_ROOT.'img_new'; //no tralier
		$out = false;//error_reporting=ALL is truly annoying at times
		
		if(is_array($array)) {
			$out = '<ul';
			$out .= $class ? ' class="'.$class.'"' : '';
			$out .= '>';
			
			foreach($array as $first => $item) {
				$haskids = array_key_exists(2, $item) ? true : false;
				
				$out .= '<li';
				$out .= $haskids ? ' class="haskids"' : '';
				$out .= '><a';
				$out .= $first == $firstActive ? ' class="active"' : '';
				$out .= ' href="'.$item[1].'">'.$item[0];
				$out .= $haskids && $first != $firstActive ? '<img src="'.$imageDir.'/icon_arrsmall_down_w.png" alt="" />' : '';
				$out .= $nospan ? '' : '<span></span>';
				$out .= '</a>';
				
				if($haskids) {
					$kid = '';
					$doSubMenu = false;
					
					foreach($item[2] as $second => $kiditem) {
						if(!$hasSubMenu && !$doSubMenu && $second == $secondActive)
							$doSubMenu = true;
						
						$kidhaskids = array_key_exists(2, $kiditem) ? true : false;
						
						$kid .= '<li';
						$kid .= $kidhaskids ? ' class="haskids"' : '';
						$kid .= '><a';
						$kid .= $secondActive && $second == $secondActive ? ' class="active"' : '';
						$kid .= ' href="'.$kiditem[1].'">'.$kiditem[0].'</a>';
						
						if($kidhaskids) {
							$kid .= '<ul>';
							foreach($kiditem[2] as $third => $babyitem)
								$kid .= '<li><a href="'.$babyitem[1].'">'.$babyitem[0].'</a></li>';
							$kid .= '</ul>';
						}//endif kidhaskids					
					}//endforeach kiditem
					
					if($doSubMenu && $first == $firstActive)
						$hasSubMenu = $kid;
					
					$out .= $first != $firstActive ? '<ul>'.$kid.'</ul>' : '';
					unset($kid);
					
				}//endif first has kids
				$out .= '</li>';
			}//endforeach menuitems
			$out .= '</ul>';
		}//endif is_array $array
		
		return $out;
	
	}//end soapMakeMenu()
	
	function GetMenuItems($module=false, $position='left') { //some day I really wanna put this into a class, this is just redic
		$out = false;
		$path = false;
		
		switch($module) {
		case 'kwt': //keyword tracker
			if($position == 'left') {
				$out = array( //label, url, array of children if any
					'overview' => array('Statistics','/BevoMedia/KeywordTracker/Overview.html',array(
							'overview' => array('Cumulative','/BevoMedia/KeywordTracker/Overview.html'),
							'lp' => array('Landing Page Stats','/BevoMedia/KeywordTracker/LandingPage.html'),
							'spy' => array('Visitor Spy','/BevoMedia/KeywordTracker/VisitorSpy.html')
							)
						),
					'ppc' => array('PPC Tracker','/BevoMedia/KeywordTracker/Broad.html',array(
							'bidded' => array('Bidded KWs','/BevoMedia/KeywordTracker/Broad.html'),
							'exact' => array('Exact KWs','/BevoMedia/KeywordTracker/Exact.html'),
							'campaigns' => array('Campaigns','/BevoMedia/KeywordTracker/Campaign.html'),
							'adgroups' => array('Ad Groups','/BevoMedia/KeywordTracker/Adgroup.html'),
							'advars' => array('Ad Variations','/BevoMedia/KeywordTracker/Offer.html'),
							)
						),
					'ppv' => array('PPV Tracker','/BevoMedia/KeywordTracker/PPVStats.html'),
					'mb' => array('Media Buy Stats','/BevoMedia/KeywordTracker/MediaBuys.html')
				);
			
			} else {
				$out = array(
					'codes' => array('Tracking Codes','/BevoMedia/KeywordTracker/CreatedCodes.html',array(
							'existing' => array('Existing Codes','/BevoMedia/KeywordTracker/CreatedCodes.html'),
							'new' => array('Create New','/BevoMedia/KeywordTracker/Code.html',array(
								'adwords' => array('Google Adwords','/BevoMedia/KeywordTracker/Code.html?Select=google'),
								'yahoo' => array('Yahoo! Search','/BevoMedia/KeywordTracker/Code.html?Select=yahoo'),
								'msn' => array('MSN AdCenter / Bing','/BevoMedia/KeywordTracker/Code.html?Select=msn'),
								'trafficvance' => array('Traffic Vance','/BevoMedia/KeywordTracker/Code.html?Select=trafficvance'),
								'adon' => array('AdOn Network','/BevoMedia/KeywordTracker/Code.html?Select=adon'),
								'mediatraffic' => array('Media Traffic','/BevoMedia/KeywordTracker/Code.html?Select=mediatraffic'),
								'dircpv' => array('Direct CPV','/BevoMedia/KeywordTracker/Code.html?Select=dircpv'),
								'leadimpact' => array('Lead Impact','/BevoMedia/KeywordTracker/Code.html?Select=leadimpact'),
								'other' => array('Other...','/BevoMedia/KeywordTracker/Code.html?Select=other'),
									)
								),
							),
						),
					'rotators' => array('Rotators','/BevoMedia/KeywordTracker/LandingPageRotationSetup.html',array(
							'rotators_lp_overview' => array('Landing Page Rotators','/BevoMedia/KeywordTracker/LandingPageRotationSetup.html'),
							'rotators_offer_overview' => array('Offer Rotators','/BevoMedia/KeywordTracker/OfferRotationSetup.html')
							),
						),
					'adjust_media_cpc' => array('Adjust Media CPC','/BevoMedia/KeywordTracker/AdjustMediaBuyPrice.html'),
					'subids' => array('SubIDs','/BevoMedia/KeywordTracker/SubIDLookup.html', array(
								'lookup' => array('Lookup','/BevoMedia/KeywordTracker/SubIDLookup.html'),
								'upload' => array('Manual Upload','/BevoMedia/KeywordTracker/ManuallyUploadSubIDs.html'),
							)
						),
					);
			}
			break;
		
		case 'tut' : //tutorials
			if($position == 'left') {
				$path = '/BevoMedia/Publisher';
				$out = array(
					'general' => array('General',$path.'/PPCTutorials.html',array(
							array('Why Free?',$path.'/PPCTutorials.html#Whyfree'),
							array('Data Security',$path.'/PPCTutorials.html#DataSecurity'),
							array('Getting Into Networks',$path.'/PPCTutorials.html#GettingIntoNetworks'),
							array('Negative Experiences with Networks?',$path.'/PPCTutorials.html#NegativePartnerExperience'),
							array('Adding Networks',$path.'/PPCTutorials.html#AddingNetworks')
							)						
						),
					'ppc' => array('PPC Management',$path.'/PPCTutorialsPPC.html',array(
							array('Affiliate Networks',$path.'/PPCTutorialsPPC.html#AddingAffNetworks'),
							array('PPC Networks',$path.'/PPCTutorialsPPC.html#AddingPPCNetworks'),
							array('Campaign Editor',$path.'/PPCTutorialsPPC.html#CampaignEditorTutorial'),
							array('Campaign Upload Status',$path.'/PPCTutorialsPPC.html#BevoEditorHowLong'),
							array('API Fees',$path.'/PPCTutorialsPPC.html#APIFeesExplained')
							)						
						),
					'search' => array('Search Engine PPC Management',$path.'/PPCTutorialsSE.html',array(
							array('Google Adwords',$path.'/PPCTutorialsSE.html#AdwordsVideo'),
							array('Yahoo! Search Marketing',$path.'/PPCTutorialsSE.html#YSMVideo'),
							array('MSN AdCenter',$path.'/PPCTutorialsSE.html#MSNVideo')							
							)						
						),
					'kwtracker' => array('Keyword Tracker',$path.'/PPCTutorialsKWTracker.html',array(
							array('How-To: Landing Pages',$path.'/PPCTutorialsKWTracker.html#HowdoIlandingpage'),
							array('How-To: Direct Linking',$path.'/PPCTutorialsKWTracker.html#HowdoIdirectlink'),
							array('How-To: Media Buys',$path.'/PPCTutorialsKWTracker.html#HowdoImediabuy'),
							array('Tracking Methods',$path.'/PPCTutorialsKWTracker.html#TrackingMethods'),
							array('Network Not Featured Yet',$path.'/PPCTutorialsKWTracker.html#UnsupportedNetworks')
							)						
						),
					'selfhosted' => array('Bevo Self-Hosted',$path.'/PPCTutorialsSelfHosted.html',array(
							array('"Create Views" Error',$path.'/PPCTutorialsSelfHosted.html#createviewserror'),
							array('500 Internal Server Error',$path.'/PPCTutorialsSelfHosted.html#internalerror'),
							array('"PHP out of memory"',$path.'/PPCTutorialsSelfHosted.html#outofmemoryerror'),
							array('LiquidWeb Tracking Error',$path.'/PPCTutorialsSelfHosted.html#liquidweberror'),
							array('How-To: Re-Install Bevo',$path.'/PPCTutorialsSelfHosted.html#reinstallbevo')
							)					
						),
					'marketplace' => array('Marketplace',$path.'/PPCTutorialsMarketplace.html',array(
							array('How To Use',$path.'/PPCTutorialsMarketplace.html#HowtoMarketplace')	
							)
						),
					'verify' => array('Verified',$path.'/VerifiedHelp.html',array(
							array('What does it mean to be verified',$path.'/VerifiedHelp.html#Meaning'),
							array('How Do I get a verified account', '/BevoMedia/User/AddCreditCard.html'),
							array('How do I cancel my verified account',$path.'/VerifiedHelp.html#Cancel'),	
							)
						),
					);
			}
			break;
			
		case 'ppvtools' : //PPV Research Tools
			$path = '/BevoMedia/PPVTools';
			if($position == 'left') {
				$out = array( //label, url, array of children if any
					'urls' => array('Get URLs',$path.'/PageSniper.html',array(
							'pagesniper' => array('Get URLs from Keywords',$path.'/PageSniper.html'),
							'extractor' => array('Extract Links from a Site',$path.'/Extractor.html'),
							'alexa' => array('Alexa Search Ranking',$path.'/Alexa.html')
							)
						),
					'keywords' => array('Get Keywords',$path.'/PageSearchKeyword.html',array(
							'suggested_kws' => array('Suggested Keywords',$path.'/PageSearchKeyword.html'),
							'site_kws' => array('Site Keywords',$path.'/SiteKeywords.html')
							)
						),
					'keywordspy' => array('Keyword Spy',$path.'/WebSiteSpy.html',array(
							'spy_website' => array('Website Spy',$path.'/WebSiteSpy.html'),
							'spy_kw' => array('Keyword Spy',$path.'/KeywordSpy.html'),
							'spy_comp' => array('Keyword Comparator',$path.'/KeywordComparator.html')
							)
						),
					'linkbuilder' => array('List Builder',$path.'/LinkBuilder.html')
				);
			
			}//endif position left
			
			break;
		}//end switch module
		
		return $out;
	}//end GetmenuItems
