<?php

function queueUpdateOffers($NetDetails)
{
   	$Queue = new QueueComponent();
	$JobID =  $Queue->CreateJobID($NetDetails['name'] . ' Offers');

	$job = <<<EOM
<?php
\$PATH = '/var/www/bevomedia/Application/trunk/Applications/BevoMedia/Common/';
require_once(\$PATH.'AbsoluteIncludeHelper.include.php');
require_once(\$PATH.'OfferImport.class.php');
require_once(\$PATH.'StatImport.class.php');
require_once(\$PATH.'User.class.php');	
require_once(\$PATH."/Network_Classes/{$NetDetails['name']}.php");
\$network = new {$NetDetails['name']};
\$network->setJobId("{$JobID}");
\$network->logTransaction("Getting offers...\n");
\$num = 0;
try {
	\$offerEnvelope = \$network->getOffers();
	\$offerImport = new OfferImport({$NetDetails['id']});
	foreach(\$offerEnvelope->Offers() as \$Offer)
	{
		\$num +=1;
		\$offerImport->insertOffer(\$Offer);
	}
	\$network->logTransaction('Got '.\$num.' offers', 'success');
} catch (Exception \$e) {
	echo "Error encountered: ". \$e->getMessage()."\n";
	\$network->logTransaction('Failed getting offers', 'error', \$e->getMessage());
}
?>
EOM;
	$Queue->SendEnvelope($JobID, $job);
}

function queueUpdateStats($NetDetails, $date = false)
{
    $Sql = "SELECT
    				id,
					user__id,
					loginId,
					password,
					otherId
				FROM
					bevomedia_user_aff_network
				WHERE
					network__id = {$NetDetails['id']} AND
					status = 3 
				";
    $Result = mysql_query($Sql);
    while($Row = mysql_fetch_assoc($Result))
    {
        queueUpdateStatsForUser($NetDetails, $Row, $date);
    }
}
function queueUpdateStatsForUser($NetDetails, $AffNetworkUser, $date = false)
{
	if(!$date)
		$date = date('Y-m-d');
	echo "Updating {$NetDetails['name']} stats for user #{$AffNetworkUser['user__id']}\n";
   	$Queue = new QueueComponent();
	$JobID =  $Queue->CreateJobID($NetDetails['name'] . ' Stats', $AffNetworkUser['user__id']);
	$job = <<<EOM
<?php
\$PATH = '/var/www/bevomedia/Application/trunk/Applications/BevoMedia/Common/';
require_once(\$PATH.'AbsoluteIncludeHelper.include.php');
require_once(\$PATH.'OfferImport.class.php');
require_once(\$PATH.'StatImport.class.php');
require_once(\$PATH.'User.class.php');
require_once(\$PATH."/Network_Classes/{$NetDetails['name']}.php");
\$network = new {$NetDetails['name']};
\$network->setJobId("{$JobID}");
\$network->setPublisherId("{$AffNetworkUser['otherId']}");
\$network->setPublisherLogin("{$AffNetworkUser['loginId']}");
\$network->setPublisherPassword("{$AffNetworkUser['password']}");
\$statImport = new StatImport({$NetDetails['id']}, {$AffNetworkUser['user__id']});
\$statEnvelope = false;
\$count = 0;
\$user = new User({$AffNetworkUser['user__id']});
if ( (\$user->vaultID==0) && (!\$user->IsSubscribed(User::PRODUCT_INSTALL_NETWORKS)) && (\$user->membershipType!='premium') )
{
	echo "Account not verified...";
	return;
}


try {
	if(\$network->login() === false)
	{
	  \$network->logTransaction('Error logging in, aborting stats pull!', 'warning');
	  die();
	}
	echo "Logged in!\n";
	\$statEnvelope = \$network->getStats('{$date}');
	\$stats = \$statImport->processStatEnvelope(\$statEnvelope);
	\$stats_info = array(
						'clicks' => 0,
						'conversions' => 0,
						'revenue' => 0,
						'click_subids' => array(),
						'convert_subids' => array(),
						'offer_ids' => array());
	foreach(\$stats as \$stat)
	{
		\$stats_info['clicks'] += \$stat->clicks;
		\$stats_info['conversions'] += \$stat->conversions;
		\$stats_info['revenue'] += \$stat->revenue;
		\$stats_info['click_subids'][]= \$stat->subId;
		if(\$stat->conversions > 0)
			\$stats_info['convert_subids'][] = \$stat->subId;
		if(!in_array(\$stat->offerId, \$stats_info['offer_ids']))
			\$stats_info['offer_ids'][] = \$stat->offerId;
	}
	echo "Got stats!\n";
	\$network->logTransaction('Got stats for '. count(\$stats_info['offer_ids']) . ' offers on {$AffNetworkUser['loginId']}', 'success', print_r(\$stats_info, true));
	\$user->setLastNetworkUpdate(date('c'));
	\$count += count(\$stats);
} catch (Exception \$e) {
	echo "Error encountered: ". \$e->getMessage()."\n";
	\$network->logTransaction('Failed to get stats for {$AffNetworkUser['loginId']}', 'error', \$e->getMessage());
}
?>
EOM;
    $Queue->SendEnvelope($JobID, $job);
}