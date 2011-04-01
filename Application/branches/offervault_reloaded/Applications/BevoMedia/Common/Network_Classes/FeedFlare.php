<?php 

require_once('HasOffers.Abstract.php');

/**
 * FeedFlare.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2010 RCS
 * @author RCS
 * @version 0.0.1
 */
class FeedFLare Extends HasOffersAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $offersApiKey = 'AFFfk9AVYiZvXNVVbx1zlZYXfNiuJO';
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsApiUrl = 'http://network.feedflaremedia.com/stats/stats.json';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = 'http://network.feedflaremedia.com/stats/lead_report.json';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersApiUrl = 'http://network.feedflaremedia.com/offers/offers.json';
	
	
	/**
	 * FeedFlare class constructor.
	 *
	 */
	Public Function __construct()
	{
		$this->networkId = 1066;
	}
}