<?php
require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');

require_once(PATH . 'CatchUpSql.class.php');
$ch = new CatchUpSql(1);

$ch->process('2010-04-01');


exit();



$msn = new Accounts_Yahoo(1);
$msn->GetInfo(11);
print(int)$msn->VerifyAccountAPI();

//$l = $msn->getAPI()->getActiveCampaigns();
$l = $msn->getAPI()->getAllCampaigns();
print_r($l);
exit;
$c = $msn->GetAPI()->getCampaigns();
print sizeof($c);
$l = $msn->GetAPI()->getAssignedQuota();
print_r($l);
$r = $msn->GetAPI()->getRemainingQuota();
print_r($r);
print_r($msn);


exit;


require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');
require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'AffiliateNetworkStatService.class.php');

$AffNetStatServ = new AffiliateNetworkStatService(1024, 'ryan@bevomedia.com', 'yoyoyo1025');
print (int)$AffNetStatServ->login();

//1024	Ads4Dough



exit;
	require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');
	//require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');

	$Account = new Accounts_Adwords(1);
	$Account->getInfo(6);
	
	//$Account->addCampaignAPI('Test Campaign', 5000, 'Some Description');

	$jsonArgs = '["AddCampaignAPI","Test New System 18","630","",{"countries":["AF","DZ","AS","CL","CN","CC","EE","PY","SK","UG"]},[""],"SearchContent"]';
	$TempJSON = new Services_JSON();
	$Args = $TempJSON->decode($jsonArgs);
	$Func = $Args[0];
	array_shift($Args);
	foreach($Args as $Key=>$Val)if($Val == '$Tempoutput')eval('$Args[$Key] = ' . $Val . ';');
	//$Tempoutput = call_user_func_array(array($Account, $Func), $Args);
	//print_r($Tempoutput);
	$Tempoutput = 154433;
	$jsonArgs = '["AddAdGroupAPI","Test Ad Group","$Tempoutput","1","Search",["AdGroup","Negative","Keywords"],"2"]';
	$TempJSON = new Services_JSON();
	$Args = $TempJSON->decode($jsonArgs);
	$Func = $Args[0];
	array_shift($Args);
	foreach($Args as $Key=>$Val)if($Val == '$Tempoutput')eval('$Args[$Key] = ' . $Val . ';');
	//$Tempoutput = call_user_func_array(array($Account, $Func), $Args);
	/*echo (is_float($Tempoutput)?'{'.$Func.'}Success.'."
	":('{'.$Func.'}Error:' . $Tempoutput ."
	"));*/
	$Tempoutput = 3184260181;
	$jsonArgs = '["AddKeywordAPI","\"phrase\"","1.0","http://www.test.com","$Tempoutput","0"]';
	$TempJSON = new Services_JSON();
	$Args = $TempJSON->decode($jsonArgs);
	$Func = $Args[0];
	array_shift($Args);
	foreach($Args as $Key=>$Val)if($Val == '$Tempoutput')eval('$Args[$Key] = ' . $Val . ';');
	$Tempoutput = call_user_func_array(array($Account, $Func), $Args);
		
	print_r($Tempoutput);
	die;

	//$Account->addCampaignNegativeKeywords(154433, array("Campaign","Negative","Keywords"));
	
	$c = $Account->getCampaignsAPI();
	foreach($c as $campaign)
	{
		print $campaign->id . ': ' . $campaign->name . ' ' . $campaign->status . '<br/>';
		print_r($campaign);
		print '<br/><br/>';
	}
	
	//require_once(PATH . 'adwords_api' . DIRECTORY_SEPARATOR . 'adwords_import.php');
	//update(1, 50);
	
	
	exit;
?>

<?php 	
	$Sql = 'SELECT id, title FROM bevomedia_aff_network WHERE model = "CPA"';
	$Result = mysql_query($Sql);
	$i = 0;
	$offerCount = 0;
	
	date_default_timezone_set('America/New_York');
	while(false !== ($Row = mysql_fetch_assoc($Result)))
	{
		$NetworkID = $Row['id'];
		$Network = $Row['title'];
			
		$Network = str_replace('Affiliate.com', 'AffiliateDotCom', $Network);
		$Network = str_replace('NeverblueAds', 'NeverBlue', $Network);
		
		//if($Network != 'NeverBlue')
			//continue;
			
		if(file_exists(ABSWEBSERVICESDIR . '/Network_Classes/' . $Network . '.php'))
		{
			echo $i . ':' . "\t" . 'FILE EXISTS: ' . $Network . '<br/>';
			require_once(PATH . 'OfferImport.class.php');
			require_once(ABSWEBSERVICESDIR . '/Network_Classes/' . $Network . '.php');
			$Network = new $Network();
			$OfferEnvelope = $Network->getOffers();
			$OfferImport = new OfferImport($NetworkID);
			
			foreach($OfferEnvelope->Offers() as $Offer)
			{
				$OfferImport->insertOffer($Offer);
			}
			$offerCount += sizeOf($OfferEnvelope->Offers());
			echo "\t" . '>> OFFERS IMPORTED: ' . sizeOf($OfferEnvelope->Offers()) . '<br/>';
			$i++;
		}
	}
	echo '<br/>' . 'TOTAL OFFERS IMPORTED: ' . $offerCount . '<br/>';
	die;
?>