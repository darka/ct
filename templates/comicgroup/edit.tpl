<form action="comicgroup.php?action=edit_title&amp;id={$id}" method="post">
	<fieldset>
		<legend>Edit Comic Group Title</legend>

		<input type="hidden" name="edit_title" value="1" />
		
		<label for="title">Title:</label>
		<input type="text" name="title" id="title" value="{$current_title}" maxlength="50" /><br />

		<input type="submit" value="Save Title" />
	</fieldset>
</form>
