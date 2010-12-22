<?php
/**
 * User Controller
 */

/**
 * User Controller
 *
 * Controller for generic User related pages such as processing log in attempts, changing the user's password, updating profile information,
 * submitting a ticket and more.
 * @category   RCS Framework
 * @package    Controllers
 * @subpackage UsersController
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */

require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');

Class PPVToolsController extends ClassComponent
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
		$this->{'PageHelper'} = new PageHelper();
		$this->{'PageDesc'} = new PageDesc();
		$_db = Zend_Registry::get('Instance/DatabaseObj');
		
		
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
               $_SESSION['apiKey'] = $_GET['apiKey'];
			$this->isApi = true;
			$this->isApiStr = "&apiKey=".$_GET['apiKey'];
		}
		
		
		
		$this->db = $_db;
		if(isset($_SESSION['User']['ID']))
		{
			$user = new User();
			$user->getInfo($_SESSION['User']['ID']);
			$this->{'User'} = $user;
			Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
		}
		
		if ( ($user->vaultID==0) && (!$user->IsSubscribed(User::PRODUCT_FREE_RESEARCH)) )
		{
			header('Location: /BevoMedia/User/AddCreditCard.html');
			die;
		}
		
		
		if(!isset($_SESSION['User']) || !intval($_SESSION['User']['ID']))
		{
			$page = Zend_Registry::get('Instance/Function');
			$noLoginNeeded = array('Register', 'Login', 'ProcessLogin');
			if(!in_array($page, $noLoginNeeded))
			{
				$_SESSION['loginLocation'] = $_SERVER['REQUEST_URI'];
				header('Location: /BevoMedia/Index/');
				die;
			}
		}
		if($this->User->vaultID == 0)
		{
//			$_GET['basicRequired'] = '1';
//		  header('Location: /BevoMedia/Marketplace/Premium.html');
//		  die;
		}
	}
	
	Public Function Tools()
	{
		
	}
	
	Public Function SendToListBuilder()
	{
		$User = new User($_SESSION['User']['ID']);
		
		if ($User->apiCalls<50)
		{
			$User->AddUserAPICallsCharge();
		}
		
		$User->AddUserPPVCharge();

		$_SESSION['PPVPost'] = $_POST;		
		header('Location: /BevoMedia/PPVTools/LinkBuilder.html');
		
		die;
		
	}
	
	

	
}

?>