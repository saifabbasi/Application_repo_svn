<?php 

require_once('CrakRevenue.Abstract.php');

/**
 * FireLead.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class CrakRevenue Extends CrakRevenueAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://www.crakrevenue.com/web_offers?action=search&category=&country=';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://www.crakrevenue.com/';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'michael.chambrello@bevomedia.com';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'bevo1025';

	
	/**
	 * FireLead class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}