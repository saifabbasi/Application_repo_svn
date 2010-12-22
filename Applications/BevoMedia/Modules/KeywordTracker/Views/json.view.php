<?php

global $userId;
$userId = $this->User->id;

if($_GET['list'] == 'ppv_campaign')
{
	$filter_ppvprovider = 0;
	if(isset($_GET['provider']) && is_numeric($_GET['provider']))
		$filter_ppvprovider = (int)$_GET['provider'];
	
	$sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = ". $userId ." AND providerType = " . $filter_ppvprovider . " AND name != ''";
	$query = mysql_query($sql);
	$data = array();
	while($row = mysql_fetch_array($query))
		$data[] = $row;

	echo json_encode($data);
}

if($_GET['list'] == 'account')
{
	$filter_ppcprovider = 0;
	if(isset($_GET['ppcprovider']) && is_numeric($_GET['ppcprovider']))
		$filter_ppcprovider = (int)$_GET['ppcprovider'];
		
		

$sql = "SELECT 1 AS `providerId`,
			       _utf8'Google Adwords' AS `providerName`,
			       `Accounts_Adwords`.`id` AS `accountId`,
			       `Accounts_Adwords`.`user__id` AS `user__id`,
			       `Accounts_Adwords`.`username` AS `accountName`
			FROM 
				`bevomedia_accounts_adwords` `Accounts_Adwords`
			WHERE 
				(Accounts_Adwords.user__id = $userId) 
			UNION
			SELECT 2 AS `providerId`,
			       _utf8'Yahoo Search Marketing' AS `providerName`,
			       `Accounts_Yahoo`.`id` AS `accountId`,
			       `Accounts_Yahoo`.`user__id` AS `user__id`,
			       `Accounts_Yahoo`.`username` AS `accountName`
			FROM 
				`bevomedia_accounts_yahoo` `Accounts_Yahoo`
			WHERE
				(Accounts_Yahoo.user__id = $userId)
			UNION
			SELECT 3 AS `providerId`,
			       _utf8'Microsoft adCenter' AS `providerName`,
			       `Accounts_MSNAdCenter`.`id` AS `accountId`,
			       `Accounts_MSNAdCenter`.`user__id` AS `user__id`,
			       `Accounts_MSNAdCenter`.`username` AS `accountName`
			FROM 
				`bevomedia_accounts_msnadcenter` `Accounts_MSNAdCenter`
			WHERE				 
				(Accounts_MSNAdCenter.user__id = $userId) 
			ORDER BY
				providerName,
				accountName
";

	if ($filter_ppcprovider==1)
	{
		$Table = "bevomedia_accounts_adwords";
	} else
	if ($filter_ppcprovider==2)
	{	
		$Table = "bevomedia_accounts_yahoo";
	} else
	if ($filter_ppcprovider==3)
	{	
		$Table = "bevomedia_accounts_msnadcenter";
	}
	
	$sql = "SELECT
		       `id` AS `accountId`,
		       `username` AS `accountName`
			FROM
				{$Table}
			WHERE
				(user__id = {$userId}) AND
				(deleted = 0)
			";
				

//	$sql = "SELECT accountId, accountName FROM bevomedia_view_ppc_accounts WHERE user__id = ".$userId." AND providerId = ".$filter_ppcprovider." ORDER BY accountName";
	$query = mysql_query($sql);
	$data = array();
	while($row = mysql_fetch_array($query))
		$data[] = $row;

	echo json_encode($data);
}

if($_GET['list'] == 'campaign')
{
	$filter_ppcaccount = 0;
	if(isset($_GET['ppcaccount']) && is_numeric($_GET['ppcaccount']))
		$filter_ppcaccount = (int)$_GET['ppcaccount'];

	$sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = ".$userId." AND AccountID = ".$filter_ppcaccount." ORDER BY name";
	$query = mysql_query($sql);
	$data = array();
	while($row = mysql_fetch_array($query))
		$data[] = $row;

	echo json_encode($data);
}

if($_GET['list'] == 'adgroup')
{
	$filter_ppccampaign = 0;
	if(isset($_GET['ppccampaign']) && is_numeric($_GET['ppccampaign']))
		$filter_ppccampaign = (int)$_GET['ppccampaign'];

	if($filter_ppccampaign == '')
	{
		echo json_encode(array());
		return;
	}
	
	$sql = "SELECT 
    			bevomedia_ppc_adgroups.id, 
    			bevomedia_ppc_adgroups.name 
    		FROM 
    			bevomedia_ppc_adgroups,
    			bevomedia_ppc_campaigns
    		WHERE 
    			(bevomedia_ppc_campaigns.id = bevomedia_ppc_adgroups.campaignId) AND
    			(user__id = ".$userId.") AND 
    			(campaignId = ".$filter_ppccampaign.")
    		ORDER BY name";
		
	$query = mysql_query($sql);
	$data = array();
	while($row = mysql_fetch_array($query))
		$data[] = $row;

	echo json_encode($data);
}

if($_GET['list'] == 'advar')
{
	$filter_ppcadgroup = 0;
	if(isset($_GET['ppcadgroup']) && is_numeric($_GET['ppcadgroup']))
		$filter_ppcadgroup = (int)$_GET['ppcadgroup'];

	if($filter_ppcadgroup == '')
	{
		echo '';
		return;
	}
	$sql = "SELECT id, title FROM bevomedia_ppc_advariations WHERE adGroupId = ".$filter_ppcadgroup." ORDER BY title";
	$query = mysql_query($sql);
	$data = array();
	while($row = mysql_fetch_array($query))
		$data[] = $row;

	echo json_encode($data);
}

exit;