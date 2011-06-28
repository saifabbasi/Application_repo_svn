<?php 

/**
 * Offer.php
 */
class Offer {
	/**
	 * @var integer $offerId
	 */
	public $offerId = NULL;
	
	/**
	 * @var string $name
	 */
	public $name = NULL;
	
	/**
	 * @var string $description
	 */
	public $description = NULL;
	
	/**
	 * @var array $category
	 */
	public $category = array();
	
	/**
	 * @var array $subcategory
	 */
	public $subcategory = array();
	
	/**
	 * @var string $openDate
	 */
	public $openDate = NULL;
	
	/**
	 * @var string $expireDate
	 */
	public $expireDate = NULL;
	
	/**
	 * @access public
	 * @var array $countries
	 */
	public $countries = array();
	
	/**
	 * @access public
	 * @var float $payout
	 */
	public $payout = 0.0;
	
	/**
	 * @access public
	 * @var float $percent
	 */
	public $percent = 0.0;
	
	/**
	 * @access public
	 * @var string $trackUrl
	 */
	public $trackUrl = NULL;
	
	/**
	 * @access public
	 * @var string $previewUrl
	 */
	public $previewUrl = NULL;
	
	/**
	 * @access public
	 * @var string $type
	 */
	public $type = NULL;
	
	/**
	 * @access public
	 * @var string $cakeStatus
	 */
	public $cakeStatus = '';
	
	/**
	 * @access public
	 * @var string $ecpc
	 */
	public $ecpc = NULL;
	
	/**
	 * Offer class constructor.
	 */
	public function __construct()
	{
		
	}
}

?>
