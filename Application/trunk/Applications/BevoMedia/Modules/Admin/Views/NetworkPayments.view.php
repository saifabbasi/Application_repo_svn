<h2 class='adminPageHeading'>Network Payments</h2>
	
<br /><br />
	
	<table width="100%">
		<tr>			
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>Network Name</th>
			<th>Name</th>
			<th>Phone</th>
			<th>Payment Term</th>
			<th>Created</th>
			<th>Expiring</th>			
		</tr>
	<?php 
		foreach ($this->Networks as $Network)
		{
	?>
		<tr>			
			<td>
				<a href="#" class="ExpandNetwork" id="ExpandNetwork_<?php echo $Network->ID?>" NetworkID="<?php echo $Network->ID?>">[+]</a>
			</td>
			<td><?php echo $Network->ID?></td>
			<td>
				<a href="#" class="ExpandNetwork" NetworkID="<?php echo $Network->ID?>"><?php echo $Network->NetworkName?></a>
			</td>
			<td><?php echo $Network->Name?></td>
			<td><?php echo $Network->Phone?></td>
			<td><?php echo (is_object($Network->PaymentTermInfo))?$Network->PaymentTermInfo->Name:'Custom Price'?></td>
			<td><?php echo date('m/d/Y', strtotime($Network->Created));?></td>
			<td><?php echo date('m/d/Y', strtotime($Network->Expiring));?></td>			
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="6">
				<div class=".networkPaymentInfo" id="networkPaymentInfo_<?php echo $Network->ID?>" style="display: none;">
				
				<fieldset>
					<legend>Contact Info</legend>
					<div>
						<table>
							<tr>
								<th width="150">E-mail:</th>
								<td><?php echo $Network->Email?></td>
							</tr>
							<tr valign="top">
								<th>Address:</th>
								<td>
								<?php echo $Network->Address1?><br />
								<?php echo $Network->Address2?><br />
								<?php echo $Network->City?>, <?php echo $Network->State?><br />
								<?php echo $Network->Country?>
								</td>
							</tr>
							<tr>
								<th>Approximate Number of<br />Affiliates:</th>
								<td><?php echo $Network->ApproximateNumberAffiliates?></td>
							</tr>
							<tr>
								<th>Tracking Platform:</th>
								<td><?php echo $Network->TrackingPlatform?></td>
							</tr>
							<tr>
								<th>Comment:</th>
								<td><?php echo $Network->Comment?></td>
							</tr>
							<tr>
								<th>Customer Vault ID:</th>
								<td><?php echo $Network->CustomerVaultID?></td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<th>Status:</th>
								<td>
									<?php if ($Network->Active==1) { ?>
										<a href="/BevoMedia/Admin/NetworkPayments.html?Deactivate=<?php echo $Network->ID?>" onclick="return confirm('Are you sure you want to DEACTIVATE this network?');">Deactivate</a>
									<?php } else { ?>
										<a href="/BevoMedia/Admin/NetworkPayments.html?Activate=<?php echo $Network->ID?>" onclick="return confirm('Are you sure you want to ACTIVATE this network?');">Activate</a>
									<?php } ?>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
				
				<fieldset>
					<legend>Transactions</legend>
					<table width="75%">
						<tr>
							<th>Transaction ID</th>
							<th>Amount</th>
							<th>Date/Time</th>
						</tr>
				<?php 
					foreach ($Network->Payments as $Payment)
					{
				?>
						<tr>
							<td><?php echo $Payment->TransactionID?></td>
							<td>$<?php echo $Payment->Amount?></td>
							<td><?php echo date("m/d/Y G:i:s", strtotime($Payment->Created))?></td>
						</tr>
				<?php 
					}
					
					if (count($Network->Payments)==0)
					{
				?>
						<tr>
							<td align="center" colspan="3">No transactions present.</td>
						</tr>
				<?php 	
					}
				?>
					</table>
				</fieldset>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="7">&nbsp;</td>
		</tr>
		
	<?php 
		}
	?>
	</table>



	
	<script type="text/javascript">
		$(document).ready(function() {

			$('.ExpandNetwork').click(function() {

				var NetworkID = $(this).attr('NetworkID');
				$('#networkPaymentInfo_'+NetworkID).toggle();

				var LinkID = '#ExpandNetwork_'+NetworkID;
				if ($(LinkID).html()=='[+]')
				{
					$(LinkID).html('[-]');
				} else
				if ($(LinkID).html()=='[-]')
				{
					$(LinkID).html('[+]');
				}

				return false;
			});

		});
	</script>
