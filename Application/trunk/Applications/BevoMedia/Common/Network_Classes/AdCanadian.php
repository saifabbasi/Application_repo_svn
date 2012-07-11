<?php 

require_once('AdCommunal.Abstract.php');

/**
 * AdCanadian
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class AdCanadian Extends AdCommunalAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://www.adcanadian.com/offerfeed.asp';
	
	
	/**
	 * @access protected
	 * @var string $offersAffiliateId
	 */
	protected $offersAffiliateId = 'networks@bevomedia.com';
	
	/**
	 * @access protected
	 * @var string $offersApiKey
	 */
	protected $offersApiKey = '5858ea228cc2edf88721699b2c8638e5';
	
	/**
	 * Convert2Media class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}