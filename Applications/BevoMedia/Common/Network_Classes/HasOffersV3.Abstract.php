<?php

require_once('Networks.Abstract.php');



/**
 * HasOffersV3.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
abstract class HasOffersV3Abstract Extends NetworksAbstract {
	
	/**
	 * @access private
	 * @var string $publisherId
	 */
	protected $publisherId = '';
	
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsApiUrl = '';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersApiUrl = '';
	
	/**
	 * @access protected
	 * @var string $networkId
	 */
	protected $networkId = '';

	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function login()
	{
		return true;
	}
	
	
	/**
	 * Retrieve stats for this user.
	 *
	 * @param String $Date
	 * @return Boolean	False if error occurs.
	 */
	Public Function getStats($Date = '')
	{
		return true;
	}



	/**
	 * Retrieve offers.
 	 */
	public function getOffers()
	{ 
		if ($this->networkId==1109) {
			$params = array(
				'Format' => 'json'
				,'Target' => 'Offer'
				,'Method' => 'findAll'
				,'Service' => 'HasOffers'
				,'Version' => 2
				,'NetworkId' => 'bevo'
				,'NetworkToken' => $this->offersApiKey
				,'api_key' => $this->offersApiKey
			);

			$url = $this->offersApiUrl.http_build_query( $params );
			$data = file_get_contents($url);
			
	    	
			$Offers = json_decode($data);
			$Offers = $Offers->response->data;
		} else {
		
			$params = array(
					'NetworkId' => $this->networkId
					,'api_key' => $this->offersApiKey
			);

			$this->offersApiUrl = rtrim($this->offersApiUrl, '&');
			$url = $this->offersApiUrl.'&'.http_build_query( $params );
			
			
			$data = file_get_contents($url);
				
			
			$Offers = json_decode($data);
			$Offers = $Offers->response->data;
			
		}
		
		$Output = new OfferEnvelope();
		foreach($Offers as $Offer)
		{
			$OfferObj = new Offer();
			$OfferObj->offerId = $Offer->Offer->id;
			$OfferObj->name = $Offer->Offer->name;
			$OfferObj->description = $Offer->Offer->description;
//			$OfferObj->expireDate = $Offer->expiration_date;
//			$OfferObj->countries = explode(', ', $Offer->countries);
//			$OfferObj->category = explode(', ', $Offer->categories);
			$OfferObj->payout = $Offer->Offer->default_payout;
			$OfferObj->type = $Offer->Offer->payout_type;
			$OfferObj->previewUrl = $Offer->Offer->offer_url;
			
//			$OfferObj->offerType = 'Lead';
//			if (strstr($Offer->payout, '%')) {
				$OfferObj->offerType = 'Sale';
//			}
			
			$OfferObj->imageUrl = '';
			$OfferObj->dateAdded = date('Y-m-d');
			
			$Output->addOfferObject($OfferObj);
		}
		return $Output;
	}
	
	
}