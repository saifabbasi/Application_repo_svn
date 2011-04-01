<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/ui.daterangepicker.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/jquery-ui-1.7.1.custom.css" rel="stylesheet" type="text/css" />

<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/jquery.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/daterangepicker.jQuery.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/jquery-ui-1.7.1.custom.min.js" type="text/javascript"></script>

<form method='POST'>
	<input type='hidden' name='id' value='<?php print $this->id; ?>'/>
	
	Date:<br/>
	<input type="text" style="width: 125px;" value="" id="datepicker" name="Date" />
	<br/><br/>
	<input type='submit' name='FormSubmit'/>
</form>

<script type="text/javascript">	
	$(function(){
		  $('#datepicker').daterangepicker(); 
	 });
</script>

