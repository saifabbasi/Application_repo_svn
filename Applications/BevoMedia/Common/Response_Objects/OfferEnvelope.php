<?php 

require_once('Offer.php');

/**
 * OfferEnvelope.php
 */

class OfferEnvelope {
	/**
	 * @access protected
	 * @var array $offers
	 */
	protected $offers = array();
	
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
	public function __construct()
	{
		
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
	 * Return an array of Offer objects.
	 *
	 * @return array
	 */
	public function Offers()
	{
		return $this->offers;
	}
	
	/**
	 * Add an Offer object to the offers array.
	 *
	 * @param Offer $offerObject
	 * @return OfferEnvelope
	 */
	public function addOfferObject($offerObject)
	{
		if(!is_array($offerObject->category))
		{
			$offerObject->category = array($offerObject->category);
		}
		if(!is_array($offerObject->countries))
		{
			$offerObject->countries = array($offerObject->countries);
		}
		if($this->IsEmpty())
		{
			$this->empty = false;
		}
		$this->offers[] = $offerObject;
		return $this;
	}
}

?>