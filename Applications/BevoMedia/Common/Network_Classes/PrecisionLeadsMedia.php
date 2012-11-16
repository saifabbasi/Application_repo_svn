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
class PrecisionLeadsMedia Extends HasOffersAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $offersApiKey = 'AFFwm4v4DcYkuf0BHJztp3WE65XlON';
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsApiUrl = 'http://precisionleads.hasoffers.com/stats/stats.json';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = 'http://precisionleads.hasoffers.com/stats/lead_report.json';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersApiUrl = 'http://precisionleads.hasoffers.com/offers/offers.json';
	
	
	/**
	 * EWA class constructor.
	 *
	 */
	Public Function __construct()
	{
		$this->networkId = 1105;
	}
}