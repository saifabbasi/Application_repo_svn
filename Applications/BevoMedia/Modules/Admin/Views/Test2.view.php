<?php
	require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');
	$Sql = 'SELECT id, title FROM bevomedia_aff_network WHERE model = "CPA"';
	$Result = mysql_query($Sql);
	$i = 0;
	
	date_default_timezone_set('America/New_York');
	while(false !== ($Row = mysql_fetch_assoc($Result)))
	{
		$NetworkID = $Row['id'];
		$Network = $Row['title'];
			
		$Network = str_replace('Affiliate.com', 'AffiliateDotCom', $Network);
		$Network = str_replace('NeverblueAds', 'NeverBlue', $Network);
		
		if($Network != 'AffiliateDotCom')
				continue;
			
		if(file_exists(ABSWEBSERVICESDIR . '/Network_Classes/' . $Network . '.php'))
		{
			echo $i . ':' . "\t" . 'FILE EXISTS: ' . $Network . "\n" . '<br/>';
			require_once(PATH . 'StatImport.class.php');
			require_once(ABSWEBSERVICESDIR . '/Network_Classes/' . $Network . '.php');
			$Network = new $Network();
			
			$Sql = "SELECT 
						user__id AS userId,
						loginId AS publisherLogin, 
						password AS publisherPassword, 
						otherId AS publisherId 
					FROM 
						bevomedia_user_aff_network
					WHERE
						network__id = {$NetworkID}";
			$UserResult = mysql_query($Sql);
			while(false !== ($UserRow = mysql_fetch_assoc($UserResult)))
			{
				$publisherLogin = $UserRow['publisherLogin'];
				$publisherPassword = $UserRow['publisherPassword'];
				$publisherId = $UserRow['publisherId'];
				$userId = $UserRow['userId'];
				
				if($Network->ApiName() == 'NeverBlue')
				{
					$publisherLogin = $publisherId;
				}
				
				print 'RETRIEVING STATS FOR (' . $publisherId . ') ' . $publisherLogin . ':' . $publisherPassword . '<br/>' . "\n";
				
				if($publisherLogin == '' || $publisherPassword == '')
				{
					print 'MOVING ON...' . "\n";
					continue;
				}
				$Network->setPublisherId($publisherId);
				$Network->setPublisherLogin($publisherLogin);
				$Network->setPublisherPassword($publisherPassword);
				$Network->login();
				$Stats = $Network->getStats('2010-01-15');
				if($Stats)
				{
					print_r($Stats);
					$StatImport = new StatImport($NetworkID, $userId);
					$StatImport->processStatEnvelope($Stats);
				}else{
					echo "\n" . 'STATS FAILED<br/>' . "\n";
				}
			}
			$i++;
		}
	}
	
	die;

?>	

<?php
	require_once('C:\wamp\www\beta.bevomedia.localhost\www\Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');
	$au = new apility_assist(23);
	$l = $au->getAllCampaigns();
	
	print_r($l);
	die;
	
	$msn = new msn_api('nickthrolson', 'beans611421');
	$r = $msn->getCampaigns();
	print "\n";
	print_r($r);
	
?>