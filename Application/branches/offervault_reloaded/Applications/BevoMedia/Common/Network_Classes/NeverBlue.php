<?php 

require_once('NeverBlue.Abstract.php');

/**
 * NeverBlue.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class NeverBlue Extends NeverBlueAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
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
	 * NeverBlue class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}