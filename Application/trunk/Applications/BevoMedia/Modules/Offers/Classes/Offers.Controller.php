<?php

	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
	
	Class OffersController extends ClassComponent
	{
		Public $GUID		= NULL;
		
		Public Function __construct()
		{
			parent::GenerateGUID();
			$this->{'PageHelper'} = new PageHelper();
			$this->{'PageDesc'} = new PageDesc();
			
			if(!isset($_SESSION['User']) || !intval($_SESSION['User']['ID']))
			{
				if (Zend_Registry::get('Instance/Function')=='OfferLogin')
				{
					return;					
				}
				
				$_SESSION['OfferHubRedirectPage'] = Zend_Registry::get('Instance/Function');
				$_SESSION['loginLocation'] = $_SERVER['REQUEST_URI'];
				header('Location: /BevoMedia/Index/Index.html?OfferLogin');
				die;
			}
			
			$user = new User();
			$user->getInfo($_SESSION['User']['ID']);
			$this->{'User'} = $user;
			Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
			
			$this->db = Zend_Registry::get('Instance/DatabaseObj');
			
			if (isset($_COOKIE['v3apps'])) {
				Zend_Registry::set('Instance/LayoutType', 'apps-layout-v2');
			}
		}
		
		Public Function _SetPageSize()
		{
			if(isset($_GET['size']))
			{
				$_SESSION['pageSize'] = $_GET['size'];
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			exit;
		}
		
		Public Function NameYourPayout()
		{
			
			
			$Sql = "SELECT
						bevomedia_name_your_price_niche.*
					FROM
						bevomedia_name_your_price_niche
					ORDER BY
						bevomedia_name_your_price_niche.Name			
					";
			$this->Niches = $this->db->fetchAll($Sql);
			
		}
		
		Public Function NameYourPayoutResult()
		{
			if (isset($_POST['SubmitAjax'])) {
				
				sleep(4);
				
				$Sql = "SELECT
							bevomedia_name_your_price.NetworkID,
							bevomedia_name_your_price_networks.NetworkName
						FROM
							bevomedia_name_your_price,
							bevomedia_name_your_price_networks
						WHERE
							(bevomedia_name_your_price_networks.ID = bevomedia_name_your_price.NetworkID) AND
							(bevomedia_name_your_price.UserID = ?) AND
							(bevomedia_name_your_price.NicheID = ?) AND
							(bevomedia_name_your_price.DesiredBidPayout = ?) AND
							(bevomedia_name_your_price.DesiredEPC = ?) AND
							(bevomedia_name_your_price.TrafficSource = ?) 
						";
				
				$Check = $this->db->fetchRow($Sql, array($this->User->id, $_POST['Niche'], $_POST['DesiredBidPayout'], $_POST['DesiredEPC'], $_POST['TrafficSource']));
				if (isset($Check->NetworkID)) {
					
					$NetworkID = $Check->NetworkID;
					$NetworkName = $Check->NetworkName;
					
				} else {				
				
					$Sql = "SELECT
								bevomedia_name_your_price_niche.ID,
								bevomedia_name_your_price_networks.ID as `NetworkID`,
								bevomedia_name_your_price_rates.Rate,
								bevomedia_name_your_price_networks.NetworkName
							FROM
								bevomedia_name_your_price_niche
							LEFT JOIN
								bevomedia_name_your_price_rates ON (bevomedia_name_your_price_rates.NicheID = bevomedia_name_your_price_niche.ID) 
							LEFT JOIN
								bevomedia_name_your_price_networks ON (bevomedia_name_your_price_rates.NetworkID = bevomedia_name_your_price_networks.ID) 
							WHERE
								(bevomedia_name_your_price_niche.ID = ?)
							";
					$Results = $this->db->fetchAll($Sql, $_POST['Niche']);
					
					$Array = array();
					
					foreach ($Results as $Row) 
					{
						for ($i=0; $i<intval($Row->Rate); $i++) 
						{
							$Array[] = $Row;
						}
					}
					
					$ID = rand(0, count($Array)-1);
					
					$BestMatch = $Array[$ID];
					
//					echo '<pre>';
//					print_r($BestMatch);
//					print_r($_POST);
//					die;

					$Sql = "SELECT
								Name
							FROM
								bevomedia_name_your_price_niche
							WHERE
								(ID = ?)					
							";
					$NicheName = $this->db->fetchRow($Sql, $BestMatch->ID);
					$NicheName = $NicheName->Name;
					
					
					$NetworkID = $BestMatch->NetworkID;
					$NetworkName = $BestMatch->NetworkName;
					
					$InsertArray = array(
											'UserID' => $this->User->id,
											'NicheID' => $BestMatch->ID,
											'NetworkID' => $BestMatch->NetworkID,
											'CurrentPayout' => $_POST['CurrentPayout'],
											'DesiredBidPayout' => $_POST['DesiredBidPayout'],
											'CurrentEPC' => $_POST['CurrentEPC'],
											'DesiredEPC' => $_POST['DesiredEPC'],
											'TrafficSource' => $_POST['TrafficSource'],
										);
					$this->db->insert('bevomedia_name_your_price', $InsertArray);
					
				}
				
				$Body = "First Name: {$this->User->firstName}<br />
						  Last Name: {$this->User->lastName}<br />
						  E-mail: {$this->User->email}<br /><br />	
						  
						  
						  Best Match: {$NetworkName}<br /><br />
						  
						  Offer Name: {$_POST['SuggestedOffer']}<br />
						  Niche: {$NicheName} <br />
						  Current Payout: {$_POST['CurrentPayout']}<br />
						  Desired Bid Payout: {$_POST['DesiredBidPayout']}<br />
						  Current EPC: {$_POST['CurrentEPC']}<br />
						  Desired EPC: {$_POST['DesiredEPC']}<br />
						  Traffic Source: {$_POST['TrafficSource']}<br /><br />
						  						  						  
						";
				
				$Headers  = 'MIME-Version: 1.0' . "\r\n";
				$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				mail('payoutbid@bevomedia.com', 'Payout Bid', $Body, $Headers);

				
				$array = array('ID' => $NetworkID);
				echo json_encode($array);
//				header('Location: /BevoMedia/Offers/NameYourPayoutResult.html?ID='.$NetworkID);				
				die;
			}
			
			
			$Sql = "SELECT
						NetworkName,
						AffNetworkID
					FROM
						bevomedia_name_your_price_networks
					WHERE
						ID = ?			
					";
			$Row = $this->db->fetchRow($Sql, $_GET['ID']);
			$this->NetworkName = $Row->NetworkName;
			$this->AffNetworkID = $Row->AffNetworkID; 
		}

		Public Function AskNegotiatePaymentTerms()
		{
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
			
			$OfferID = $_GET['OfferID'];
			
			$Sql = "SELECT
						bevomedia_offers.title,
						bevomedia_offers.offer__id,
						bevomedia_offers.payout,
						bevomedia_aff_network.title as `networkName`
					FROM
						bevomedia_offers,
						bevomedia_aff_network
					WHERE
						(bevomedia_aff_network.id = bevomedia_offers.network__id) AND
						(bevomedia_offers.id = ?)
				";
			$Offer = $this->db->fetchRow($Sql, $OfferID);
			
			$Message = "User ID: {$this->User->id}<br />
						User Email: {$this->User->email}<br />
						Offer Title: {$Offer->title}<br />
						Offer Number: {$Offer->offer__id}<br />
						Offer Payout: {$Offer->payout}<br />
						Network Name: {$Offer->networkName}<br />						
						";
			
			$Headers  = 'MIME-Version: 1.0' . "\r\n";
			$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			mail('negotiation@bevomedia.com', 'Negotiate Payment Terms', $Message, $Headers);
			
			die;
			
		}
		
		Public Function OfferImportFrame()
		{
			$NetworkID = $_GET['network__id'];
			
			$Sql = "SELECT
						bevomedia_user_aff_network.id,
						bevomedia_user_aff_network.loginId as `AffiliateID`,
						bevomedia_user_aff_network.otherId as `APIKey`,
						bevomedia_aff_network.url
					FROM
						bevomedia_aff_network,
						bevomedia_user_aff_network
					WHERE
						(bevomedia_user_aff_network.network__id	= bevomedia_aff_network.id) AND 
						(bevomedia_user_aff_network.user__id = ?) AND
						(bevomedia_user_aff_network.network__id = ?) AND
						(bevomedia_user_aff_network.loginId <> '') AND
						(bevomedia_user_aff_network.otherId <> '') 
					";
			
			$Row = $this->db->fetchRow($Sql, array($_SESSION['User']['ID'], $NetworkID));
			
			if (!isset($Row->id))
			{
				header('Location: /BevoMedia/Publisher/EditNetwork.html?network='.$NetworkID);
				die;
			} else 
			{
				$this->NetworkInfo = $Row;
			}
		}
		
		Public Function OfferLogin()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			if (isset($_POST['Login']))
			{ 
				$User = new User(); 
				$LoginAttempt = $User->login($_POST['Email'], md5($_POST['Password']));
				if($LoginAttempt==true)
				{
					$ID = $User->getIdUsingEmail($_POST['Email']);
					$User->getInfo($ID);
					$_SESSION['User']['ID'] = $User->id;					
					
					echo "<script type='text/javascript'>";
					echo "
							parent.location = '{$_POST['Url']}';
						";
					echo "</script>";
					
					//header('Location: '.$_POST['Url']);
					die;
				} else
				{
					$this->Error = 'You entered wrong credentials.';
				}
			}
		}
	}

?>