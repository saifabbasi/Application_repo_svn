	<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>
	
	<div id="pageinfo" class="sub">
		<h2>Register Network</h2>	
	</div>
	
	
	<div class="clear"></div>

	<form method="post" id="signupForm" class="signupForm" action="/BevoMedia/Networks/SignUpProcess.html">
				
		<label for="NetworkName">
			<span class="label">[required] Network Name:</span>
			<input type="text" name="NetworkName" value="" id="NetworkName" class="required" />
		</label>
		
		<label for="ContactName">
			<span class="label">[required] Contact Name:</span>
			<input type="text" name="ContactName" value="" id="ContactName" class="required" />
		</label>
		
		<label for="ContactEmail">
			<span class="label">[required] Contact Email:</span>
			<input type="text" name="ContactEmail" value="" id="ContactEmail" class="required" />
		</label>
		
		<label for="ContactPhone">
			<span class="label">[required] Contact Phone:</span>
			<input type="text" name="ContactPhone" value="" id="ContactPhone" class="required" />
		</label>
		
		<label for="ContactAddress1">
			<span class="label">[required] Contact Address 1:</span>
			<input type="text" name="ContactAddress1" value="" id="ContactAddress1" class="required" />
		</label>
		
		<label for="ContactAddress2">
			<span class="label">Contact Address 2:</span>
			<input type="text" name="ContactAddress2" value="" id="ContactAddress2" />
		</label>
		
		<label for="ContactCity">
			<span class="label">[required] Contact City:</span>
			<input type="text" name="ContactCity" value="" id="ContactCity" class="required" />
		</label>
		
		<label for="ContactCountry">
			<span class="label">[required] Contact Country:</span>
			<select id="ContactCountry" name="ContactCountry" class="required">
				<option value=""></option>
<?php 
	foreach ($this->Countries as $CountryItt)
	{
?>
				<option value="<?php echo $CountryItt->code?>" <?php echo $Selected;?>><?php echo $CountryItt->country?></option>
<?php 
	}
?>
			</select>
		</label>
		
		<label for="ContactState">
			<span class="label">[required] Contact State:</span>
			<select id="ContactState" name="ContactState" class="required">
				<option value='-1'>N/A</option>
			</select>
		</label>
		
		<label for="ApproximateNumberAffiliates">
			<span class="label">[required] Approximate Number of Affiliates in Network:</span>
			<input type="text" name="ApproximateNumberAffiliates" value="" id="ApproximateNumberAffiliates" class="required" />
		</label>
		<br />
		<label for="TrackingPlatform">
			<span class="label">[required] Tracking Platform:</span>
			<input type="text" name="TrackingPlatform" value="" id="TrackingPlatform" class="required" />
			<span>(Ex: Direct Track, Linktrust, Custom, ect)</span>
		</label>
		
		<label for="Comment">
			<span class="label">Comment:</span>
			<textarea name="Comment" id="Comment" rows="5" cols="25"></textarea>
		</label>
		
		<br /><br />
		
		<input type="submit" id="Continue" name="Continue" value="Continue" />
		<br /><br />
		<div>
			*Be sure to send a vector image of your network logo to <a href="mailto:networks@bevomedia.com">networks@bevomedia.com</a>.<br />
			*Bevo must have a master account with your network for implementation
		</div>
		
	</form>
	
	
	<br />
	
	<script type="text/javascript">
	$('#signupForm #Continue').click(function() {
		var Inputs = $('#signupForm .required');

		var FailedValidation = false;
		$.each(Inputs, function(index, Input) {
			if (Input.value=='')
			{
				FailedValidation = true;
			}
		});

		if (FailedValidation)
		{
			alert('You must fill in all required fields.');
			return false;
		}
		
		return true;
	});
	</script>
	
	<script type="text/javascript">
	//<![CDATA[
		$(document).ready(function() {
			$('#ContactCountry').val('');
		});
		
		$('#ContactCountry').change(function() {

			if ($(this).val()=='US' || $(this).val()=='CA')
			{
				$('#ContactState option').remove();

				$.getJSON('/BevoMedia/Networks/JSONGetCountryStates.html', {'Code': $(this).val()}, function(Data, TextStatus) {

					$('#ContactState').append("<option value=''>Select State</option>");
					$.each(Data, function(Index, Value) {
						$('#ContactState').append("<option value='"+Value['initials']+"'>"+Value['name']+"</option>");
					});
					
				});
				
				
			} else
			{
				$('#ContactState option').remove();
				
				$('#ContactState').append("<option value='-1'>N/A</option>");
			}
			
		});
	//]]>
	</script>
	
	