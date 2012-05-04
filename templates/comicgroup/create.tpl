<form action="comicgroup.php?action=create" method="post">
	<fieldset>
		<legend>Create Comic Group</legend>

		<input type="hidden" name="create_group" value="1" />
		
		<label for="title">Title:</label>
		<input type="text" name="title" id="title" maxlength="50" /><br />

		<input type="submit" value="Save Group" />
	</fieldset>
</form>
