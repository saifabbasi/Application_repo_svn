<?php 

require_once('DirectTrack.Abstract.php');

/**
 * ClickBooth.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class ClickBooth Extends DirectTrackAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://publishers.clickbooth.com/api/soap_affiliate.php?wsdl';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'CD79014';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';
	
	/**
	 * @access protected
	 * @var string $apiClient
	 */
	protected $apiClient = 'integraclick';
	
	/**
	 * ClickBooth class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}