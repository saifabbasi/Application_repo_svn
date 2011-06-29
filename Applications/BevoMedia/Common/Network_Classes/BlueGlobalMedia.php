<?php 

require_once('Hitpath.Abstract.php');

/**
 * Ads4Dough.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class BlueGlobalMedia Extends HitpathAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://reporting.bgmtracker.com/api.php';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'https://affiliate.bgmtracker.com/offersdetail2.php';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://affiliate.bgmtracker.com/login.php';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'networks@bevomedia.com';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'bevo1025';
	
	/**
	 * Ads4Dough class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}