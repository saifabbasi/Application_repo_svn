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
				$_SESSION['loginLocation'] = $_SERVER['REQUEST_URI'];
				header('Location: /BevoMedia/Index/');
				die;
			}
			
			$user = new User();
			$user->getInfo($_SESSION['User']['ID']);
			$this->{'User'} = $user;
			Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
			
			$this->db = Zend_Registry::get('Instance/DatabaseObj');
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
						  Last Name: {$this->User->lastName}<br /><br />	
						  
						  
						  Best Match: {$NetworkName}<br /><br />
						  
						  Offer Name: {$_POST['SuggestedOffer']}<br />
						  Niche: {$_POST['NicheName']} <br />
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
		
	}

?>