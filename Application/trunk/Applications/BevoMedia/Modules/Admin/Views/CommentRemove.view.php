
	<?php 
		if (isset($_POST['CommentID']) && intval($_POST['CommentID'])>0)
		{
	?>
		<div>
			Comment removed.
		</div>
		<br />
	<?php 
		}
	?>

	<form method="post">
		Comment ID: <input type="text" name="CommentID" value="" />
		<br /><br />
		<input type="submit" name="Submit" value="Remove" onclick="return confirm('Are you sure you want to remove this comment?');" />
	</form>