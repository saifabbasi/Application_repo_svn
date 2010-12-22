<?php 

require_once('Stat.php');
require_once('StatsTotal.php');

/**
 * StatEnvelope.php
 */

class StatEnvelope {
	/**
	 * @access protected
	 * @var string $date
	 */
	protected $date = NULL;
	
	/**
	 * @access protected
	 * @var array $stats
	 */
	protected $stats = array();
	
	/**
	 * @access protected
	 * @var StatsTotal $totalStats
	 */
	protected $totalStats = NULL;
	
	/**
	 * @access protected
	 * @var boolean $empty 
	 */
	protected $empty = true;
	
	/**
	 * StatEnvelope construct
	 *
	 * @param string $date
	 */
	public function __construct($date = false)
	{
		if($date !== false)
		{
			$this->setDate($date);
		}
		$this->stats[] = new Stat();
		$this->totalStats = new StatsTotal();
	}
	
	/**
	 * Return an array of Stat objects.
	 *
	 * @return array
	 */
	public function Stats()
	{
		return $this->stats;
	}
	
	/**
	 * Return this totalStats object.
	 *
	 * @return StatsTotal
	 */
	public function TotalStats()
	{
		return $this->totalStats;
	}
	
	/**
	 * Return the date for this set of stats.
	 *
	 * @return String
	 */
	public function Date()
	{
		return $this->date;
	}
	
	/**
	 * Return if this Envelope is empty.
	 *
	 * @return boolean
	 */
	public function IsEmpty()
	{
		return $this->empty;
	}
	
	/**
	 * Add a Stat object to the stats array.
	 *
	 * @param Stat $statObject
	 * @return StatEnvelope
	 */
	public function addStatObject($statObject)
	{
		if($this->IsEmpty())
		{
			$this->empty = false;
			$this->stats = array();
		}
		$this->stats[] = $statObject;
		$this->calcTotalStats();
		return $this;
	}
	
	/**
	 * Calculate stat totals.
	 */
	private function calcTotalStats()
	{
		if(sizeof($this->stats) == 1)
		{
			$this->totalStats->clicks = $this->stats[0]->clicks;
			$this->totalStats->conversions = $this->stats[0]->conversions;
			$this->totalStats->revenue = $this->stats[0]->revenue;
			$this->totalStats->uniqueOfferIds = $this->totalStats->uniqueSubIds = 1;
		}else{
			$this->totalStats = new StatsTotal();
			$UniqueOfferIds = array();
			$UniqueSubIds = array();
			for($i=0; $i<sizeof($this->stats); $i++)
			{
				$Stat = $this->stats[$i];
				$this->totalStats->clicks += $Stat->clicks;
				$this->totalStats->conversions += $Stat->conversions;
				$this->totalStats->revenue += $Stat->revenue;
				$UniqueSubIds[$Stat->subId] = true;
				$UniqueOfferIds[$Stat->offerId] = true;
			}
			$this->totalStats->uniqueSubIds = sizeof($UniqueSubIds);
			$this->totalStats->uniqueOfferIds = sizeof($UniqueOfferIds);
		}
		return $this->totalStats;
	}
	
	/**
	 * Set the date for this set of stats.
	 *
	 * @param string $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}
}

?>