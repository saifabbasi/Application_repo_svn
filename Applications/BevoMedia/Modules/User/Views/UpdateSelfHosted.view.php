<h1>
	Update Self Hosted
</h1>

<?php

	require_once(PATH . 'PhoneHome.class.php');
	$ph = new PhoneHome();
	$ph->bevo_auth($this->User);
	
	if($ph->updateReady())
	{
		$updateReady = true;
	}else{
		$updateReady = false;
		$updateCooldown = $ph->getCooldownRemaining();
	}
?>

<br/>

<?php if($updateReady) { ?>
<?php ?>
<h2>Updating</h2>
<?php
if($ph->disabled)
    die("You've opted out of sending data to Bevo servers.");
if($ph->doUpdate())
    echo "Update successful!";
else
    echo "Update failed!";
?>
You will be able to update again in approximately <?php echo $ph->getCooldownRemaining()?> minutes.

<iframe  width='100%' style="display: none; border:none;" src='/BevoMedia/User/UpdateSelfHostedDownload.html'></iframe>


<?php } else {?>
<h2>Update Not Ready</h2>
<?php $updateCooldown = $ph->getCooldownRemaining(); ?>
You can update again in approximately <?php echo $updateCooldown?> minutes.
<?php } ?>