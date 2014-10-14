<?php 

require_once('HasOffersV3.Abstract.php');

/**
 * CPAStaxx.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2010 RCS
 * @author RCS
 * @version 0.0.1
 */
class vCommission Extends HasOffersV3Abstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $offersApiKey = '57999cdcf8dbdab766847e38ccdca921074d6966e9a8883eaadfa2a93f0c387b';
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsApiUrl = 'http://affiliates.kissmyads.com/stats/stats.json';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = 'http://affiliates.kissmyads.com/stats/lead_report.json';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersApiUrl = 'http://api.hasoffers.com/v3/Affiliate_Offer.json?Method=findAll';

	/**
	 * @access protected
	 * @var string $networkId
	 */
	protected $networkId = 'vcm';
	
	
	/**
	 * EWA class constructor.
	 *
	 */
	Public Function __construct()
	{
//		$this->networkId = 'vcm';
	}
}