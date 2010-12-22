<?php 

/**
 * Stat.php
 */

class Stat {
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
	 * @var string $subId
	 */
	public $subId = NULL;
	
	/**
	 * @access public
	 * @var string $offerId
	 */
	public $offerId = NULL;
	
	/**
	 * Stat class constructor.
	 *
	 * @param integer $clicks
	 * @param integer $conversions
	 * @param float $revenue
	 * @param string $subId
	 * @param string $offerId
	 */
	public function __construct($clicks = 0, $conversions = 0, $revenue = 0, $subId = NULL, $offerId = NULL)
	{
		$this->clicks = $clicks;
		$this->conversions = $conversions;
		$this->revenue = $revenue;
		$this->subId = $subId;
		$this->offerId = $offerId;
	}
}

?>