
	<table width="100%">
	<tr>
		<td>Title</td>
		<td>Date</td>
		<td>Password</td>
		<td>Url</td>
		<td>&nbsp;</td>
	</tr>
<?php	
	foreach ($this->webinarsData as $webinar)
	{
		$date = $webinar->Date;
		if (strstr($date, '00:00:00')) {
			$date = date('m/d/Y', strtotime($date));
		} else {
			$date = date('m/d/Y g:i a', strtotime($date));
		}
?>
	<tr>
		<td><?=$webinar->Title?></td>
		<td><?=$date?></td>
		<td><?=$webinar->Password?></td>
		<td><?=$webinar->Url?></td>
		<td>
			<a href="/BevoMedia/Admin/Webinars.html?DeleteID=<?=$webinar->ID?>" onclick="return confirm('Are you sure you want to delete this webinar?');">Delete</a>
		</td>
	</tr>
<?php 
	}
?>
	</table>
	
	<br /><br />
	
	<div>
	<form method="post">
	<fieldset style="width: 300px;">
		<legend>Insert Webinar</legend>
		<table>
			<tr>
				<td>Title:</td>
				<td>
					<input type="text" name="title" value="" />
				</td>
			</tr>
			<tr>
				<td>Date:</td>
				<td>
					<input type="text" id="date" name="date" value="" />
				</td>
			</tr>
			<tr>
				<td>Time:</td>
				<td>
					<input type="text" id="time" name="time" value="" /> (HH:MM) format
				</td>
			</tr>
			<tr>
				<td>Password:</td>
				<td>
					<input type="text" name="password" value="" />
				</td>
			</tr>
			<tr>
				<td>Url:</td>
				<td>
					<input type="text" name="url" value="" />
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="insert" value="Insert" />
				</td>
			</tr>
		</table>
	</fieldset>
	</form>
	</div>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('#date').datepicker();
		});
	</script>
	