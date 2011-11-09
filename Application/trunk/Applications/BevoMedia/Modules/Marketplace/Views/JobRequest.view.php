<script>
function validate()
{
	validates = true;
	validate = Array('contactName', 'contactEmail', 'contactPhone', 'projectName', 'description');
	$.each(validate, function (i,k) {
		if($('#'+k).val() == '')
			validates = false;
	});
	if(!validates)
		alert('Please fill in all fields');
	return validates;
}
</script>
<form action="JobSubmit.html" method="post" onSubmit="return validate();">
<input type="hidden" name="projectType" id="projectType" value="<?= @$_GET['type'] ?>" />
<input type="hidden" name="user__id" id="user__id" value="<?= $this->User->id ?>" />
<table border="0">
	<tr>
		<td>
			Your name:
		</td>
		<td>
			<input type="text" name="contactName" id="contactName" value="<?= $this->User->firstName . ' ' . $this->User->lastName ?>" />
		</td>
	</tr>
	<tr>
		<td>
			Contact email:
		</td>
		<td>
			<input type="text" name="contactEmail" id="contactEmail" value="<?= $this->User->email ?>" />
		</td>
	</tr>
	<tr>
		<td>
			Phone Number:
		</td>
		<td>
			<input type="text" name="contactPhone" id="contactPhone" value="<?= $this->User->phone ?>" />
		</td>
	</tr>
	<tr>
		<td>
			Project name:
		</td>
		<td>
			<input type="text" name="projectName" id="projectName" value="" />
		</td>
	</tr>
	<tr>
		<td>
			Project Type:
		</td>
		<td>
			<?= strtoupper(@$_GET['type']) ?>
		</td>
	</tr>
	<tr>
		<td>
			Project Description:<br /><i>Please be as descriptive as possible</i>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea cols="55" rows="7" name="description" id="description"></textarea>
		</td>
	</tr>
	<tr>
		<td><input type="submit" name="submit" id="submit" value="Submit Job Request" />
		</td>
	</tr>
</table>
</form>