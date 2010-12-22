<?php

require(PATH."CloakRedirect.class.php");
require(PATH."DirectLink.class.php");


if($this->{'Application/Mode'} == 'SelfHosted')
{
  $webHost = 'http://' . $_SERVER['HTTP_HOST'] . '/';
  $trackHost = 'http://' . $_SERVER['HTTP_HOST'] . '/track/';
  $trackHostSecure = 'https://' . $_SERVER['HTTP_HOST'] . '/track/';
  $trackHostBase = $_SERVER['HTTP_HOST'] . '/track/';
}else{
  $webHost = 'http://beta.bevomedia.com/';
  $trackHost = 'http://track.bevomedia.com/';
  $trackHostSecure = "https://track.bevomedia.com/";
  $trackHostBase = 'track.bevomedia.com/';
}


global $userId;
$userId = $this->User->id;

$isTracker = true;


function insertCampaign($name, $type = 4)
{
  global $db;
  global $userId;
  mysql_query('INSERT INTO bevomedia_ppc_campaigns (user__id,ProviderType,AccountID,Name) VALUES ('. $userId . ', '.$type. ', 0, "' . $name . '")');
  return mysql_insert_id();
}

function insertAdGroup($name, $cID)
{
  global $db;
  global $userId;
  mysql_query('INSERT INTO bevomedia_ppc_adgroups (CampaignID, Name) VALUES ('. $cID . ', "' . $name . '")');
  return mysql_insert_id();
}

function insertAdVar($name, $agID)
{
  global $db;
  global $userId;
  mysql_query('INSERT INTO bevomedia_ppc_advariations (adGroupId, title, apiAdId) VALUES ('. $agID . ', "' . $name . '", 1)');
  return mysql_insert_id();
}
function insertAdVarMB($name, $agID, $bid)
{
  global $db;
  global $userId;
  $bid = floatval($bid);
  mysql_query('INSERT INTO bevomedia_ppc_advariations (adGroupId, title, apiAdId) VALUES ('. $agID . ', "' . $name . '", 1)');
  $avID = mysql_insert_id();
  mysql_query('UPDATE bevomedia_ppc_advariations SET apiAdId = "' . $avID . '" WHERE id = ' . $avID . ' LIMIT 1');
  mysql_query('INSERT INTO bevomedia_ppc_advariations_stats (advariationsId, cost) VALUES ( ' . $avID . ', ' . $bid .')');
  return $avID;
}
function insertAdVarPPV($name, $agID, $bid)
{
  global $db;
  global $userId;
  $bid = floatval($bid);
  mysql_query('INSERT INTO bevomedia_ppc_advariations (adGroupId, title, apiAdId) VALUES ('. $agID . ', "' . $name . '", 1)');
  $avID = mysql_insert_id();
  mysql_query('UPDATE bevomedia_ppc_advariations SET apiAdId = "' . $avID . '" WHERE id = ' . $avID . ' LIMIT 1');
  mysql_query('INSERT INTO bevomedia_ppc_advariations_stats (advariationsId, cost) VALUES ( ' . $avID . ', ' . $bid .')');
  return $avID;
}

##Take data and generate the track code and instruction
if(isset($_POST['init']))
{
  $unique = false;
  $cloaking = false;
  if(isset($_POST['cloak']))
  {
	$cloaking = true;
  }
  if(isset($_POST['unique']))
  {
	$unique = true;
  }

  $directlink = false;
  if(isset($_POST['directlink']))
  {
	$directlink = true;
  }
  $autotrack= false;
  $saleAmt = 0;
  if(isset($_POST['autotrack']))
  {
	$autotrack = true;
  } else {
	$saleAmt = number_format(floatval(@$_POST['saleAmt']), 2, '.', '');
  }

  $searchEngine = $_POST['se'];


  if($searchEngine == 'other')
  {
	$mbcampaign = (empty($_POST['mediabuy_campaign']))?'[MediaBuy Campaign]':$_POST['mediabuy_campaign'];
	$mbadgroup = (empty($_POST['mediabuy_adgroup']))?'[MediaBuy Ad Group]':$_POST['mediabuy_adgroup'];

	$cID = ($_POST['campaign_input_id-id'] == '')?insertCampaign($mbcampaign):$_POST['campaign_input_id-id'];
	$agID = ($_POST['adgroup_input_id-id'] == '')?insertAdGroup($mbadgroup, $cID):$_POST['adgroup_input_id-id'];

	$bid = floatval($_POST['mediabuy_cost']);
	if($_POST['mediabuy_cost_type'] == 'CPM')
	{
	  $bid = $bid / 1000;
	}
	$avID = insertAdVarMB($mbadgroup, $agID, $bid);
  }

  if($searchEngine == 'adon')
  {
	$aocampaign = (empty($_POST['adon_campaign']))?'[Adon Campaign]':$_POST['adon_campaign'];
	$cID = insertCampaign($aocampaign, 6);
	$agID = insertAdGroup($aocampaign, $cID);
	$avID = insertAdVarPPV($aocampaign, $agID, $_POST['adon_cpm']);
  }

  if($searchEngine == 'trafficvance')
  {
	$ppvcampaign = (empty($_POST['ppv_campaign']))?'[PPV Campaign]':$_POST['ppv_campaign'];
	$cID = insertCampaign($ppvcampaign, 5);
	$agID = insertAdGroup($ppvcampaign, $cID);
	$avID = insertAdVarPPV($ppvcampaign, $agID, $_POST['ppv_cpm']);
  }

  if($searchEngine == 'mediatraffic')
  {
	$medtrafcampaign = (empty($_POST['medtraf_campaign']))?'[Media Traffic Campaign]':$_POST['medtraf_campaign'];
	$cID = insertCampaign($medtrafcampaign, 7);
	$agID = insertAdGroup($medtrafcampaign, $cID);
	$avID = insertAdVarPPV($medtrafcampaign, $agID, $_POST['medtraf_cpm']);
  }

  if($searchEngine == 'dircpv')
  {
	$dircpvcampaign = (empty($_POST['dircpv_campaign']))?'[DirectCPV Campaign]':$_POST['dircpv_campaign'];
	$cID = insertCampaign($dircpvcampaign, 8);
	$agID = insertAdGroup($dircpvcampaign, $cID);
	$avID = insertAdVarPPV($dircpvcampaign, $agID, $_POST['dircpv_cpm']);
  }
  
  if($searchEngine == 'leadimpact')
  {
	$leadimpactcampaign = (empty($_POST['leadimpact_campaign']))?'[LeadImpact Campaign]':$_POST['leadimpact_campaign'];
	$cID = insertCampaign($leadimpactcampaign, 9);
	$agID = insertAdGroup($leadimpactcampaign, $cID);
	$avID = insertAdVarPPV($leadimpactcampaign, $agID, $_POST['leadimpact_cpm']);
  }

  $offerUrl     = trim($_POST['offurl']);
  $landingPage  = $_POST['lp'] ? $_POST['lp'] : null;
  
  
  
  $LandingPageRotator = false;
  if (substr($landingPage, 0, 6)=='ROTATE')
  {
		$GroupId = explode('.', $landingPage);
		$GroupId = intval($GroupId[1]);
		
		$landingPage = $trackHost. "lp/".$GroupId.'/';
		
		$LandingPageRotator = true;
  } 
  {
	  $landingPageUrlArray = parse_url($landingPage);
	  $landingPage = $landingPageUrlArray['scheme'].'://'.$landingPageUrlArray['host'];
	  if (isset($landingPageUrlArray['path']))
	  {
			$landingPage .= $landingPageUrlArray['path']; 
	  } else
	  {
	  		$landingPage .= '/';
	  }
	  
	  if (isset($landingPageUrlArray['query']))
	  {
	  		$landingPage .= '?'.$landingPageUrlArray['query'];
	  }
  
	  if(strpos($landingPage, '?') !== false)
		$landingPage .= '&';
	  else
		$landingPage .= '?';
	
	  switch($searchEngine)
	  {
	  case 'google':
		$landingPage .= 'bevo_k={keyword}&bevo_c={creative}&bevo_m={ifsearch:s}{ifcontent:c}&bevo_p=google';
		break;
	  case 'msn':
		$landingPage .= 'bevo_r={QueryString}&bevo_k={keyword}&bevo_c={AdId}&bevo_m={MatchType}&bevo_p=msn';
		break;
	  case 'other':
		$landingPage .= "bevo_cc=$cID&bevo_ca=$agID&bevo_c=$avID&bevo_k=&bevo_r=&bevo_o=".$_POST['mediabuy_advar'];
		break;
	  case 'adon':
		$landingPage .= "bevo_c={$avID}&bevo_o=SEARCHTEXT";
		break;
	  case 'mediatraffic':
		$landingPage .= "bevo_c={$avID}";
		break;
	  case 'dircpv':
		$landingPage .= "bevo_c={$avID}&bevo_o={{keyword}}&bevo_u={{sm_vurl}}";
		break;
	  case 'leadimpact':
		$landingPage .= "bevo_c={$avID}&keyword_passthrough=";
		break;
	  case 'trafficvance':
		$landingPage .= "bevo_o={%%\$KEYWORD%%}&bevo_c={$avID}";
		break;
	  case 'yahoo':
		$landingPage .= "bevo_p=yahoo";
	  default:
		$landingPage .= ''; // Nothing appended, tracking URLs handles it
		break;
	  }
	
	  $landingPage = trim($landingPage, '?&'); // Clean up any hanging characters
  }
  

  if($cloaking == true)
  {
	$Cloak = new CloakRedirect();
	$cloakId = $Cloak->Insert($this->User->id, $offerUrl);
	$offerUrl = $trackHost . 'cloak.php?id='.$cloakId.'&cookie=';
  }

  if($directlink == true)
  {
	$DirectLink = new DirectLink();
	$dlId = $DirectLink->Insert($this->User->id, $offerUrl, $unique);


	if($searchEngine == 'google')
	{
	  $dlId .= '?bevo_c={creative}&bevo_k={keyword}&bevo_m={ifsearch:s}{ifcontent:c}';
	}
	if($searchEngine == 'msn')
	{
	  $dlId .= '?bevo_c={AdId}&bevo_k={keyword}&bevo_m={MatchType}';
	}

	if($searchEngine == 'adon')
	{
	  $DirectLink->UpdateAdVarID($dlId, $avID);
	  $dlId .= '/SEARCHTEXT';
	}
	if($searchEngine == 'trafficvance')
	{
	  $DirectLink->UpdateAdVarID($dlId, $avID);
	  $dlId .= '/%%$KEYWORD%%';
	}
	if($searchEngine == 'mediatraffic')
	{
	  $DirectLink->UpdateAdVarID($dlId, $avID);
	}
	if($searchEngine == 'leadimpact')
	{
	  $DirectLink->UpdateAdVarID($dlId, $avID);
	}
	if($searchEngine == 'dircpv')
	{
	  $DirectLink->UpdateAdVarID($dlId, $avID);
	  $dlId .= '/{{keyword}}{{sm_vurl}}';
	}
	if($searchEngine == 'other')
	{
	  $DirectLink->UpdateAdVarID($dlId, $avID);
	}

	if(isset($_POST['mediabuy_advar']) && !empty($_POST['mediabuy_advar']))
	{
	  $dlId .= ('/'.$_POST['mediabuy_advar']);
	}
  }


  $clickThrough = '@file_get_contents("'.$trackHost.'ct.php?cookie=".$_COOKIE["bevo_sid"]);';

  $rdcode = "<?php";
  if(substr($offerUrl, 0, 6) == 'ROTATE')
  {
  	$rdcode .= "\n".$clickThrough;
	$rdcode .= "\n\$Links = array();";
	$Links = new OfferRotatorLink();
	$linksId = substr($offerUrl, 7);
	$Links = $Links->GetAllForGroupID($linksId);
	foreach($Links as $Link)
	  for($i=0; $i<$Link->ratio; $i++)
		$rdcode .= "\n\$Links[] = '$Link->link';";

	$rdcode .= "\n\$Location = rand(1,sizeof(\$Links)) - 1;";
	$rdcode .= "\nheader('Location: '. \$Links[\$Location].\$_COOKIE['bevo_sid']);";
  } else {
  	$rdcode .= "\n".$clickThrough;
	$rdcode .= "\nheader('Location: $offerUrl'.\$_COOKIE['bevo_sid']);";
  }
  $rdcode .= "\n?>";


  $pixel = '<img src="' . $trackHost . 'px/'.$userId.'?amount='.$saleAmt.'&subid=" />';
  $secure_pixel = '<img src="' . $trackHostSecure . 'px/'.$userId.'?amount='.$saleAmt.'&subid=" />';
  $pixel_amount = '<img src="' . $trackHost . 'px/'.$userId.'?subid=&amount=35.00" />';
  $postback = $trackHost . 'pb/'.$userId.'?amount='.$saleAmt.'&subid=';




  $m_lp = mysql_escape_string($_POST['lp']);
  $m_offer = mysql_escape_string($_POST['offurl']);
  $m_desti = mysql_escape_string($landingPage);
  if($directlink == true)
  {
	$m_desti = mysql_escape_string($trackHost . 'dc/' . $dlId);
  }
  $trackHostBaseJS = $trackHostBase . ($unique ? 'visitors' : 'clicks') . '/' . $userId;
  $m_lpcode = <<<END
<script type="text/javascript">
var jsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
document.write(unescape("%3Cscript src='" + jsHost + "$trackHostBaseJS' type='text/javascript'%3E%3C/script%3E"));
</script>
END;
  $m_lpcode = mysql_escape_string($m_lpcode);

  $m_rdcode = mysql_escape_string($rdcode);
  $m_pixel = mysql_escape_string($pixel);
  $m_postback = mysql_escape_string($postback);

  $sql = "INSERT INTO bevomedia_tracker_getcodes
	(
	  `user__id`,
	  `landingpage`,
	  `offerurl`,
	  `destinationurl`,
	  `landingpagecode`,
	  `redirectcode`,
	  `conversionpixel`,
	  `postbackurl`
	)
	VALUES
	(
	  \"{$this->User->id}\",
	  \"{$m_lp}\",
	  \"{$m_offer}\",
	  \"{$m_desti}\",
	  \"{$m_lpcode}\",
	  \"{$m_rdcode}\",
	  \"{$m_pixel}\",
	  \"{$m_postback}\"
	)";
  mysql_query($sql);
?>


<?php
  // BEGIN HTML OUTPUT
  // (Instructions, codes, links.)
?>

	<h2>Tracking Code Instructions</h2>

	<h3>Step 1: Destination URL (Required for all tracking methods)</h3>

	<p>This is the destination URL you should link to from your PPC campaigns.</p>
	<?php if(isset($searchEngine) && $searchEngine == 'yahoo'):?>

		<a class="tooltip" title="Your Tracking URLs can be turned on by logging into your YSM account, going to your Administration tab, and navigate to your Tracking URLs section and select 'On'.">
			*Your "Tracking URLs" must be turned ON in your Yahoo account in order for your keywords to track correctly.
		</a>

		<style type="text/css">
			#tooltip{
				line-height: 1.231; font-family: Arial; font-size: 13px;
				position:absolute;
				border:1px solid #333;
				background:#f7f5d1;
				padding:2px 5px;
				display:none;
				width:285px;
				}
			.tooltip {
				text-decoration: none !important;
				font-size: 11px;
				}
			.tooltip.defaultLink {
				color: maroon;
				font-size: 12px;
				font-style: normal;
				font-weight: normal;
				font-size: 12px;
				}
		</style>
	<?php endif?>

	<?php if($directlink) { ?>
		<p><i>Use this as the destination url of your campaign to have the clicks tracked and your user
					forwarded to your real affiliate link. Your ad variation appears after the slash "/" of your destination URL and you may edit it at any time.</i></p>
	<p><textarea class="code" rows="1" readonly="readonly" wrap="off"><?php echo $trackHost?>dc/<?php echo $dlId; ?></textarea></p>
	<?php } else { ?>
		<p><textarea class="code" rows="1" readonly="readonly" wrap="off"><?php echo htmlspecialchars($landingPage); ?></textarea></p>
	<?php } ?>

	<?php if(!$directlink) { ?>


		<h3>Step 2: Landing Page Code (Required for all tracking methods)</h3>
		<p>This snippet of Javascript code should be placed on your landing page before the </body> tag at the bottom of your page.</p>
		<p>
			<textarea class="code" rows="5" readonly="readonly" wrap="off"><script type="text/javascript">
			var jsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
document.write(unescape("%3Cscript src='" + jsHost + "<?php echo $trackHostBaseJS; ?>' type='text/javascript'%3E%3C/script%3E"));
</script></textarea>
		</p>

			<h3>Step 3: Redirect to Offer URL (Required for all tracking methods)</h3>
			<p>Use the following PHP code to redirect to the offer URL from your landing page. This is required to properly pass the "subid" of each visitor.</p>
			<p>
<textarea class="code" rows="4" readonly="readonly" wrap="off"><?= htmlspecialchars($rdcode) ?>
</textarea>
	</p>
<?php

  if($searchEngine == 'yahoo')
  {
?>
			<p><strong>Note</strong>: You must turn Tracking URLs on in your Yahoo account.</p>
			<p>To turn Tracking URLs on:</p>
			<ol>
				<li>Click the Administration tab, and then the Tracking URLs sub-tab. The Tracking URLs page opens.</li>
				<li>click the Tracking URLs On button.</li>
				<li>Click Save Changes.</li>
			</ol>
<?php
  }

?>
	<?php } else { ?>
		<h3>Step 2:</h3>
		<?php if($autotrack) { ?><p>You're done! Your network stats are being updated automatically, just send your traffic to the link above to begin tracking.</p><?php  } ?>
	<?php } ?>
	<?php if(!$autotrack) { ?>

	<h2>Conversion tracking methods</h2>
	Manually tell Bevo about your conversions, either by placing a pixel on the sale/conversion page, or by configuring a postback with your Affiliate Network.
	<br /><i>(You don't need this if you've added your affiliate network account to Bevo)</i>
	<br />
	<br />
	<h3 style="color: #f00">You MUST pass a SubID to the pixel or postback URL.</h3>
	<p style="color: #F00">The pixels below end in "<b>&subid=</b>" but THIS IS NOT COMPLETE! Depending on the offer, this value must be a macro to dynamically insert a SubID value per visitor.
	<br />For example, if your offer is running through Azoogle, you must change the pixel URL to "<b>&subid=%%SUB_ID%%</b>".
	<br />If you are unsure, ask your affiliate manager at the network for help.</p>
	<h3>Conversion Pixel</h3>
	<p>Place the following pixel on the conversion (or "thank you") page that appears when the visitor completes an action.</p>
	<p><textarea class="code" rows="1" readonly="readonly" wrap="off"><?php echo htmlspecialchars($pixel); ?></textarea></p>
	<?php if($this->{'Application/Mode'} == 'SelfHosted') { ?>
	<p>If you have an SSL certificate installed on your domain, you can use the following SSL ("secure") pixel</p>
	<?php } else { ?>
	<p>Use this if your source requires an SSL ("secure") pixel</p>
	<?php } ?>
	<p><textarea class="code" rows="1" readonly="readonly" wrap="off"><?php echo htmlspecialchars($secure_pixel); ?></textarea></p>

	<h3>Post-back URL</h3>
	<p>If the network you work with supports post-back URLs, you can use this URL. The network should use this post-back URL and call it when a lead or sale takes place and they should put the subid at the end of the url.</p>
	<p><textarea class="code" rows="1" readonly="readonly" wrap="off"><?php echo htmlspecialchars($postback); ?></textarea></p>
<?php
	}
}