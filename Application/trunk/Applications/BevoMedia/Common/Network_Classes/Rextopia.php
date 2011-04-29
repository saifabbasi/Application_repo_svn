<?php 

require_once('CakeMarketing.Abstract.php');

/**
 * Rextopia.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class Rextopia Extends CakeMarketingAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = 'http://login.rextopia.com/affiliates/api/1/reports.asmx/Clicks';
	
	/**
	 * @access protected
	 * @var string $apiUrlConversions
	 */
	protected $apiUrlConversions = 'http://login.rextopia.com/affiliates/api/1/reports.asmx/Conversions';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://login.rextopia.com/affiliates/api/1/offers.asmx/OfferFeed';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://login.rextopia.com/login.php';
	
	/**
	 * @access protected
	 * @var string $offersAffiliateId
	 */
	protected $offersAffiliateId = '11827';
	
	/**
	 * @access protected
	 * @var string $offersApiKey
	 */
	protected $offersApiKey = 'G9B7bjcaQ0Y';
	
	/**
	 * Convert2Media class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}