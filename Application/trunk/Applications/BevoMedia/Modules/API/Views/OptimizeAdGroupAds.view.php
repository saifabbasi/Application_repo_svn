<h2 style='margin:0'>Optimizing All Ad Variations in this AdGroup</h2>
<?php

function flush_buffers()
{
    ob_end_flush();
    ob_flush();
    flush();
    ob_start(); 
}

function progress_bar($pc)
{
	print '<div style="float:left; height:100%; background-color:#008800; width:'.$pc.'%;">&nbsp;</div>';
	flush_buffers();
}

set_time_limit(0);
$notEnoughCred = false;
		
if(isset($_GET['Confirm']))
{
	require_once(PATH . 'AbsoluteIncludeHelper.include.php');
	print '<i>Processing...</i><br/><div style="width:400px; text-align:left; height:24px; padding:2px; border: dashed 1px #323232;">';
	if($this->Ad->ProviderType == '1')
	{
		
		$pct = sizeOf($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized']);
		$pct *= 4;
		$pct = 100/$pct;
		
		$calls = 1;
		$apility_user = new apility_assist($this->Ad->AccountID);
		
		foreach($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized'] as $Ad)
		{			
			$newUrl = $this->AdHelper->CheckAdURL($Ad->url, $Ad->ProviderType, true);
			progress_bar($pct);
			$c_id = $apility_user->getCampaignIdUsingName($Ad->CampaignName);
			progress_bar($pct);
			$ag_id = $apility_user->getAdGroupIdUsingName($c_id, $Ad->AdGroupName);
	
			progress_bar($pct);
			$this->AdHelper->UpdateAdWithOptimizedURL($Ad->ID, $newUrl);
			progress_bar($pct);
			$calls += 3;
			$apility_user->updateAdUrl($ag_id, $Ad->api_ad_id, $newUrl);
		}
		$sql = "INSERT INTO Adwords_API_Usage (Accounts_Adwords_ID, API_Calls) VALUES ('{$this->Ad->AccountID}', $calls)";
		$db = Zend_Registry::get('Instance/DatabaseObj');
		$db->exec($sql);
	}
	
	if($this->Ad->ProviderType == '3')
	{
		$msn_account = new Accounts_MSNAdCenter();
		$msn_account->GetInfo($this->Ad->AccountID);
		$msn = new msn_api($msn_account->Username, $msn_account->Password);
		$pct = sizeOf($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized']);
		$pct *= 6;
		$pct = 100/$pct;
		foreach($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized'] as $Ad)
		{
			progress_bar($pct);
			$newUrl = $this->AdHelper->CheckAdURL($Ad->url, $Ad->ProviderType, true);
			progress_bar($pct);
			$c_id = $msn->getCampaignIdUsingName($Ad->CampaignName);
			progress_bar($pct);
			$ag_id = $msn->getAdGroupIdUsingName($Ad->AdGroupName, $c_id);
			progress_bar($pct);
			$msn->updateAdDestinationUrl($ag_id, $Ad->api_ad_id, $newUrl);
			progress_bar($pct);
			$this->AdHelper->UpdateAdWithOptimizedURL($Ad->ID, $newUrl);
			progress_bar($pct);
			
		}

		
	}
	
	if($this->Ad->ProviderType == '1' || $this->Ad->ProviderType == '3')
	{
	?>
	
	</div>

	<?php 
	}//if($this->Ad->ProviderType == '1' || $this->Ad->ProviderType == '3')
	
	?>
	
	<div id='LayoutAssist_Shadowbox_Close_Timer'></div>
	<script language="Javascript">
	//<![CDATA[
		LayoutAssist.shadowboxCloseTimer(4);
		LayoutAssist.parentLocationTimer(3.5, '[PARENT.LOCATION.HREF]');
	//]]>
	</script>

	<?php 
}else{

?>

Are you sure you want to optimize the following ads?
<br/>
<?php 
	if($this->Ad->ProviderType == '1')
	{
		$calls = 1 + (3*sizeOf($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized']));
		$notEnoughCred = false;
		echo "<i>(this update will cost $". number_format($this->apiuse->CalcCost($calls), 2) ." to process)<br/></i>";	
		if($this->apiuse->Balance < $this->apiuse->CalcCost($calls))
		{
			$notEnoughCred = true;
		}
	}
	
?>
<br/>
<div id='ppp' style="height:300px; overflow:scroll;">
<?php
	foreach($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized'] as $K=>$Ad):
	$lineOne = stripslashes($Ad->description);
	$lineTwo = false;
?>
<p style="float:left; text-align: left; width: 250px; margin: 10px; line-height: 1.231; font-family: Arial; font-size: 13px;">
	<a style="text-decoration: underline; font-size: 123.1%; color: rgb(0, 0, 222);" href="<?php print $Ad->url; ?>"><?php print $Ad->title; ?></a>
	<br>
	<span><?php echo $lineOne?></span><br>
	<?php if($lineTwo):?>
		<span><?php echo $lineTwo?></span><br>
	<?php endif?>
	<span style="color: rgb(0, 128, 0);"><span style="font-weight: bolder;"><?php print $Ad->display_url; ?></span></span>
</p>

<?php echo ($K%2!=0)?"<br style='clear:both;'><br/>":'';?>
<?php endforeach?>
</div>


<a href='javascript:parent.Shadowbox.close();'>Cancel</a>

<?php }?>