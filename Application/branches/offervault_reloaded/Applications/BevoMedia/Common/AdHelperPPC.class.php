<?php

/**
 * AdHelperPPC Class
 */

/**
 * Helper class used to check, retrieve and modify Ad Variation destination URL's to properly work with the BevoMedia keyword tracker.
 *
 * This class is used to check if the Ad Variation destination URL has the proper variables that will be used to gather statistic information using the BevoMedia keyword tarcker.
 * The PPC Providers that use this functionality are Google Adwords and MSN Ad Center.  Yahoo does not require modified URL's for statistic tracking.
 * @category    BevoMedia
 * @package     Application
 * @subpackage  Common
 * @copyright   Copyright (c) 2009 RCS
 * @author		RCS
 * @version     0.1
 */

Class AdHelperPPC
{
	/**
	 * Table name to provider type associative array.
	 *
	 * @var Array $TableName
	 */
	Private $TableName = array(1=>'adwords', 2=>'yahoo', 3=>'msnadcenter');
	
	/**
	 * @var Integer $User_ID
	 */
	Public $User_ID;
	
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Private $_db;

	/**
	 * Constructor
	 *
	 * @param Integer $User_ID
	 */
	Public Function __construct($User_ID = false)
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		
		if($User_ID !== false)
		{
			$this->User_ID = $User_ID;
		}
	}
	
	/**
	 * Checks to see if provided URL contains the parameters for specified $providerType.
	 * If $ReturnOptimized is true, this will return the optimized URL instead of a boolean value.
	 *
	 * <pre>
	 * MSN:		http://landingpage.com/index.php?	bevo_r={QueryString}&bevo_k={keyword}&bevo_c={AdId}&bevo_m={MatchType}
	 * Adwords:	http://landingpage.com/index.php?	bevo_k={keyword}&bevo_c={creative}&bevo_m={ifsearch:s}{ifcontent:c}
	 * @param String $Url
	 * @param Integer $providerType
	 * @param Boolean $ReturnOptimized
	 * @return Mixed
	 */
	Public Function CheckAdURL($Url, $providerType, $ReturnOptimized = false)
	{
		$Output = true;
		$QueryVars = array(	'1'=>array('bevo_k'=>'{keyword}', 'bevo_c'=>'{creative}', 'bevo_m'=>'{ifsearch:s}{ifcontent:c}'),
							'2'=>array(),
							'3'=>array('bevo_r'=>'{QueryString}', 'bevo_k'=>'{keyword}', 'bevo_c'=>'{AdId}', 'bevo_m'=>'{MatchType}')
						);
		$Url = explode('?', $Url);
		$Base = $Url[0];
		if(!isset($Url[1]))
		{
			$Url[1] = '';
		}

		$Query = array();
		if(strlen($Url[1]) > 0)
		{
			$Temp = explode('&', $Url[1]);
			foreach($Temp as $Value)
			{
				$Value = explode('=', $Value);
				$Query[$Value[0]] = @$Value[1] ? @$Value[1] : '';
			}
		}

		if(isset($QueryVars[$providerType]))
		{
		    foreach($QueryVars[$providerType] as $Key=>$Value)
    		{
    			if(!in_array($Key, array_keys($Query)))
    			{
    				$Query[$Key] = $Value;
    				$Output = false;
    			}
		    }
		}
		$OutUrl = $Base . '?';
		foreach($Query as $Key=>$Value)
		{
			$OutUrl .= $Key . '=' . $Value . '&';
		}
		$OutUrl = substr($OutUrl, 0, -1);
		
		if($ReturnOptimized == true)
		{
			return $OutUrl;
		}
		
		return $Output;

	}
	
	/**
	 * Returns the provided $Rows as an associative tiered array where Campaigns contain Ad Groups which contain Ad Variations.
	 * @todo Provide example.
	 *
	 * @param Array $Rows
	 * @return Array
	 */
	Public Function FormatRowsAsCampaignArray($Rows)
	{
		$Output = array();
		
		foreach($Rows as $Row)
		{
			if(!in_array($Row->CampaignName, array_keys($Output)))
			{
				$Output[$Row->CampaignName] = array();
			}
			
			if(!in_array($Row->AdGroupName, array_keys($Output[$Row->CampaignName])))
			{
				$Output[$Row->CampaignName][$Row->AdGroupName] = array('Rows'=>array(), 'Optimized'=>array(), 'NeedToBeOptimized'=>array());
			}
			
			$Row->optimizedUrl = $this->CheckAdURL($Row->url, $Row->providerType, true);
			$Output[$Row->CampaignName][$Row->AdGroupName]['Rows'][] = $Row;
			if($this->CheckAdURL($Row->url, $Row->providerType))
			{
				$Output[$Row->CampaignName][$Row->AdGroupName]['Optimized'][] = $Row;
			}else{
				$Output[$Row->CampaignName][$Row->AdGroupName]['NeedToBeOptimized'][] = $Row;
			}
		}
				
		return $Output;
	}
	
	/**
	 * Returns all rows that match the specified $providerType which belong to this $User_ID.
	 *
	 * @param Integer $providerType
	 * @return Array
	 */
	Public Function LoadAdsByProvider($providerType)
	{
		$ProviderTable = $this->TableName[$providerType];
		$Sql = "SELECT bevomedia_ppc_advariations.*, bevomedia_ppc_campaigns.Name as CampaignName, bevomedia_ppc_adgroups.Name as AdGroupName, bevomedia_ppc_campaigns.providerType, bevomedia_ppc_campaigns.accountId
				FROM bevomedia_ppc_advariations
				LEFT JOIN bevomedia_ppc_adgroups ON bevomedia_ppc_advariations.AdGroupID = bevomedia_ppc_adgroups.ID
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
				LEFT JOIN bevomedia_accounts_$ProviderTable ON bevomedia_ppc_campaigns.accountId = bevomedia_accounts_$ProviderTable.ID
				WHERE bevomedia_ppc_campaigns.user__id = {$this->User_ID} AND bevomedia_ppc_campaigns.providerType = $providerType
				GROUP BY apiAdId
				ORDER BY CampaignID, AdGroupID
				";
		
		$Rows = $this->_db->fetchAll($Sql);
		
		return $Rows;
	}
	
	/**
	 * Returns all rows that belong to the specified $AdGroupID.
	 *
	 * @param Integer $AdGroupID
	 * @return Array
	 */
	Public Function LoadAdsByAdGroup($AdGroupID)
	{
		$Sql = "SELECT bevomedia_ppc_advariations.*, bevomedia_ppc_campaigns.Name as CampaignName, bevomedia_ppc_adgroups.Name as AdGroupName, bevomedia_ppc_campaigns.providerType, bevomedia_ppc_campaigns.accountId
				FROM bevomedia_ppc_advariations
				LEFT JOIN bevomedia_ppc_adgroups ON bevomedia_ppc_advariations.AdGroupID = bevomedia_ppc_adgroups.ID
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
				WHERE bevomedia_ppc_adgroups.ID = $AdGroupID
				GROUP BY apiAdId
				ORDER BY CampaignID, AdGroupID
				";
		
		$Rows = $this->_db->fetchAll($Sql);
		
		return $Rows;
	}
	
	/**
	 * Returns a single Ad Variation which matches the specified $ID.
	 *
	 * @param Integer $ID
	 * @return Zend_DB_Table_Row
	 */
	Public Function LoadSingleAdById($ID)
	{
		$Sql = "SELECT bevomedia_ppc_advariations.*, bevomedia_ppc_campaigns.Name as CampaignName, bevomedia_ppc_adgroups.Name as AdGroupName, bevomedia_ppc_campaigns.providerType
				FROM bevomedia_ppc_advariations
				LEFT JOIN bevomedia_ppc_adgroups ON bevomedia_ppc_advariations.AdGroupID = bevomedia_ppc_adgroups.ID
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
				WHERE bevomedia_ppc_advariations.ID = $ID
				GROUP BY apiAdId
				ORDER BY CampaignID, AdGroupID
				";
		
		$Rows = $this->_db->fetchAll($Sql);
		$providerType = $Rows[0]->providerType;
		
		$ProviderTable = $this->TableName[$providerType];
		$Sql = "SELECT bevomedia_ppc_advariations.*, bevomedia_ppc_campaigns.Name as CampaignName, bevomedia_ppc_adgroups.Name as AdGroupName, bevomedia_ppc_campaigns.providerType, Accounts_$ProviderTable.ID as accountId
				FROM bevomedia_ppc_advariations
				LEFT JOIN bevomedia_ppc_adgroups ON bevomedia_ppc_advariations.AdGroupID = bevomedia_ppc_adgroups.ID
				LEFT JOIN bevomedia_ppc_campaigns ON bevomedia_ppc_adgroups.CampaignID = bevomedia_ppc_campaigns.ID
				LEFT JOIN bevomedia_accounts_$ProviderTable ON bevomedia_ppc_campaigns.accountId = bevomedia_accounts_$ProviderTable.ID
				WHERE bevomedia_ppc_advariations.ID = $ID
				GROUP BY apiAdId
				ORDER BY CampaignID, AdGroupID
				";
				
		$Rows = $this->_db->fetchAll($Sql);
		return $Rows[0];
	}
	
	/**
	 * Modify the destination URL to $NewURL of an ad variation matching the provided $AdVariation_ID.
	 *
	 * @param Integer $AdVariation_ID
	 * @param String $NewURL
	 */
	Public Function UpdateAdWithOptimizedURL($AdVariation_ID, $NewURL)
	{
		$Sql = "UPDATE bevomedia_ppc_advariations SET url = '$NewURL' WHERE id = $AdVariation_ID";
		$this->_db->exec($Sql);
		
	}
	
	
}



?>
