<?php
/**
 * Networks Controller
 */

/**
 * Networks Controller
 *
 * Controller for pages when user is generally not required to be logged in.
 * This includes pages that allow the user to register an account, retrieve their password or view information about the site.
 *
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2010 RCS
 * @author 		RCS
 * @version 	0.1
 */
require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/lib_nmi/nmiDirectPost.class.php');

Class NetworksController extends ClassComponent
{

	/**
	 * @var Mixed $GUID
	 */
	Public $GUID		= NULL;

	/* @var $_db Zend_Db */
	Protected $_db		= false;



	/**
	 * Constructor
	 */
	Public Function __construct()
	{
		parent::GenerateGUID();

		$this->PageHelper = new PageHelper();

		$page = Zend_Registry::get('Instance/Function');
		$this->{'page'} = $page;

		$this->db = Zend_Registry::get('Instance/DatabaseObj');
	}
	
	Public Function Sign()
	{
		$_SESSION['LowPrices'] = true;
		unset($_SESSION['OneTerm']);
		unset($_SESSION['Custom416']);
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_adwords_countries
				ORDER BY
					(code <> "US"), country
				';
		$this->Countries = $this->db->fetchAll($Sql);
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_state
				ORDER BY
					name
				';
		$this->States = $this->db->fetchAll($Sql);
	}

	Public Function SignUp()
	{
		unset($_SESSION['LowPrices']);
		unset($_SESSION['OneTerm']);
		unset($_SESSION['Custom416']);
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_adwords_countries
				ORDER BY
					(code <> "US"), country
				';
		$this->Countries = $this->db->fetchAll($Sql);
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_state
				ORDER BY
					name
				';
		$this->States = $this->db->fetchAll($Sql);
	}

	Public Function SignUpCustom()
	{
		unset($_SESSION['LowPrices']);
		$_SESSION['OneTerm'] = true;
		$_SESSION['Custom416'] = true;
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_adwords_countries
				ORDER BY
					(code <> "US"), country
				';
		$this->Countries = $this->db->fetchAll($Sql);
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_state
				ORDER BY
					name
				';
		$this->States = $this->db->fetchAll($Sql);
	}
	
	Public Function Register()
	{
		$_SESSION['OneTerm'] = true;
		unset($_SESSION['LowPrices']);
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_adwords_countries
				ORDER BY
					(code <> "US"), country
				';
		$this->Countries = $this->db->fetchAll($Sql);
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_state
				ORDER BY
					name
				';
		$this->States = $this->db->fetchAll($Sql);
	}

	Public Function SignUpProcess()
	{
		$_SESSION['SignUpData'] = $_POST;

		header('Location: /BevoMedia/Networks/NetworkPayment.html');
		die;
	}

	Public Function NetworkPayment()
	{
		if (!isset($_SESSION['SignUpData']))
		{
			header('Location: /');
			die;
		}
		
		$Sql = 'SELECT
					*
				FROM
					bevomedia_adwords_countries
				ORDER BY
					(code <> "US"), country
				';
		$this->Countries = $this->db->fetchAll($Sql);


		$Sql = 'SELECT
					*
				FROM
					bevomedia_state
				ORDER BY
					name
				';
		$this->States = $this->db->fetchAll($Sql);

		
		
		$this->PaymentTerms = $this->GetPaymentPlans();

		if (isset($_POST['Continue']))
		{
			$_SESSION['PaymentData'] = $_POST;
				
			header('Location: /BevoMedia/Networks/PaymentConfirmation.html');
			die;
		}
	}

	Public Function PaymentConfirmation()
	{
		if (!isset($_SESSION['PaymentData']))
		{
			header('Location: /BevoMedia/Networks/NetworkPayment.html');
			die;
		}
		
		$this->PaymentTerm = $this->GetPaymentPlans($_SESSION['PaymentData']['PaymentTerm']);
	}

	Public Function GetPaymentPlans($ID = null, $LowPrices)
	{
		if ($ID!=null)
		{
			$Add = ' AND (bevomedia_network_payment_terms.ID = '.intval($ID).')';
		}
		
		$AddTerms = '';
		if (isset($_SESSION['LowPrices']) && ($_SESSION['LowPrices']==true))
		{
			$AddTerms = " AND (ID BETWEEN 1 AND 5) ";
		} else
		if (isset($_SESSION['Custom416'])) {
		    $AddTerms = " AND (ID = 12) ";
		} else
		if (isset($_SESSION['OneTerm']) && ($_SESSION['OneTerm']==true)) 
		{
			$AddTerms = " AND (ID = 11) ";
		} else
		{
			$AddTerms = " AND (ID BETWEEN 6 AND 10) ";
		}

		$Sql = "SELECT
					*
				FROM
					bevomedia_network_payment_terms
				WHERE
					(1=1) 
					$Add
					$AddTerms
				ORDER BY
					ID
				"; 
					$Results = $this->db->fetchAll($Sql);

					if ( ($ID!=null) && (count($Results)>0) )
					{
						return $Results[0];
					}

					return $Results;
	}

	Public Function ProcessPayment()
	{
		if (isset($_POST['SubmitPayment']))
		{ 
			$TermID = $_SESSION['PaymentData']['PaymentTerm'];
			$ChargeAmount = 0;
				
			if ($TermID==-1)
			{
				$ChargeAmount = floatval($_SESSION['PaymentData']['CustomPrice']);
				
				$TermMonths = 1;
				$Expiring = date('Y-m-d', strtotime('+1 months', time()));
			} else
			{
				$PaymentTerm = $this->GetPaymentPlans($TermID);
				$TermMonths = $PaymentTerm->TermLength;
				$Expiring = date('Y-m-d', strtotime('+'.$TermMonths.' months', time()));
				
				if ($PaymentTerm->OnlyUpfront==1)
				{
					$ChargeAmount = $PaymentTerm->UpfrontPayment;
				} else
				{
					$ChargeAmount = $PaymentTerm->MonthlyPayment;
				}
			}	
			
			
			
				
			$Array = array(
							'NetworkName' => $_SESSION['SignUpData']['NetworkName'],
							'Name' => $_SESSION['SignUpData']['ContactName'],
							'Email' => $_SESSION['SignUpData']['ContactEmail'],
							'Phone' => $_SESSION['SignUpData']['ContactPhone'],
							'Address1' => $_SESSION['SignUpData']['ContactAddress1'],
							'Address2' => $_SESSION['SignUpData']['ContactAddress2'],
							'City' => $_SESSION['SignUpData']['ContactCity'],
							'State' => $_SESSION['SignUpData']['ContactState'],
							'Country' => $_SESSION['SignUpData']['ContactCountry'],
							'ApproximateNumberAffiliates' => $_SESSION['SignUpData']['ApproximateNumberAffiliates'],
							'TrackingPlatform' => $_SESSION['SignUpData']['TrackingPlatform'],
							'Comment' => $_SESSION['SignUpData']['Comment'],
							'PaymentTermID' => $_SESSION['PaymentData']['PaymentTerm'],
							'Expiring' => $Expiring,
			);
			
			if (isset($_SESSION['NetworkContactID']))
			{
				$NetworkContactID = $_SESSION['NetworkContactID'];
			} else 
			{
				$NetworkContactID = $this->AddContactInfo($Array);
				$_SESSION['NetworkContactID'] = $NetworkContactID;
			}
				
			
			
				
			$CCPayment = new nmiDirectPost();

			$CCPayment->setAmount($ChargeAmount);	
			
			if ($TermID!=-1)
			{
				$CCPayment->setProductSku($PaymentTerm->SKU);
				$CCPayment->setOrderDescription($PaymentTerm->SKU);
			}
			
			
			$CCPayment->setCcNumber($_SESSION['PaymentData']['CreditCardNumber']);
			$CCPayment->setCcExp($_SESSION['PaymentData']['ExpirationMonth'].$_SESSION['PaymentData']['ExpirationYeah']);
			$CCPayment->setCvv($_SESSION['PaymentData']['CVV']);
				
			$CCPayment->setCompany($_SESSION['PaymentData']['Company']);
			$CCPayment->setFirstName($_SESSION['PaymentData']['FirstName']);
			$CCPayment->setLastName($_SESSION['PaymentData']['LastName']);
			$CCPayment->setAddress1($_SESSION['PaymentData']['Address1']);
			$CCPayment->setAddress2($_SESSION['PaymentData']['Address2']);
			$CCPayment->setCity($_SESSION['PaymentData']['City']);
			$CCPayment->setZip($_SESSION['PaymentData']['Zip']);
			$CCPayment->setPhone($_SESSION['PaymentData']['Phone']);
			$CCPayment->setEmail($_SESSION['SignUpData']['ContactEmail']);
			
			$CCPayment->setCountry($_SESSION['PaymentData']['Country']);
			
			if ($_SESSION['PaymentData']['State']!=-1)
			{
				$CCPayment->setState($_SESSION['PaymentData']['State']);
			}
			
			$CCPayment->sale();
			
//			$CCPayment->addAndCharge($ChargeAmount);
			
			$Result = $CCPayment->execute();
			
			
			$CustomerVaultID = null;
			$TransactionID = null;
			
			switch($Result['response'])
			{
			    case 1: //Success
//					$CustomerVaultID = $Result['customer_vault_id'];
					$TransactionID = $Result['transactionid'];
					
//					$Array = array(
//									'CustomerVaultID' => $CustomerVaultID,
//								  );
//				    $this->db->update('bevomedia_network_contact_info', $Array, ' ID = '.$NetworkContactID);
					
			        break;
			    default:			    	
			    	header('Location: /BevoMedia/Networks/NetworkPayment.html?Response='.$Result['responsetext']);
			    	die;
			    	break;
			}
			
			
			$Info = array(
							'NetworkContactID'  => $NetworkContactID,
							'TransactionID'		=> $TransactionID,
							'Amount'			=> $ChargeAmount,
						);
			$PaymentID = $this->AddContactPayment($Info);
			
			$Body = "
						<h3>Network Registration Transaction</h3>
						
						<br /><br />
						
							<h3>Contact Info</h3>
	
							<table border='0'>
								<tr>
									<td width='200'>Network Name:</td>
									<td>{$_SESSION['SignUpData']['NetworkName']}</td>
								</tr>
								<tr>
									<td>Name:</td>
									<td>{$_SESSION['SignUpData']['ContactName']}</td>
								</tr>
								<tr>
									<td>E-mail:</td>
									<td>{$_SESSION['SignUpData']['ContactEmail']}</td>
								</tr>
								<tr>
									<td>Phone:</td>
									<td>{$_SESSION['SignUpData']['ContactPhone']}</td>
								</tr>
								<tr>
									<td>Address 1:</td>
									<td>{$_SESSION['SignUpData']['ContactAddress1']}</td>
								</tr>
								<tr>
									<td>Address 2:</td>
									<td>{$_SESSION['SignUpData']['ContactAddress2']}</td>
								</tr>
								<tr>
									<td>City:</td>
									<td>{$_SESSION['SignUpData']['ContactCity']}</td>
								</tr>
								<tr>
									<td>State:</td>
									<td>{$_SESSION['SignUpData']['ContactState']}</td>
								</tr>
								<tr>
									<td>Country:</td>
									<td>{$_SESSION['SignUpData']['ContactCountry']}</td>
								</tr>
								<tr>
									<td>Approximate Number of<br />Affiliates in Network:</td>
									<td>{$_SESSION['SignUpData']['ApproximateNumberAffiliates']}</td>
								</tr>
								<tr>
									<td>Tracking Platform:</td>
									<td>{$_SESSION['SignUpData']['TrackingPlatform']}</td>
								</tr>
								<tr>
									<td>Comment:</td>
									<td>{$_SESSION['SignUpData']['Comment']}</td>
								</tr>
							</table>
							
							<br />
							
							<h3>Payment Info</h3>
						
							<table>
								<tr>
									<td width='200'>Company:</td>
									<td>{$_SESSION['PaymentData']['Company']}</td>
								</tr>
								<tr>
									<td>First Name:</td>
									<td>{$_SESSION['PaymentData']['FirstName']}</td>
								</tr>
								<tr>
									<td>Last Name:</td>
									<td>{$_SESSION['PaymentData']['LastName']}</td>
								</tr>
								<tr>
									<td>Address 1:</td>
									<td>{$_SESSION['PaymentData']['Address1']}</td>
								</tr>
								<tr>
									<td>Address 2:</td>
									<td>{$_SESSION['PaymentData']['Address2']}</td>
								</tr>
								<tr>
									<td>City:</td>
									<td>{$_SESSION['PaymentData']['City']}</td>
								</tr>
								<tr>
									<td>State:</td>
									<td>{$_SESSION['PaymentData']['State']}</td>
								</tr>
								<tr>
									<td>Country:</td>
									<td>{$_SESSION['PaymentData']['Country']}</td>
								</tr>
								<tr>
									<td>Zip:</td>
									<td>{$_SESSION['PaymentData']['Zip']}</td>
								</tr>
								<tr>
									<td>Phone:</td>
									<td>{$_SESSION['PaymentData']['Phone']}</td>
								</tr>
						
					"; 
			
			$Headers  = 'MIME-Version: 1.0' . "\r\n";
			$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			mail('ryan@bevomedia.com', 'Network Registration Transaction', $Body, $Headers);

			
			unset($_SESSION['SignUpData']);
			unset($_SESSION['PaymentData']);
			unset($_SESSION['NetworkContactID']);
			
			header('Location: /BevoMedia/Networks/PaymentComplete.html');
	    	die;
		}
	}

	Public Function AddContactInfo($Info)
	{
		try {
		$this->db->insert('bevomedia_network_contact_info', $Info);
		} catch (Exception $e)
		{
			die($e->getMessage());
		}
		return $this->db->lastInsertId();
	}
	
	Public Function AddContactPayment($Info)
	{
		$this->db->insert('bevomedia_network_payments', $Info);
		return $this->db->lastInsertId();
	}
	
	Public Function PaymentComplete()
	{
		if (isset($_SESSION['SignUpData'])) unset($_SESSION['SignUpData']);
		if (isset($_SESSION['PaymentData'])) unset($_SESSION['PaymentData']);
		if (isset($_SESSION['NetworkContactID'])) unset($_SESSION['NetworkContactID']);
	}
	
	Public Function JSONGetCountryStates()
	{
		$Code = $_GET['Code'];
		
		$Sql = "SELECT
					bevomedia_state.initials,
					bevomedia_state.name
				FROM	
					bevomedia_adwords_countries,
					bevomedia_state
				WHERE
					(bevomedia_adwords_countries.id = bevomedia_state.CountryID) AND 
					(code = ?)
				";
		$this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
		echo json_encode($this->db->fetchAll($Sql, $Code));
		die;
	}

}

?>
