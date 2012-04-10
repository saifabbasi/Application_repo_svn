<?php

class OfferImport {
	private $offersTableName = 'bevomedia_offers';
	private $categoryTableName = 'bevomedia_category';
	private $multipleCategoryTableName = 'bevomedia_mcategorie';
	private $multipleCategoryToOffersTableName = 'bevomedia_mcategorie_offers';
	private $multipleCountryTableName = 'bevomedia_mcountry';
	private $multipleCountryToOffersTableName = 'bevomedia_mcountry_offers';
	
	private $tableToOfferBinding = array(
		'offer__id'=>'offerId', 'title'=>'name', 'detail'=>'description', 'launchedOn'=>'openDate',
		'expiresOn'=>'expireDate', 'trackingCodesUrl'=>'trackUrl', 'payout'=>'payout', 'epc'=>'ecpc',
		'imageUrl' => 'imageUrl', 'offerType' => 'offerType', 'dateAdded' => 'dateAdded',
		'previewUrl' => 'previewUrl', 'cakeStatus' => 'cakeStatus', 'cakeCreativeId' => 'creativeInfo', 'advertiserExtendedTerms' => 'advertiserExtendedTerms'
	);
	private $networkId = 0;
	private $userId = 0;
	
	public function __construct($NetworkID)
	{
		$this->networkId = $NetworkID;
		
		$sql = "UPDATE ".$this->offersTableName." SET archived = 1 WHERE edited = 0 AND network__id = " . $this->networkId;
		mysql_query($sql);
	}
	
	public function insertOffer($OfferObj)
	{
		$Verticales['Insurance'] = array('Insurance');
		$Verticales['Business'] = array('budget', 'print', 'card');
		$Verticales['Weight Loss'] = array('Weight', 'colon', 'acai', 'diet');
		$Verticales['Mobile'] = array('phone', 'mobile', 'pin', 'ringtone', 'ringtones', 'quiz');
		$Verticales['Freebie'] = array('freebie', 'sweepstakes', 'freebies', 'apple', 'ipad', 'ipod', 'macbook', 'laptop');
		$Verticales['Credit'] = array('credit');
		$Verticales['Legal'] = array('Legal');
		$Verticales['Bizopp'] = array('Bizopp', 'Biz opp');
		$Verticales['Dating'] = array('singles', 'single', 'dating', 'match', 'amateur', 'women');
		$Verticales['Gaming and Auctions'] = array('Auction', 'Penny');
		$Verticales['Financial'] = array('financial', 'debt', 'payday', 'loan', 'loans');
		$Verticales['Health'] = array('skin', 'teeth', 'whitening', 'anti age', 'antiage', 'beauty', 'health', 'resv', 'resveratrol', 'hair');
		$Verticales['Education'] = array('Education', 'edu', 'class', 'classes', 'courses', 'college', 'university');
		$Verticales['Incentive'] = array('Incentive');
		$Verticales['Downloads'] = array('Download', 'install');
		$Verticales['Zip'] = array('Zip');
				
				
				
		$Sql = "SELECT id, offer__id, edited FROM " . $this->offersTableName . " WHERE offer__id= '" . $OfferObj->offerId . "' AND network__id = '" . $this->networkId . "'";
		$Result = mysql_query($Sql);
		if(mysql_num_rows($Result)>0)
		{
			$Row = mysql_fetch_assoc($Result);
			$OfferInsertID = $Row['id'];
			$Edited = $Row['edited'];
			
			if ($Edited==1) {
				return;
			}
			
			
			$UpdateSql = '';
			foreach($this->tableToOfferBinding as $columnName=>$offerParam)
			{
				if (isset($OfferObj->{$offerParam}) && ($OfferObj->{$offerParam} != NULL))
				{
					$UpdateSql .= $columnName . " = '" . mysql_escape_string($OfferObj->{$offerParam}) . "', ";
				}
			}
			
			
			$CategoryID = 0;
			if(sizeof($OfferObj->category)>0)
			{
				$CategoryID = $this->getCategoryId($OfferObj->category[0]);
			} else {
				$Title = $OfferObj->name;
				$Description = $OfferObj->description;
				
				
				foreach ($Verticales as $VerticalName => $VerticalKeywords)
				{
					$found = false;
					foreach ($VerticalKeywords as $VerticalKeyword) {
						if (stristr($Title, $VerticalKeyword) || stristr($Description, $VerticalKeyword)) 
						{
							$found = true;
							$CategoryID = $this->getCategoryId($VerticalName);
							break;
						}
					}
					if ($found) break;
				}
			}
			
			
			
			$UpdateSql .= "network__id = '" . $this->networkId . "', category__id = ".intval($CategoryID);
			
			if (isset($OfferObj->offerContractId))
			{
				$UpdateSql .= ", offerContractID = " . $OfferObj->offerContractId;
			}
			
			$Sql = "UPDATE " . $this->offersTableName . " SET " . $UpdateSql . ", archived = 0 WHERE id = '" . $OfferInsertID . "'";
		
			$Result = mysql_query($Sql);
			if(!$Result){
				die(mysql_error());
			}
		}else{
			$ColumnNames = '';
			$ColumnValues = '';
			foreach($this->tableToOfferBinding as $columnName=>$offerParam)
			{
				if (isset($OfferObj->{$offerParam}) && ($OfferObj->{$offerParam} != NULL))
				{
					$ColumnNames .= $columnName . ',';
					$ColumnValues .= "'" . mysql_escape_string($OfferObj->{$offerParam}) . "',";
				}
			}
			
			$CategoryID = 0;
			if(sizeof($OfferObj->category)>0)
			{
				$CategoryID = $this->getCategoryId($OfferObj->category[0]);
			} else {
				$Title = $OfferObj->name;
				$Description = $OfferObj->description;
				
				
				
				foreach ($Verticales as $VerticalName => $VerticalKeywords)
				{
					$found = false;
					foreach ($VerticalKeywords as $VerticalKeyword) {
						if (stristr($Title, $VerticalKeyword) || stristr($Description, $VerticalKeyword)) 
						{
							$found = true;
							$CategoryID = $this->getCategoryId($VerticalName);
							break;
						}
					}
					if ($found) break;
				}
			}
			
			$ColumnNames .= 'network__id,user__id,category__id';
			$ColumnValues .= $this->networkId . ',' . $this->userId . ',' . $CategoryID;
			
			if (isset($OfferObj->offerContractId))
			{
				$ColumnNames .= ',offerContractID';
				$ColumnValues .= ', '.$OfferObj->offerContractId;
			}
			
			$Sql = "INSERT INTO " . $this->offersTableName . " (" . $ColumnNames . ") VALUES (" . $ColumnValues . ")";
			
			$Result = mysql_query($Sql);
			if(!$Result){
				die(mysql_error());
			}
			$OfferInsertID = mysql_insert_id();
		}
		
		if(sizeof($OfferObj->category)>1)
		{
			$Sql = "DELETE FROM " . $this->multipleCategoryToOffersTableName . " WHERE offer__id = '" . $OfferInsertID . "'";
			$Result = mysql_query($Sql);
			
			foreach($OfferObj->category as $CategoryName)
			{
				$Sql = "INSERT INTO " . $this->multipleCategoryToOffersTableName . " (mCategorie__id,offer__id) VALUES (" . $this->getMultipleCategoryId($CategoryName) . "," . $OfferInsertID . ")";
				mysql_query($Sql);
			}
		}
		
		if(sizeof($OfferObj->countries)>0)
		{
			$Sql = "DELETE FROM " . $this->multipleCountryToOffersTableName . " WHERE offer__id = '" . $OfferInsertID . "'";
			$Result = mysql_query($Sql);
			
			foreach($OfferObj->countries as $CountryName)
			{
				$Sql = "INSERT INTO " . $this->multipleCountryToOffersTableName . " (mCountry__id,offer__id) VALUES (" . $this->getMultipleCountryId($CountryName) . "," . $OfferInsertID . ")";
				mysql_query($Sql);
			}
		}
	}
	
	private function getCategoryId($CategoryName)
	{
		$Sql = "SELECT id FROM " . $this->categoryTableName . " WHERE title = '" . mysql_escape_string($CategoryName) . "'";
		$Result = mysql_query($Sql);
		if(!mysql_num_rows($Result))
		{
			return $this->insertCategory($CategoryName);
		}
		$Row = mysql_fetch_assoc($Result);
		return $Row['id'];
	}
	
	private function insertCategory($CategoryName)
	{
		$Sql = "INSERT INTO " . $this->categoryTableName . " (title) VALUES ('" . mysql_escape_string($CategoryName) . "')";
		mysql_query($Sql);
		return mysql_insert_id();
	}
	
	private function getMultipleCategoryId($CategoryName)
	{
		$Sql = "SELECT id FROM " . $this->multipleCategoryTableName. " WHERE mCategorie = '" . mysql_escape_string($CategoryName) . "'";
		$Result = mysql_query($Sql);
		if(!mysql_num_rows($Result))
		{
			return $this->insertMultipleCategory($CategoryName);
		}
		$Row = mysql_fetch_assoc($Result);
		return $Row['id'];
	}
	
	private function insertMultipleCategory($CategoryName)
	{
		$Sql = "INSERT INTO " . $this->multipleCategoryTableName . " (mCategorie) VALUES ('" . mysql_escape_string($CategoryName) . "')";
		mysql_query($Sql);
		return mysql_insert_id();
	}
	
	private function getMultipleCountryId($CountryName)
	{
		$Sql = "SELECT id FROM " . $this->multipleCountryTableName. " WHERE mCountry = '" . mysql_escape_string($CountryName) . "'";
		$Result = mysql_query($Sql);
		if(!mysql_num_rows($Result))
		{
			return $this->insertMultipleCountry($CountryName);
		}
		$Row = mysql_fetch_assoc($Result);
		return $Row['id'];
	}
	
	private function insertMultipleCountry($CountryName)
	{
		$Sql = "INSERT INTO " . $this->multipleCountryTableName . " (mCountry) VALUES ('" . mysql_escape_string($CountryName) . "')";
		mysql_query($Sql);
		return mysql_insert_id();
	}
}


?>
