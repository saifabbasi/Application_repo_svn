<?php 

require_once('Azoogle.Abstract.php');

/**
 * Azoogle.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class Azoogle Extends AzoogleAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://login.azoogleads.com/soap/azads2_server.php';
	
	/**
	 * @access protected
	 * @var string $offerTrackUrl
	 */
	protected $offerTrackingCodesUrl = 'https://login.azoogleads.com/affiliate/offer/display_detailed_offer?menu_category=&category_id=&offer_id=';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'ryan@bevomedia.com';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';
	
	/**
	 * Azoogle class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}