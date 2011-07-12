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
class CrushAds Extends CakeMarketingAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://affiliates.crushads.com/affiliates/api/1/reports.asmx/Clicks';
	
	/**
	 * @access protected
	 * @var string $apiUrlConversions
	 */
	protected $apiUrlConversions = 'http://affiliates.crushads.com/affiliates/api/1/reports.asmx/Conversions';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://affiliates.crushads.com/affiliates/api/1/offers.asmx/OfferFeed';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://affiliates.crushads.com/login.php';
	
	/**
	 * @access protected
	 * @var string $offersAffiliateId
	 */
	protected $offersAffiliateId = '257';
	
	/**
	 * @access protected
	 * @var string $offersApiKey
	 */
	protected $offersApiKey = 'KyMGB739pfs';
	
	/**
	 * Convert2Media class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}