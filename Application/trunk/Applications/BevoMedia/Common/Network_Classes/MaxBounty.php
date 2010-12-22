<?php 

require_once('MaxBounty.Abstract.php');

/**
 * MaxBounty.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class MaxBounty Extends MaxBountyAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://www.maxbounty.com/api/api.cfc?wsdl';
	
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