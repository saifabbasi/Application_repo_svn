
	<table id="InvoiceRequestsTable" width="100%" class="adminPublisherTable">
		<tr style="text-align: left;">
			<th>ID</th>
			<th>User</th>
			<th width="100">Transaction ID</th>
			<th width="75">Amount</th>
			<th width="75">&nbsp;</th>
		</tr>
	
	<?php 
		foreach ($this->AllInvoices as $Invoice) {
	?>
	
		<tr>
			<td><a href="/BevoMedia/Admin/ViewPublisher.html?id=<?php echo $Invoice->UserID?>" target="_blank"><?php echo $Invoice->UserID?></a></td>
			<td><?php echo $Invoice->firstName.' '.$Invoice->lastName?></td>
			<td><?php echo $Invoice->TransactionID;?></td>
			<td>$<?php echo number_format($Invoice->Price, 2)?></td>
			<td><a href="/BevoMedia/Admin/InvoiceRequests.html?markAsSent=<?php echo $Invoice->id?>">Mark as sent</a></td>
		</tr>
	
	<?php 
		}
		
		if (count($this->AllInvoices)==0) {
	?>
		<tr>
			<td colspan="5" align="center">There are not any invoice requests.</td>
		</tr>
	<?php 
		}
	?>
	
	
	
	</table>
	

	
	<script type="text/javascript">
		$(document).ready(function(){
			  
			  $('#InvoiceRequestsTable tr').hover(
					   function()
					   {
					   		$(this).addClass("lightBlueRow");
					   },
					   function()
					   {
					   		$(this).removeClass("lightBlueRow");
					   }
			  );
			  
			  $('.UserItem').click(function() {
				  	if ($(this).attr('id')!='') {
			  			$('.subItem'+$(this).attr('id')).toggle();
				  	}
			  });
			  
		 });
	</script>