<?php
	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
	
	Class MarketplaceController extends ClassComponent
	{
		Public $GUID		= NULL;
		
		Public Function __construct()
		{
			parent::GenerateGUID();
			$this->{'PageHelper'} = new PageHelper();
			$this->{'PageDesc'} = new PageDesc();
			$this->db = Zend_Registry::get('Instance/DatabaseObj');
			$useApiKey = false;
			if(isset($_GET['apiKey']))
			{
			    $useApiKey = true;
                $user = new User();
                $userId = $user->getUserIdByAPIKey($_GET['apiKey']);
                $user->getInfo(intval($userId));
                if(empty($userId))
                {
                    echo '<div style="background: #fff; color: #F00;">';
                    echo '<br /><br />';
                    echo "This service requires a BevoMedia.com account; you haven't setup a BevoMedia.com account to sync with this selfhosted account.";
                    echo '<br /><br />';
                    echo '</div>';
                    exit;
                }
                $this->User = $user;
                $_SESSION['User']['ID'] = $user->id;
			}
			
			if(!$useApiKey && (!isset($_SESSION['User']) || !intval($_SESSION['User']['ID'])))
			{
				$_SESSION['loginLocation'] = $_SERVER['REQUEST_URI'];
				header('Location: /BevoMedia/Index/');
				exit;
			}
            if(!$useApiKey)
            {
			    $user = new User();
			    $user->getInfo($_SESSION['User']['ID']);
			    $this->{'User'} = $user;
            }
            Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
		}
		
		Public Function MentorshipProgram()
		{
		}
		
		Public Function PremiumSignup()
		{
		
		}
		
		Public Function PremiumReview()
		{
			
		}
		
		Public Function JobRequest()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		}
		
		Public Function AcceptQuote()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			if(isset($_GET['id']))
			{
				$this->db->update('bevomedia_marketplace', array('status' => 'accepted'), 'id='.intval($_GET['id']));
			}
		}
		
		
		Public Function JobSubmit()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$inserts = array();
			foreach(array('user__id', 'contactName', 'contactEmail', 'contactPhone', 'projectName', 'projectType', 'description') as $k)
				$inserts[$k] = @$_POST[$k] ? $_POST[$k] : '';
			$inserts['status'] = 'pendingApproval';
			$this->db->insert('bevomedia_marketplace', $inserts);
			echo 'Your job request has been submitted. A Bevo employee will review your request and quote a price, usually within 24 hours. You will recieve an email when the quote is available.';
			$MailComponentObject = new MailComponent();
			$MailComponentObject->setFrom('no-reply@bevomedia.com');
        	
			$EmailContent = "Marketplace Job Request:<br>\r\n
			<br>\r\n
			Name: {$inserts[contactName]}<br >\r\n
			Phone: {$inserts[contactPhone]}<br>\r\n
			Email: {$inserts[contactEmail]}<br>\r\n
			Project Type: {$inserts[projectType]}<br>\r\n
			Project Name: {$inserts[projectName]}<br>\r\n
			Description: {$inserts[description]}<br>\r\n
			";
			
			$MailComponentObject->setSubject('Marketplace Job Request');
			$MailComponentObject->setHTML($EmailContent);
			$MailComponentObject->send(array('marketplace@bevomedia.com'));
		}
		
		Public Function MarketplacePayment()
		{
			if(!intval(@$_GET['id']))
				die('fatal error, no id specified');
				
			$BaseURL = Zend_Registry::get('System/BaseURL');
			
			$job = $this->db->fetchRow('select * from bevomedia_marketplace where id='.intval($_GET['id']));
			// Include the paypal library
			include_once (PATH . 'Paypal.class.php');
			
			// Create an instance of the paypal library
			$myPaypal = new Paypal();
			
			$myPaypal->addField('cmd', '_xclick');
			
			// Specify your paypal email
			$myPaypal->addField('business', 'payments@bevomedia.com');
			
			// Specify the currency
			$myPaypal->addField('currency_code', 'USD');
			
			// Specify the url where paypal will send the user on success/failure
			$myPaypal->addField('return', $BaseURL . 'BevoMedia/Marketplace/MarketplacePaypalSuccess.html');
			$myPaypal->addField('cancel_return', $BaseURL . 'BevoMedia/Marketplace/MarketplacePaypalFailure.html');
			
			// Specify the url where paypal will send the IPN
			$myPaypal->addField('notify_url', $BaseURL . 'BevoMedia/API/PaypalPostback.html');
			
			// Specify the product information
			$myPaypal->addField('item_name', 'Bevo Marketplace: ' . $job->projectName);
			
			// Transaction name
			$myPaypal->addField('bn', 'BevoMedia_Subscribe_Premium_US');
			
			// no shipping address
			$myPaypal->addField('no_shipping', '1');
			
			// marketplace price
			$myPaypal->addField('amount', number_format($job->quotedPrice, 2));

			// No note [required for subscribe]
			$myPaypal->addField('no_note', '1');
			
			// Specify any custom value
			$myPaypal->addField('custom', $job->id);
			//print $ID;
			
			// Enable test mode if needed
			//$myPaypal->enableTestMode();
			
			// Let's start the train!
			$this->content = $myPaypal->submitPayment();

		}
		Public Function PremiumSubmit()
		{
		  $BaseURL = Zend_Registry::get('System/BaseURL');
		  $cost = $this->db->fetchOne('select value from bevomedia_settings where name="Premium_Cost"');
			$PremiumOrder = new PremiumOrder();
			$ID = $PremiumOrder->Insert($_POST);
			// Include the paypal library
			include_once (PATH . 'Paypal.class.php');
			
			// Create an instance of the paypal library
			$myPaypal = new Paypal();
			
			$myPaypal->addField('cmd', '_xclick-subscriptions');
			
			// Specify your paypal email
			$myPaypal->addField('business', 'payments@bevomedia.com');
			
			// Specify the currency
			$myPaypal->addField('currency_code', 'USD');
			
			// Specify the url where paypal will send the user on success/failure
			$myPaypal->addField('return', $BaseURL . 'BevoMedia/Marketplace/PremiumPaypalSuccess.html');
			$myPaypal->addField('cancel_return', $BaseURL . 'BevoMedia/Marketplace/PremiumPaypalFailure.html');
			
			// Specify the url where paypal will send the IPN
			$myPaypal->addField('notify_url', $BaseURL . 'BevoMedia/API/PaypalPostback.html');
			
			// Specify the product information
			$myPaypal->addField('item_name', 'Bevo Premium');
			
			// Transaction name
			$myPaypal->addField('bn', 'BevoMedia_Subscribe_Premium_US');
			
			// no shipping address
			$myPaypal->addField('no_shipping', '1');
			
			// $200  per month
			$myPaypal->addField('a3', $cost);
			$myPaypal->addField('p3', '1');
			$myPaypal->addField('t3', 'M');
			// Recurring
			$myPaypal->addField('src', '1');
			// No note [required for subscribe]
			$myPaypal->addField('no_note', '1');
			
			// Specify any custom value
			$myPaypal->addField('custom', $ID);
			//print $ID;
			
			// Enable test mode if needed
			//$myPaypal->enableTestMode();
			
			// Let's start the train!
			$this->content = $myPaypal->submitPayment();

		}
		
		
	}
?>