

	<form method="get" action="" name="frmRange">
	<p align="right">
	<table align="right" cellspacing="0" cellpadding="0" class="datetable">
	  <tr>
	    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php print isset($_GET['DateRange'])?$_GET['DateRange']:$this->defaultDateRange; ; ?>" /></td>
		<td><input class="formsubmit" type="submit" /></td>
	  </tr>
	</table>
	</p>
	</form>
	
	<br />
	
	<table width="100%" id="ProfitTable" class="adminPublisherTable">
		<tr>
			<th>ID</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>E-mail</th>
			<th>Total</th>
		</tr>
		
	<?php 
		$Total = 0;
		foreach ($this->Results as $Result)
		{
	?>
		<tr class="UserItem" id="<?php echo $Result->id?>">
			<td><?=$Result->id?></td>
			<td><?=$Result->firstName?></td>
			<td><?=$Result->lastName?></td>
			<td><?=$Result->email?></td>
			<td>$<?=number_format($Result->Total, 2)?></td>
		</tr>
	<?php 
			$Total += number_format($Result->Total, 2);
		}
	?>
		<tr>
			<td colspan="5" style="border-bottom: 1px #efefef solid;">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
			<td>$<?=number_format($Total, 2)?></td>
		</tr>
	</table>
	
	<script type="text/javascript">
		$(document).ready(function(){
			  $('#datepicker').daterangepicker();

			  $('#ProfitTable tr').hover(
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
			  			window.location = '/BevoMedia/Admin/ViewPublisher.html?id='+$(this).attr('id');
				  	}
			  });
			  
		 });
	</script>