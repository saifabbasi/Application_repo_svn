
	<a href="/BevoMedia/Admin/BlacklistAdvertisers.html">&lt;- Advertisers</a> |
	<a rel="shadowbox;height=400;width=500" href="/BevoMedia/Admin/PostAdvertiserComment.html?ID=<?=$this->Post->ID?>">Post Comment</a> |
	<a rel="shadowbox;height=400;width=500" href="/BevoMedia/Admin/PostAdvertiser.html?ID=<?=$this->Post->ID?>">Edit Post</a>
	
	<br /><br />
	
	<h1><?=$this->Post->Name?></h1>
	
	<div>
		E-mail: <?php echo $this->Post->Email; ?><br />
		Network posted by: <?=($this->Post->Username=='')?$this->Post->NetworkName:$this->Post->Username; ?><br />
		Known Individuals attached to this company: <?php echo $this->Post->KnownAttachedIndividuals; ?><br />
		<br />
	</div>
	
	<div>
		<?php echo $this->Post->Text; ?>
	</div>
	
	<br /><br />
	<hr />
	
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
	<a rel="shadowbox;height=400;width=500" href="/BevoMedia/Admin/PostAdvertiserComment.html?ID=<?=$this->Post->ID?>&CommentID=<?=$Comment->ID?>">Edit</a> |
	<a href="/BevoMedia/Admin/ViewAdvertiserPost.html?ID=<?=$_GET['ID']?>&DeleteID=<?=$Comment->ID?>" onclick="return confirm('Are you sure you want to delete this comment?');">Delete Comment</a>
	<hr />
	<br /><br />
<?php 
	}
?>
	
	