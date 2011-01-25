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

    
    
//$networks[] = networkArray('Azoogle', 1000);
//$networks[] = networkArray('ClickBank', 1040);
//$networks[] = networkArray('MaxBounty', 1016);
$networks[] = networkArray('NeverBlue', 1006);
//$networks[] = networkArray('Ads4Dough', 1024, 'Hitpath');
//$networks[] = networkArray('Convert2Media', 1028, 'Hitpath');
//$networks[] = networkArray('W4', 1048, 'Hitpath');
//$networks[] = networkArray('AffiliateDotCom', 1030, 'DirectTrack');
//$networks[] = networkArray('ClickBooth', 1023, 'DirectTrack');
//$networks[] = networkArray('CommissionEmpire', 1046, 'DirectTrack');
//$networks[] = networkArray('Copeac', 1007, 'DirectTrack');
//$networks[] = networkArray('FluxAds', 1034, 'DirectTrack');
//$networks[] = networkArray('Rextopia', 1044, 'DirectTrack');
//$networks[] = networkArray('XY7', 1037, 'DirectTrack');
//$networks[] = networkArray('Adex', 1050, 'LinkTrust');
//$networks[] = networkArray('Adfinity', 1049, 'LinkTrust');
//$networks[] = networkArray('BlinkAds', 1051, 'LinkTrust');
//$networks[] = networkArray('Epicenter', 1054, 'LinkTrust');
//$networks[] = networkArray('EWA', 1052, 'LinkTrust');
//$networks[] = networkArray('FireLead', 1053, 'LinkTrust');
//$networks[] = networkArray('Sybarite', 1055, 'LinkTrust');
//$networks[] = networkArray('ProfitKingsMedia', 1056, 'LinkTrust');
//$networks[] = networkArray('Sybarite', 1055, 'LinkTrust');
//$networks[] = networkArray('ProfitKingsMedia', 1056, 'LinkTrust');
//$networks[] = networkArray('GetAds', 1057, 'LinkTrust');
//$networks[] = networkArray('EliteClicksMedia', 1058, 'Hitpath');
//$networks[] = networkArray('CPAStaxx', 1059, 'LinkTrust');
//$networks[] = networkArray('PeerFly', 1060);
//$networks[] = networkArray('DiabloMedia', 1061);
//$networks[] = networkArray('WolfStorm', 1062);
//$networks[] = networkArray('NDemand', 1063, 'LinkTrust');
//$networks[] = networkArray('LazyProfits', 1064, 'HasOffers');

foreach($networks as $network)
{
    try {
        if($Offers)
        {
            queueUpdateOffers($network);
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
