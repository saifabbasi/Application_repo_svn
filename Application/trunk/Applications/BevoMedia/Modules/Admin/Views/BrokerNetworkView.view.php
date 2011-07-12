
<?=$this->TopMenu?>

<style>
<!--
	#brokerNetworkForm label
	{
		width: 110px;
		display: inline-block;
	}
-->
</style>

<a href="/BevoMedia/Admin/Networks.html">&lt;- Broker Networks</a> |
<a href="/BevoMedia/Admin/BrokerNetworkForm.html?ID=<?=$_GET['ID']?>">Edit</a> |
<a href="/BevoMedia/Admin/BrokerNetworkView.html?LoginID=<?=$_GET['ID']?>" target="_blank">Login as Network</a>

<br /><br />

<form id="brokerNetworkForm" method="post">
	<label for="Username">Username:</label>
	<?=isset($this->BrokerNetwork->Username)?$this->BrokerNetwork->Username:''?>
	<br /><br />
	
	<label for="Password">Password:</label>
	<?=isset($this->BrokerNetwork->Password)?$this->BrokerNetwork->Password:''?>
	<br /><br />
	
	<label for="TrackingPlatformID">Tracking Platform:</label>
	<?=$this->TrackingPlatform?>
	<br /><br />
	
	<label for="AffiliateNetworkID">Affiliate Network:</label>
	<?=isset($this->AffiliateNetwork)?$this->AffiliateNetwork:''?>
	<br /><br />
	
	<label for="Name">Name:</label>
	<?=isset($this->BrokerNetwork->Name)?$this->BrokerNetwork->Name:''?>
	<br /><br />
	
	<label for="Email">Email:</label>
	<?=isset($this->BrokerNetwork->Email)?$this->BrokerNetwork->Email:''?>
	<br /><br />
	
	<label for="Phone">Phone:</label>
	<?=isset($this->BrokerNetwork->Phone)?$this->BrokerNetwork->Phone:''?>
	<br /><br />
	
	<label for="PaymentPlan">Payment Plan:</label>
	$<?=isset($this->BrokerNetwork->PaymentPlan)?$this->BrokerNetwork->PaymentPlan:''?>
	<?=$this->BrokerNetwork->PaymentPlanTerm?>
	<br /><br />
	
	
	<label for="IsIntegrated">Is Integrated on Offer Brokering System:</label>
	<?=(isset($this->BrokerNetwork->IsIntegrated) && ($this->BrokerNetwork->IsIntegrated==1))?'Yes':'No'?>
	<br /><br />
	
	
	
	