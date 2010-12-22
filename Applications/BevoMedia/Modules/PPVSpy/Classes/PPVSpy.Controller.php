<?php

/**
 * PPVSpy Class
 */

/**
 * User Class
 * 
 * User Class
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2010 RCS
 * @author 		RCS
 * @version 	0.1
 */

require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');

Class PPVSpyController {
	
	/**
	 * @var Zend_Db_Adapter_Abstract $db
	 */
	Protected $db = false;
	
	public function __construct()
	{
		$this->db = Zend_Registry::get('Instance/DatabaseObj');
		
		$this->{'PageHelper'} = new PageHelper();
		$this->{'PageDesc'} = new PageDesc();
		
		
		if(isset($_SESSION['User']['ID']))
		{
			$user = new User();
			$user->getInfo($_SESSION['User']['ID']);
			$this->{'User'} = $user;
			Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
		}
		
		$page = Zend_Registry::get('Instance/Function');
		if(!isset($_SESSION['User']) || !intval($_SESSION['User']['ID']))
		{
			$noLoginNeeded = array('Register', 'Login', 'ProcessLogin');
			if(!in_array($page, $noLoginNeeded))
			{
				$_SESSION['loginLocation'] = $_SERVER['REQUEST_URI'];
				header('Location: /BevoMedia/Index/');
				die;
			}
		}
		
		if (!$user->IsSubscribed(User::PRODUCT_PPVSPY_MONTHLY) && !$user->IsSubscribed(User::PRODUCT_PPVSPY_YEARLY) && !$user->IsSubscribed(User::PRODUCT_FREE_PPVSPY)) {
			header('Location: /');
			die;
		}
	}
	
	public function MostSeenPopups()
	{
	
	}
	
}


