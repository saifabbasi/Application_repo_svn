<?php 

require_once('LinkTrust.Abstract.php');

/**
 * Sybarite.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class Sybarite Extends LinkTrustAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://affiliates.sybariteelite.com/RequestOffers.aspx';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://affiliates.sybariteelite.com/Welcome/LogInAndSignUp.aspx';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = '138205';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';
	
	/**
	 * @access protected
	 * @var string $publisherLogin
	 */
	protected $publisherLogin = '138205';
	
	/**
	 * @access protected
	 * @var string $publisherPassword
	 */
	protected $publisherPassword = 'yoyoyo1025';

	/**
	 * @access protected
	 * @var string $viewState
	 */			
	protected $viewState = '';

	/**
	 * @access protected
	 * @var string $eventValidation
	 */			
	protected $eventValidation = '';

	/**
	 * @access protected
	 * @var string $userNameFieldName
	 */			
	protected $userNameFieldName = 'ctl00$ContentPlaceHolder1$lcLogin$txtUserName';

	/**
	 * @access protected
	 * @var string $userNameFieldId
	 */			
	protected $userNameFieldId = 'ctl00_ContentPlaceHolder1_lcLogin_txtUserName';

	/**
	 * @access protected
	 * @var string $passwordFieldName
	 */			
	protected $passwordFieldName = 'ctl00$ContentPlaceHolder1$lcLogin$txtPassword';

	/**
	 * @access protected
	 * @var string $passwordFieldId
	 */			
	protected $passwordFieldId = 'ctl00_ContentPlaceHolder1_lcLogin_txtPassword';

	/**
	 * @access protected
	 * @var string $submitButtonName
	 */			
	protected $submitButtonName = 'ctl00$ContentPlaceHolder1$lcLogin$btnSubmit';

	/**
	 * @access protected
	 * @var string $submitButtonId
	 */			
	protected $submitButtonId = 'ctl00_ContentPlaceHolder1_lcLogin_btnSubmit';
	
	/**
	 * @access protected
	 * @var string $csvUrl
	 */
	protected $csvUrl = 'http://affiliates.sybariteelite.com/RptSubIdBreakout.aspx';
	
	/**
	 * @access protected
	 * @var string $viewState
	 */			
	protected $csvViewState = '';

	/**
	 * @access protected
	 * @var string $eventValidation
	 */			
	protected $csvEventValidation = '';
	
	/**
	 * Sybarite class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}