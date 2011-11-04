<h2 style='margin:0'>Optimizing Single Ad Variation</h2>
<?php 
set_time_limit(0);

if(isset($_GET['Confirm']))
{
	require_once(PATH . 'AbsoluteIncludeHelper.include.php');
	if($this->Ad->ProviderType == '1')
	{
		
		$apility_user = new apility_assist($this->Ad->AccountID);
		$cs = $apility_user->getAllCampaigns();
		foreach($cs as $c)
		{
			if($c->name == $this->Ad->CampaignName)
			{
				$ag_id = $apility_user->getAdGroupIdUsingName($c->id, $this->Ad->AdGroupName);
				break;
			}
		}
	
	
		$this->AdHelper->UpdateAdWithOptimizedURL($this->Ad->ID, $this->newUrl);
		$apility_user->updateAdUrl($ag_id, $this->Ad->api_ad_id, $this->newUrl);
		$sql = "INSERT INTO Adwords_API_Usage (Accounts_Adwords_ID, API_Calls) VALUES ('{$this->Ad->AccountID}', 4)";
		$db = Zend_Registry::get('Instance/DatabaseObj');
		$db->exec($sql);
	}
	
	if($this->Ad->ProviderType == '3')
	{
		$msn_account = new Accounts_MSNAdCenter();
		$msn_account->GetInfo($this->Ad->AccountID);
		$msn = new msn_api($msn_account->Username, $msn_account->Password);
		$c_id = $msn->getCampaignIdUsingName($this->Ad->CampaignName);
		$ag_id = $msn->getAdGroupIdUsingName($this->Ad->AdGroupName, $c_id);
		$msn->updateAdDestinationUrl($ag_id, $this->Ad->api_ad_id, $this->newUrl);
		$this->AdHelper->UpdateAdWithOptimizedURL($this->Ad->ID, $this->newUrl);
		
	}
	
	if($this->Ad->ProviderType == '1' || $this->Ad->ProviderType == '3')
	{
	?>
	
	<br/>
	Modifying destination url from<br/>
	<?php print $this->ad->url; ?><br/>
	to<br/>
	<?php print $this->newUrl;; ?>
	<br/><br/>

	<?php 
	}//if($this->Ad->ProviderType == '1' || $this->Ad->ProviderType == '3')
	
	?>
	
	<div id='LayoutAssist_Shadowbox_Close_Timer'></div>
	<script language="Javascript">
		LayoutAssist.shadowboxCloseTimer(4);
		LayoutAssist.parentLocationTimer(3.5, '[PARENT.LOCATION.HREF]');
	</script>

	<?php 
}else{

?>

Are you sure you want to optimize the following ad?
<br/>
<?php 
	if($this->Ad->ProviderType == '1')
	{
		$notEnoughCred = false;
		echo "<i>(this update will cost $". number_format($this->apiuse->CalcCost(4), 2) ." to process)<br/></i>";	
		if($this->apiuse->Balance < $this->apiuse->CalcCost(4))
		{
			$notEnoughCred = true;
		}
	}
	
?>
<br/>

<?php
	$Ad = $this->Ad;
	$lineOne = stripslashes($Ad->description);
	$lineTwo = false;
?>
<p id='ppp' style="text-align: left; width: 250px; margin: auto; line-height: 1.231; font-family: Arial; font-size: 13px;">
	<a style="text-decoration: underline; font-size: 123.1%; color: rgb(0, 0, 222);" href="<?php print $Ad->url; ?>"><?php print $Ad->title; ?></a>
	<br />
	<span><?php echo $lineOne?></span><br />
	<?php if($lineTwo):?>
		<span><?php echo $lineTwo?></span><br />
	<?php endif?>
	<span style="color: rgb(0, 128, 0);"><span style="font-weight: bolder;"><?php print $Ad->display_url; ?></span></span>
</p>

<br/>


<a href='javascript:parent.Shadowbox.close();'>Cancel</a>

<?php }?>