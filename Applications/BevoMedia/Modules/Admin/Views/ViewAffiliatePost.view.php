
	<a href="/BevoMedia/Admin/BlacklistAffiliates.html">&lt;- Affiliates</a> |
	<a rel="shadowbox;height=400;width=500" href="/BevoMedia/Admin/PostAffiliateComment.html?ID=<?=$this->Post->ID?>">Post Comment</a>
	
	<br /><br />
	
	<h1><?=$this->Post->Name?></h1>
	
	<div>
		E-mail: <?php echo $this->Post->Email; ?><br />
		Network posted by: <?php echo $this->Post->NetworkName; ?><br /><br />
	</div>
	
	<div>
		<?php echo $this->Post->Text; ?>
	</div>
	
	<br /><br />
	
<?php 
	foreach ($this->Comments as $Comment)
	{
?>
	<div>
		<div><?=$Comment->Title?> by <?=($Comment->Username=='')?$Comment->NetworkName:$Comment->Username;?></div>
		<div>Date: <?=date('m/d/Y H:i a', strtotime($Comment->Created))?></div>
		<br />
		<div><?=$Comment->Text?></div>
	</div>
	
	<br /><br />
<?php 
	}
?>
	
	