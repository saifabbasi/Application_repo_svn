<?php
require_once(PATH . "Legacy.Abstraction.class.php");

//require(PATH.'inc_daterange.php');


global $filter_keyword;
global $filter_visitorip;
global $filter_ppcaccount;
global $filter_ppcprovider;
global $filter_ppccampaign;
global $filter_ppcadgroup;
global $filter_ppcadvar;

$request = array_merge($_COOKIE, $_GET);

LegacyAbstraction::$strDateRangeVal = false;
if(isset($request['DateRange']))
	LegacyAbstraction::$strDateRangeVal = $request['DateRange'];
else
{
	// Default to last 7 days
	LegacyAbstraction::$strStartDateVal = date('n/d/Y', time() - (60*60*24*7));
	LegacyAbstraction::$strEndDateVal = date('n/d/Y', time());
	LegacyAbstraction::$strDateRangeVal = LegacyAbstraction::$strStartDateVal.' - '.LegacyAbstraction::$strEndDateVal;
}

LegacyAbstraction::ParseDateRange();

$filter_ppcprovider = 4;

$filter_ppcaccount = 0;
if(!empty($request['ppcaccount']) && is_numeric($request['ppcaccount']))
	$filter_ppcaccount = (int)$request['ppcaccount'];

$filter_ppccampaign = 0;
if(!empty($request['ppccampaign']) && is_numeric($request['ppccampaign']))
	$filter_ppccampaign = (int)$request['ppccampaign'];

$filter_ppcadgroup = 0;
if(!empty($request['ppcadgroup']) && is_numeric($request['ppcadgroup']))
	$filter_ppcadgroup = (int)$request['ppcadgroup'];

$filter_ppcadvar = 0;
if(!empty($request['ppcadvar']) && is_numeric($request['ppcadvar']))
	$filter_ppcadvar = (int)$request['ppcadvar'];
	
$filter_keyword = '';
$filter_keywordid = 0;
if(!empty($request['keyword']))
{
	$filter_keyword = $request['keyword'];
	$sql = "SELECT ID FROM keyword_tracker_keywords WHERE Keyword = '".mysql_real_escape_string($filter_keyword)."'";
	$query = mysql_query($sql);
	if($row = mysql_fetch_array($query))
		$filter_keywordid = $row['ID'];
}

$filter_visitorip = '';
$filter_visitoripid = 0;
if(!empty($request['visitorip']))
{
	$filter_visitorip = $request['visitorip'];
	$sql = "SELECT id FROM bevomedia_tracker_ips WHERE ipAddress = '".mysql_real_escape_string($filter_visitorip)."'";
	$query = mysql_query($sql);
	if($row = mysql_fetch_array($query))
		$filter_visitoripid = $row['id'];
}


// Create snippet of SQL WHERE statement for filtering data
$filtering_rev_sql = '';
$filtering_cost_sql = '';

if($filter_ppcprovider != 0)
{
	$filtering_rev_sql .= ' AND campaigns.ProviderType = '.$filter_ppcprovider;
	$filtering_cost_sql .= ' AND campaigns.ProviderType = '.$filter_ppcprovider;
}

if($filter_ppcaccount != 0)
{
	$filtering_rev_sql .= ' AND accounts.account_id = '.$filter_ppcaccount;
	$filtering_cost_sql .= ' AND accounts.account_id = '.$filter_ppcaccount;
}

if($filter_ppccampaign != 0)
{
	$filtering_rev_sql .= ' AND campaigns.ID = '.$filter_ppccampaign;
	$filtering_cost_sql .= ' AND campaigns.ID = '.$filter_ppccampaign;
}

if($filter_ppcadgroup != 0)
{
	$filtering_rev_sql .= ' AND adgroups.ID = '.$filter_ppcadgroup .' AND advars.AdGroupID = ' . $filter_ppcadgroup;
	$filtering_cost_sql .= ' AND adgroups.ID = '.$filter_ppcadgroup .' AND advars.AdGroupID = ' . $filter_ppcadgroup;
}

if($filter_ppcadvar != 0)
{
	$filtering_rev_sql .= ' AND advars.ID = '.$filter_ppcadvar;
	$filtering_cost_sql .= ' AND advars.ID = '.$filter_ppcadvar;
}

if(!empty($filter_keyword))
	$filtering_rev_sql .= ' AND (stats.raw_keyword_id = '.$filter_keywordid.' OR stats.bid_keyword_id = '.$filter_keywordid.')';

if(!empty($filter_visitorip))
	$filtering_rev_sql .= ' AND stats.ip_id = '.$filter_visitoripid;

/* HTML */

function show_filtering_table()
{
	global $userId;
	
	global $filter_keyword;
	global $filter_visitorip;
	global $filter_ppcaccount;
	global $filter_ppcprovider;
	global $filter_ppccampaign;
	global $filter_ppcadgroup;
	global $filter_ppcadvar;
?>
<form method="get">

<div class="filtering">
	<div class="col-left">
		<div class="option">
			<label for="pcccampaign">Campaign</label>
			<select name="ppccampaign" id="ppccampaign">
				<option value="">--</option>
				<?php
					$sql = "SELECT ID, Name FROM ppc_campaigns WHERE Name != '' AND UserID = ".$userId." AND ProviderType = 4 GROUP BY Name ORDER BY Name";
					$query = mysql_query($sql);
					while($row = mysql_fetch_array($query))
					{
						echo '<option value="'.$row['ID'].'"';
						if($row['ID'] == $filter_ppccampaign)
							echo ' selected="selected"';
						echo '>'.htmlentities($row['Name']).'</option>';
					}
				?>
			</select>
		</div>
		<div class="option">
			<label for="ppcadgroup">Ad Group</label>
			<select name="ppcadgroup" id="ppcadgroup">
				<option value="">--</option>
				<?php
				if(!empty($filter_ppccampaign))
				{
					$sql = "SELECT ID, Name FROM ppc_adgroups WHERE CampaignID = ".$filter_ppccampaign." GROUP BY Name ORDER BY Name";
					$query = mysql_query($sql);
					while($row = mysql_fetch_array($query))
					{
						echo '<option value="'.$row['ID'].'"';
						if($row['ID'] == $filter_ppcadgroup)
							echo ' selected="selected"';
						echo '>'.htmlentities($row['Name']).'</option>';
					}
				}
				?>
			</select>
		</div>
		<div class="option">
			<label for="ppcadvar">Ad Variation</label>
			<select name="ppcadvar" id="ppcadvar">
				<option value="">--</option>
				<?php
				if(!empty($filter_ppcadgroup))
				{
					$sql = "SELECT ID, Title FROM ppc_advariations WHERE AdGroupID = ".$filter_ppcadgroup." ORDER BY Title";
					$query = mysql_query($sql);
					while($row = mysql_fetch_array($query))
					{
						echo '<option value="'.$row['ID'].'"';
						if($row['ID'] == $filter_ppcadvar)
							echo ' selected="selected"';
						echo '>'.htmlentities($row['Title']).'</option>';
					}
				}
				?>
			</select>
		</div>
	</div>
	<div class="col-right">
		<div class="option">
			<label for="datepicker">Date(s)</label>
			<input type="text" name="DateRange" id="datepicker" value="<?php echo htmlentities(LegacyAbstraction::$strDateRangeVal); ?>" size="20" />
		</div>
		<?php /* ?>
		<div class="option">
			<label for="keyword">Keyword</label>
			<input type="text" id="keyword" name="keyword" value="<?php echo htmlentities($filter_keyword); ?>" size="20" />
		</div>
		<div class="option">
			<label for="visitorip">Visitor IP Address</label>
			<input type="text" id="visitorip" name="visitorip" value="<?php echo htmlentities($filter_visitorip); ?>" size="20" />
		</div>
		<?php //*/?>
	</div>
	<div class="actions">
		<input type="image" height="25" width="30" src="/Themes/BevoMedia/img/gobutton.gif">
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {

		$('#ppccampaign').change( function() {
			$.getJSON("/BevoMedia/KeywordTracker/json.html?list=adgroup&ppccampaign=" + $(this).val(), function(data) {
				var options = '<option value="">--</option>';
				for (var i = 0; i < data.length; i++) {
					options += '<option value="' + data[i].ID + '">' + data[i].Name + '</option>';
				}
				$('#ppcadgroup').html(options);
				$('#ppcadgroup').val('');
			});
		});

		$('#ppcadgroup').change( function() {
			$.getJSON("/BevoMedia/KeywordTracker/json.html?list=advar&ppcadgroup=" + $(this).val(), function(data) {
				var options = '<option value="">--</option>';
				for (var i = 0; i < data.length; i++) {
					options += '<option value="' + data[i].ID + '">' + data[i].Title + '</option>';
				}
				$('#ppcadvar').html(options);
				$('#ppcadvar').val('');
			});
		});

	});
</script>

</form>
<?php
}

/* Helper functions */

function ParseDateRange() {
	

	// No Date Picked, Use Todays Date
	if (empty(LegacyAbstraction::$strDateRangeVal)) {
		LegacyAbstraction::$strStartDateVal = date('n/d/Y', time() - (60*60*24*7));
		LegacyAbstraction::$strEndDateVal = date('n/d/Y', time());
		LegacyAbstraction::$strDateRangeVal = LegacyAbstraction::$strStartDateVal.' - '.LegacyAbstraction::$strEndDateVal;
		return LegacyAbstraction::$strDateRangeVal;
	}

	// Single Date (Doesn't Contain a - Character)
	if (strpos(LegacyAbstraction::$strDateRangeVal, '-') === false) {
		LegacyAbstraction::$strStartDateVal = LegacyAbstraction::$strDateRangeVal;
		LegacyAbstraction::$strEndDateVal = LegacyAbstraction::$strDateRangeVal;
		return LegacyAbstraction::$strDateRangeVal;
	}
	else {
		// Split Date Range, Assign to StartDate and EndDate
		$arrDate = explode('-', LegacyAbstraction::$strDateRangeVal);
		LegacyAbstraction::$strStartDateVal = trim($arrDate[0]);
		LegacyAbstraction::$strEndDateVal = trim($arrDate[1]);
		return LegacyAbstraction::$strDateRangeVal;
	}
}

global $userId;
$userId = $this->User->ID;
$isTrackerPage = true;

$AdRefs = array();
while($row = mysql_fetch_assoc($query))
{	
	if($row['clicks'] != 0)
	{
		$row['avgcpc'] = 0;
		$row['avgepc'] = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];
	}else{
		$row['avgcpc'] = 0;
		$row['avgepc'] = 0;
	}
	
	$AdRefs[] = $row;
}

$data = array();

$data = $AdRefs;

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
	return '';
}

function sort_by_account($a, $b) { return (strcmp ($a['ad_name'],$b['ad_name']));    }

function sort_by_clicks_asc($a, $b) { return ( ($a['clicks'] > $b['clicks']));}
function sort_by_clicks_desc($a, $b) { return ( ($a['clicks'] < $b['clicks']));}
function sort_by_title_asc($a, $b) { return ( ($a['ad_title'] > $b['ad_title']));}
function sort_by_title_desc($a, $b) { return ( ($a['ad_title'] < $b['ad_title']));}
function sort_by_conversions_asc($a, $b) { return ( ($a['conversions'] > $b['conversions']));}
function sort_by_conversions_desc($a, $b) { return ( ($a['conversions'] < $b['conversions']));}
function sort_by_revenue_asc($a, $b) { return ( ($a['revenue'] > $b['revenue']));}
function sort_by_revenue_desc($a, $b) { return ( ($a['revenue'] < $b['revenue']));}
function sort_by_epc_asc($a, $b) { return ( ($a['avgepc'] > $b['avgepc']));}
function sort_by_epc_desc($a, $b) { return ( ($a['avgepc'] < $b['avgepc']));}
function sort_by_referrer_asc($a, $b) { return ( strcmp($a['referrer_url'],$b['referrer_url']));}
function sort_by_referrer_desc($a, $b) { return ( strcmp($b['referrer_url'],$a['referrer_url']));}
//uasort($data, 'sort_by_account');
if(isset($_GET['sortBy']))
{
	if(isset($_GET['sortByOrder']))
	{
		$sortByOrder = 'desc';
		
	}else{
		$sortByOrder = 'asc';
	}
	uasort($data, 'sort_by_' . $_GET['sortBy'] . '_' . $sortByOrder);
}

if(isset($_GET['ExportCSV']) && $_GET['ExportCSV'] == 'FILE')
{
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=AdGroup.csv");
	header("Pragma: no-cache");
	header("Expires: 0"); 

	print '"Campaign","Ad Group","Referrer URL","Ad Title","Clicks","Conversions","CTR","Revenue","Avg EPC"' . "\r\n";	
	foreach($data as $row)
	{
		$temp = array();
		$temp['campaign_name'] = $row['campaign_name'];
		$temp['adgroup_name'] = $row['ad_name'];
		$temp['referrer_url'] = $row['referrer_url'];
		$temp['title'] = $row['ad_title'];
		$temp['clicks'] = (isset($row['clicks'])?($row['clicks']):'0'); $row['clicks'] = $temp['clicks'];
		$temp['conversions'] = (isset($row['conversions'])?($row['conversions']):'0');
		$temp['ctr'] = ($row['clicks'] == 0) ? 0 : $row['conversions'] / $row['clicks'] * 100;
		$temp['revenue'] = (isset($row['revenue'])?($row['revenue']):'0'); $row['revenue'] = $temp['revenue'];
		$temp['avgepc'] = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];

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


<div class="SkyBox"><div class="SkyBoxTopLeft"><div class="SkyBoxTopRight"><div class="SkyBoxBotLeft"><div class="SkyBoxBotRight">
    <table width="570" cellspacing="0" cellpadding="5" border="0">
        <tr valign="top">
            <td width="127"><img src="<?=SCRIPT_ROOT?>img/bevotracklogo.png" style="padding-left: 5px; padding-right: 10px;" border="0" alt="" /></td>
            <td class="main">
                <h4>Recent Media Buy Click Statistics</h4>
                <br />
                View the detailed statistics of your Media Buys. Easily track which pages are getting your clicks, and conversions, giving you all the information you need to optimize. Be sure to have your tracking pixel or postback url placed to track your conversions.
<br /><br />
</td>
        </tr>
    </table>
</div></div></div></div></div>
<br/>
<?php print $this->tracker_menu; ; ?>
<br/>
<?php //show_filtering_table(); ?>
<!-- END Script Block for Chart chartOverview -->




<?php 

$UserID = $this->User->ID;

$CampaignID = (isset($_GET['ppccampaign']) && $_GET['ppccampaign'] != '')?($_GET['ppccampaign']):false;
$AdGroupID = (isset($_GET['ppcadgroup']) && $_GET['ppcadgroup'] != '')?($_GET['ppcadgroup']):false;
$AdVarID = (isset($_GET['ppcadvar']) && $_GET['ppcadvar'] != '')?($_GET['ppcadvar']):false;

$AndSql = '';
$AndSql .= ($CampaignID !== false)?' AND pc.ID = '.$CampaignID:'';
$AndSql .= ($AdGroupID !== false)?' AND pa.ID = '.$AdGroupID:'';
$AndSql .= ($AdVarID !== false)?' AND pav.ID = '.$AdVarID:'';

$query = "
		SELECT 
			pc.ProviderType as providertype,
			tc.click_id as id,
			pav.title as ad_title,
			tc.referrer_url,
			pc.Name as CampaignName,
			pa.Name as AdGroupName,
			sum(DISTINCT afs.CLICKS) as clicks,
			sum(afs.CONVERSIONS) as conversions,
			sum(afs.REVENUE) as revenue,
			optional.data as optional
		FROM 
			tracker_clicks tc 
		JOIN
			adpalace_user_aff_network_subid afs
			ON (afs.USERID = {$UserID} AND afs.SUB_ID = tc.sub_Id)
		JOIN
			ppc_advariations pav
			ON pav.id = tc.creative_id
		JOIN
			ppc_adgroups pa
			ON pa.ID = pav.AdGroupID
		JOIN
			ppc_campaigns pc
			ON (pc.UserID = {$UserID} AND pc.ID = pa.CampaignID)
		LEFT JOIN
			tracker_clicks_optional optional ON optional.click_id = tc.click_id
		WHERE 
			pc.Name != ''
			{$AndSql}
		GROUP BY
			pc.Name, pa.Name, tc.click_id, pav.title, tc.referrer_url 
		ORDER BY
			tc.click_date DESC, pav.title ASC
		LIMIT 0, 250
	";
		
$query = mysql_query($query);
$data = array();

while($row = mysql_fetch_assoc($query))
{	
	/*$data[$row['id']] = $row;
	
	if(@$row['clicks'] != 0)
	{
		$data[$row['id']]['avgcpc'] = 0;
		$data[$row['id']]['avgepc'] = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];
	}else{
		$data[$row['id']]['avgcpc'] = 0;
		$data[$row['id']]['avgepc'] = 0;
	}

	@$data[$row['id']]['profit'] = $data[$row['id']]['revenue'];
	*/
	$data[] = $row;
}

//uasort($data, 'sort_by_account');


// Create the chart XML
?>

<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
	    <td class="hhl">&nbsp;</td>
	    <td style="text-align: center;">Campaign</td>
	    <td style="text-align: center;">Ad Group</td>
		<td style="text-align: center;">Ad Title</td>
		<td style="text-align: center;">Referrer URL</td>
		<td style="text-align: center;">Conv</td>
		<td style="text-align: center;">Revenue</td>
		<td style="text-align: center;">EPC</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
		<?php
		$i = 0; $previous_account = ''; $previous_campaign = '';
		$total_clicks = 0; $total_conversions = 0; $total_revenue = 0; $total_cost = 0; $total_profit = 0;
		if(count($data) > 0)
		{
			foreach($data as $row)
			{
				// Account
				if($previous_account != @$row['account_name'])
				{
					?>
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="5"><strong><?php echo htmlentities($row['account_name']); ?></strong></td>
						<td class="tail">&nbsp;</td>
					</tr>
					<?php
					$previous_account = $row['account_name'];
					$previous_campaign = '';	
				}
				
				// Campaign
				if($previous_campaign != @$row['campaign_name'])
				{
					?>
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="5"><div style="padding-left: 10px;"><strong><?php echo htmlentities($row['campaign_name']); ?></strong></div></td>
						<td class="tail">&nbsp;</td>
					</tr>
					<?php
					$previous_campaign = $row['campaign_name'];			
				}
				
				@$ctr = ($row['clicks'] == 0) ? 0 : $row['conversions'] / $row['clicks'] * 100;
				@$cpc = ($row['clicks'] == 0) ? 0 : $row['cost'] / $row['clicks'];
				@$epc = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];
				?>
				<tr<?php if($i++ % 2 == 1) { echo ' class="AltRow"'; } ?>>
					<td class="border">&nbsp;</td>
					<td><?php echo $row['CampaignName']?></td>
					<td><?php echo $row['AdGroupName']?></td>
					<!-- 
					<td><?php echo htmlentities($row['ad_title']); ?></td>
					-->
					<td><?php if($row['optional'] != ''):?><?php echo $row['optional']?><?php else:?><?php echo $row['ad_title']?><?php endif?></td>
					<td class=""><?php echo shorten($row['referrer_url'], true) ?></td>
					<td class="number"><?php echo @number_format($row['conversions'], 0); ?></td>
					<td class="number">$<?php echo @number_format($row['revenue'], 2); ?></td>
					<td class="number">$<?php echo @number_format($epc, 2); ?></td>
					<td class="tail">&nbsp;</td>
				</tr>
				<?php
				@$total_clicks += $row['clicks'];
				@$total_conversions += $row['conversions'];
				@$total_revenue += $row['revenue'];
				@$total_cost += $row['cost'];
				@$total_profit += $row['profit'];
			}
		}
		else	
		{
			?>
			<tr>
				<td class="border">&nbsp;</td>
				<td colspan="5">No active ad groups found for the selected time frame.</td>
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
			<td style="">&nbsp;</td>
			<td style="">&nbsp;</td>
			<td>Total</td>
			<td style="">&nbsp;</td>
			<td class="number"><?php echo number_format($total_conversions, 0); ?></td>
			<td class="number">$<?php echo number_format($total_revenue, 2); ?></td>
			<td class="number">$<?php echo number_format($total_epc, 2); ?></td>
			<td class="tail">&nbsp;</td>
		</tr>
	</tbody>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="7">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>

<br/><br/>

