<?php 

require_once('HasOffers.Abstract.php');

/**
 * MediaForce
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2010 RCS
 * @author RCS
 * @version 0.0.1
 */
class MediaForce Extends HasOffersAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $offersApiKey = 'AFFOaAJ4OZUkviND3JcUis9gRh9Xud';
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsApiUrl = 'http://mediaforceltd.hasoffers.com/stats/stats.json';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = 'http://mediaforceltd.hasoffers.com/stats/lead_report.json';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersApiUrl = 'http://mediaforceltd.hasoffers.com/offers/offers.json';
	
	
	/**
	 * EWA class constructor.
	 *
	 */
	Public Function __construct()
	{
		$this->networkId = 1096;
	}
}