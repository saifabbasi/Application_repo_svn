<?php 

require_once('HasOffers.Abstract.php');

/**
 * Leadnomics
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2010 RCS
 * @author RCS
 * @version 0.0.1
 */
class Leadnomics Extends NetworksAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $offersApiKey = 'AFFcrFPW3Dq2PsHeUuiweXCgkbJyJs';
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsApiUrl = 'http://publishers.leadnomics.com/stats/stats.json';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = 'http://publishers.leadnomics.com/stats/lead_report.json';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersApiUrl = 'http://publishers.leadnomics.com/offers/offers.json';
	
	
	/**
	 * EWA class constructor.
	 *
	 */
	Public Function __construct()
	{
		$this->networkId = 1097;
	}
	
	/**
	 * Retrieve offers.
 	 */
	public function getOffers()
	{
		
		return true;
	}
	
	Public Function getStats($Date = '')
	{
		return true;
	}
}