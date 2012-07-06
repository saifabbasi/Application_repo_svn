<?php 

require_once('CakeMarketing.Abstract.php');

/**
 * ClickBooth
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class ClickBooth Extends CakeMarketingAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://affiliates.clickbooth.com/affiliates/api/1/reports.asmx/Clicks';
	
	/**
	 * @access protected
	 * @var string $apiUrlConversions
	 */
	protected $apiUrlConversions = 'http://affiliates.clickbooth.com/affiliates/api/1/reports.asmx/Conversions';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://affiliates.clickbooth.com/affiliates/api/1/offers.asmx/OfferFeed';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://affiliate.c2mclicks.com/login.php';
	
	/**
	 * @access protected
	 * @var string $offersAffiliateId
	 */
	protected $offersAffiliateId = '79014';
	
	/**
	 * @access protected
	 * @var string $offersApiKey
	 */
	protected $offersApiKey = '7Q7pAgrzCpWDgiajccYZA';
	
	/**
	 * Convert2Media class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}