<form method="post">
	<table>
		<tr>
			<td>User ID:</td>
			<td><input type="text" name="userId"></input></td>
		</tr>
		<tr>
			<td>Amount:</td>
			<td><input type="text" name="amount"></input></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submit" value="Charge"></input></td>
		</tr>
	</table>
</form>

<?php echo $this->{'resultMessage'}?>