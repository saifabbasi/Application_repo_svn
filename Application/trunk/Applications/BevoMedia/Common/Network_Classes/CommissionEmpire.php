<?php 

require_once('DirectTrack.Abstract.php');

/**
 * CommissionEmpire.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class CommissionEmpire Extends DirectTrackAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://secure.directtrack.com/api/soap_affiliate.php';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'CD2300';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';
	
	/**
	 * @access protected
	 * @var string $apiClient
	 */
	protected $apiClient = 'commissionempire';
	
	/**
	 * CommissionEmpire class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}