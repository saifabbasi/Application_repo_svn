	
	<br /><br />
	
	<div>
		Click on a user to see the referrals.
	</div>
	
	<table id="ReferralsTable" width="100%" class="adminPublisherTable">
		<tr style="text-align: left;">
			<th>ID</th>
			<th>User</th>
			<th width="75">Total Referrals</th>
			<th width="75">Referral Bonus</th>
		</tr>
		
	<?php 
		$User = new User();
		foreach ($this->Referrals as $Referral)
		{
	?>
		<tr class="UserItem" id="<?php echo $Referral->id?>">
			<td><a href="/BevoMedia/Admin/ViewPublisher.html?id=<?php echo $Referral->id?>" target="_blank"><?php echo $Referral->id?></a></td>
			<td><?php echo $Referral->firstName.' '.$Referral->lastName?></td>
			<td><?php echo $Referral->TotalUsers;?></td>
			<td>$<?php echo number_format($Referral->TotalRevenue, 2)?></td>
		</tr>
		
		
		<tr class="subItem<?php echo $Referral->id?>" style="display: none;">
			<td colspan="4" style="padding-left: 200px;">
				<table width="80%;">
					<tr>
						<th style="font-size: 12px;">ID</th>
						<th style="font-size: 12px;">Referred User</th>
						<th style="font-size: 12px;">Signed Up</th>
						<th style="font-size: 12px;" width="75">Revenue</th>
					</tr>
			<?php 
				foreach ($User->ListReferrals($Referral->id) as $ReferralItem)
				{
			?>
					<tr>
						<td><a href="/BevoMedia/Admin/ViewPublisher.html?id=<?php echo $ReferralItem->id?>" target="_blank"><?php echo $ReferralItem->id?></a></td>
						<td><?php echo $ReferralItem->firstName.' '.$ReferralItem->lastName?></td>
						<td><?php echo date('m/d/Y', strtotime($ReferralItem->Date));?></td>
						<td>$<?php echo number_format($ReferralItem->TotalRevenue, 2)?></td>
					</tr>
			<?php 
				}
			?>
				</table>
			
			</td>
		</tr>
		
	<?php 
		}
	?>	
		
	</table>
	
	<script type="text/javascript">
		$(document).ready(function(){
			  
			  $('#ReferralsTable tr').hover(
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
	