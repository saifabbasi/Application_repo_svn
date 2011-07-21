	
	<img src="/Themes/BevoMedia/img/pagedesc_coachingwebinars.png" />
	<br /><br />
<?php 
	if (isset($this->WebinarInfo->ID))
	{
?>
	Next Webinar Date: <?=date('m/d/Y g:i a', strtotime($this->WebinarInfo->Date))?>
	<br /><br />
	Webinar Password: <?=$this->WebinarInfo->Password?>
<?php 
	} else 
	{
?>
	To Be Announced
<?php 
	}
?>
