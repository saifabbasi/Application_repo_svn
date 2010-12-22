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
if($Yesterday)
  echo " --- YESTERDAY --- \n";
require_once('AbsoluteIncludeHelper.include.php');
require_once('GetNetworkStats.php');
require_once('OfferImport.class.php');
require_once('StatImport.class.php');
require_once('User.class.php');

// Get the networks
$networks = array();
function networkArray($name, $id, $type = false) {
    return array('name' => $name, 'id' => $id, 'type' => $type); }

$networks[] = networkArray('Azoogle', 1000);
$networks[] = networkArray('ClickBank', 1040);
$networks[] = networkArray('MaxBounty', 1016);
$networks[] = networkArray('NeverBlue', 1006);
$networks[] = networkArray('Ads4Dough', 1024, 'Hitpath');
$networks[] = networkArray('Convert2Media', 1028, 'Hitpath');
$networks[] = networkArray('W4', 1048, 'Hitpath');
$networks[] = networkArray('AffiliateDotCom', 1030, 'DirectTrack');
$networks[] = networkArray('ClickBooth', 1023, 'DirectTrack');
$networks[] = networkArray('CommissionEmpire', 1046, 'DirectTrack');
$networks[] = networkArray('Copeac', 1007, 'DirectTrack');
$networks[] = networkArray('FluxAds', 1034, 'DirectTrack');
$networks[] = networkArray('Rextopia', 1044, 'DirectTrack');
$networks[] = networkArray('XY7', 1037, 'DirectTrack');
$networks[] = networkArray('Adex', 1050, 'LinkTrust');
$networks[] = networkArray('Adfinity', 1049, 'LinkTrust');
$networks[] = networkArray('BlinkAds', 1051, 'LinkTrust');
$networks[] = networkArray('Epicenter', 1054, 'LinkTrust');
$networks[] = networkArray('EWA', 1052, 'LinkTrust');
$networks[] = networkArray('FireLead', 1053, 'LinkTrust');
$ids = array();
$do_networks = array();
$date = $Yesterday ? date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")) ) : date('Y-m-d');
foreach($argv as $v)
{
  if(is_numeric($v))
  {
	$ids[]=$v; 
  } else {
	foreach($networks as $n)
	  if(strcasecmp($n['name'], $v) ===0)
		$do_networks[] = $n;
	  elseif(strtotime($v) != strtotime(''))
		$date = date('Y-m-d', strtotime($v));
  }
}
echo "Date: " . $date . "\n";
if(empty($ids))
{
  echo "All users\n";
  $ids = "";
} else {
  echo 'Users: ' . implode(', ', $ids) . "\n";
  $ids = "user__id in (".implode(',', $ids) . ") AND ";
}
if(empty($do_networks))
{
  echo "All networks\n";
  $do_networks = $networks;
} else {
  echo 'Networks: ' . print_r($do_networks, true) . "\n";
}
foreach($do_networks as $network)
{
    try {
	  $Sql = "SELECT
					  id,
					  user__id,
					  loginId,
					  password,
					  otherId
				  FROM
					  bevomedia_user_aff_network
				  WHERE
					  network__id = {$network['id']} AND
					  $ids
					  status = 3
				  ";
	  $Result = mysql_query($Sql);
	  while($Row = mysql_fetch_assoc($Result))
	  {
		  queueUpdateStatsForUser($network, $Row, $date);
	  }
    } catch (Exception $e) {
    	var_dump($e);
    }
}
	
?>