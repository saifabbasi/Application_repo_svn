<?php 

require_once('DirectTrack.Abstract.php');

/**
 * Rextopia.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class Rextopia Extends DirectTrackAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://affiliates.copeac.com/api/soap_affiliate.php';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'CD11827';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';
	
	/**
	 * @access protected
	 * @var string $apiClient
	 */
	protected $apiClient = 'rextopia';
	
	/**
	 * Copeac class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}