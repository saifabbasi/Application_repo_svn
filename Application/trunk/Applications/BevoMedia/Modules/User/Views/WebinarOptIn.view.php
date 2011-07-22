
	
	<img src="/Themes/BevoMedia/img/pagedesc_coachingwebinars.png" />
	<br /><br />

	<div>
		The Bevo Personalized Coaching Webinars are free of cost to verified Bevo members. Please Opt-in to receieve notices about the upcoming webinars and webinar passwords.
	</div>
	<br /><br />
	
	<label>
		<input type="checkbox" id="optIn" name="optIn" value="1" /> OPT IN to Coaching Webinars	
	</label>
	
	<br /><br />
	
	<div>
		Have a question? Email <a href="mailto:coaching@bevomedia.com">coaching@bevomedia.com</a>
	</div>
	
	<script type="text/javascript">
		$('#optIn').click(function() {
			window.location = '/BevoMedia/User/WebinarOptIn.html?optIn=1';
		});
	</script>