<?php
/**
 * Class which assists layout templates.
 */

/**
 * Class which assists layout templates.
 * 
 * Class which assists layout templates by generating headings, common drop down form choices and assisting with conditional aesthetic changes
 * such as setting which "tab" a user is on within the menu.
 *  
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
Class PageHelper {
	
	/**
	 * @var String $Heading
	 */
	Public $Heading;

	/**
	 * @var String $SubHeading
	 */
	Public $SubHeading;

	/**
	 * @var String $HeadingImage
	 */
	Public $HeadingImage;
	
	/**
	 * @var String $Controller
	 */
	Public $Controller;

	/**
	 * @var String $Function
	 */
	Public $Function;
	
	/**
	 * @var String $Application
	 */
	Public $Application;
	
	/**
	 * @var String $Area
	 */
	Public $Area;	
	
	
	/**
	 * Constructor
	 */
	Public Function __construct()
	{
		$this->Init();
		$this->PopulateDisplayValues();
	}
	
	Private Function Init()
	{
		$this->Controller = Zend_Registry::Get('Instance/Module');
		$this->Function = Zend_Registry::Get('Instance/Function');
		$this->Application = Zend_Registry::Get('Instance/Application');
	}
	
	Private Function PopulateDisplayValues()
	{
		$PagePresets = array();
		
		//[ Controller Function Area ]
		
		//User
		$PagePresets['User/*/HeadingImage'] = 'icon-mynetworks.gif';
		$PagePresets['User/Index/Area'] = 'Overview';
		
		$PagePresets['User/AdwordsAPIUsage/Area'] =
		
			//SelfHosted
			$PagePresets['User/SelfHostedAdmin/UniquePageName'] = 'user_selfhostedadmin';
			$PagePresets['User/SelfHostedAdmin/HeadingImage'] = 'pagedesc_accountinfo.png';
			$PagePresets['User/SelfHostedAdmin/Heading'] = 'Bevo Self-Hosted Admin Panel';
			$PagePresets['User/SelfHostedAdmin/SubHeading'] = 'Edit the local users on your self-hosted service. Link up local accounts to bevomedia.com accounts to take advantage of our API features!';
			
			$PagePresets['User/SelfHostedLogin/UniquePageName'] = 'user_selfhostedlogin';
			$PagePresets['User/SelfHostedLogin/HeadingImage'] = 'opencode_page.png';
			$PagePresets['User/SelfHostedLogin/Heading'] = 'Bevo Media Self-Hosted Solutions';
			$PagePresets['User/SelfHostedLogin/SubHeading'] = 'Bevo Media offers a Self Hosted solution for serious publishers. The self hosted version is designed for high volume publishers, and allows all data to be stored on the users server to ensure 100% security. Bevo Self Hosted was built to scale, allowing publishers to send a mass amount of traffic with a reliable campaign management and tracking solution.';
			
			$PagePresets['User/SelfHostedTOS/UniquePageName'] = 'user_selfhostedlogin';
			$PagePresets['User/SelfHostedTOS/HeadingImage'] = 'opencode_page.png';
			$PagePresets['User/SelfHostedTOS/Heading'] = 'Bevo Media Self-Hosted Solutions - ToS';
			$PagePresets['User/SelfHostedTOS/SubHeading'] = 'Bevo Media offers a Self Hosted solution for serious publishers. The self hosted version is designed for high volume publishers, and allows all data to be stored on the users server to ensure 100% security. Bevo Premium offers unlimited API calls, as well as access to our premium features. Bevo Premium allows users to comfortably enjoy all of the features within Bevo Media.';
			
			$PagePresets['User/RackspaceWizard/UniquePageName'] = 'user_rackspacewizard';
			$PagePresets['User/RackspaceWizard/HeadingImage'] = 'opencode_page.png';
			$PagePresets['User/RackspaceWizard/Heading'] = 'Bevo Media Self-Hosted Solutions';
			$PagePresets['User/RackspaceWizard/SubHeading'] = 'Bevo Self-hosted requires efficient servers because of the complexity and power of the interface. In order to make the usability as easy and smooth as possible on the user, Bevo has partnered with Rackspace. With Rackspace, installing the Bevo Self-host is easier and recommended to ensure a speedy, uncomplicated installation to servers that are proven to be reliable.';
			
			$PagePresets['User/ServerScript/UniquePageName'] = 'user_serverscript';
			$PagePresets['User/ServerScript/HeadingImage'] = 'opencode_page.png';
			$PagePresets['User/ServerScript/Heading'] = 'Server Setup';
			$PagePresets['User/ServerScript/SubHeading'] = 'Learn how to get Bevo running on Rackspace, or any Ubuntu or Debian server.';
			
			$PagePresets['User/SelfHostedLoginDownload/UniquePageName'] = 'user_selfhosteddownload';
			$PagePresets['User/SelfHostedLoginDownload/HeadingImage'] = 'opencode_page.png';
			$PagePresets['User/SelfHostedLoginDownload/Heading'] = 'Download Bevo Media Self-Hosted';
			$PagePresets['User/SelfHostedLoginDownload/SubHeading'] = '<b>IMPORTANT:</b> We\'re working really hard to make Bevo easy to install, but this is still the beta period, and we can\'t predict everyone\'s machine configuration. If you\'re running into trouble, please let us know so we can provide better instructions for installing in all environments.</p><p>Having trouble? <a href="/BevoMedia/Publisher/PPCTutorialsSelfHosted.html">Common problems and Self-Hosted FAQ</a>';
			
			//My Account
			$PagePresets['User/AccountInformation/UniquePageName'] = 'user_accountinfo';
			$PagePresets['User/AccountInformation/HeadingImage'] = 'pagedesc_accountinfo.png';
			$PagePresets['User/AccountInformation/Heading'] = 'My Bevo Account';
			$PagePresets['User/AccountInformation/SubHeading'] = 'Edit your BeVo Media account details. Here you can change your account passwords, deactivate networks, and add a website to your account. If you have any questions at all, feel free to submit a ticket.';
			
			//Billing
			$PagePresets['User/Invoice/UniquePageName'] = 'user_accountinfo';
			$PagePresets['User/Invoice/HeadingImage'] = 'pagedesc_accountinfo.png';
			$PagePresets['User/Invoice/Heading'] = 'Billing';
			$PagePresets['User/Invoice/SubHeading'] = 'View your billing information.';
			
			$PagePresets['User/MyProducts/UniquePageName'] = 'user_accountinfo';
			$PagePresets['User/MyProducts/HeadingImage'] = 'pagedesc_accountinfo.png';
			$PagePresets['User/MyProducts/Heading'] = 'My Products';
			$PagePresets['User/MyProducts/SubHeading'] = 'View your product information.';
			
			$PagePresets['User/ChangeProfile/UniquePageName'] = 'user_changeprofile';
			$PagePresets['User/ChangeProfile/HeadingImage'] = 'pagedesc_accountinfo.png';
			$PagePresets['User/ChangeProfile/Heading'] = 'Change Your BeVo Media Account Information';
			$PagePresets['User/ChangeProfile/SubHeading'] = 'This is your Bevo profile information. To ensure that we can reach you (for example, when you submit a support ticket), please make sure that your profile information is accurate and up-to-date.';
			
			$PagePresets['User/CreditCard/UniquePageName'] = 'user_creditcard';
			$PagePresets['User/CreditCard/HeadingImage'] = 'pagedesc_accountinfo.png';
			$PagePresets['User/CreditCard/Heading'] = 'Update Payment Options';
			$PagePresets['User/CreditCard/SubHeading'] = 'Update or Remove Payment Options';
			
			$PagePresets['User/Referrals/UniquePageName'] = 'user_referrals';
			$PagePresets['User/Referrals/HeadingImage'] = 'pagedesc_accountinfo.png';
			$PagePresets['User/Referrals/Heading'] = 'Referrals';
			$PagePresets['User/Referrals/SubHeading'] = 'User our referral system and earn money';			
			
			$PagePresets['User/AddCreditCard/UniquePageName'] = 'user_addcc';
			$PagePresets['User/AddCreditCard/HeadingImage'] = '';
			$PagePresets['User/AddCreditCard/Heading'] = '';
			$PagePresets['User/AddCreditCard/SubHeading'] = '';
			
			//Apps
			$PagePresets['User/AppStore/UniquePageName'] = 'user_appstore';
			$PagePresets['User/AppStore/HeadingImage'] = 'pagedesc_appstore.png';
			$PagePresets['User/AppStore/Heading'] = 'Bevo App Store';
			$PagePresets['User/AppStore/SubHeading'] = 'In the Bevo App Store, Verified users will have the opportunity to purchase some of the best affiliate marketing tools in the industry. Bevo Media prides itself on being a premium platform for quality affiliate marketers and offering tools that will take profits to the next level.';
			
			
			
		//Marketplace
		$PagePresets['Marketplace/*/Area'] = 'Marketplace';
		
			$PagePresets['Marketplace/Index/UniquePageName'] = 'mp_index';
			$PagePresets['Marketplace/Index/HeadingImage'] = 'pagedesc_market.png';
			$PagePresets['Marketplace/Index/Heading'] = 'Bevo Marketplace';
			$PagePresets['Marketplace/Index/SubHeading'] = 'Looking for a designer, a programmer, article writer or SEO? The Bevo marketplace has everything you need. The Bevo team knows the industry inside and out and provides quality work with a quick turnaround time. Simply request a project and you\'ll be in touch with a Bevo representative to agree to project terms and initiate.';
			
			$PagePresets['Marketplace/MarketplacePaypalFailure/UniquePageName'] = 'mp_paypalfailure';
			$PagePresets['Marketplace/MarketplacePaypalFailure/HeadingImage'] = 'pagedesc_market.png';
			$PagePresets['Marketplace/MarketplacePaypalFailure/Heading'] = 'Failure!';
			$PagePresets['Marketplace/MarketplacePaypalFailure/SubHeading'] = 'You either cancelled the transaction, or a fatal Paypal error occurred. <a href="/BevoMedia/Marketplace/">Go back</a>';
			
			$PagePresets['Marketplace/MarketplacePaypalSuccess/UniquePageName'] = 'mp_paypalsuccess';
			$PagePresets['Marketplace/MarketplacePaypalSuccess/HeadingImage'] = 'pagedesc_market.png';
			$PagePresets['Marketplace/MarketplacePaypalSuccess/Heading'] = 'Success!';
			$PagePresets['Marketplace/MarketplacePaypalSuccess/SubHeading'] = 'Your BevoMedia Marketplace transaction has been paid for. We\'ll submit your job request to the service provider, who will get started right away. <a href="/BevoMedia/Marketplace/">Return to the Marketplace</a>';
			
			$PagePresets['Marketplace/MentorshipProgram/UniquePageName'] = 'mp_mentorship';
			$PagePresets['Marketplace/MentorshipProgram/HeadingImage'] = 'pagedesc_diploma.png';
			$PagePresets['Marketplace/MentorshipProgram/Heading'] = 'The Bevo Mentorship Program';
			$PagePresets['Marketplace/MentorshipProgram/SubHeading'] = 'The Bevo Mentorship Program is an all inclusive, all access program designed to guide those who are new to the industry into becoming successful internet marketers. The Bevo consultants are well-seasoned affiliate marketers who have experienced great success in the industry and enjoy helping others learn the ropes. The mentorship program is operated by BevoSearch, a subsidiary consulting firm of BevoMedia.';
			
			$PagePresets['Marketplace/Premium/UniquePageName'] = 'mp_premium';
			$PagePresets['Marketplace/Premium/HeadingImage'] = 'bevopremium_headicon.gif';
			$PagePresets['Marketplace/Premium/Heading'] = 'Sign Up For A Bevo Premium Account';
			$PagePresets['Marketplace/Premium/SubHeading'] = 'To get the most out of your Bevo experience, upgrade to Bevo Premium. With Bevo Premium, users are given unlimited API calls, a personal technical support contact and access to our premium PPV and PPC tools. Bevo Premium is definitely the best value for such a powerful internet marketing application anywhere!';
			
			$PagePresets['Marketplace/PremiumSignup/UniquePageName'] = 'mp_premiumsignup';
			$PagePresets['Marketplace/PremiumSignup/HeadingImage'] = 'bevopremium_headicon.gif';
			$PagePresets['Marketplace/PremiumSignup/Heading'] = 'Sign Up For A Bevo Premium Account: Review';
			$PagePresets['Marketplace/PremiumSignup/SubHeading'] = 'Make sure your PayPal account information is accurate, then proceed.';

			$PagePresets['Marketplace/PremiumReview/UniquePageName'] = 'mp_premiumreview';
			$PagePresets['Marketplace/PremiumReview/HeadingImage'] = 'bevopremium_headicon.gif';
			$PagePresets['Marketplace/PremiumReview/Heading'] = 'Sign Up For A Bevo Premium Account: Step 2';
			$PagePresets['Marketplace/PremiumReview/SubHeading'] = 'Review your PayPal information, then proceed to the last step.';

			$PagePresets['Marketplace/PremiumSubmit/UniquePageName'] = 'mp_premiumsubmit';
			$PagePresets['Marketplace/PremiumSubmit/HeadingImage'] = 'bevopremium_headicon.gif';
			$PagePresets['Marketplace/PremiumSubmit/Heading'] = 'Processing...';
			$PagePresets['Marketplace/PremiumSubmit/SubHeading'] = ' ';

			$PagePresets['Marketplace/PremiumPaypalFailure/UniquePageName'] = 'mp_premiumfailure';
			$PagePresets['Marketplace/PremiumPaypalFailure/HeadingImage'] =  'bevopremium_headicon.gif';
			$PagePresets['Marketplace/PremiumPaypalFailure/Heading'] = 'An error occured!';
			$PagePresets['Marketplace/PremiumPaypalFailure/SubHeading'] = 'You either cancelled the transaction, or a fatal Paypal error occurred.';
			
			$PagePresets['Marketplace/PremiumPaypalSuccess/UniquePageName'] = 'mp_premiumsuccess';
			$PagePresets['Marketplace/PremiumPaypalSuccess/HeadingImage'] =  'bevopremium_headicon.gif';
			$PagePresets['Marketplace/PremiumPaypalSuccess/Heading'] = 'Welcome to Bevo Premium!';
			$PagePresets['Marketplace/PremiumPaypalSuccess/SubHeading'] = 'Thanks for upgrading, your BevoMedia transaction is now complete. Now you can unleash the full BevoMedia feature set!';			
			
		
		//Offers
		$PagePresets['Offers/*/Area'] = 'Offers';
		
			$PagePresets['Offers/NameYourPayout/UniquePageName'] = 'offers_name_your_payout';
			$PagePresets['Offers/NameYourPayout/HeadingImage'] = 'pagedesc_nyp.png';
			$PagePresets['Offers/NameYourPayout/Heading'] = 'Name Your Payout';
			$PagePresets['Offers/NameYourPayout/SubHeading'] = 'Get the payout you want! Enter your requested payout for a a specific offer or niche and have the Bevo networks bid for you to run with them. If a match is found, a Bevo representative will connect you with your winning network.</p><p class="nypnote">';
			
			$PagePresets['Offers/NameYourPayoutResult/UniquePageName'] = 'offers_name_your_payout';
			$PagePresets['Offers/NameYourPayoutResult/HeadingImage'] = 'pagedesc_nyp.png';
			$PagePresets['Offers/NameYourPayoutResult/Heading'] = 'Name Your Payout';
			$PagePresets['Offers/NameYourPayoutResult/SubHeading'] = 'Get the payout you want! Enter your requested payout for a a specific offer or niche and have the Bevo networks bid for you to run with them. If a match is found, a Bevo representative will connect you with your winning network.</p><p>Please note: the highest bidding network may not match your desired payout and/or EPC <em>exactly</em>, but it\'s the winner among all bidders.';			
		
			$PagePresets['Offers/Index/UniquePageName'] = 'offers_index';
			$PagePresets['Offers/Index/HeadingImage'] = 'pagedesc_offervault.png';
			$PagePresets['Offers/Index/Heading'] = 'Bevo Offer Hub';
			$PagePresets['Offers/Index/SubHeading'] = 'Using Bevo\'s revolutionary Offer Search Tool, you can search for offers across all affiliate networks.';
			
			$PagePresets['Offers/BestPerformers/UniquePageName'] = 'offers_bestperf';
			$PagePresets['Offers/BestPerformers/HeadingImage'] = 'pagedesc_offervault.png';
			$PagePresets['Offers/BestPerformers/Heading'] = 'Bevo Offer Hub';
			$PagePresets['Offers/BestPerformers/SubHeading'] = 'Using Bevo\'s revolutionary Offer Search Tool, you can search for offers across all affiliate networks.';
			
			$PagePresets['Offers/MySavedLists/UniquePageName'] = 'offers_mysavedlists';
			$PagePresets['Offers/MySavedLists/HeadingImage'] = 'pagedesc_mysavedlists.png';
			$PagePresets['Offers/MySavedLists/Heading'] = 'My Saved Offer Lists';
			$PagePresets['Offers/MySavedLists/SubHeading'] = 'Offer Lists make it easy for you to save offers you are considering for a campaign. Use the yellow button to the left of any offer in the search results to add that offer to a list!';
			
			$PagePresets['Offers/MyStats/UniquePageName'] = 'offers_mystats';
			$PagePresets['Offers/MyStats/HeadingImage'] = 'pagedesc_mynetworkstats.png';
			$PagePresets['Offers/MyStats/Heading'] = 'My Network Stats';
			$PagePresets['Offers/MyStats/SubHeading'] = 'View your detailed statistics within your affiliate network. This page allows you to see which offers generated stats, and the SubIDs associated with your affiliate account.';
			
			/*
			$PagePresets['Offers/Index/UniquePageName'] = 'offers_index';
			$PagePresets['Offers/Index/HeadingImage'] = 'pagedesc_offers.png';
			$PagePresets['Offers/Index/Heading'] = 'Analyze Offer Performance';
			$PagePresets['Offers/Index/SubHeading'] = 'Analyze your performance with a specific network below by clicking on stats - you can even drill down to your performance on a specific offer and sub id. Using Bevo\'s Search Offer Tool, you can search for CPA offers across all of the affiliate networks of which you are a member.';
			*/
			$PagePresets['Offers/Search/UniquePageName'] = 'offers_search';
			$PagePresets['Offers/Search/HeadingImage'] = 'pagedesc_offers.png';
			$PagePresets['Offers/Search/Heading'] = 'Your Offer Search Results';
			$PagePresets['Offers/Search/SubHeading'] = 'Using Bevo\'s Search Offer Tool, you can search for CPA offers across all of the affiliate networks of which you are a member.';
			
			$PagePresets['Offers/OfferAnalysis/UniquePageName'] = 'offers_offeranalysis';
			$PagePresets['Offers/OfferAnalysis/HeadingImage'] = 'pagedesc_bevovault.png';
			$PagePresets['Offers/OfferAnalysis/Heading'] = 'Retrieve Tracking Codes';
			$PagePresets['Offers/OfferAnalysis/SubHeading'] = 'Click on the corresponding network button to retrieve tracking codes from the respective affiliate network. Using Bevo\'s Search Offer Tool, you can search for CPA offers across all of the affiliate networks of which you are a member.';
			
			$PagePresets['Offers/Stats/UniquePageName'] = 'offers_stats';
			$PagePresets['Offers/Stats/HeadingImage'] = 'pagedesc_bevovault.png';
			$PagePresets['Offers/Stats/Heading'] = 'View Network Stats';
			$PagePresets['Offers/Stats/SubHeading'] = 'View your detailed statistics within your affiliate network. This page allows you to see which offers generated stats and the subIDs associated with your affiliate account.';
			
			$PagePresets['Offers/StatsIndustry/UniquePageName'] = 'offers_statsindustry';
			$PagePresets['Offers/StatsIndustry/HeadingImage'] = 'pagedesc_bevovault.png';
			$PagePresets['Offers/StatsIndustry/Heading'] = 'View Network Stats';
			$PagePresets['Offers/StatsIndustry/SubHeading'] = 'View your detailed statistics within your affiliate network. This page allows you to see which offers generated stats and the subIDs associated with your affiliate account.';
			
			$PagePresets['Offers/SubReport/UniquePageName'] = 'offers_subreport';
			$PagePresets['Offers/SubReport/HeadingImage'] = 'pagedesc_bevovault.png';
			$PagePresets['Offers/SubReport/Heading'] = 'View Network Stats';
			$PagePresets['Offers/SubReport/SubHeading'] = 'View your detailed statistics within your affiliate network. This page allows you to see which offers generated stats and the subIDs associated with your affiliate account.';
			
			$PagePresets['Offers/Detail/UniquePageName'] = 'offers_detail';
			$PagePresets['Offers/Detail/HeadingImage'] = 'pagedesc_bevovault.png';
			$PagePresets['Offers/Detail/Heading'] = 'View and Retrieve Your Offers';
			$PagePresets['Offers/Detail/SubHeading'] = 'In this section, you can view all of your offers  for your affiliate networks. Drill down to see specific offer details, and retrieve your codes right from the Bevo Interface!';
		
		//Publisher
		$PagePresets['Publisher/*/HeadingImage'] = 'pagedesc_ppc.png';
		$PagePresets['Publisher/*/Area'] =
		
			//My Networks
			$PagePresets['Publisher/Index/Area'] = 'MyNetworks';
			
			$PagePresets['Publisher/Index/UniquePageName'] = 'pub_mynetworks';
			$PagePresets['Publisher/Index/HeadingImage'] = 'pagedesc_mynetworks.png';
			$PagePresets['Publisher/Index/Heading'] = 'My Networks';
			$PagePresets['Publisher/Index/SubHeading'] = 'Apply to any of the networks below so that Bevo can consolidate your stats onto its interface. Bevo has partnerships with the most popular and trusted affiliate networks listed below. Feel free to apply to new networks, even in a new advertising model - with Bevo\'s Network Consolidation all of your network stats are centralized onto the Bevo interface!';
			
			$PagePresets['Publisher/Reviews/UniquePageName'] = 'pub_reviews';
			$PagePresets['Publisher/Reviews/HeadingImage'] = 'pagedesc_mynetworks.png';
			$PagePresets['Publisher/Reviews/Heading'] = 'Network Reviews';
			$PagePresets['Publisher/Reviews/SubHeading'] = ' On the reviews page, Bevo users can rate and comment about the networks they have used. Hearing directly from publishers about their experiences with networks give Bevo users a leg up when deciding who to run with. Rate and leave a comment to help out your fellow publishers and make Bevo as interactive as possible. Feedback and ratings come from Bevo users and Affiliatepaying.com voters.';			
			
			//PPC
			$PagePresets['Publisher/PPCManager/Area'] = 'PPCManager';
		
			$PagePresets['Publisher/PPCManager/UniquePageName'] = 'ppc_manager';
			$PagePresets['Publisher/PPCManager/HeadingImage'] = 'pagedesc_ppc.png';
			$PagePresets['Publisher/PPCManager/Heading'] = 'Pay Per Click Management';
			$PagePresets['Publisher/PPCManager/SubHeading'] = 'Bevo\'s PPC Management gives our publishers the opportunity not only to examine their search marketing expenses, but also to edit and create all of their campaigns. Check out your search campaign stats, create or edit a campaign and gain an in-depth view of exactly where your money is going.';
                    
                    	$PagePresets['Publisher/CreatePPC/UniquePageName'] = 'ppc_createppc';
			$PagePresets['Publisher/CreatePPC/HeadingImage'] = 'pagedesc_ppccampeditor.png';
			$PagePresets['Publisher/CreatePPC/Heading'] = 'PPC Campaign Manager';
			$PagePresets['Publisher/CreatePPC/SubHeading'] = 'Bevo\'s PPC Management gives our publishers the opportunity not only to examine their search marketing expenses, but also to edit and create all of their campaigns. Check out your search campaign stats, create or edit a campaign and gain an in-depth view of exactly where your money is going.';
                    
			$PagePresets['Publisher/CreatePPCSubmit/UniquePageName'] = 'ppc_createppcsubmit';
			$PagePresets['Publisher/CreatePPCSubmit/HeadingImage'] = 'pagedesc_ppccampeditor.png';
			$PagePresets['Publisher/CreatePPCSubmit/Heading'] = 'Your items have been added to the PPC Create Queue.';
			$PagePresets['Publisher/CreatePPCSubmit/SubHeading'] = 'Campaigns you have created may take up to 15-20 minutes to appear in your PPC Account, and up to an hour to appear in your selfhosted Bevo account. You may close your browser or navigate throughout the site as you wish. You can view your queue progress in the Campaign Editor Queue at any time.';
			
                  	$PagePresets['Publisher/CreatePPCSaved/UniquePageName'] = 'ppc_createppcsaved';
			$PagePresets['Publisher/CreatePPCSaved/HeadingImage'] = 'pagedesc_ppccampeditor.png';
			$PagePresets['Publisher/CreatePPCSaved/Heading'] = 'Your Session has been saved.';
			$PagePresets['Publisher/CreatePPCSaved/SubHeading'] = 'You can load your session from the "Output" tab on the <a href="CreatePPC.html">CreatePPC</a> page.';
			
			$PagePresets['Publisher/PPCQueueProgress/UniquePageName'] = 'ppc_ppcqueueprogress';
			$PagePresets['Publisher/PPCQueueProgress/HeadingImage'] = 'pagedesc_ppccampeditor.png';
			$PagePresets['Publisher/PPCQueueProgress/Heading'] = 'PPC Campaign Create Queue Progress';
			$PagePresets['Publisher/PPCQueueProgress/SubHeading'] = 'View the status of your created campaigns in the queue.';
			
			$PagePresets['Publisher/AccountStatsPPC/UniquePageName'] = 'ppc_accountstats';
			$PagePresets['Publisher/AccountStatsPPC/HeadingImage'] = 'pagedesc_ppccampeditor.png';
			$PagePresets['Publisher/AccountStatsPPC/Heading'] = 'Search Engine PPC Account Stats';
			$PagePresets['Publisher/AccountStatsPPC/SubHeading'] = 'Analyze your PPC expenses at the Campaign level. The time period of the data can be changed which will be reflected in the graph and table. Click on the name of a Campaign to drill down to the Ad Group level.';
			
			$PagePresets['Publisher/AdwordsManualUpload/UniquePageName'] = 'ppc_adwordsmanualupload';
			$PagePresets['Publisher/AdwordsManualUpload/HeadingImage'] = 'logo_googleadwords.png';
			$PagePresets['Publisher/AdwordsManualUpload/Heading'] = 'Upload Google Adwords Stats';
			$PagePresets['Publisher/AdwordsManualUpload/SubHeading'] = 'Import your Google Adwords campaign stats. <b>Note:</b> All overlapping dates with data will be overwritten with the most recent upload. This includes data retrieved via API as well. You might also be interested in the <a href="/BevoMedia/Publisher/PPCTutorialsSE.html#AdwordsVideo">video tutorial</a>.';

			$PagePresets['Publisher/YahooManualUpload/UniquePageName'] = 'ppc_yahoomanualupload';
			$PagePresets['Publisher/YahooManualUpload/HeadingImage'] = 'logo_ysm.png';
			$PagePresets['Publisher/YahooManualUpload/Heading'] = 'Upload Yahoo! Search Marketing Stats';
			$PagePresets['Publisher/YahooManualUpload/SubHeading'] = 'Import your Yahoo! campaign stats. <b>Note:</b> All overlapping dates with data will be overwritten with the most recent upload. This includes data retrieved via API as well. You might also be interested in the <a href="/BevoMedia/Publisher/PPCTutorialsSE.html#YSMVideo">video tutorial</a>.';

			$PagePresets['Publisher/MSNManualUpload/UniquePageName'] = 'ppc_msnmanualupload';
			$PagePresets['Publisher/MSNManualUpload/HeadingImage'] = 'logo_msnadcenter.png';
			$PagePresets['Publisher/MSNManualUpload/Heading'] = 'Upload MSN AdCenter Stats';
			$PagePresets['Publisher/MSNManualUpload/SubHeading'] = 'Import your AdCenter campaign stats. <b>Note:</b> All overlapping dates with data will be overwritten with the most recent upload. This includes data retrieved via API as well. You might also be interested in the <a href="/BevoMedia/Publisher/PPCTutorialsSE.html#MSNVideo">video tutorial</a>.';
			
			$PagePresets['Publisher/CampaignStatsPPC/UniquePageName'] = 'ppc_campaignstatsppc';
			$PagePresets['Publisher/CampaignStatsPPC/HeadingImage'] = 'pagedesc_ppc.png';
			$PagePresets['Publisher/CampaignStatsPPC/Heading'] = 'PPC Campaign Stats';
			$PagePresets['Publisher/CampaignStatsPPC/SubHeading'] = 'Analyze your PPC expenses at the Ad Group level. The time period of the data can be changed which will be reflected in the graph and table. Click on the name of an Ad Group to drill down to the Keyword level.';
			
			$PagePresets['Publisher/AdGroupStatsPPC/UniquePageName'] = 'ppc_adgroupstatsppc';
			$PagePresets['Publisher/AdGroupStatsPPC/HeadingImage'] = 'pagedesc_ppc.png';
			$PagePresets['Publisher/AdGroupStatsPPC/Heading'] = 'AdGroup Stats';
			$PagePresets['Publisher/AdGroupStatsPPC/SubHeading'] = 'Analyze your PPC expenses at the Keyword level. The time period of the data can be changed which will be reflected in the graph and table.';
			
			$PagePresets['Publisher/AdGroupAdVariationsPPC/UniquePageName'] = 'ppc_adgroupadvariationsppc';
			$PagePresets['Publisher/AdGroupAdVariationsPPC/HeadingImage'] = 'pagedesc_ppc.png';
			$PagePresets['Publisher/AdGroupAdVariationsPPC/Heading'] = 'Ad Variation Stats';
			$PagePresets['Publisher/AdGroupAdVariationsPPC/SubHeading'] = 'Analyze your PPC expenses at the Keyword level. The time period of the data can be changed which will be reflected in the graph and table.</p><p>Your ad variation stats will be updated tonight.';			
			
			
			//Classroom
			$PagePresets['Publisher/Classroom/Area'] = 
			$PagePresets['Publisher/ClassroomVideo/Area'] = 
			$PagePresets['Publisher/ClassroomChapter/Area'] = 'Classroom';
			
			$PagePresets['Publisher/Classroom/UniquePageName'] = 'pub_classroom';//group w kb
			$PagePresets['Publisher/Classroom/HeadingImage'] = 'pagedesc_bevoclass.png';
			$PagePresets['Publisher/Classroom/Heading'] = 'The Bevo Classroom';
			$PagePresets['Publisher/Classroom/SubHeading'] = 'Be sure to take advantage of all of our free affiliate marketing resources. We have complied a wealth of informational material and are consistently updating it to ensure Bevo publisher\'s are the best and brightest.';
			
			$PagePresets['Publisher/ClassroomChapter/UniquePageName'] = 'pub_classroom';
			$PagePresets['Publisher/ClassroomChapter/HeadingImage'] = 'pagedesc_bevoclass.png';
			$PagePresets['Publisher/ClassroomChapter/Heading'] = 'The Bevo Classroom';
			$PagePresets['Publisher/ClassroomChapter/SubHeading'] = 'Be sure to take advantage of all of our free affiliate marketing resources. We have complied a wealth of informational material and are consistently updating it to ensure Bevo publisher\'s are the best and brightest.';
			
			$PagePresets['Publisher/ClassroomVideo/UniquePageName'] = 'pub_classroom';
			$PagePresets['Publisher/ClassroomVideo/HeadingImage'] = 'pagedesc_bevoclass.png';
			$PagePresets['Publisher/ClassroomVideo/Heading'] = 'The Bevo Classroom';
			$PagePresets['Publisher/ClassroomVideo/SubHeading'] = 'Be sure to take advantage of all of our free affiliate marketing resources. We have complied a wealth of informational material and are consistently updating it to ensure Bevo publisher\'s are the best and brightest.';
			
			$PagePresets['Publisher/KB/Area'] = 'Classroom';
			$PagePresets['Publisher/KBPost/Area'] = 'Classroom';
			
			$PagePresets['Publisher/KB/UniquePageName'] = 'pub_classroom';
			$PagePresets['Publisher/KB/HeadingImage'] = 'pagedesc_bevoclass.png';
			$PagePresets['Publisher/KB/Heading'] = 'The Knowledge Base';
			$PagePresets['Publisher/KB/SubHeading'] = 'Read through our first-class articles to learn about anything you\'ve ever wanted to know about internet marketing.';
			
			$PagePresets['Publisher/KBPost/UniquePageName'] = 'pub_classroom';
			$PagePresets['Publisher/KBPost/HeadingImage'] = 'pagedesc_bevoclass.png';
			$PagePresets['Publisher/KBPost/Heading'] = 'The Knowledge Base';
			$PagePresets['Publisher/KBPost/SubHeading'] = 'Read through our first-class articles to learn about anything you\'ve ever wanted to know about internet marketing.';
			
			//Tutorials
			$PagePresets['Publisher/PPCTutorials/Area'] = 'PPCTutorials';
			$PagePresets['Publisher/PPCTutorialsPPC/Area'] = 'PPCTutorials';
			$PagePresets['Publisher/PPCTutorialsSE/Area'] = 'PPCTutorials';
			$PagePresets['Publisher/PPCTutorialsKWTracker/Area'] = 'PPCTutorials';
			$PagePresets['Publisher/PPCTutorialsSelfHosted/Area'] = 'PPCTutorials';
			$PagePresets['Publisher/PPCTutorialsMarketplace/Area'] = 'PPCTutorials';
			$PagePresets['Publisher/VerifiedHelp/Area'] = 'PPCTutorials';
			$PagePresets['Publisher/PPVSpyHelp/Area'] = 'PPCTutorials';
			
			$PagePresets['Publisher/PPCTutorials/UniquePageName'] = 'tutorials_all'; //same for this group
			$PagePresets['Publisher/PPCTutorials/HeadingImage'] = 'pagedesc_tutorials.png';
			$PagePresets['Publisher/PPCTutorials/Heading'] = 'General F.A.Q.';
			$PagePresets['Publisher/PPCTutorials/SubHeading'] = 'Have a question about Bevo? Check out some of our most commonly asked questions from our users below.';
			
			$PagePresets['Publisher/PPCTutorialsPPC/UniquePageName'] = 'tutorials_all';
			$PagePresets['Publisher/PPCTutorialsPPC/HeadingImage'] = 'pagedesc_tutorials.png';
			$PagePresets['Publisher/PPCTutorialsPPC/Heading'] = 'PPC Tutorials';
			$PagePresets['Publisher/PPCTutorialsPPC/SubHeading'] = 'Have a question about Bevo? Check out some of our most commonly asked questions from our users below.';
			
			$PagePresets['Publisher/PPCTutorialsSE/UniquePageName'] = 'tutorials_all';
			$PagePresets['Publisher/PPCTutorialsSE/HeadingImage'] = 'pagedesc_tutorials.png';
			$PagePresets['Publisher/PPCTutorialsSE/Heading'] = 'Search Engine PPC Tutorials';
			$PagePresets['Publisher/PPCTutorialsSE/SubHeading'] = 'Have a question about Bevo? Check out some of our most commonly asked questions from our users below.';
			
			$PagePresets['Publisher/PPCTutorialsKWTracker/UniquePageName'] = 'tutorials_all';
			$PagePresets['Publisher/PPCTutorialsKWTracker/HeadingImage'] = 'pagedesc_tutorials.png';
			$PagePresets['Publisher/PPCTutorialsKWTracker/Heading'] = 'Keyword Tracker Tutorials';
			$PagePresets['Publisher/PPCTutorialsKWTracker/SubHeading'] = 'Have a question about Bevo? Check out some of our most commonly asked questions from our users below.';
			
			$PagePresets['Publisher/PPCTutorialsSelfHosted/UniquePageName'] = 'tutorials_all';
			$PagePresets['Publisher/PPCTutorialsSelfHosted/HeadingImage'] = 'pagedesc_tutorials.png';
			$PagePresets['Publisher/PPCTutorialsSelfHosted/Heading'] = 'Bevo Self-Hosted Tutorials';
			$PagePresets['Publisher/PPCTutorialsSelfHosted/SubHeading'] = 'Have a question about Bevo? Check out some of our most commonly asked questions from our users below.';
			
			$PagePresets['Publisher/PPCTutorialsMarketplace/UniquePageName'] = 'tutorials_all';
			$PagePresets['Publisher/PPCTutorialsMarketplace/HeadingImage'] = 'pagedesc_tutorials.png';
			$PagePresets['Publisher/PPCTutorialsMarketplace/Heading'] = 'Marketplace Tutorials';
			$PagePresets['Publisher/PPCTutorialsMarketplace/SubHeading'] = 'Have a question about Bevo? Check out some of our most commonly asked questions from our users below.';			

			$PagePresets['Publisher/VerifiedHelp/UniquePageName'] = 'tutorials_all';
			$PagePresets['Publisher/VerifiedHelp/HeadingImage'] = 'pagedesc_tutorials.png';
			$PagePresets['Publisher/VerifiedHelp/Heading'] = 'Verified Accounts';
			$PagePresets['Publisher/VerifiedHelp/SubHeading'] = 'Have a question about Bevo? Check out some of our most commonly asked questions from our users below.';

			$PagePresets['Publisher/PPVSpyHelp/UniquePageName'] = 'tutorials_all';
			$PagePresets['Publisher/PPVSpyHelp/HeadingImage'] = 'pagedesc_tutorials.png';
			$PagePresets['Publisher/PPVSpyHelp/Heading'] = 'PPV Spy';
			$PagePresets['Publisher/PPVSpyHelp/SubHeading'] = 'Have a question about Bevo? Check out some of our most commonly asked questions from our users below.';			
            

		//Analytics
		$PagePresets['Analytics/*/Area'] = 'Analytics';
		$PagePresets['Analytics/*/HeadingImage'] = 'logo_googleanalytics.png';
		
			$PagePresets['Analytics/AnalyticsDetail/UniquePageName'] = 'analytics_detail';
			$PagePresets['Analytics/AnalyticsDetail/Heading'] = 'Google Analytics';
			$PagePresets['Analytics/AnalyticsDetail/SubHeading'] = 'Bevo has created its own interface to view and analyze all of your Google analytics stats. Here we load all of the data from your Google Aalytics account and condense it into easy to use information. Simply enter your login info on the <a href="/BevoMedia/Publisher/Index.html">Setup &raquo; My Networks page</a> for Bevo to bring up all of your website statistics.';
			
			$PagePresets['Analytics/AnalyticDemograph/UniquePageName'] = 'analytics_demograph';
			$PagePresets['Analytics/AnalyticDemograph/Heading'] = 'Visitor Demographics';
			$PagePresets['Analytics/AnalyticDemograph/SubHeading'] = 'Drill down to determine the demographics of the users who are visiting your site. Are you creating content that keeps users coming back to your site? Is your site reaching the correct audience?';
			
			$PagePresets['Analytics/AnalyticContent/UniquePageName'] = 'analytics_content';
			$PagePresets['Analytics/AnalyticContent/Heading'] = 'Content Analysis';
			$PagePresets['Analytics/AnalyticContent/SubHeading'] = 'Dig deep into your content to determine which pages are the most popular, how many visitors go to which pages, and how long visitors stay on each page.  Examine your bounce rate to see if visitors are going on to additional pages of your site, or leaving to another site.';
			
			$PagePresets['Analytics/AnalyticSources/UniquePageName'] = 'analytics_sources';
			$PagePresets['Analytics/AnalyticSources/Heading'] = 'Traffic Sources';
			$PagePresets['Analytics/AnalyticSources/SubHeading'] = 'Check out where the visitors to your web site are coming from. Are visitors coming from search engines, other web sites, or directly to your web site?  Understanding where your visitors are coming from can help with deciding where to put your traffic generating efforts into.';

		//KeywordTracker
		$PagePresets['KeywordTracker/*/Area'] = 'KeywordTracker';
		$PagePresets['KeywordTracker/*/HeadingImage'] = 'pagedesc_bevotrack.png';
		
			$PagePresets['KeywordTracker/Overview/UniquePageName'] = 'kwt_overview';
			$PagePresets['KeywordTracker/Overview/Heading'] = 'Keyword Tracker';
			$PagePresets['KeywordTracker/Overview/SubHeading'] = 'Bevo has developed the easiest keyword tracking tool in the world to help improve the efficiency of your search marketing campaigns. Through implementing a simple tracking code on your landing page, Bevo will compile all the information you need to analyze and optimize your search campaigns.';
			
			$PagePresets['KeywordTracker/LandingPage/UniquePageName'] = 'kwt_lp';
			$PagePresets['KeywordTracker/LandingPage/Heading'] = 'Landing Page Stats';
			$PagePresets['KeywordTracker/LandingPage/SubHeading'] = 'Bevo Track will also keep track of the performance for each of your Landing Pages. Compare the relative performance of each Landing Page. This makes split testing your pages easy and fast!';
			
			$PagePresets['KeywordTracker/VisitorSpy/UniquePageName'] = 'kwt_visitorspy';
			$PagePresets['KeywordTracker/VisitorSpy/Heading'] = 'Visitor Spy';
			$PagePresets['KeywordTracker/VisitorSpy/SubHeading'] = 'Here you can review the visitors who are clicking on your ads. Check out who is clicking on what.';
			
			$PagePresets['KeywordTracker/Broad/UniquePageName'] = 'kwt_broad';
			$PagePresets['KeywordTracker/Broad/Heading'] = 'Broad Keyword Conversion Stats';
			$PagePresets['KeywordTracker/Broad/SubHeading'] = 'Bidded keywords are the actual terms you are bidding on. This page breaks down the specific conversion statistics of your bidded keywords. Easily break down how much you are spending, and making, with each of your bidded keywords.';
			
			$PagePresets['KeywordTracker/Exact/UniquePageName'] = 'kwt_exact';
			$PagePresets['KeywordTracker/Exact/Heading'] = 'Exact Keyword Conversion Stats';
			$PagePresets['KeywordTracker/Exact/SubHeading'] = 'This page breaks down the specific conversion statistics of the exact keywords searched. Easily break down how much you are spending, and making, with each of your keywords.';
			
			$PagePresets['KeywordTracker/Campaign/UniquePageName'] = 'kwt_campaign';
			$PagePresets['KeywordTracker/Campaign/Heading'] = 'Campaign Tracker';
			$PagePresets['KeywordTracker/Campaign/SubHeading'] = 'View your network statistics from the Campaign level.  Select the Campaigns you would like to compare and the respective statistic you would like to analyze.';
			
			$PagePresets['KeywordTracker/Adgroup/UniquePageName'] = 'kwt_adgroup';
			$PagePresets['KeywordTracker/Adgroup/Heading'] = 'Ad Group Tracker';
			$PagePresets['KeywordTracker/Adgroup/SubHeading'] = 'View your network statistics from the Ad Group level. Select the Ad Groups you would like to compare and the respective statistic you would like to analyze.';
			
			$PagePresets['KeywordTracker/Offer/UniquePageName'] = 'kwt_offer';
			$PagePresets['KeywordTracker/Offer/Heading'] = 'Ad Variation Tracker';
			$PagePresets['KeywordTracker/Offer/SubHeading'] = 'Bevo Track will also keep track of the performance for each of your ad variations. Find a preview of the ad variation below, along with its relative performance. This makes split testing ads easy and fast!';
			
			$PagePresets['KeywordTracker/PPVStats/UniquePageName'] = 'kwt_ppvstats';
			$PagePresets['KeywordTracker/PPVStats/Heading'] = 'PPV Keyword Conversion Stats';
			$PagePresets['KeywordTracker/PPVStats/SubHeading'] = 'This page breaks down the specific conversion statistics of your PPV keywords and URLs. Easily break down how much you are spending, and making, with each of your PPV keywords.';
			
			$PagePresets['KeywordTracker/MediaBuys/UniquePageName'] = 'kwt_mediabuys';
			$PagePresets['KeywordTracker/MediaBuys/Heading'] = 'Media Buy Stats';
			$PagePresets['KeywordTracker/MediaBuys/SubHeading'] = 'View the detailed statistics of your Media Buys. Easily track which pages are getting your clicks, and conversions, giving you all the information you need to optimize. Be sure to have your tracking pixel or postback url placed to track your conversions.';
			
			$PagePresets['KeywordTracker/CreatedCodes/UniquePageName'] = 'kwt_createdcodes';
			$PagePresets['KeywordTracker/CreatedCodes/Heading'] = 'Keyword Tracker: Existing Codes';
			$PagePresets['KeywordTracker/CreatedCodes/SubHeading'] = 'Below are codes that you have previously created.';
			
			$PagePresets['KeywordTracker/Code/UniquePageName'] = 'kwt_code';
			$PagePresets['KeywordTracker/Code/Heading'] = 'Keyword Tracker: Get New Codes';
			$PagePresets['KeywordTracker/Code/SubHeading'] = 'Follow the instructions below to add our special tracking code on the end of your offer URL. You may also find these tutorials useful: Setting up tracking for
			<a href="/BevoMedia/Publisher/PPCTutorialsKWTracker.html#HowdoIdirectlink">Direct Link Campaigns</a>, 
			<a href="/BevoMedia/Publisher/PPCTutorialsKWTracker.html#HowdoIlandingpage">Landing Pages</a>, or 
			<a href="/BevoMedia/Publisher/PPCTutorialsKWTracker.html#HowdoImediabuy">Media Buys</a>
			<br/>
			<br/>Click <a href="/BevoMedia/Publisher/PPCTutorialsKWTracker.html#MultipleOffers">here</a> for help on tracking multiple offers on a landing page.';
			
			//rotators
			$PagePresets['KeywordTracker/LandingPageRotationSetup/UniquePageName'] = 'kwt_lprotationsetup';
			$PagePresets['KeywordTracker/LandingPageRotationSetup/Heading'] = 'Landing Page Rotators';
			$PagePresets['KeywordTracker/LandingPageRotationSetup/SubHeading'] = 'On this page, you can see your the landing page rotators you\'ve set up previously.';
			
			$PagePresets['KeywordTracker/LandingPageRotationNew/UniquePageName'] = 'kwt_lprotationnew';
			$PagePresets['KeywordTracker/LandingPageRotationNew/Heading'] = 'Create New Landing Page Rotation';
			$PagePresets['KeywordTracker/LandingPageRotationNew/SubHeading'] = 'Split test different landing pages, or variations of the same LP, to find out which one performs best.';
			
			$PagePresets['KeywordTracker/LandingPageRotationEdit/UniquePageName'] = 'kwt_lprotationedit';
			$PagePresets['KeywordTracker/LandingPageRotationEdit/Heading'] = 'Edit Existing Landing Page Rotation';
			$PagePresets['KeywordTracker/LandingPageRotationEdit/SubHeading'] = 'Here, you can edit a landing page rotator you\'ve added previously.</p><p><a href="/BevoMedia/KeywordTracker/LandingPageRotationSetup.html">Back to all LP Rotators</a>';
			
			$PagePresets['KeywordTracker/OfferRotationSetup/UniquePageName'] = 'kwt_offerrotationsetup';
			$PagePresets['KeywordTracker/OfferRotationSetup/Heading'] = 'Offer Rotators';
			$PagePresets['KeywordTracker/OfferRotationSetup/SubHeading'] = 'On this page, you can see your the offer rotators you\'ve set up previously.';
			
			$PagePresets['KeywordTracker/OfferRotationNew/UniquePageName'] = 'kwt_offerrotationnew';
			$PagePresets['KeywordTracker/OfferRotationNew/Heading'] = 'Create New Offer Rotation';
			$PagePresets['KeywordTracker/OfferRotationNew/SubHeading'] = 'Split test different offers, or the same offers from different networks, to find out which one performs best.';
			
			$PagePresets['KeywordTracker/OfferRotationEdit/UniquePageName'] = 'kwt_offerrotationedit';
			$PagePresets['KeywordTracker/OfferRotationEdit/Heading'] = 'Edit Existing Offer Rotation';
			$PagePresets['KeywordTracker/OfferRotationEdit/SubHeading'] = 'Here, you can edit an offer rotator you\'ve added previously.</p><p><a href="/BevoMedia/KeywordTracker/OfferRotationSetup.html">Back to all Offer Rotators</a>';
			
			
			$PagePresets['KeywordTracker/AdjustMediaBuyPrice/UniquePageName'] = 'kwt_adjustmediabuyprice';
			$PagePresets['KeywordTracker/AdjustMediaBuyPrice/Heading'] = 'Adjust Media Buy Bid Prices';
			$PagePresets['KeywordTracker/AdjustMediaBuyPrice/SubHeading'] = 'Use the following tool to adjust the bid prices of you media campaign. Your bid adjustments will be seen in your tracking reports.';
			
			$PagePresets['KeywordTracker/SubIDLookup/UniquePageName'] = 'kwt_subidlookup';
			$PagePresets['KeywordTracker/SubIDLookup/Heading'] = 'SubID Lookup';
			$PagePresets['KeywordTracker/SubIDLookup/SubHeading'] = 'Need to find more information about a click that you can\'t find in the Visitor Spy? Type in the SubID of the click on this page and see more information about the visitor.';
			
			$PagePresets['KeywordTracker/ManuallyUploadSubIDs/UniquePageName'] = 'kwt_manuallyuploadsubids';//same for this group
			$PagePresets['KeywordTracker/ManuallyUploadSubIDs/Heading'] = 'Manually Upload SubIDs with Conversions: Step 1';
			$PagePresets['KeywordTracker/ManuallyUploadSubIDs/SubHeading'] = 'On this page, you can manually upload your converted SubIDs to track conversions. Copy and paste your subids that generated conversions into the field below. <b>IMPORTANT:</b> Do not upload all of your subids, be sure to only upload your <b>CONVERTED</b> subids, i.e. the subids that are converted into a sale or lead.';
			
			$PagePresets['KeywordTracker/ManuallyUploadSubIDsAssign/UniquePageName'] = 'kwt_manuallyuploadsubids';
			$PagePresets['KeywordTracker/ManuallyUploadSubIDsAssign/Heading'] = 'Step 2: Assign Conversions';
			$PagePresets['KeywordTracker/ManuallyUploadSubIDsAssign/SubHeading'] = 'Enter how many conversions did a Subid produced, and what was the revenue generated from this subid was.';
			
			$PagePresets['KeywordTracker/ManuallyUploadSubIDsComplete/UniquePageName'] = 'kwt_manuallyuploadsubids';
			$PagePresets['KeywordTracker/ManuallyUploadSubIDsComplete/Heading'] = 'Step 3: Success!';
			$PagePresets['KeywordTracker/ManuallyUploadSubIDsComplete/SubHeading'] = '<a href="/BevoMedia/KeywordTracker/SubIDLookup.html">Your reports</a> should reflect the conversions in several minutes.';
				
		$PagePresets['KeywordTracker/Geoparting/HeadingImage'] = 'pagedesc_geo.png';
			$PagePresets['KeywordTracker/Geoparting/UniquePageName'] = 'geop';
			$PagePresets['KeywordTracker/Geoparting/Heading'] = 'Geoparting';
			$PagePresets['KeywordTracker/Geoparting/SubHeading'] = 'See the locations of your clicks and conversions. Break down each click all the way down to the city level! View your clicks on the map below. This is a feature for verified Bevo members.';
			
		//Research
		$PagePresets['PPVTools/*/Area'] = 'PPVTools';
		$PagePresets['PPVTools/*/HeadingImage'] = 'pagedesc_ppv.png';
		
			$PagePresets['PPVTools/Tools/UniquePageName'] = 'ppv_tools';
			$PagePresets['PPVTools/Tools/Heading'] = 'Premium Research Tools';
			$PagePresets['PPVTools/Tools/SubHeading'] = 'Whether you have a search, PPV, or media buy campaign, the Bevo Premium Research Tools features the most valuable, time saving internet marketing tools in the market. Take advantage of these tools today and get a leg up on your competition. <strong>Exclusively for Premium Members</strong>.</p><p>Please select which research feature you would like to use from the above menu.';
			
			$PagePresets['PPVTools/PageSniper/UniquePageName'] = 'ppv_pagesniper';
			$PagePresets['PPVTools/PageSniper/Heading'] = 'Premium Research Tools: Page Sniper';
			$PagePresets['PPVTools/PageSniper/SubHeading'] = 'Whether you have a search, PPV, or media buy campaign, the Bevo Premium Research Tools features the most valuable, time saving internet marketing tools in the market. Take advantage of these tools today and get a leg up on your competition. <strong>Exclusively for Premium Members</strong>.</p><p>Use the Page Sniper to retrieve the top ranking websites in search engines by keyword.';
			
			$PagePresets['PPVTools/Extractor/UniquePageName'] = 'ppv_extractor';
			$PagePresets['PPVTools/Extractor/Heading'] = 'Premium Research Tools: URL Extractor';
			$PagePresets['PPVTools/Extractor/SubHeading'] = 'Use the URL Extractor to extract links from any website or page.';
			
			$PagePresets['PPVTools/Alexa/UniquePageName'] = 'ppv_alexa';
			$PagePresets['PPVTools/Alexa/Heading'] = 'Premium Research Tools: Alexa Search Ranking';
			$PagePresets['PPVTools/Alexa/SubHeading'] = 'Use our Alexa Tool to gather the top-ranking websites on <a href="http://www.alexa.com/" target="_blank">Alexa.com</a> by keyword.';
			
			$PagePresets['PPVTools/PageSearchKeyword/UniquePageName'] = 'ppv_pagesearchkeyword';
			$PagePresets['PPVTools/PageSearchKeyword/Heading'] = 'Premium Research Tools: Get Suggested Keywords';
			$PagePresets['PPVTools/PageSearchKeyword/SubHeading'] = 'Enter multiple keywords and receive one-, two-, and three-word keyword suggestions.';
			
			$PagePresets['PPVTools/SiteKeywords/UniquePageName'] = 'ppv_sitekeywords';
			$PagePresets['PPVTools/SiteKeywords/Heading'] = 'Premium Research Tools: Get Site Keywords';
			$PagePresets['PPVTools/SiteKeywords/SubHeading'] = 'Enter multiple URLs and receive one-, two-, and three-word keyword suggestions.';
			
			$PagePresets['PPVTools/WebSiteSpy/UniquePageName'] = 'ppv_websitespy';
			$PagePresets['PPVTools/WebSiteSpy/Heading'] = 'Premium Research Tools: Website Spy';
			$PagePresets['PPVTools/WebSiteSpy/SubHeading'] = 'Enter an URL and receive related campaign information including average budget, clicks, ad position, CPC and site rating.';
			
			$PagePresets['PPVTools/KeywordSpy/UniquePageName'] = 'ppv_keywordspy';
			$PagePresets['PPVTools/KeywordSpy/Heading'] = 'Premium Research Tools: Keyword Spy';
			$PagePresets['PPVTools/KeywordSpy/SubHeading'] = 'Enter a keyword and receive related campaign information including average CPC, search volume, search results, popularity and competition.';
			
			$PagePresets['PPVTools/KeywordComparator/UniquePageName'] = 'ppv_keywordcomp';
			$PagePresets['PPVTools/KeywordComparator/Heading'] = 'Premium Research Tools: Keyword Comparator';
			$PagePresets['PPVTools/KeywordComparator/SubHeading'] = ' ';
			
			$PagePresets['PPVTools/LinkBuilder/UniquePageName'] = 'ppv_linkbuilder';
			$PagePresets['PPVTools/LinkBuilder/Heading'] = 'Premium Research Tools: The List Builder';
			$PagePresets['PPVTools/LinkBuilder/SubHeading'] = 'The List Builder Tool allows users to wrap keywords, extract the list into a CSV file, add states, countries, common first names and more common keywords.';

		
		//Geotargeting
		$PagePresets['Geotargeting/*/Area'] = 'Geotargeting';
		$PagePresets['Geotargeting/*/HeadingImage'] = 'pagedesc_geo.png';
		
			$PagePresets['Geotargeting/Index/UniquePageName'] = 'geot_exist';
			$PagePresets['Geotargeting/Index/Heading'] = 'Existing Geotargets';
			$PagePresets['Geotargeting/Index/SubHeading'] = 'Geotarget your landing pages and offers. This feature allows you to show different pages based on where the viewer is coming from in the world. Target by any city, state, or country in the world!<br/><br/>Need help with this feature? <a href="/BevoMedia/Publisher/PPCTutorialsKWTracker.html#HowToUseGeotargeting">Click here</a> for a video tutorial.';
			
			$PagePresets['Geotargeting/EditGeotarget/UniquePageName'] = 'geot_edit';
			$PagePresets['Geotargeting/EditGeotarget/Heading'] = 'Edit Geotarget';
			$PagePresets['Geotargeting/EditGeotarget/SubHeading'] = 'Geotarget your landing pages and offers. This feature allows you to show different pages based on where the viewer is coming from in the world. Target by any city, state, or country in the world!<br/><br/>Need help with this feature? <a href="/BevoMedia/Publisher/PPCTutorialsKWTracker.html#HowToUseGeotargeting">Click here</a> for a video tutorial.';
			
			$PagePresets['Geotargeting/NewGeotarget/UniquePageName'] = 'geot_new';
			$PagePresets['Geotargeting/NewGeotarget/Heading'] = 'New Geotarget';
			$PagePresets['Geotargeting/NewGeotarget/SubHeading'] = 'Geotarget your landing pages and offers. This feature allows you to show different pages based on where the viewer is coming from in the world. Target by any city, state, or country in the world!<br/><br/>Need help with this feature? <a href="/BevoMedia/Publisher/PPCTutorialsKWTracker.html#HowToUseGeotargeting">Click here</a> for a video tutorial.';
			
		
		//Timetargeting
		$PagePresets['Timetargeting/*/Area'] = 'Timetargeting';
		$PagePresets['Timetargeting/*/HeadingImage'] = 'pagedesc_dayparting.png';
		
			$PagePresets['Timetargeting/Index/UniquePageName'] = 'Timet_exist';
			$PagePresets['Timetargeting/Index/Heading'] = 'Existing Daytargets';
			$PagePresets['Timetargeting/Index/SubHeading'] = 'Target your landing pages and offers by both day and hour! This feature allows you to see the time period your campaign converts the best at, and allows you to optimize and target accordingly.';
			
			$PagePresets['Timetargeting/EditTimetarget/UniquePageName'] = 'Timet_edit';
			$PagePresets['Timetargeting/EditTimetarget/Heading'] = 'Edit Daytarget';
			$PagePresets['Timetargeting/EditTimetarget/SubHeading'] = 'Target your landing pages and offers by both day and hour! This feature allows you to see the time period your campaign converts the best at, and allows you to optimize and target accordingly.';
			
			$PagePresets['Timetargeting/NewTimetarget/UniquePageName'] = 'Timet_new';
			$PagePresets['Timetargeting/NewTimetarget/Heading'] = 'New Daytarget';
			$PagePresets['Timetargeting/NewTimetarget/SubHeading'] = 'Target your landing pages and offers by both day and hour! This feature allows you to see the time period your campaign converts the best at, and allows you to optimize and target accordingly.';
			
		//Tools
			//api call history
			$PagePresets['User/ApiCalls/UniquePageName'] = 'user_apicalls';
			$PagePresets['User/ApiCalls/HeadingImage'] = 'pagedesc_tools.png';
			$PagePresets['User/ApiCalls/Heading'] = 'API Call History';
			$PagePresets['User/ApiCalls/SubHeading'] = 'View a history and breakdown of exactly when your Bevo account made API calls to update your stats.</p><p>We are currently adjusting the number of API calls used across the site. Your usage may drastically increase or decrease on a daily basis while we tweak these values.';
			
			$PagePresets['User/ManageStats/UniquePageName'] = 'user_managestats';
			$PagePresets['User/ManageStats/HeadingImage'] = 'pagedesc_tools.png';
			$PagePresets['User/ManageStats/Heading'] = 'Manage/Delete Your Stats';
			$PagePresets['User/ManageStats/SubHeading'] = 'Get rid of old ballast and clean up your ould and outdated statistics on this page. It\'s helpful to delete old stats when you\'ve already analyzed everything you could possibly analyze in an old campaign, and they would only clutter up the interface.';
			
		
		//Admin
		$PagePresets['Admin/*/Heading'] = 'Admin';
		
		$PagePresets['Admin/Publishers/SubHeading'] = 
		$PagePresets['Admin/SearchPublishers/SubHeading'] = 
		$PagePresets['Admin/NewApplications/SubHeading'] = 
		$PagePresets['Admin/ViewPublisher/SubHeading'] = 
		$PagePresets['Admin/ViewNotes/SubHeading'] = 
		$PagePresets['Admin/AllPublishers/SubHeading'] = 
		$PagePresets['Admin/DeletedPublishers/SubHeading'] = 
		$PagePresets['Admin/EmailPublishers/SubHeading'] = 
		$PagePresets['Admin/Tickets/SubHeading'] = 
		$PagePresets['Admin/APIUsage/SubHeading'] = 
		$PagePresets['Admin/APIUsageDetails/SubHeading'] = 
		$PagePresets['Admin/PublisherPPCAccounts/SubHeading'] = 
		$PagePresets['Admin/PublisherStats/SubHeading'] = 
		$PagePresets['Admin/PublisherStatsCollapse/SubHeading'] = 
		$PagePresets['Admin/PublisherStatsDetail/SubHeading'] = 
		$PagePresets['Admin/AddDemoData/SubHeading'] = 
		$PagePresets['Admin/EditPublisher/SubHeading'] = 'Publishers';
		
		$PagePresets['Admin/Mentors/SubHeading'] = 'Mentors';
		
		$PagePresets['Admin/NetworkStatsAverages/SubHeading'] =
		$PagePresets['Admin/NetworkStatsCollapse/SubHeading'] =
		$PagePresets['Admin/AffiliateNetworks/SubHeading'] =
		$PagePresets['Admin/AffiliateNetworkUsers/SubHeading'] =
		$PagePresets['Admin/NetworkStats/SubHeading'] = 'Networks';
		
		$PagePresets['Admin/*/Area'] = 'Index';
		$PagePresets['Admin/Tickets/Area'] = 
		$PagePresets['Admin/APIUsage/Area'] = 
		$PagePresets['Admin/ViewNotes/Area'] =
		$PagePresets['Admin/Publishers/Area'] = 
		$PagePresets['Admin/ViewPublisher/Area'] =
		$PagePresets['Admin/AllPublishers/Area'] = 
		$PagePresets['Admin/PublisherStats/Area'] = 
		$PagePresets['Admin/NewApplications/Area'] = 
		$PagePresets['Admin/APIUsageDetails/Area'] = 
		$PagePresets['Admin/EmailPublishers/Area'] = 
		$PagePresets['Admin/SearchPublishers/Area'] = 
		$PagePresets['Admin/DeletedPublishers/Area'] = 
		$PagePresets['Admin/PublisherPPCAccounts/Area'] = 
		$PagePresets['Admin/PublisherStatsDetail/Area'] = 
		$PagePresets['Admin/PublisherStatsCollapse/Area'] = 
		$PagePresets['Admin/AddDemoData/Area'] = 
		$PagePresets['Admin/EditPublisher/Area'] = 'Publishers';
		
		$PagePresets['Admin/Mentors/Area'] = 'Mentors';
		
		$PagePresets['Admin/NetworkStatsAverages/Area'] =
		$PagePresets['Admin/NetworkStatsCollapse/Area'] =
		$PagePresets['Admin/AffiliateNetworks/Area'] =
		$PagePresets['Admin/AffiliateNetworkUsers/Area'] =
		$PagePresets['Admin/NetworkStats/Area'] = 'Networks';
		
		$PagePresets['Admin/Queue/Area'] =
		$PagePresets['Admin/Crons/Area'] = 
		$PagePresets['Admin/Queue/Heading'] =
		$PagePresets['Admin/Crons/Heading'] = 'Crons/Queue';
		$PagePresets['Admin/Queue/SubHeading'] = 'Queue Status';
		$PagePresets['Admin/Crons/SubHeading'] = 'Crons Status';
		
		$PagePresets['Admin/SelfHostedPublishers/Area'] =
		$PagePresets['Admin/SelfHostedAPIUse/Area'] =
		$PagePresets['Admin/SelfHostedAPIUse/Heading'] = 
		$PagePresets['Admin/SelfHostedPublishers/Heading'] = 'Self Hosted';
		$PagePresets['Admin/SelfHostedPublishers/SubHeading'] = 'Browse Self Hosted Publishers';
		
		
		$PagePresets['Admin/Settings/Heading'] = 'Settings';
		$PagePresets['Admin/Settings/SubHeading'] = 'Modify Settings';
		$PagePresets['Admin/Settings/Area'] = 'Settings';
		
		
		$QuickArr = array('Heading', 'SubHeading', 'HeadingImage', 'Area', 'UniquePageName');
		foreach($QuickArr as $Q)
		{
			$this->{$Q} = '';
			if(isset($PagePresets[$this->Controller.'/*/'.$Q]))
				$this->{$Q} = $PagePresets[$this->Controller.'/*/'.$Q];
				
			if(isset($PagePresets[$this->Controller.'/'.$this->Function.'/'.$Q]))
				$this->{$Q} = $PagePresets[$this->Controller.'/'.$this->Function.'/'.$Q];
		}
	}
	
	/**
	 * Wrapper function for URLEncode.
	 *
	 * @param String $URL
	 * @return String
	 */
	Public Function URLEncode($URL)
	{
		return urlencode($URL);
	}
	
	/**
	 * Wrapper function for URLDecode.
	 *
	 * @param String $URL
	 * @return String
	 */
	Public Function URLDecode($URL)
	{
		return urldecode($URL);
	}
	
	/**
	 * Remove the specified Key ($Element) from the specified $Array.
	 * $Array is passed by reference.
	 * 
	 * @param Array $Array
	 * @param Mixed $Element
	 */
	Public Static Function RemoveArrayKey(&$Array, $Element)
	{
		$Output = array();
		foreach($Array as $Key=>$Value)
		{
			if($Element !== $Key)
				$Output[$Key] = $Value;
		}
		$Array = $Output;
	}
	
	/**
	 * Remove the specified Value ($Element) from the specified $Array.
	 * $Array is passed by reference.
	 * 
	 * @param Array $Array
	 * @param Mixed $Element
	 */
	Public Static Function RemoveArrayElement(&$Array, $Element)
	{
		$Output = array();
		foreach($Array as $Key=>$Value)
		{
			if($Element !== $Value)
				$Output[$Key] = $Value;
		}
		$Array = $Output;
	}
	
	/**
	 * Removes the specified $Elements from the specified $Array.
	 * $Array is passed by reference.
	 *
	 * @param Array $Array
	 * @param Array $Elements
	 * @see RemoveArrayElement()
	 */
	Public Static Function RemoveArrayElements(&$Array, $Elements)
	{
		foreach($Elements as $Element)
		{
			PageHelper::RemoveArrayElement($Array, $Element);
		}
	}
	
	/**
	 * Returns an array of common instant messenger protocols.
	 *
	 * @todo Add sample output.
	 * @return Array
	 */
	Public Function GetMessengers()
	{
		return array('AIM', 'YAHOO_MESSENGER', 'MSN_MESSENGER', 'GTALK');
	}
	
	/**
	 * Returns an associative array of common instant messenger protocols where the Key is an all caps non-spaced identifier and the Value is capitalized and contains spaces.
	 *
	 * @todo Add sample output.
	 * @return Array
	 */
	Public Static Function GetMessengersViewLabels()
	{
		return array('AIM'=>'AIM', 'YAHOO_MESSENGER'=>'Yahoo Messenger', 'MSN_MESSENGER'=>'MSN Messenger', 'GTALK'=>'Gtalk');
	}
	
	/**
	 * Returns an associative array of the marketing methods where the Key is an all caps identifier and the Value is capitalized and may contain symbols.
	 *
	 * @todo Add sample output.
	 * @return Array
	 */
	Public Static Function GetMarketingMethodsViewLabels()
	{
		return array('EMAIL'=>'Email', 'KEYWORD'=>'Keyword', 'WEB'=>'Web/Seo', 'OTHER'=>'Other');
	}
	
	/**
	 * Return an array of marketing methods.
	 *
	 * @todo Add sample output.
	 * @return Array
	 */
	Public Function GetMarketingMethods()
	{
		return array('EMAIL', 'KEYWORD', 'WEB', 'OTHER');
	}
	
	/**
	 * Truncate a string to a certain length of characters and append an extension like "..." if it was actually truncated, else return untouched
	 *
	 * @param $str string that has to be truncated
	 * @param $length int length of characters before $str gets cut off
	 * @param $ext string extension that will be appended if $str gets cut off
	 * @return string
	 */
	 Public Function TruncTxt($str='', $length=20, $ext='...') {
		if($str != '') {
			$out = substr($str, 0, $length);
			if(strlen($out) < strlen($str))
				$out .= $ext;
			
			return $out;
		}
	}//TruncTxt()
	
	/** FixNetworkRating
	  * fixes the rating if necessary, else returns the existing value.
	  * @param $networkTitle string the title of the network, ususally $networkobject->title
	  * @param $rating int usually $networkobject->rating
	  * @return int the new (or existing) rating
	  */
	Public Function FixNetworkRating($networkTitle=false, $rating=false) {
		
		switch($networkTitle) {
			case 'EpicDirect': $out = 5; break;
			case 'Copeac': $out = 3; break;
			case 'Affiliate.com': $out = 3; break;
			case 'EWA': $out = 5; break;
			case 'FireLead': $out = 4; break;
			case 'ClickBooth': $out = 4; break;
			case 'ProfitKingsMedia': $out = 3; break;
			case 'W4': $out = 4; break;
			case 'XY7': $out = 2; break;
			case 'CPAProsperity': $out = 4; break;
			default: $out = $rating;
		}
		
		return $out;
		
	}//FixNetworkRating()
}


?>
