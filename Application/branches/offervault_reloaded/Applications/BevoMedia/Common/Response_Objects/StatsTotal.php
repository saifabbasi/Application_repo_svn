<?php 

/**
 * StatsTotal.php
 */

class StatsTotal {
	/**
	 * @access public
	 * @var integer $clicks
	 */
	public $clicks = 0;
	
	/**
	 * @access public
	 * @var integer $conversions
	 */
	public $conversions = 0;
	
	/**
	 * @access public
	 * @var float $revenue
	 */
	public $revenue = 0;
	
	/**
	 * @access public
	 * @var string $uniqueSubIds
	 */
	public $uniqueSubIds = NULL;
	
	/**
	 * @access public
	 * @var string $uniqueSubIds
	 */
	public $uniqueOfferIds = NULL;
	
	public function __construct($clicks = 0, $conversions = 0, $revenue = 0, $uniqueSubIds = 0, $uniqueOfferIds = 0)
	{
		$this->clicks = $clicks;
		$this->conversions = $conversions;
		$this->revenue = $revenue;
		$this->uniqueSubIds = $uniqueSubIds;
		$this->uniqueOfferIds = $uniqueOfferIds;
	}
}

?>