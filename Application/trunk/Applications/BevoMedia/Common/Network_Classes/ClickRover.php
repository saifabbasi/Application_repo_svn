<?php 

require_once('HasOffers.Abstract.php');

/**
 * ClickRover.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2010 RCS
 * @author RCS
 * @version 0.0.1
 */
class ClickRover Extends HasOffersAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $offersApiKey = 'AFF06SeroDhu7eyXfh7Md3MGiutqYQ';
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsApiUrl = 'http://network.clickrover.com/stats/stats.json';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = 'http://network.clickrover.com/stats/lead_report.json';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersApiUrl = 'http://network.clickrover.com/offers/offers.json';
	
	
	/**
	 * EWA class constructor.
	 *
	 */
	Public Function __construct()
	{
		
	}
}