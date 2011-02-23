<h2 class='adminPageHeading floatRight'>Performance Connector</h2>

<a href='PerformanceConnector.html'>View All</a>

<b></b>
<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr class='textAlignLeft'>
	<th>
		User 
	</th>
	<th>
		Email
	</th>
	<th>
		Networks Emailed
	</th>
	<th>
		Niches
	</th>
	<th>

	</th>
</tr>

<?php foreach ($this->perfConn as $perfConn):?>
<tr>
	<td>
		<?php print $perfConn->userId?>
	</td>
	<td>
		<?php print $perfConn->userEmail?>
	</td>
	<td>
		<?php if($perfConn->networks):?>
			<?php print $perfConn->networks?>
		<?php else:?>
			<i>N/A</i>
		<?php endif;?>
	</td>
	<td>
		<?php if($perfConn->niches):?>
			<a href='#' class='showNiches'>Show Niches</a>
		<?php else:?>
			<i>None Selected</i>
		<?php endif;?>
	</td>
	<td>
		<a href='PerformanceConnectorEdit.html?id=<?php echo $perfConn->userId?>'>Edit</a>
	</td>
</tr>
<?php if($perfConn->niches):?>
<tr class="lightBlueRow textAlignRight displayNone">
	<td colspan="5">
		<?php print $perfConn->niches?>
	</td>
</tr>
<?php endif;?>

<?php endforeach;?>

</table>

<script language="javascript">
$(document).ready(function() {
	$('.showNiches').click(function(){
		$(this).parent().parent().next().toggle();
		return false;
	});
});

</script>