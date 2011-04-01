<?php 

require_once('DirectTrack.Abstract.php');

/**
 * AffiliateDotCom.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class AffiliateDotCom Extends DirectTrackAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://login.tracking101.com/api/soap_affiliate.php';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'CD35101';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';
	
	/**
	 * @access protected
	 * @var string $apiClient
	 */
	protected $apiClient = 'cash4creatives';
	
	/**
	 * AffiliateDotCom class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}