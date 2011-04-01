<?php
require_once(PATH . "Legacy.Abstraction.class.php");

function getPPCAccountInfo($providerType, $accountId)
{
	$accountId = intval($accountId);
	
	if ($providerType==1)
	{
		$providerName = 'Google Adwords';
		$Table = "bevomedia_accounts_adwords";
	} else
	if ($providerType==2)
	{	
		$providerName = 'Yahoo Search Marketing';
		$Table = "bevomedia_accounts_yahoo";
	} else
	if ($providerType==3)
	{	
		$providerName = 'Microsoft adCenter';
		$Table = "bevomedia_accounts_msnadcenter";
	}
	
	$Sql = "SELECT
			       _utf8'{$providerName}' AS `providerName`,
			       `id` AS `accountId`,
			       `user__id` AS `user__id`,
			       `username` AS `accountName`
			FROM
				{$Table}
			WHERE
				{$Table}.id = {$accountId}
			";
	$result = mysql_query($Sql);
	if (count($result)>0)
	{
		$row = mysql_fetch_assoc($result);
		return $row;
	}
	
	return array();
}


global $userId;
$userId = $this->User->id;
$isTrackerPage = true;
$stDate = $this->StartDate;
$enDate = $this->EndDate;
$filtering_sql = $this->filter->getPPCNormalizedSql();
$filtering_rev_sql = $filtering_sql[0];
$filtering_cost_sql = $filtering_sql[1];


$sql = "SELECT
			campaigns.name AS campaign_name,
			campaigns.providerType as provider_type,
			campaigns.providerType,
			campaigns.accountId,
			ads.name AS ad_name,
			ads.id as adGroupId,
			advars.id AS id,
			advars.title,
			advars.url,
			advars.displayUrl,
			advars.description,
			COALESCE(COUNT(DISTINCT clicks.id), 0) AS clicks,
			COALESCE(SUM(subid.conversions), 0) AS conversions,
			COALESCE(SUM(subid.revenue), 0) AS revenue

		
		FROM 
			(`bevomedia_tracker_clicks` `clicks`
      		LEFT JOIN `bevomedia_user_aff_network_subid` `subid` ON(((`clicks`.`user__id` = `subid`.`user__id`)
                                                               AND (`clicks`.`subId` = `subid`.`subId`)
                                                               AND (`subid`.`statDate` >= `clicks`.`clickDate`))))
			LEFT JOIN bevomedia_ppc_advariations AS advars ON
				clicks.creativeId = advars.apiAdId
			LEFT JOIN bevomedia_ppc_adgroups AS ads ON
				advars.adGroupId = ads.id
			LEFT JOIN bevomedia_ppc_campaigns AS campaigns ON
				ads.CampaignID = campaigns.id
				AND campaigns.user__id = clicks.user__id
				
			LEFT JOIN bevomedia_accounts_adwords ON (campaigns.AccountID = bevomedia_accounts_adwords.id) 
			LEFT JOIN bevomedia_accounts_yahoo ON (campaigns.AccountID = bevomedia_accounts_yahoo.id) 
			LEFT JOIN bevomedia_accounts_msnadcenter ON (campaigns.AccountID = bevomedia_accounts_msnadcenter.id)
		WHERE
			clicks.user__id = $userId
			AND clicks.creativeId != ''
			and campaigns.ProviderType in (1,2,3)
			AND clicks.clickDate BETWEEN '$stDate' AND '$enDate'
			$filtering_rev_sql
			AND ( (bevomedia_accounts_adwords.deleted = 0) OR (bevomedia_accounts_yahoo.deleted = 0) OR (bevomedia_accounts_msnadcenter.deleted = 0) OR (bevomedia_accounts_adwords.deleted IS NULL) OR (bevomedia_accounts_yahoo.deleted IS NULL) OR (bevomedia_accounts_msnadcenter.deleted IS NULL)  )
		GROUP BY
			advars.title,
			advars.url,
			advars.displayUrl,
			advars.description
		ORDER BY
			campaigns.ProviderType,
			campaigns.accountId,
			campaigns.name,
			ads.name,
			advars.title
		";

//echo '<pre>'.$sql; die;



//$sql = "
//		SELECT
//			CONCAT(accounts.providerName , ' - ', CONVERT(accounts.accountName USING UTF8)) AS account_name,
//			campaigns.name AS campaign_name,
//			campaigns.providerType as provider_type,
//			ads.name AS ad_name,
//			ads.id as adGroupId,
//			advars.id AS id,
//			advars.title,
//			advars.url,
//			advars.displayUrl,
//			advars.description,
//			COALESCE(COUNT(DISTINCT stats.id), 0) AS clicks,
//			COALESCE(SUM(stats.conversions), 0) AS conversions,
//			COALESCE(SUM(stats.revenue), 0) AS revenue
//		FROM
//			bevomedia_view_click_stats AS stats
//			LEFT JOIN bevomedia_ppc_advariations AS advars ON
//				stats.creativeId = advars.apiAdId
//			LEFT JOIN bevomedia_ppc_adgroups AS ads ON
//				advars.adGroupId = ads.id
//			LEFT JOIN bevomedia_ppc_campaigns AS campaigns ON
//				ads.CampaignID = campaigns.id
//				AND campaigns.user__id = stats.user__id
//			LEFT JOIN bevomedia_view_ppc_accounts AS accounts ON
//				campaigns.providerType = accounts.providerId
//				AND campaigns.user__id = accounts.user__id
//				AND campaigns.accountId = accounts.accountId
//		WHERE
//			stats.user__id = $userId
//			AND stats.creativeId != ''
//			and campaigns.ProviderType in (1,2,3)
//			AND stats.clickDate BETWEEN '$stDate' AND '$enDate'
//			$filtering_rev_sql
//		GROUP BY
//			advars.title,
//			advars.url,
//			advars.displayUrl,
//			advars.description
//		ORDER BY
//			accounts.providerName,
//			accounts.accountName,
//			campaigns.name,
//			ads.name,
//			advars.title
//	";

$query = mysql_query($sql);
$data = array();

while($row = mysql_fetch_array($query))
{
	$ppcAccountInfo = getPPCAccountInfo($row['providerType'], $row['accountId']);
	$row['account_name'] = $ppcAccountInfo['providerName'].' - '.$ppcAccountInfo['accountName'];
	
	
	$data[$row['title'].$row['url'].$row['displayUrl'].$row['description']] = $row;
}

// Get cost data
$query = "
		SELECT
			c.name AS campaign_name,
			c.providerType as provider_type,
			c.providerType,
			c.accountId,
			a.name AS ad_name,
			advars.id AS id,
			a.id AS adGroupId,
			advars.title,
			advars.url,
			advars.displayUrl,
			advars.description,
			SUM(stats.cost) AS cost
		FROM
			bevomedia_ppc_advariations_stats AS stats
			JOIN bevomedia_ppc_advariations AS advars ON
				stats.advariationsId = advars.id
			JOIN bevomedia_ppc_adgroups AS a ON
				advars.adGroupId = a.id
			JOIN bevomedia_ppc_campaigns AS c ON
				a.CampaignID = c.id
				
		WHERE
			(stats.statDate BETWEEN '$stDate' AND '$enDate') AND
			(c.user__id = {$userId})
			$filtering_cost_sql
		GROUP BY
			advars.title,
			advars.url,
			advars.displayUrl,
			advars.description
		HAVING
			SUM(stats.cost) > 0
		ORDER BY
			c.ProviderType,
			c.accountId,
			c.name,
			a.name
	";
			
//echo '<pre>'.$query; die;
			
			
$query = mysql_query($query);
	
while($row = mysql_fetch_assoc($query))
{
	$ppcAccountInfo = getPPCAccountInfo($row['providerType'], $row['accountId']);
	$row['account_name'] = $ppcAccountInfo['providerName'].' - '.$ppcAccountInfo['accountName'];
	
	$id = $row['title'].$row['url'].$row['displayUrl'].$row['description'];
	if(!isset($data[$id]))
	{
		$data[$id] = array();
	}
	$data[$id ]['cost'] = $row['cost'];
	if(!isset($row['revenue']))
		$row['revenue'] = 0;
	$data[$id]['profit'] = $row['revenue'] - $row['cost'];
	$data[$id]['provider_type'] = $row['provider_type'];
	$data[$id]['adGroupId'] = $row['adGroupId'];
	$data[$id]['id'] = $row['id'];
	
	if(empty($data[$id]['title']))
	{
		$data[$id]['campaign_name'] = $row['campaign_name'];
		$data[$id]['ad_name'] = $row['ad_name'];
		$data[$id]['account_name'] = $row['account_name'];
		$data[$id]['title'] = $row['title'];
		$data[$id]['url'] = $row['url'];
		$data[$id]['displayUrl'] = $row['displayUrl'];
		$data[$id]['description'] = $row['description'];
	}
}

function sort_by_account($a, $b)
{
	return (strcmp ($a['account_name'].$a['campaign_name'].$a['ad_name'].$a['title'], $b['account_name'].$b['campaign_name'].$b['ad_name'].$b['title']));
}
uasort($data, 'sort_by_account');

// Create the chart XML
$steps = (count($data) > 10) ? count($data) / 10 : 1;
$chartXML = "<chart showBorder='0' bgAlpha='0,0' numberPrefix='$' formatNumberScale='0' labelDisplay='ROTATE' lineThickness='1' showValues='0' labelStep='".$steps."' slantLabels='1'>";
foreach ($data as $row)
	@$chartXML .= "<set label='".htmlentities($row['var_name'])."' value='".number_format($row['revenue'], 2, '.', '')."' />";
if(!$data)
	@$chartXML .= "<set label='".htmlentities(str_replace("'","",$stDate))."' value='".number_format(0, 2, '.', '')."' />";
$chartXML .= "</chart>";


if(isset($_GET['ExportCSV']) && $_GET['ExportCSV'] == 'FILE')
{
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=Offer.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	print '"Account","Campaign","Ad Group","Title","URL","Display URL","Description","Clicks","Conversions","CTR","Earnings","Cost","Profit","Avg CPC","Avg EPC"' . "\r\n";
	foreach($data as $row)
	{
		$temp = array();
		$temp['account_name'] = $row['account_name'];
		$temp['campaign_name'] = $row['campaign_name'];
		$temp['name'] = $row['ad_name'];
		$temp['title'] = $row['title'];
		$temp['url'] = $row['url'];
		$temp['displayUrl'] = $row['displayUrl'];
		$temp['description'] = $row['description'];
		$temp['clicks'] = (isset($row['clicks'])?($row['clicks']):'0'); $row['clicks'] = $temp['clicks'];
		$temp['conversions'] = (isset($row['conversions'])?($row['conversions']):'0');
		$temp['ctr'] = ($row['clicks'] == 0) ? 0 : $row['conversions'] / $row['clicks'] * 100;
		$temp['revenue'] = (isset($row['revenue'])?($row['revenue']):'0'); $row['revenue'] = $temp['revenue'];
		$temp['cost'] = (isset($row['cost'])?($row['cost']):'0'); $row['cost'] = $temp['cost'];
		$temp['profit'] = (isset($row['profit'])?($row['profit']):'0');
		$temp['avgcpc'] = ($row['clicks'] == 0) ? 0 : $row['cost'] / $row['clicks'];
		$temp['avgepc'] = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];

		$roi = ($row['cost'] == 0) ? 0 : $row['profit'] / $row['cost'] * 100;
		$First = true;
		foreach($temp as $v)
		{
			echo ($First)?'':",";
			$First = false;
			print '"' . $v .'"';
		}
		print "\r\n";
	}
	exit;
}
?>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','ppc','advars');
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<?php $this->filter->show_filtering_table(); ?>

<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
	    <td class="hhl">&nbsp;</td>
		<td>Ad Variation</td>
		<td style="text-align: center;">Clicks</td>
		<td style="text-align: center;">Conv</td>
		<td style="text-align: center;">CTR</td>
		<td style="text-align: center;">Earnings</td>
		<td style="text-align: center;">Cost</td>
		<td style="text-align: center;">Profit</td>
		<td style="text-align: center;">CPC</td>
		<td style="text-align: center;">EPC</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
		<?php
		$AdHelper = new AdHelperPPC($this->User->id);
			
			
		$i = 0; $previous_account = ''; $previous_campaign = ''; $previous_adgroup = '';
		$total_clicks = 0; $total_conversions = 0; $total_revenue = 0; $total_cost = 0; $total_profit = 0;
		if(count($data) > 0)
		{
			foreach($data as $row)
			{
				// Account
				if($previous_account != $row['account_name'])
				{
					?>
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="9"><strong><?php echo htmlentities($row['account_name']); ?></strong></td>
						<td class="tail">&nbsp;</td>
					</tr>
					<?php
					$previous_account = $row['account_name'];
					$previous_campaign = '';
					$previous_adgroup = '';
				}
				
				// Campaign
				if($previous_campaign != $row['campaign_name'])
				{
					?>
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="9"><div style="padding-left: 10px;"><strong><?php echo htmlentities($row['campaign_name']); ?></strong></div></td>
						<td class="tail">&nbsp;</td>
					</tr>
					<?php
					$previous_campaign = $row['campaign_name'];
					$previous_adgroup = '';
				}
				
				// Ad Group
				if($previous_campaign != $row['ad_name'])
				{
					?>
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="3"><div style="padding-left: 20px;"><strong><?php echo htmlentities($row['ad_name']); ?></strong></div></td>
						<td colspan="6" class="textAlignRightForced">
						<?php
							$Ads = $AdHelper->LoadAdsByAdGroup($row['adGroupId']);
							$AdGroupName = $Ads[0]->AdGroupName;
							$adGroupId = $Ads[0]->adGroupId;
							$CampaignName = $Ads[0]->CampaignName;
							$Ads = $AdHelper->FormatRowsAsCampaignArray($Ads);
						?>
						</td>
						<td class="tail">&nbsp;</td>
					</tr>
					<?php
					$previous_adgroup = $row['ad_name'];
				}
				
				$row['clicks'] = isset($row['clicks'])?$row['clicks']:0;
				$row['conversions'] = isset($row['conversions'])?$row['conversions']:0;
				$row['revenue'] = isset($row['revenue'])?$row['revenue']:0;
				
				$ctr = ($row['clicks'] == 0) ? 0 : $row['conversions'] / $row['clicks'] * 100;
				@$cpc = ($row['clicks'] == 0) ? 0 : $row['cost'] / $row['clicks'];
				$epc = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];
				?>
				<tr<?php if($i++ % 2 == 1) { echo ' class="AltRow"'; } ?>>
					<td class="border">&nbsp;</td>
					<td>
						<div class="ad">
							<span class="headline"><?php echo htmlentities($row['title']); ?></span><br />
							<span class="description"><?php echo htmlentities($row['description']); ?></span><br />
							<span class="displayUrl"><?php echo htmlentities($row['displayUrl']); ?></span>
						</div>
					</td>
					<td class="number"><?php echo number_format($row['clicks'], 0); ?></td>
					<td class="number"><?php echo number_format($row['conversions'], 0); ?></td>
					<td class="number"><?php echo number_format($ctr, 2); ?>%</td>
					<td class="number">$<?php echo number_format($row['revenue'], 2); ?></td>
					<td class="number">$<?php echo @number_format($row['cost'], 2); ?></td>
					<td class="number">$<?php echo @number_format($row['profit'], 2); ?></td>
					<td class="number">$<?php echo number_format($cpc, 2); ?></td>
					<td class="number">$<?php echo number_format($epc, 2); ?></td>
					<td class="tail">&nbsp;</td>
				</tr>
				<?php
				$total_clicks += $row['clicks'];
				$total_conversions += $row['conversions'];
				$total_revenue += $row['revenue'];
				@$total_cost += $row['cost'];
				@$total_profit += $row['profit'];
			}
		}
		else
		{
			?>
			<tr>
				<td class="border">&nbsp;</td>
				<td colspan="9">No active ad variations found for the selected time frame.</td>
				<td class="tail">&nbsp;</td>
			</tr>
			<?php
		}
		
		$total_ctr = ($total_clicks == 0) ? 0 : $total_conversions / $total_clicks * 100;
		$total_cpc = ($total_clicks == 0) ? 0 : $total_cost / $total_clicks;
		$total_epc = ($total_clicks == 0) ? 0 : $total_revenue / $total_clicks;
		?>
		<tr class="total">
			<td class="border">&nbsp;</td>
			<td>Total</td>
			<td class="number"><?php echo number_format($total_clicks, 0); ?></td>
			<td class="number"><?php echo number_format($total_conversions, 0); ?></td>
			<td class="number"><?php echo number_format($total_ctr, 2); ?>%</td>
			<td class="number">$<?php echo number_format($total_revenue, 2); ?></td>
			<td class="number">$<?php echo number_format($total_cost, 2); ?></td>
			<td class="number">$<?php echo number_format($total_profit, 2); ?></td>
			<td class="number">$<?php echo number_format($total_cpc, 2); ?></td>
			<td class="number">$<?php echo number_format($total_epc, 2); ?></td>
			<td class="tail">&nbsp;</td>
		</tr>
	</tbody>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="9">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>

<a class="tbtn floatright" href='?<?php echo $_SERVER['QUERY_STRING']?>&ExportCSV=FILE' >Export to CSV</a>

<br/>

<br/>
<center>
*Campaigns uploaded NOT using the Bevo Editor will show:<br/>
<b>
	"Temporary Ad Variation" for the first day the campaign is live. The appropriate ad variation will fill in after the nightly cron.<br/>
</b>
<br/>
</center>
