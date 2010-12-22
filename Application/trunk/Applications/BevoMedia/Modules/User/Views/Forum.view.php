<h1>Create Username</h1>

<br /><br /><br /><br /><br />

<?
	if (isset($this->UsernameExists))
	{
?>
	<div align="center" style="color: #f00;">
		Username already exists. Please choose another one.
	</div>
	<br /><br />
<?
	}
?>

<form method="post">
	<table width="300" style="margin-left: auto; margin-right: auto;">
		<tr>
			<td>Username:</td>
			<td>
				<input type="text" name="Username" value="" />
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="Submit" value="Create" />
			</td>
		</tr>
	</table>
</form>