<form enctype="multipart/form-data" action="comic.php?action=reupload&amp;id={$id}" method="post">
	<fieldset>
		<legend>Reupload Comic</legend>

		<input type="hidden" name="reupload" value="1" />
		<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />

		<b>Current Image:</b><br />
		<img src="comics/{$current_comic}" alt="Current Image" /><br />
		
		<label for="image">New Image:</label>
		<input name="image" type="file" /><br />

		<input type="submit" value="Upload" />
	</fieldset>
</form>
