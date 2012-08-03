<?php

	require_once('Networks.Abstract.php');

	class RevenueStreetAbstract extends NetworksAbstract  {
		
		/**
		 * @access protected
		 * @var string $offersUrl
		 */
		protected $offersUrl = '';
		
		/**
		 * @access protected
		 * @var string $offersAffiliateId
		 */
		protected $offersAffiliateId = '';
		
		/**
		 * @access protected
		 * @var string $offersApiKey
		 */
		protected $offersApiKey = '';
		
		Public Function login() {}
		
		public function getOffers()
		{
		    $api_function_call = $this->offersUrl;
		 
		    $post_params = array(
		        'User_ID' => $this->offersAffiliateId,
		        'API_Key' => $this->offersApiKey
		    );    
		 
		    $ch = curl_init();
		 
		    curl_setopt($ch, CURLOPT_URL, $api_function_call);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
		 
		    $offers = curl_exec($ch);
		 
		    curl_close($ch);
		    
		
			libxml_use_internal_errors(true);
			$xml = simplexml_load_string($offers);
			
			if (!$xml)
			{
				return;
			}

			$Output = new OfferEnvelope();
			foreach ($xml->record as $offer)
			{
				$offerId = $offer->id;
				$offerName = $offer->name;
				$payout = $offer->payment_amount_1_1;
				$previewUrl = $offer->lp_url;
				$description = $offer->notes;
				
				$offerType = 'Lead';
				if (strstr($payout, '%')) {
					$offerType = 'Sale';
				}
				
				
				$thumbnailImageUrl = '';
				
				if (isset($offer->countries_full))
				foreach ($offer->countries_full->record as $country)
				{
					$countries[] = $country->title;
				}
				
				if (isset($offer->categories_full))
				foreach ($offer->categories_full->record as $category)
				{
					$categories[] = $category->title;
				}
				
				
				$OfferObj = new Offer();
				$OfferObj->offerId = $offerId;
				$OfferObj->name = $offerName;
				$OfferObj->description = $description;
				$OfferObj->previewUrl = $previewUrl;
				$OfferObj->imageUrl = '';
				$OfferObj->offerContractId = $offerContract;
								
				$OfferObj->offerType = $offerType;
				
				$OfferObj->payout = str_replace('$', '', $payout);
				$OfferObj->dateAdded = date('Y-m-d');
				$OfferObj->countries = $countries;
				$OfferObj->category = $categories;

				$Output->addOfferObject($OfferObj);
			}
			
			return $Output;
		}
		
		public function getStats($Date = '')
		{
		    $StartDate = $EndDate = $Date;
			if (empty($Date))
			{
				$StartDate = date('Y-m-d');
				$EndDate = date('Y-m-d 23:59:59');
			}
			
			if ($EndDate==date('Y-m-d', strtotime($EndDate))) 
			{
				$EndDate = $EndDate.' 23:59:59';
			}
			
			$StartDate = date('m/d/Y', strtotime($StartDate));
			$EndDate = date('m/d/Y', strtotime($EndDate));
			
			$url = $this->apiUrl;
			
			// API Function URL
		    $api_function_call = $url;
		 
		    // API Parameters
		    $post_params = array(
		        'User_ID' => $this->publisherLogin,
		        'API_Key' => $this->publisherId,
		        'date_from' => $StartDate,
		        'date_to' => $EndDate,
		        'breakdown' => 2
		    );    
		 
		    $ch = curl_init();
		 
		    curl_setopt($ch, CURLOPT_URL, $api_function_call);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
		 
		    $result = curl_exec($ch);
			
		   
			libxml_use_internal_errors(true);
			$xml = simplexml_load_string($result);
			if (!$xml)
			{
				return;
			}
			
			
			$Output = new StatEnvelope($Date);
			
			foreach ($xml->record as $record)
			{
				$clicks = (int) $record->c;
				$conversions = (float) $record->s;
				$revenue = (float) $record->revenue;
				$offerId = (int) $record->offer_id;
				
				$TempStat = new Stat($clicks, $conversions, $revenue, '', $offerId);
   				$Output->addStatObject($TempStat);
   				
			}
			return $Output;
		}
		
	}