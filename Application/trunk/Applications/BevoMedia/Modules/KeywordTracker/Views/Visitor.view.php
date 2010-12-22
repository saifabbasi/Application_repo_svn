<?php

require_once(PATH . "Legacy.Abstraction.class.php");

$isTrackerPage = true;
require(PATH.'inc_daterange.php');

global $userId, $isSelfHosted;
$userId = $this->User->id;
$isSelfHosted = $this->User->IsSelfHosted();

	$stDate = $this->EndDate;
	$enDate = $this->StartDate;
	
	
	$sql = "SELECT `click`.`user__id` AS `user__id`,
				       `click`.`subId` AS `subId`,
				       `click`.`clickTime` AS `clickTime`,
				       `ip`.`ipAddress` AS `ipAddress`,
				       `click`.`referrerUrl` AS `referrerUrl`,
				       `campaign`.`name` AS `campaignName`,
				       `adgroup`.`name` AS `adgroupName`,
				       `creative`.`title` AS `creativeTitle`,
				       `lp`.`landingPageUrl` AS `lp`,
				       `bid_keyword`.`keyword` AS `bidKeyword`,
				       `raw_keyword`.`keyword` AS `rawKeyword`,
				       
				       FROM_UNIXTIME(click.clickTime) as at,
						bevomedia_tracker_clicks_optional.data as optional,
						afs.conversions as conv
				FROM (((((((`bevomedia_tracker_clicks` `click`
				            JOIN `bevomedia_tracker_ips` `ip` ON((`click`.`ipId` = `ip`.`id`)))
				           JOIN `bevomedia_tracker_landing_pages` `lp` ON((`click`.`landingPageId` = `lp`.`id`)))
				          LEFT JOIN `bevomedia_ppc_advariations` `creative` ON((`click`.`creativeId` = `creative`.`apiAdId`)))
				         LEFT JOIN `bevomedia_ppc_adgroups` `adgroup` ON((`creative`.`adGroupId` = `adgroup`.`id`)))
				        LEFT JOIN `bevomedia_ppc_campaigns` `campaign` ON(((`adgroup`.`campaignId` = `campaign`.`id`)
				                                                           AND (`campaign`.`user__id` = `click`.`user__id`))))
				       LEFT JOIN `bevomedia_keyword_tracker_keywords` `bid_keyword` ON((`click`.`bidKeywordId` = `bid_keyword`.`id`)))
				      LEFT JOIN `bevomedia_keyword_tracker_keywords` `raw_keyword` ON((`click`.`rawKeywordId` = `raw_keyword`.`id`)))
				      
				      LEFT JOIN bevomedia_user_aff_network_subid afs ON (afs.subId = click.subId AND afs.user__id = click.user__id)
				      LEFT JOIN bevomedia_tracker_clicks_optional ON click.subId = bevomedia_tracker_clicks_optional.clickId
				      
				WHERE 
					(`click`.`clickTime` >= (unix_timestamp(now()) - ((60 * 60) * 24))) AND
					(click.user__id = $userId)
				GROUP BY 
					user__id, click.subId, clickTime
									
				ORDER BY clickTime DESC 
				LIMIT 250
		  
		  		";
	
//	echo '<pre>'.$sql; die;
//	
//	$sql = "
//		SELECT
//			recent_visitors.*,
//			bevomedia_tracker_clicks_optional.data as optional,
//			afs.conversions as conv
//		FROM
//			bevomedia_view_recent_visitors AS recent_visitors
//		LEFT JOIN bevomedia_user_aff_network_subid afs ON (afs.subId = recent_visitors.subId AND afs.user__id = recent_visitors.user__id)
//		LEFT JOIN bevomedia_tracker_clicks_optional ON recent_visitors.subId = bevomedia_tracker_clicks_optional.clickId
//		WHERE
//			recent_visitors.user__id = $userId
//		GROUP BY
//			user__id, recent_visitors.subId, clickTime
//		ORDER BY
//			clickTime DESC
//		LIMIT 250";
		
		
	$query = mysql_query($sql);
	$rows = array();
	while($row = mysql_fetch_assoc($query))
	{
		if($row['rawKeyword'] == '' && !empty($row['referrerUrl']))
		{
			$ParseUrl = parse_url($row['referrerUrl']);
			
			$ParseOutput = array();
			if(isset($ParseUrl['query']))
			{
				parse_str($ParseUrl['query'], $ParseOutput);
				
				if(isset($ParseOutput['q']))
				{
					$row['rawKeyword'] = $ParseOutput['q'];
				}
				
				if(isset($ParseOutput['p']))
				{
					$row['rawKeyword'] = $ParseOutput['p'];
				}
				
				if(isset($ParseOutput['query']))
				{
					$row['rawKeyword'] = $ParseOutput['query'];
				}
				
			}
			
		}
		
		if(!stristr($row['creativeTitle'], "sandbox") )
		{
			$rows[] = $row;
		}
		
		
	}
	
	$data = $rows;

?>

<style type='text/css'>
.btable {width: 100%;}
.StackRight {display: none !important;}
.StackLeft {width: 100%;}
</style>

<div class="SkyBox"><div class="SkyBoxTopLeft"><div class="SkyBoxTopRight"><div class="SkyBoxBotLeft"><div class="SkyBoxBotRight">
    <table width="550" cellspacing="0" cellpadding="5" border="0">
        <tr valign="top">
            <td><img src="<?=SCRIPT_ROOT?>img/bevotracklogo.png" style="padding-left: 5px; padding-right: 10px;" border=0 alt=""></td>
            <td class="main">
                <h4>Keyword Tracker- Visitor Info</h4>
                <br>
                Here you can review the visitors who are clicking on your ads.  Check out who is clicking on what.  <br /><br />
            </td>
        </tr>
    </table>
</div></div></div></div></div>

<br/>

<div style="width: 600px; margin: auto;">
<?=$this->tracker_menu;?>
</div>
<br/>
<?php
function shorten($text, $link = false, $len = 25)
{
	if(strlen($text) > $len)
	{
		if($link)
			echo '<a href="'.htmlentities($text).'" target="_blank" title="'.htmlentities($text).'">'.htmlentities(substr($text, 0, $len)).'&hellip;</a>';
		else
			echo '<abbr title="'.htmlentities($text).'">'.htmlentities(substr($text, 0, $len)).'&hellip;</abbr>';
	}
	else
	{
		if($link)
			echo '<a href="'.htmlentities($text).'" target="_blank">'.htmlentities($text).'</a>';
		else
			echo htmlentities($text);
	}
}
?>
<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
	    <td class="hhl">&nbsp;</td>
		<td nowrap="nowrap">Sub ID</td>
		<td>Date</td>
		<td>IP</td>
		<td>Referrer</td>
		<td>Creative</td>
		<td nowrap="nowrap">Landing Page</td>
		<td nowrap="nowrap" width="100">Searched KW</td>
		<td>Conversion</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
		<?php
		if(count($data) > 0)
		{
			$i = 0;
			foreach($data as $row)
			{
				?>
				<tr<?php if($i++ % 2 == 1) { echo ' class="AltRow"'; } ?>>
					<td class="border">&nbsp;</td>
					<td nowrap="nowrap"><?php echo htmlentities($row['subId']); ?></td>
					<td style="padding-left: 3px;" nowrap="nowrap"><?php echo date('Y-m-d h:ia', $row['clickTime']); ?></td>
					<td style="padding-left: 3px;" nowrap="nowrap"><?php echo htmlentities($row['ipAddress']); ?></td>
					<td style="padding-left: 3px;" nowrap="nowrap"><?php echo shorten($row['referrerUrl'], true); ?></td>
					<td style="padding-left: 3px;" nowrap="nowrap"><?php if($row['optional'] != ''):?><?php echo $row['optional']?><?php else:?><?php echo $row['creativeTitle']?><?php endif?></td>
					<td style="padding-left: 3px;" nowrap="nowrap"><?php echo shorten($row['lp'], true); ?></td>
					<td style="padding-left: 3px;" nowrap="nowrap"><?php echo shorten($row['rawKeyword']); ?></td>
					<td style="padding-left: 3px; text-align:center;" nowrap="nowrap"><?php if($row['conv']>=1): ?> <img src='/Themes/BevoMedia/img/checkmark.png'/> <?php endif?></td>
					<td class="tail">&nbsp;</td>
				</tr>
				<?php
			}
		}
		else
		{
			?>
			<tr>
				<td class="border">&nbsp;</td>
				<td colspan="8">No visitors found in the last 24 hours.</td>
				<td class="tail">&nbsp;</td>
			</tr>
			<?php
		}
		?>
	</tbody>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="8">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>


<br/>
<center>
*Campaigns uploaded NOT using the Bevo Editor will show:<br/>
<b>
	"Temporary Ad Variation" for the first day the campaign is live. The appropriate ad variation will fill in after the nightly cron.<br/>
</b>
<br/>
</center>