<?
	if (isset($_POST['Submit']))
	{
?>
	<p>Comment has been submitted for approval.</p>
<?
		return;
	}
?>

<p>
	Please comment on this network and reason
	you are giving your selected rating.
</p>
<br />
<br />

<form method="post">
	<table width="300" style="margin-left: auto; margin-right: auto;">
		<tr>
			<td>
				Comment:
			</td>
		</tr>
		<tr>
			<td>
				<textarea name="comment" cols="40" rows="10"></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="Submit" value="Post" />
			</td>
		</tr>
	</table>
</form>


<br />
<br />
<p>
*Please note, all negative reviews
are taken very seriously. Profanity will not be tolerated. All comments
are moderated before going live.
</p>
