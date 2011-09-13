<?php 

require_once('CakeMarketing.Abstract.php');

/**
 * Convert2Media.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class UniqueLeads Extends CakeMarketingAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://uniqueleadsaffiliates.com/affiliates/api/1/reports.asmx/Clicks';
	
	/**
	 * @access protected
	 * @var string $apiUrlConversions
	 */
	protected $apiUrlConversions = 'http://uniqueleadsaffiliates.com/affiliates/api/1/reports.asmx/Conversions';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://uniqueleadsaffiliates.com/affiliates/api/1/offers.asmx/OfferFeed';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://uniqueleadsaffiliates.com/login.php';
	
	/**
	 * @access protected
	 * @var string $offersAffiliateId
	 */
	protected $offersAffiliateId = '300682';
	
	/**
	 * @access protected
	 * @var string $offersApiKey
	 */
	protected $offersApiKey = 'DM1pzre9uOnzSf8MzqEug';
	
	/**
	 * Convert2Media class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}