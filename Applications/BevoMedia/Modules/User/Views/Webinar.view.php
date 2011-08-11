	
	<img src="/Themes/BevoMedia/img/pagedesc_coachingwebinars.png" />
	<br /><br />
<?php 
	if (isset($this->WebinarInfo->ID))
	{
		$time = 'TBD';
		$date = $this->WebinarInfo->Date;
		
		if (strstr($date, '00:00:00')) {
			
		} else {
			$time = date('g:i a', strtotime($date)).' Eastern Standard Time (GMT -5)';
		}
		$date = date('m/d/Y', strtotime($date));
?>
	Next Webinar Date: <?=$date?>
	<br />
	Time: <?=$time?>
	<br /><br />
	Webinar Password: <?=$this->WebinarInfo->Password?>
	<br /><br />
	Webinar Url: <a href="<?=$this->WebinarInfo->Url?>"><?=$this->WebinarInfo->Url?></a>
<?php 
	} else 
	{
?>
	To Be Announced
<?php 
	}
?>

	<br /><br />
	
	<div>
		Have a question? Email <a href="mailto:coaching@bevomedia.com">coaching@bevomedia.com</a>
	</div>