<?php
/**
 * Index Controller
 */

/**
 * Index Controller
 *
 * Controller for pages when user is generally not required to be logged in.
 * This includes pages that allow the user to register an account, retrieve their password or view information about the site.
 *
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');

Class IndexController extends ClassComponent
{
	
	/**
	 * @var Mixed $GUID
	 */
	Public $GUID		= NULL;
	
	/**
	 * Constructor
	 */
	Public Function __construct()
	{
		parent::GenerateGUID();
	
		$this->PageHelper = new PageHelper();
		
		$page = Zend_Registry::get('Instance/Function');
		$this->{'page'} = $page;
	    if(!isset($addToURL))
          $addToURL = '';
		if(!isset($_SESSION['User']['ID']))
		{
			if(isset($_COOKIE['BEVO_REMEMBER_LOGIN_ID']))
			{
				$_SESSION['User']['ID'] = $_COOKIE['BEVO_REMEMBER_LOGIN_ID'];
				if(isset($_SESSION['loginLocation']) && !strstr($_SESSION['loginLocation'], '_') )
				{
					header('Location: ' . $_SESSION['loginLocation']);
					unset($_SESSION['loginLocation']);
				}else{
					header('Location: /BevoMedia/User/Index.html' . $addToURL);
				}
			}
		}
	}
	
	Public Function Index()
	{
		if(Zend_Registry::get('Application/Mode') == 'SelfHosted')
		{
			header('Location: /BevoMedia/Index/SelfHostedLogin.html');
			exit();
		}
	}
	
	/**
	 * Self Hosted Login Page Functionality
	 */
	Public Function SelfHostedLogin()
	{
		Zend_Registry::set('Instance/LayoutType', 'blank-layout');
	}
	
	/**
	 * Reset Password Page Functionality
	 */
	Public Function ResetPassword()
	{
		if(isset($_POST['resetPasswordSubmit']))
		{
			$User = new User;
			$User->GetInfo($User->GetIdUsingEmail($_POST['Email']));
			if(!isset($User->id))
			{
				$this->Message = 'EMAIL_NOT_FOUND';
			}else
			{
				if($User->VerifyResetCode($_POST['EmailCode']))
				{
					$User->ChangePassword($_POST['Password']);
					$User->ClearResetCode();
					header('Location: /BevoMedia/Index/Login.html?Email='.$_POST['Email']);
					die;
				}else{
					$this->Message = 'BAD_CODE';
					
				}
			}
		}
	}
	
	/**
	 * Forgot Password Page Functionality
	 */
	Public Function ForgotPassword()
	{
		$User = new User();
		$this->Message = false;
		
		if(isset($_POST['forgotPasswordSubmit']))
		{
			$ID = $User->GetIdUsingEmail($_POST['Email']);
			$User = new User($ID);
			if(!isset($User->id))
			{
				$this->Message = 'EMAIL_NOT_FOUND';
			}else
			{
				$User->ResetPassword();
				$this->Message = 'EMAIL_SENT';
			}
		}
	}
	
	/**
	 * Login Page Functionality
	 */
	Public Function Login()
	{
		if(isset($_GET['Error']))
		{
			$this->{'Error'.$_GET['Error']} = true;
		}
		if(isset($_GET['Email']))
		{
			$this->Email = $_GET['Email'];
		}
	}
	
	/**
	 * Close Shadowbox Functionality
	 */
	Public Function CloseShadowbox()
	{
		$this->Location = false;
		if(isset($_GET['goto']))
		{
			$this->Location = $this->PageHelper->URLDecode($_GET['goto']);
		}
	}
	
	public function AdScoutReport()
	{
		$db = Zend_Registry::get('Instance/DatabaseObj');
		
		$sql = "SELECT
					COUNT(bevomedia_user_payments.ID) as `TotalSales`,
					SUM(bevomedia_user_payments.Price) as `TotalRevenue`
				FROM
					bevomedia_user_payments,
					bevomedia_user
				WHERE
					(bevomedia_user_payments.UserID = bevomedia_user.id) AND
					((bevomedia_user_payments.ProductID = 17) || (bevomedia_user_payments.ProductID = 18)) AND
					(bevomedia_user_payments.Paid = 1) AND
					(bevomedia_user_payments.Deleted = 0)
				";
		$totals = $db->fetchRow($sql);
		$this->TotalSales = (int) $totals->TotalSales;
		$this->TotalRevenue = number_format($totals->TotalRevenue, 2);
		
		$date = date('Y-m-d');
		$sql = "SELECT
					COUNT(bevomedia_user_payments.ID) as `TotalSales`,
					SUM(bevomedia_user_payments.Price) as `TotalRevenue`
				FROM
					bevomedia_user_payments,
					bevomedia_user
				WHERE
					(bevomedia_user_payments.UserID = bevomedia_user.id) AND
					((bevomedia_user_payments.ProductID = 17) || (bevomedia_user_payments.ProductID = 18)) AND
					(bevomedia_user_payments.Paid = 1) AND
					(bevomedia_user_payments.Date BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59') AND
					(bevomedia_user_payments.Deleted = 0)
				";
		$totals = $db->fetchRow($sql);
		$this->TodaySales = (int) $totals->TodaySales;
		$this->TodayRevenue = number_format($totals->TodayRevenue, 2);
		
		
		
		$sql = "SELECT
					bevomedia_user.email,
					bevomedia_user_payments.UserID,
					SUM(bevomedia_user_payments.Price) as `Price`
				FROM
					bevomedia_user_payments,
					bevomedia_user
				WHERE
					(bevomedia_user_payments.UserID = bevomedia_user.id) AND
					((bevomedia_user_payments.ProductID = 17) || (bevomedia_user_payments.ProductID = 18)) AND
					(bevomedia_user_payments.Paid = 1) AND
					(bevomedia_user_payments.Deleted = 0)
				GROUP BY
					bevomedia_user.id
				ORDER BY
					bevomedia_user_payments.ID
				";
		$this->Payments = $db->fetchAll($sql);
		
		foreach ($this->Payments as $key => $payment)
		{
			$sql = "SELECT
						SUM(bevomedia_user_payments.Price) as `TotalRevenue`
					FROM
						bevomedia_user_payments,
						bevomedia_user
					WHERE
						(bevomedia_user_payments.UserID = bevomedia_user.id) AND
						((bevomedia_user_payments.ProductID = 17) || (bevomedia_user_payments.ProductID = 18) || (bevomedia_user_payments.ProductID = 2)) AND
						(bevomedia_user_payments.Paid = 1) AND
						(bevomedia_user_payments.Deleted = 0) AND
						(bevomedia_user_payments.UserID = {$payment->UserID})
				";
			
			$TotalRevenue = $db->fetchOne($sql);
			$this->Payments[$key]->TotalRevenue += $TotalRevenue;

			
			$fromDate = date('Y-m-1');
			$toDate = date('Y-m-31');
			$sql = "SELECT
						SUM(bevomedia_user_payments.Price) as `TotalRevenue`
					FROM
						bevomedia_user_payments,
						bevomedia_user
					WHERE
						(bevomedia_user_payments.UserID = bevomedia_user.id) AND
						((bevomedia_user_payments.ProductID = 17) || (bevomedia_user_payments.ProductID = 18) || (bevomedia_user_payments.ProductID = 2)) AND
						(bevomedia_user_payments.Paid = 1) AND
						(bevomedia_user_payments.Deleted = 0) AND
						(bevomedia_user_payments.UserID = {$payment->UserID}) AND
						(bevomedia_user_payments.Date BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59') 
				";
			
			$TotalMonthRevenue = $db->fetchOne($sql);
			$this->Payments[$key]->TotalMonthRevenue += $TotalMonthRevenue; 
			
			
		}
	}
}

?>