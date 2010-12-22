<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','subids','upload'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper,false); ?>

<div class="pagecontent">
<h3><?php print $this->insertCount; ?> SubIDs have been inserted.</h3>


<?php /* why a form here?
<form method='post' action='ManuallyUploadSubIDsComplete.html'>
	<fieldset class='textAlignCenter'>
	<br/>
	<h1>Success!</h1>
	<h3><?php print $this->insertCount; ?> SubIDs inserted.</h3>
	<br/>
	</fieldset>
</form> */ ?>
</div><!--close pagecontent-->