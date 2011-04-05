<h2 class='adminPageHeading floatRight'>Performance Connector Edit</h2>

<a href='PerformanceConnector.html'>View All</a>


<h1><?php print $this->User->firstName; ?> <?php print $this->User->lastName; ?></h1>

<br/>

<b>User ID:</b> <?php print $this->User->id; ?> <br/>
<b>User Email:</b> <?php print $this->User->email; ?> <br/>
<?php 
	$contactInfo = $this->User->getPerformanceConnectorContact();
	if ($contactInfo!=null)
	{
?>
<b>IM:</b> <?php print $contactInfo->im.' ('.$contactInfo->im_service.')'; ?> <br/>
<b>Phone:</b> <?php print $contactInfo->phone; ?> <br/>
<?php 
	}
?>
<br/><br/>

<form method='post' style="line-height:200%">

<?php foreach ($this->networks as $network):?>
<?php $checked = (in_array($network->id, $this->userNetworks)?'checked="checked"':'');?>
<label>
	<input type="checkbox" name="network[]" <?php echo $checked?> value="<?php echo $network->id?>"/><?php echo $network->title?>
</label>

<br/>
<?php endforeach;?>

<br/>
<input type="submit" name="submit"/>

</form>