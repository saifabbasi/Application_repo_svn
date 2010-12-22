<?php if(!isset($_GET['Confirm'])):?>

<h3>
	Are you sure you want to perform an API Update?
</h3>

<p>
	Google Adwords API updates may take an extended amount of time to complete.
	<br/><br/>
	If you close this window the update will not continue to process - it is recommended that you open the link in a new window where it can perform uninterupted.
	<br/><br/>
	You will be shown the extended debug information while the update processes including api calls, microtime, data, etc.
</p>

<br/>

<a href='?id=<?php echo $_GET['id']?>&Confirm=TRUE&Debug=TRUE'>
	Update Stats Now
</a>

<br/><br/>

<a href='?id=<?php echo $_GET['id']?>&Confirm=TRUE&Debug=TRUE&NoCharge=TRUE'>
	Update Stats without Charging this User
</a>

<?php endif?>