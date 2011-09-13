	<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>
	
	<div id="pageinfo" class="sub">
		<h2>Payment</h2>	
	</div>
	
	
	<div class="clear"></div>
	
	<div class="content">
	<?php 
		if (isset($_GET['Response']))
		{
	?>
		<div style="border: 1px #E85163 solid; background-color: #F5AEB6; width: 100%; height: 45px; line-height: 45px; color: #000; text-align: center;">
			<?php echo $_GET['Response'];?>
		</div>
		<br />
	<?php 
		}
	?>
	
	<?php 
		$Company = '';
		$FirstName = '';
		$LastName = '';
		$Address1 = $_SESSION['SignUpData']['ContactAddress1'];
		$Address2 = $_SESSION['SignUpData']['ContactAddress2'];
		$City = $_SESSION['SignUpData']['ContactCity'];
		$State = $_SESSION['SignUpData']['ContactState'];
		$Zip = '';
		$Phone = $_SESSION['SignUpData']['ContactPhone'];
		$PaymentTerm = '';
		$Country = $_SESSION['SignUpData']['ContactCountry'];
		$CustomPrice = '';
	
		if (isset($_SESSION['PaymentData']))
		{
			$Company = $_SESSION['PaymentData']['Company'];
			$FirstName = $_SESSION['PaymentData']['FirstName'];
			$LastName = $_SESSION['PaymentData']['LastName'];
			$Address1 = $_SESSION['PaymentData']['Address1'];
			$Address2 = $_SESSION['PaymentData']['Address2'];
			$City = $_SESSION['PaymentData']['City'];
			$State = $_SESSION['PaymentData']['State'];
			$Zip = $_SESSION['PaymentData']['Zip'];
			$Phone = $_SESSION['PaymentData']['Phone'];
			$PaymentTerm = $_SESSION['PaymentData']['PaymentTerm'];
			$Country = $_SESSION['PaymentData']['Country'];
			$CustomPrice = $_SESSION['PaymentData']['CustomPrice'];
		} 
	?>
	

	<form method="post" class="signupForm" action="/BevoMedia/Networks/NetworkPayment.html">
		
		<label for="Company">
			<span class="label">Company:</span>
			<input type="text" name="Company" value="<?php echo $Company; ?>" id="Company" />
		</label>
		
		<label for="FirstName">
			<span class="label">[required] First Name:</span>
			<input type="text" name="FirstName" value="<?php echo $FirstName; ?>" id="FirstName" class="required" />
		</label>
		
		<label for="LastName">
			<span class="label">[required] Last Name:</span>
			<input type="text" name="LastName" value="<?php echo $LastName;?>" id="LastName" class="required" />
		</label>
		
		<label for="Address1">
			<span class="label">[required] Address 1:</span>
			<input type="text" name="Address1" value="<?php echo $Address1;?>" id="Address1" class="required" />
		</label>
		
		<label for="Address2">
			<span class="label">Address 2:</span>
			<input type="text" name="Address2" value="<?php echo $Address2;?>" id="Address2" />
		</label>
		
		<label for="City">
			<span class="label">[required] City:</span>
			<input type="text" name="City" value="<?php echo $City;?>" id="City" class="required" />
		</label>
		
		<label for="Country">
			<span class="label">[required] Country:</span>
			<select id="Country" name="Country" class="required">
				<option value=""></option>
<?php 
	foreach ($this->Countries as $CountryItt)
	{
		$Selected = '';
		if ($Country==$CountryItt->code) $Selected = 'selected';
?>
				<option value="<?php echo $CountryItt->code?>" <?php echo $Selected;?>><?php echo $CountryItt->country?></option>
<?php 
	}
?>
			</select>
		</label>
		
		<label for="State">
			<span class="label">[required] State:</span>
			<select id="State" name="State" class="required" Default="<?php echo $State?>">
				<option value='-1'>N/A</option>
			</select>
		</label>
		
		<label for="Zip">
			<span class="label">[required] Zip:</span>
			<input type="text" name="Zip" value="<?php echo $Zip;?>" id="Zip" class="required" />
		</label>
		
		<label for="Phone">
			<span class="label">[required] Phone:</span>
			<input type="text" name="Phone" value="<?php echo $Phone;?>" id="Phone" class="required" />
		</label>
		
		
		<br /><br />
		
		
		<label for="CreditCardNumber">
			<span class="label">[required] Credit Card Number:</span>
			<input type="text" name="CreditCardNumber" value="" id="CreditCardNumber" class="required" maxlength="16" />
		</label>
		
		<label for="ExpirationMonth">
			<span class="label">[required] Expiration Date:</span>
			
			<select id="ExpirationMonth" name="ExpirationMonth" class="required">
				<option value=""></option>
<?php 
	for ($i=1; $i<=12; $i++)
	{
		if ($i<10) $i = '0'.$i;
?>
				<option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php 
	}
?>
			</select>
			
			<select id="ExpirationYeah" name="ExpirationYeah" class="required">
				<option value=""></option>
<?php 
	for ($i=date('Y'); $i<=(date('Y')+10); $i++)
	{
		$value = substr($i, 2);
?>
				<option value="<?php echo $value;?>"><?php echo $i;?></option>
<?php 
	}
?>
			</select>
			
		</label>
		
		<label for="CVV">
			<span class="label">[required] CVV:</span>
			<input type="text" name="CVV" value="" id="CVV" class="required" size="3" maxlength="4" />
		</label>
		
		<br /><br />
		
<?php 
	foreach ($this->PaymentTerms as $Term)
	{
		$Selected = '';
		if ($Term->ID==$PaymentTerm) $Selected = 'checked';
?>
		<label>
			<input type="radio" name="PaymentTerm" value="<?php echo $Term->ID?>" class="paymentTerm" <?php echo $Selected;?> /> <?php echo $Term->Name?>
		</label>
<?php 
	}
?>

<?php 
	if (!isset($_SESSION['OneTerm']))
	{
?>
		<label style="display: inline-block;">
			<input type="radio" name="PaymentTerm" value="-1" class="paymentTerm" <?php echo $Selected;?> <?php echo ($PaymentTerm=='-1')?'checked':''; ?> /> Custom
		</label>	
		
		<input type="text" id="CustomPrice" name="CustomPrice" value="<?php echo $CustomPrice;?>" style="display: <?php echo ($PaymentTerm=='-1')?'':'none'; ?>;" />
<?php 
	}
?>		
		
		
		<br /><br />
		
		<input type="submit" id="Continue" name="Continue" value="Continue" />
		
	</form>
	
	<br />
	
	</div>
	
	<script type="text/javascript">
		$('#Continue').click(function() {

			var Inputs = $('.signupForm .required');
			var FailedValidation = false;
			var CustomPriceChecked = false;
			
			$.each(Inputs, function(index, Input) {
				if (Input.value=='')
				{
					FailedValidation = true;
				}
			});

			if (!FailedValidation)
			{
				var AnySelected = false;
				var Inputs = $('.paymentTerm');
				$.each(Inputs, function(index, Input) {
					if (Input.checked)
					{
						if (Input.value=='-1')
						{
							CustomPriceChecked = true;
						}
						
						AnySelected = true;
					}
				});

				FailedValidation = !AnySelected;
			}
			
			if (FailedValidation)
			{
				alert('You must fill in all required fields.');
				return false;
			}

			if (CustomPriceChecked)
			{
				
				var Amount = parseFloat($('#CustomPrice').val());
				
				if ( (Amount<5) || (isNaN(Amount)) || ($('#CustomPrice').val()=='') )
				{
					alert('Custom amount must be at least $5.00.');
					return false;
				}
			}
			return true;
		});
	</script>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('#Country').change();
		});
		
		$('#Country').change(function() {

			if ($(this).val()=='US' || $(this).val()=='CA')
			{
				$('#State option').remove();

				$.getJSON('/BevoMedia/Networks/JSONGetCountryStates.html', {'Code': $(this).val()}, function(Data, TextStatus) {

					$('#State').append("<option value=''>Select State</option>");
					$.each(Data, function(Index, Value) {
						var Selected = '';
						if (Value['initials']==$('#State').attr('Default'))
						{
							Selected = 'selected';
						}
						
						$('#State').append("<option value='"+Value['initials']+"' "+Selected+">"+Value['name']+"</option>");
					});
					
				});
				
				
			} else
			{
				$('#State option').remove();
				
				$('#State').append("<option value='-1'>N/A</option>");
			}
			
		});

		$('.paymentTerm').click(function() {

			if ($(this).val()==-1)
			{
				$('#CustomPrice').css('display', '');
			} else
			{
				$('#CustomPrice').css('display', 'none');
			}

		});
	</script>
	