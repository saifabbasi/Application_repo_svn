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
		<a href="ViewPublisher.html?id=<?php print $perfConn->userId?>"><?php print $perfConn->userId?></a>
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
			<a href='#' class='showNiches'>Show Info</a>
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
	<td colspan="1">
		<?php print $perfConn->experience?>
	</td>
	<td colspan="4">
		<?php print $perfConn->niches?>
		<br/>
		<?php print $perfConn->promomethods?>
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