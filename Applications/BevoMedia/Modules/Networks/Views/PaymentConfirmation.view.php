	<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>
	
	<div id="pageinfo" class="sub">
		<h2>Payment Confirmation</h2>	
	</div>
	
	<div class="clear"></div>
	
	<div class="content">
	
		<h2>Contact Info</h2>
	
		<table>
			<tr>
				<td width="200">Network Name:</td>
				<td><?php echo $_SESSION['SignUpData']['NetworkName']?></td>
			</tr>
			<tr>
				<td>Name:</td>
				<td><?php echo $_SESSION['SignUpData']['ContactName']?></td>
			</tr>
			<tr>
				<td>E-mail:</td>
				<td><?php echo $_SESSION['SignUpData']['ContactEmail']?></td>
			</tr>
			<tr>
				<td>Phone:</td>
				<td><?php echo $_SESSION['SignUpData']['ContactPhone']?></td>
			</tr>
			<tr>
				<td>Address 1:</td>
				<td><?php echo $_SESSION['SignUpData']['ContactAddress1']?></td>
			</tr>
			<tr>
				<td>Address 2:</td>
				<td><?php echo $_SESSION['SignUpData']['ContactAddress2']?></td>
			</tr>
			<tr>
				<td>City:</td>
				<td><?php echo $_SESSION['SignUpData']['ContactCity']?></td>
			</tr>
			<tr>
				<td>State:</td>
				<td><?php echo $_SESSION['SignUpData']['ContactState']?></td>
			</tr>
			<tr>
				<td>Approximate Number of<br />Affiliates in Network:</td>
				<td><?php echo $_SESSION['SignUpData']['ApproximateNumberAffiliates']?></td>
			</tr>
			<tr>
				<td>Tracking Platform:</td>
				<td><?php echo $_SESSION['SignUpData']['TrackingPlatform']?></td>
			</tr>
			<tr>
				<td>Comment:</td>
				<td><?php echo $_SESSION['SignUpData']['Comment']?></td>
			</tr>
		</table>
		
		<br />
		
		<h2>Payment Info</h2>
	
		<table>
			<tr>
				<td width="200">Company:</td>
				<td><?php echo $_SESSION['PaymentData']['Company']?></td>
			</tr>
			<tr>
				<td>First Name:</td>
				<td><?php echo $_SESSION['PaymentData']['FirstName']?></td>
			</tr>
			<tr>
				<td>Last Name:</td>
				<td><?php echo $_SESSION['PaymentData']['LastName']?></td>
			</tr>
			<tr>
				<td>Address 1:</td>
				<td><?php echo $_SESSION['PaymentData']['Address1']?></td>
			</tr>
			<tr>
				<td>Address 2:</td>
				<td><?php echo $_SESSION['PaymentData']['Address2']?></td>
			</tr>
			<tr>
				<td>City:</td>
				<td><?php echo $_SESSION['PaymentData']['City']?></td>
			</tr>
			<tr>
				<td>State:</td>
				<td><?php echo $_SESSION['PaymentData']['State']?></td>
			</tr>
			<tr>
				<td>Zip:</td>
				<td><?php echo $_SESSION['PaymentData']['Zip']?></td>
			</tr>
			<tr>
				<td>Phone:</td>
				<td><?php echo $_SESSION['PaymentData']['Phone']?></td>
			</tr>
		</table>
		
		<table>
			<tr>
				<td width="200">Credit Card Number:</td>
				<td><?php echo $_SESSION['PaymentData']['CreditCardNumber']?></td>
			</tr>
			<tr>
				<td>Expiration Date:</td>
				<td><?php echo $_SESSION['PaymentData']['ExpirationMonth']?>/<?php echo $_SESSION['PaymentData']['ExpirationYeah']?></td>
			</tr>
			<tr>
				<td>CVV:</td>
				<td><?php echo $_SESSION['PaymentData']['CVV']?></td>
			</tr>
			
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>Payment Term:</td>
				<td><?php echo (is_object($this->PaymentTerm))?$this->PaymentTerm->Name:'$'.$_SESSION['PaymentData']['CustomPrice']; ?></td>
			</tr>
		</table>
		
		<br />
		
		<form method="post" action="/BevoMedia/Networks/ProcessPayment.html">
		
			<div style="width: 50%;">
				<input type="button" name="ModifyInfo" value="Modify Info" style="float: left;" onclick="window.location = '/BevoMedia/Networks/NetworkPayment.html'; " />
				<input type="submit" name="SubmitPayment" value="Submit Payment" style="float: right;" />
				<div class="clear"></div>
			</div>
		
		</form>
	
	</div>
	
	
