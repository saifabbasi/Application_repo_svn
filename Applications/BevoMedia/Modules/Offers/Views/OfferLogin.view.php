
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>

	<div style="text-align: center">
		
		Log in below or view this search in the <a id="OfferHubLink" href="#">OfferHub</a>.

		<br /><br />
		
		<form method="post">
			<input type="hidden" id="Url" name="Url" value="/BevoMedia/Offers/Index.html" /> 
			
			<label>Username:</label>
			<input type="text" name="Email" value="" />
			<br />
			
			<label>Password:</label>
			<input type="password" name="Password" value="" />
			<br />
			
			<input type="submit" name="Login" value="Login" />
			
		</form>
		
	</div>


	
<script type="text/javascript">
	$('#OfferHubLink').click(function() {

		var url = 'http://offers.bevomedia.com/BevoMedia/OffersFront/Browse.html';
		url += window.location.hash;

		window.location = url;

		return false;
	});

	$(document).ready(function() {

		$('#Url').val($('#Url').val()+window.location.hash);
		
	});
</script>
	