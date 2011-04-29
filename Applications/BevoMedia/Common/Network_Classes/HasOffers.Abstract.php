<?php

require_once('Networks.Abstract.php');



/**
 * HasOffers.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
abstract class HasOffersAbstract Extends NetworksAbstract {
	
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
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function login()
	{
		$Date = date('Y-m-d');
		$Data = file_get_contents($this->statsApiUrl.'?api_key='.$this->publisherId.'&start_date='.$Date.'&end_date='.$Date);
		
		if (strstr($Data, 'Unexpected Error'))
		{
			return false;
		}
		
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
		if($Date == '')
		{
			$Date = date('Y-m-d');
		}
		
		$FromDate = $ToDate = $Date;
		
		$StatsData = file_get_contents($this->statsApiUrl.'?api_key='.$this->publisherId.'&start_date='.$Date.'&end_date='.$Date.'&group[]=Offer.name');
		$StatsData = json_decode($StatsData);

		$ConversionsData = file_get_contents($this->conversionsApiUrl.'?api_key='.$this->publisherId.'&start_date='.$Date.'&end_date='.$Date);
		$ConversionsData = json_decode($ConversionsData);
		$ConversionsData = $ConversionsData->data;	
		
		$TotalConverted = count($ConversionsData);
		
		$Output = new StatEnvelope($Date);
		
		$Clicks = $StatsData->data;
		
		if (count($Clicks)==0)
		{
			return false;
		}
		
		foreach ($ConversionsData as $Conversion)
		{
			foreach ($Clicks as $Key => $Click)
			{
				if ($Click->offer==$Conversion->offer)
				{
					$Clicks[$Key]->clicks = $Clicks[$Key]->clicks-1; 
				}
				
				if ($Clicks[$Key]->clicks==0)
				{
					unset($Clicks[$Key]);
				}
			}
		}
		

		$Date = date('Y-m-d', strtotime($FromDate));
		
		foreach ($Clicks as $Click)
		{
			$OfferID = '';
			$OfferName = mysql_real_escape_string($Click->offer);
			$Sql = "SELECT offer__id FROM bevomedia_offers WHERE (network__id = {$this->networkId}) AND (title = '{$OfferName}') ";
			$Result = mysql_query($Sql);
			if (mysql_num_rows($Result)>0)
			{
				$Result = mysql_fetch_assoc($Result);
				$OfferID = $Result['offer__id'];
			}
			
			$TempStat = new Stat($Click->clicks, 0, 0, '', $OfferID);
			$Output->addStatObject($TempStat);	
		}
		
		
		foreach ($ConversionsData as $Conversion)
		{
			$OfferID = '';
			$OfferName = mysql_real_escape_string($Conversion->offer);
			$Sql = "SELECT offer__id FROM bevomedia_offers WHERE (network__id = {$this->networkId}) AND (title = '{$OfferName}') "; 
			$Result = mysql_query($Sql);
			if (mysql_num_rows($Result)>0)
			{
				$Result = mysql_fetch_assoc($Result);
				$OfferID = $Result['offer__id'];
			}
			
			$Conversion->payout = str_replace('$', '', $Conversion->payout);
			
			$TempStat = new Stat(1, 1, $Conversion->payout, $Conversion->sub_id, $OfferID);
			$Output->addStatObject($TempStat);
		}
		
		return $Output;
	}



	/**
	 * Retrieve offers.
 	 */
	public function getOffers()
	{
		$Data = file_get_contents($this->offersApiUrl.'?api_key='.$this->offersApiKey);
		$Offers = json_decode($Data);
		$Offers = $Offers->data->offers;
		
		
		$Output = new OfferEnvelope();
		foreach($Offers as $Offer)
		{
			$OfferObj = new Offer();
			$OfferObj->offerId = $Offer->id;
			$OfferObj->name = $Offer->name;
			$OfferObj->description = $Offer->description;
			$OfferObj->expireDate = $Offer->expiration_date;
			$OfferObj->countries = explode(', ', $Offer->countries);
			$OfferObj->category = explode(', ', $Offer->categories);
			$OfferObj->payout = $Offer->payout;
			$OfferObj->type = $Offer->payout_type;
			$OfferObj->previewUrl = $Offer->preview_url;
			
			$OfferObj->offerType = 'Lead';
			if (strstr($Offer->payout, '%')) {
				$OfferObj->offerType = 'Sale';
			}
			
			$OfferObj->imageUrl = '';
			$OfferObj->dateAdded = date('Y-m-d');
			
			$Output->addOfferObject($OfferObj);
		}
		return $Output;
	}
	
	
}