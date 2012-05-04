<form action="comic.php?action=delete&amp;id={$id}" method="post">
	<fieldset>
		<legend>Delete Comic</legend>

		<input type="hidden" name="confirm_delete" value="1" />

		<span>Are you really sure you want to delete this comic by <i>{$user}</i>?<br />
		<b>Image:</b></span>
		<img src="comics/{$comic}" alt="Image" /><br />
		
		<input type="submit" value="Yes, Delete this Comic" />
	</fieldset>
</form>
