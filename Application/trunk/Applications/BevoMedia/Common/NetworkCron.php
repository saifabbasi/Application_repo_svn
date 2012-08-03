<?php
//Params
$Yesterday = false;
$Offers = false;
if(isset($argv) && sizeof($argv)>1)
{
	if (in_array('Yesterday', $argv))
		$Yesterday = true;
	if (in_array('Offers', $argv))
		$Offers = true;
}
else
{
    if(isset($_GET['Yesterday']))
        $Yesterday = true;
    if(isset($_GET['Offers']))
        $Offers = true;
}

require_once('AbsoluteIncludeHelper.include.php');
require_once('GetNetworkStats.php');
require_once('OfferImport.class.php');
require_once('StatImport.class.php');
require_once('User.class.php');

// Get the networks
$networks = array();
function networkArray($name, $id, $type = false) {
    return array('name' => $name, 'id' => $id, 'type' => $type); }

    

$networks[] = networkArray('PaydayPays', 1071);
$networks[] = networkArray('BlamAds', 1069, 'HasOffers');
$networks[] = networkArray('DanDingo', 1068);
$networks[] = networkArray('AdDrive', 1067);
$networks[] = networkArray('FeedFlare', 1066);
$networks[] = networkArray('CPAWay', 1065);
$networks[] = networkArray('Azoogle', 1000);
$networks[] = networkArray('ClickBank', 1040);
$networks[] = networkArray('MaxBounty', 1016);
$networks[] = networkArray('NeverBlue', 1006);
$networks[] = networkArray('Ads4Dough', 1024, 'Hitpath');
$networks[] = networkArray('Convert2Media', 1028, 'Hitpath');
$networks[] = networkArray('W4', 1048, 'Hitpath');
$networks[] = networkArray('AffiliateDotCom', 1030, 'DirectTrack');
$networks[] = networkArray('ClickBooth', 1023, 'CakeMarketing');
$networks[] = networkArray('CommissionEmpire', 1046, 'DirectTrack');
$networks[] = networkArray('Copeac', 1007, 'DirectTrack');
$networks[] = networkArray('FluxAds', 1034, 'CakeMarketing');
$networks[] = networkArray('Rextopia', 1044, 'DirectTrack');
$networks[] = networkArray('XY7', 1037, 'DirectTrack');
$networks[] = networkArray('Adex', 1050, 'LinkTrust');
$networks[] = networkArray('Adfinity', 1049, 'LinkTrust');
$networks[] = networkArray('BlinkAds', 1051, 'LinkTrust');
$networks[] = networkArray('Epicenter', 1054, 'LinkTrust');
$networks[] = networkArray('EWA', 1052, 'LinkTrust');
$networks[] = networkArray('FireLead', 1053, 'LinkTrust');
$networks[] = networkArray('Sybarite', 1055, 'LinkTrust');
$networks[] = networkArray('ProfitKingsMedia', 1056, 'LinkTrust');
$networks[] = networkArray('Sybarite', 1055, 'LinkTrust');
$networks[] = networkArray('ProfitKingsMedia', 1056, 'LinkTrust');
$networks[] = networkArray('GetAds', 1057, 'LinkTrust');
//$networks[] = networkArray('EliteClicksMedia', 1058, 'Hitpath');
$networks[] = networkArray('CPAStaxx', 1059, 'LinkTrust');
$networks[] = networkArray('PeerFly', 1060);
$networks[] = networkArray('DiabloMedia', 1061);
$networks[] = networkArray('WolfStorm', 1062, 'CakeMarketing');
$networks[] = networkArray('CPAProsperity', 1070, 'CakeMarketing');
$networks[] = networkArray('NDemand', 1063, 'LinkTrust');
$networks[] = networkArray('LazyProfits', 1064, 'HasOffers');
$networks[] = networkArray('CPAProsperity', 1070, 'CakeMarketing');
$networks[] = networkArray('EnvyusMedia', 1072, 'CakeMarketing');
$networks[] = networkArray('KonceptAds', 1073, 'HasOffers');
$networks[] = networkArray('BMIElite', 1074, 'CakeMarketing');
$networks[] = networkArray('CommissionJunction', 1038);
$networks[] = networkArray('BlueGlobalMedia', 1075, 'Hitpath');
$networks[] = networkArray('CrushAds', 1076, 'CakeMarketing');
$networks[] = networkArray('AboveAllOffers', 1077, 'CakeMarketing');
$networks[] = networkArray('AdGate', 1078, 'HasOffers');
$networks[] = networkArray('Essociate', 1079);
$networks[] = networkArray('CPATank', 1080, 'CakeMarketing');
$networks[] = networkArray('UniqueLeads', 1081, 'CakeMarketing');
$networks[] = networkArray('eFlowAds', 1082, 'Hitpath');
$networks[] = networkArray('RevenueAds', 1083, 'CakeMarketing');
$networks[] = networkArray('eMediaTraffic', 1084, 'HasOffers');
$networks[] = networkArray('AffiliatiNetwork', 1085, 'CakeMarketing');
$networks[] = networkArray('ClickWinks', 1086, 'DirectTrack');
$networks[] = networkArray('Sterkly', 1087, 'CakeMarketing');
$networks[] = networkArray('ClickRover', 1088, 'HasOffers');
$networks[] = networkArray('GetOffersDirect', 1089, 'CakeMarketing');
$networks[] = networkArray('KissMyAds', 1090, 'HasOffers');
$networks[] = networkArray('AdCommunal', 1091, 'AdCommunal');
$networks[] = networkArray('AdMobix', 1092, 'AdCommunal');
$networks[] = networkArray('AdIndian', 1093, 'AdCommunal');
$networks[] = networkArray('AdCanadian', 1094, 'AdCommunal');
$networks[] = networkArray('Jexo', 1095, 'LinkTrust');
$networks[] = networkArray('MediaForce', 1096, 'HasOffers');
$networks[] = networkArray('Leadnomics', 1097, 'HasOffers');
$networks[] = networkArray('InAds', 1098, 'LinkTrust');
$networks[] = networkArray('ClickDealer', 1099, 'HasOffers');

foreach($networks as $network)
{
    try {
        if($Offers)
        {
        	// TODO: Implement a more efficient method for offer retrieval for CPAWay
        	if($network['name'] == 'CPAWay') {
        		// NOOP
        	}else{
	            queueUpdateOffers($network);
        	}
        }
        else
        {
            if(!$Yesterday)
                queueUpdateStats($network);
            else
                queueUpdateStats($network, date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 1, date("Y"))));
        }
    } catch (Exception $e) {
    	var_dump($e);
    }
}
	
?>
