<script language='javascript'>
<?php if($this->Location !== false):?>
	<?php if($this->Location == '{PARENT}'):?>
		parent.location.href = parent.location.href;
	<?php else: ?>
	parent.location.href = "<?php print $this->Location; ?>";
	<?php endif?>
<?php endif?>
parent.Shadowbox.close();
</script>