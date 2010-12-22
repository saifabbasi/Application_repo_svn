<?php
class Filter {

    function __construct($StartDate, $EndDate, $userId) {
        $request = array_merge($_COOKIE, $_GET);
        $this->costView = in_array(@$request['costs'], array('none', 'static', 'smart')) ? $request['costs'] : 'none';
        $this->staticCost = isset($request['staticCost']) ? floatval($request['staticCost']) : .5; 
        $this->userId = $userId;
        $this->filter_ppcprovider = 0;
        $this->DateRange = ($StartDate == $EndDate) ? date('m/d/Y', strtotime($StartDate)) : date('m/d/Y', strtotime($StartDate)) . ' - ' . date('m/d/Y', strtotime($EndDate));
        if(!empty($request['ppcprovider']) && is_numeric($request['ppcprovider']))
        	$this->filter_ppcprovider = (int)$request['ppcprovider'];
        $this->filter_ppcaccount = 0;
        if(!empty($request['ppcaccount']) && is_numeric($request['ppcaccount']))
        	$this->filter_ppcaccount = (int)$request['ppcaccount'];
        
        $this->filter_ppccampaign = 0;
        if(!empty($request['ppccampaign']) && is_numeric($request['ppccampaign']))
        	$this->filter_ppccampaign = (int)$request['ppccampaign'];
        	
        $this->filter_ppcadgroup = 0;
        if(!empty($request['ppcadgroup']) && is_numeric($request['ppcadgroup']))
        	$this->filter_ppcadgroup = (int)$request['ppcadgroup'];
        
        $this->filter_keyword = '';
        $this->filter_keywordid = 0;
        if(!empty($request['keyword']))
        {
        	$this->filter_keyword = $request['keyword'];
        	$sql = "SELECT id FROM bevomedia_keyword_tracker_keywords WHERE keyword = '".mysql_real_escape_string($this->filter_keyword)."'";
        	$query = mysql_query($sql);
        	if($row = mysql_fetch_array($query))
        		$this->filter_keywordid = $row['id'];
        }
        
        $this->filter_visitorip = '';
        $this->filter_visitoripid = 0;
        if(!empty($request['visitorip']))
        {
        	$this->filter_visitorip = $request['visitorip'];
        	$sql = "SELECT id FROM bevomedia_tracker_ips WHERE ipAddress = '".mysql_real_escape_string($this->filter_visitorip)."'";
        	$query = mysql_query($sql);
        	if($row = mysql_fetch_array($query))
        		$this->filter_visitoripid = $row['id'];
        }
        
        
        // Set all as cookies
        $filter_cookie_expiration = 0;
        $filter_cookie_path = '/';
        @setcookie('costs', $this->costView, $filter_cookie_expiration, $filter_cookie_path);
        @setcookie('staticCost', $this->staticCost, $filter_cookie_expiration, $filter_cookie_path);
        @setcookie('ppcprovider', $this->filter_ppcprovider, $filter_cookie_expiration, $filter_cookie_path);
        @setcookie('ppcaccount', $this->filter_ppcaccount, $filter_cookie_expiration, $filter_cookie_path);
        @setcookie('ppccampaign', $this->filter_ppccampaign, $filter_cookie_expiration, $filter_cookie_path);
        @setcookie('ppcadgroup', $this->filter_ppcadgroup, $filter_cookie_expiration, $filter_cookie_path);
        @setcookie('DateRange', $this->DateRange, $filter_cookie_expiration, $filter_cookie_path);
        @setcookie('keyword', $this->filter_keyword, $filter_cookie_expiration, $filter_cookie_path);
        @setcookie('visitorip', $this->filter_visitorip, $filter_cookie_expiration, $filter_cookie_path);
        
    }
    
    public function getSql() {
        // Create snippet of SQL WHERE statement for filtering data
        $filtering_rev_sql = '';
        $filtering_cost_sql = '';
        $filtering_mod_sql = '';
        
        if($this->filter_ppcprovider != 0)
        {
        	$filtering_rev_sql .= ' AND accounts.providerId = '.$this->filter_ppcprovider;
        	$filtering_cost_sql .= ' AND accounts.providerId = '.$this->filter_ppcprovider;
        	$filtering_mod_sql .= ' AND campaigns.ProviderType  = '.$this->filter_ppcprovider;
        }
        
        if($this->filter_ppcaccount != 0)
        {
        	$filtering_rev_sql .= ' AND accounts.accountId = '.$this->filter_ppcaccount;
        	$filtering_cost_sql .= ' AND accounts.accountId = '.$this->filter_ppcaccount;
        	$filtering_mod_sql .= ' AND campaigns.accountId = '.$this->filter_ppcaccount;
        }
        
        if($this->filter_ppccampaign != 0)
        {
        	$filtering_rev_sql .= ' AND campaigns.id = '.$this->filter_ppccampaign;
        	$filtering_cost_sql .= ' AND campaigns.id = '.$this->filter_ppccampaign;
        	$filtering_mod_sql .= ' AND campaigns.id = '.$this->filter_ppccampaign;
        }
        
        if(!empty($this->filter_keyword))
        	$filtering_rev_sql .= ' AND (stats.raw_keyword_id = '.$this->filter_keywordid.' OR stats.bid_keyword_id = '.$this->filter_keywordid.')';
        
        if(!empty($this->filter_visitorip))
        	$filtering_rev_sql .= ' AND stats.ipId = '.$this->filter_visitoripid;
        	return array($filtering_rev_sql, $filtering_cost_sql, $filtering_mod_sql);
     }
     
	public function getPPCNormalizedSql() 
	{
        // Create snippet of SQL WHERE statement for filtering data
        $filtering_rev_sql = '';
        $filtering_cost_sql = '';
        $filtering_mod_sql = '';
        
        if($this->filter_ppcprovider != 0)
        {
        	$filtering_rev_sql .= ' AND campaigns.ProviderType = '.$this->filter_ppcprovider;
        	$filtering_cost_sql .= ' AND c.ProviderType = '.$this->filter_ppcprovider;
        	$filtering_mod_sql .= ' AND campaigns.ProviderType  = '.$this->filter_ppcprovider;
        }
        
        if($this->filter_ppcaccount != 0)
        {
        	$filtering_rev_sql .= ' AND campaigns.accountId = '.$this->filter_ppcaccount;
        	$filtering_cost_sql .= ' AND c.accountId = '.$this->filter_ppcaccount;
        	$filtering_mod_sql .= ' AND campaigns.accountId = '.$this->filter_ppcaccount;
        }
        
        if($this->filter_ppccampaign != 0)
        {
        	$filtering_rev_sql .= ' AND campaigns.id = '.$this->filter_ppccampaign;
        	$filtering_cost_sql .= ' AND c.id = '.$this->filter_ppccampaign;
        	$filtering_mod_sql .= ' AND campaigns.id = '.$this->filter_ppccampaign;
        }
        
        if ($this->filter_ppcadgroup != 0)
        {
        	$filtering_cost_sql .= ' AND a.id = '.$this->filter_ppcadgroup;
        	$filtering_rev_sql .= ' AND ads.id = '.$this->filter_ppcadgroup;
        }
        
        
        if(!empty($this->filter_keyword))
        	$filtering_rev_sql .= ' AND (stats.raw_keyword_id = '.$this->filter_keywordid.' OR stats.bid_keyword_id = '.$this->filter_keywordid.')';
        
        if(!empty($this->filter_visitorip))
        	$filtering_rev_sql .= ' AND stats.ipId = '.$this->filter_visitoripid;
        	return array($filtering_rev_sql, $filtering_cost_sql, $filtering_mod_sql);
     }
    
    function show_ppv_filtering_table()
    {
?>
<form method="get">

<div class="filtering formslim">
	<div class="col-left">
		<div class="option">
			<label for="pcccampaign">PPV Provider</label>
			<select class="formselect" name="provider" id="ppccampaign">
				<option value="">--</option>
				<?php
					$ppvSet = array(array('id'=>5, 'Name'=>'TrafficVance'), array('id'=>6, 'Name'=>'AdOn Network'), array('id'=>7, 'Name'=>'Media Traffic'), array('id'=>8, 'Name'=>'Direct CPV'), array('id'=>9, 'Name'=>'Lead Impact'));
					foreach($ppvSet as $row )
					{
						echo '<option value="'.$row['id'].'"';
						if($row['id'] == $this->filter_ppcprovider)
							echo ' selected="selected"';
						echo '>'.htmlentities($row['Name']).'</option>';
					}
				?>
			</select>
		</div>
		<div class="option">
			<label for="ppcadgroup">Campaign</label>
			<select class="formselect" name="campaign" id="ppcadgroup">
				<option value="">--</option>
				<?php
				if(!empty($this->filter_ppcprovider))
				{
					$sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = ".$this->userId." AND ProviderType = ".$this->filter_ppcprovider." AND Name != '' ORDER BY Name";
					$query = mysql_query($sql);
					while($row = mysql_fetch_array($query))
					{
						echo '<option value="'.$row['id'].'"';
						if($row['id'] == $this->filter_ppccampaign)
							echo ' selected="selected"';
						echo '>'.htmlentities($row['name']) .'</option>';
					}
				}
				?>
			</select>
		</div>
	</div>
	<div class="col-right">
		<div class="option">
			<label for="datepicker">Date(s)</label>
			<input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?= htmlentities($this->DateRange); ?>" />
		</div>
	</div>
	<div class="actions">
		<a class="tbtn floatleft" href="AdjustMediaBuyPrice.html">Adjust your campaign CPC</a>
		<input class="formsubmit track_apply floatright" type="submit" value="Apply" />
		<div class="clear"></div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {

		$('#ppccampaign').change( function() {
			$.getJSON("/BevoMedia/KeywordTracker/json.html?list=ppv_campaign&provider=" + $(this).val(), function(data) {
				var options = '<option value="">--</option>';
				for (var i = 0; i < data.length; i++) {
					options += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
				}
				$('#ppcadgroup').html(options);
				$('#ppcadgroup').val('');
			});
		});

		$('#ppcadgroup').change( function() {
			return;
			alert('hi');
			$.getJSON("/BevoMedia/KeywordTracker/json.html?list=advar&ppcadgroup=" + $(this).val(), function(data) {
				var options = '<option value="">--</option>';
				for (var i = 0; i < data.length; i++) {
					options += '<option value="' + data[i].id + '">' + data[i].title + '</option>';
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
    
    function show_filtering_table($showCostViews = false)
    {
    ?>
    <form method="get">
    <?php if($showCostViews) {
    	
    	
    	$costLink = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')).'?';
    	
    	foreach ($_GET as $Key => $Value)
    	{
    		if ($Key=='costs') continue;
    		
    		$costLink .= $Key.'='.$Value.'&';
    	}
    	$costLink .= 'costs=';
    	
//    $costLink = $_SERVER['REQUEST_URI'];
//    $costLink .= stristr($costLink, '.html?') === false ? '?' : '';
//    $costLink = preg_replace('/&?costs=[^&]/', '', $costLink);
//    $costLink .= '&costs=';
    ?>
    
<div id="kwtr-expdata-wrapper">
	<a class="button kwtr-expdata<?= $this->costView == 'none' ? ' active' : '' ?>" id="kwtr-none" href="<?= $costLink . 'none' ?>">None</a>
	<a class="button kwtr-expdata<?= $this->costView == 'static' ? ' active' : '' ?>" id="kwtr-static" href="<?= $costLink . 'static' ?>">Static</a>
	<a class="button kwtr-expdata<?= $this->costView == 'smart' ? ' active' : '' ?>" id="kwtr-smart" href="<?= $costLink . 'smart' ?>">Smart</a>
	
	<div class="kwtr-expdata-sepa"></div>
	
	<div class="kwtr-expdata-infoarea">	
		<p class="kwtr-active" id="kwtr-default-info">
			<strong>Expense Data:</strong><br />
			Hover over each button for an explanation.
		</p>
		<p class="kwtr-hide" id="kwtr-none-info">
			<strong>No Expense Data:</strong><br />
			Display your keywords and conversion stats without click expense.
		</p>
		<p class="kwtr-hide" id="kwtr-static-info">
			<strong>Static Expense Data:</strong><br />
			Display your keywords and conversion stats with static click expense.
		</p>
		<p class="kwtr-hide" id="kwtr-smart-info">
			<strong>Smart Expense Data:</strong> View your keyword, conversion, and click expense stats synced directly with your PPC account. Costs are exact and update every night.
		</p>
	</div>
	<div class="clear"></div>
	
	<script type="text/javascript">
		$(document).ready(function() {
			//keyword tracker expense data button hover
			$('a.button.kwtr-expdata').hover(function() {
				$('p.kwtr-active').hide().removeClass('kwtr-active');
				$('p#'+$(this).attr('id')+'-info').show().addClass('kwtr-active');
			});			
		});
	</script>
	
</div><!--close kwtr-expdata-wrapper-->
    <?php  } ?>
    	
    <div class="filtering formslim">
    	<div class="col-left">
    		<div class="option">
    			<label for="ppcprovider">PPC Network</label>
    			<select class="formselect" id="ppcprovider" name="ppcprovider">
    				<option value="">--</option>
    				<?php
    				
    				$userId = $this->userId;
    				$sql = "SELECT 1 AS `providerId`,
								       _utf8'Google Adwords' AS `providerName`,
								       `Accounts_Adwords`.`id` AS `accountId`,
								       `Accounts_Adwords`.`user__id` AS `user__id`,
								       `Accounts_Adwords`.`username` AS `accountName`
								FROM 
									`bevomedia_accounts_adwords` `Accounts_Adwords`
								WHERE 
									(Accounts_Adwords.user__id = {$userId}) AND
									(deleted = 0)
								GROUP BY providerName
								UNION
								SELECT 2 AS `providerId`,
								       _utf8'Yahoo Search Marketing' AS `providerName`,
								       `Accounts_Yahoo`.`id` AS `accountId`,
								       `Accounts_Yahoo`.`user__id` AS `user__id`,
								       `Accounts_Yahoo`.`username` AS `accountName`
								FROM 
									`bevomedia_accounts_yahoo` `Accounts_Yahoo`
								WHERE
									(Accounts_Yahoo.user__id = {$userId}) AND
									(deleted = 0)
								GROUP BY providerName
								UNION
								SELECT 3 AS `providerId`,
								       _utf8'Microsoft adCenter' AS `providerName`,
								       `Accounts_MSNAdCenter`.`id` AS `accountId`,
								       `Accounts_MSNAdCenter`.`user__id` AS `user__id`,
								       `Accounts_MSNAdCenter`.`username` AS `accountName`
								FROM 
									`bevomedia_accounts_msnadcenter` `Accounts_MSNAdCenter`
								WHERE				 
									(Accounts_MSNAdCenter.user__id = {$userId})  AND
									(deleted = 0)
								GROUP BY providerName
								ORDER BY
									providerName
    				
    						";
//    				echo $sql; die;
    				
//    				$sql = "SELECT DISTINCT providerId, providerName FROM bevomedia_view_ppc_accounts WHERE user__id = ".$this->userId." ORDER BY providerName";
    				$query = mysql_query($sql);
    				while($row = mysql_fetch_array($query))
    				{
    					echo '<option value="'.$row['providerId'].'"';
    					if($row['providerId'] == $this->filter_ppcprovider)
    						echo ' selected="selected"';
    					echo '>'.htmlentities($row['providerName']).'</option>';
    				}
    				?>
    			</select>
    		</div>
    		<div class="option">
    			<label for="ppcaccount">Account name</label>
    			<select class="formselect" name="ppcaccount" id="ppcaccount">
    				<option value="">--</option>
    				<?php
    				if(!empty($this->filter_ppcprovider))
    				{
    					$userId = $this->userId;
    					$filter_ppcprovider = $this->filter_ppcprovider;
	    				
    					if ($filter_ppcprovider==1)
						{
							$Table = "bevomedia_accounts_adwords";
						} else
						if ($filter_ppcprovider==2)
						{	
							$Table = "bevomedia_accounts_yahoo";
						} else
						if ($filter_ppcprovider==3)
						{	
							$Table = "bevomedia_accounts_msnadcenter";
						}
						
						$sql = "SELECT
							       `id` AS `accountId`,
							       `username` AS `accountName`
								FROM
									{$Table}
								WHERE
									(user__id = {$userId}) AND
									(deleted = 0)
								";
    					
    					
    					
//    					$sql = "SELECT accountId, accountName FROM bevomedia_view_ppc_accounts WHERE user__id = ".$this->userId." AND providerId = ".$this->filter_ppcprovider." ORDER BY accountName";
    					$query = mysql_query($sql);
    					while($row = mysql_fetch_array($query))
    					{
    						echo '<option value="'.$row['accountId'].'"';
    						if($row['accountId'] == $this->filter_ppcaccount)
    							echo ' selected="selected"';
    						echo '>'.htmlentities($row['accountName']).'</option>';
    					}
    				}
    				?>
    			</select>
    		</div>
    		<div class="option">
    			<label for="pcccampaign">Campaign</label>
    			<select class="formselect" name="ppccampaign" id="ppccampaign">
    				<option value="">--</option>
    				<?php
    				if(!empty($this->filter_ppcaccount))
    				{
    					$sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = ".$this->userId." AND accountId = ".$this->filter_ppcaccount." ORDER BY name";
    					$query = mysql_query($sql);
    					while($row = mysql_fetch_array($query))
    					{
    						echo '<option value="'.$row['id'].'"';
    						if($row['id'] == $this->filter_ppccampaign)
    							echo ' selected="selected"';
    						echo '>'.htmlentities($row['name']).'</option>';
    					}
    				}
    				?>
    			</select>
    		</div>
    		<div class="option">
    			<label for="pcccampaign">AdGroup</label>
    			<select class="formselect" name="ppcadgroup" id="ppcadgroup">
    				<option value="">--</option>
    				<?php
    				if(!empty($this->filter_ppccampaign))
    				{
    					$sql = "SELECT 
    								bevomedia_ppc_adgroups.id, 
    								bevomedia_ppc_adgroups.name 
    							FROM 
    								bevomedia_ppc_adgroups,
    								bevomedia_ppc_campaigns
    							WHERE 
    								(bevomedia_ppc_campaigns.id = bevomedia_ppc_adgroups.campaignId) AND
    								(user__id = ".$this->userId.") AND 
    								(campaignId = ".$this->filter_ppccampaign.")
    							ORDER BY name";
    					$query = mysql_query($sql);
    					while($row = mysql_fetch_array($query))
    					{
    						echo '<option value="'.$row['id'].'"';
    						if($row['id'] == $this->filter_ppcadgroup)
    							echo ' selected="selected"';
    						echo '>'.htmlentities($row['name']).'</option>';
    					}
    				}
    				?>
    			</select>
    		</div>
    	</div>
    	<div class="col-right">
    		<div class="option">
    			<label for="datepicker">Date(s)</label>
    			<input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo htmlentities($this->DateRange); ?>" />
    		</div>
    		<div class="option">
    			<label for="keyword">Keyword</label>
    			<input class="formtxt" type="text" id="keyword" name="keyword" value="<?php echo htmlentities($this->filter_keyword); ?>" />
    		</div>
    		<div class="option">
    			<label for="visitorip">Visitor IP Address</label>
    			<input class="formtxt" type="text" id="visitorip" name="visitorip" value="<?php echo htmlentities($this->filter_visitorip); ?>" />
    		</div>
    	</div>
    	<div class="actions">
		<a class="tbtn floatleft" href='/BevoMedia/KeywordTracker/ManuallyUploadSubIDs.html'>
			Manually Upload your Subids
		</a>
    		<?php if($showCostViews && $this->costView == 'static') { ?>
    			<div class="floatleft margintop">
				Estimate your static CPC:&nbsp;&nbsp;$<input class="formtxt wide_number" type='text' name="staticCost" value="<?php echo htmlentities(number_format($this->staticCost, 2, '.', '')); ?>">
			</div>
    		<?php } ?>
    		<input class="formsubmit track_apply floatright" type="submit" value="Apply">
    		<div class="clear"></div>
    	</div>
    </div>
    
    <script type="text/javascript">
    	jQuery(document).ready( function($) {
    
    		$('#ppcprovider').change( function() {
    			$.getJSON("/BevoMedia/KeywordTracker/json.html?list=account&ppcprovider=" + $(this).val(), function(data) {
    				var options = '<option value="">--</option>';
    				for (var i = 0; i < data.length; i++) {
    					options += '<option value="' + data[i].accountId + '">' + data[i].accountName + '</option>';
    				}
    				$('#ppcaccount').html(options);
    				$('#ppcaccount').val('');
    			});
    		});
    
    		$('#ppcaccount').change( function() {
    			$.getJSON("/BevoMedia/KeywordTracker/json.html?list=campaign&ppcaccount=" + $(this).val(), function(data) {
    				var options = '<option value="">--</option>';
    				for (var i = 0; i < data.length; i++) {
    					options += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
    				}
    				$('#ppccampaign').html(options);
    				$('#ppccampaign').val('');
    			});
    		});

    		$('#ppccampaign').change( function() {
    			$.getJSON("/BevoMedia/KeywordTracker/json.html?list=adgroup&ppccampaign=" + $(this).val(), function(data) {
    				var options = '<option value="">--</option>';
    				for (var i = 0; i < data.length; i++) {
    					options += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
    				}
    				$('#ppcadgroup').html(options);
    				$('#ppcadgroup').val('');
    			});
    		});
    
    	});
    </script>
					<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script> 
<style type="text/css"> 
#tooltip{
 line-height: 1.231; font-family: Arial; font-size: 13px;
  position:absolute;
	  border:1px solid #333;
		background:#f7f5d1;
		  padding:2px 5px;
			display:none;
			  width:320px;
				text-align:center;
				  }
				  .tooltip {
					  text-decoration: none !important;
						font-weight: bold;
							}
							.tooltip.defaultLink {
							  color: maroon;
								font-size: 12px;
								  font-style: normal;
									font-weight: normal;
									  font-size: 12px;
										}
										.successInstall {
										  background-color: #008800;
											border: solid 2px #ffffff;
											  color: #ffffff;
												}
												.failInstall {
												  background-color: #880000;
													border: solid 2px #ffffff;
													  color: #ffffff;
														}
													</style> 


	</form>
	<br />
<a title="Over reporting is often due to users clicking the back button in their browser, causing your javascript to fire twice.<br />We strongly recommend you always have your offers load in a new window to avoid this." class="tooltip"> 
Are your stats over-reporting?
		  <img height="12" width="12" src="/Themes/BevoMedia/img/questionMarkIcon.png"/> 
		</a>
<?php
	}
}