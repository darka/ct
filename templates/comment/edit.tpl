<form action="comment.php?action=edit&amp;id={$comment_id}" method="post">
	<fieldset>
		<legend>Edit Comment</legend>

		<input type="hidden" name="edit_comment" value="1" />
		
		{if $username}<span>Author: <b>{$username}</b></span><br />{/if}
		<label for="text">Comment:</label><br />
		<textarea name="text" id="text" rows="10" cols="45">{$text}</textarea>
		
		<input type="submit" value="Save Comment" />
	</fieldset>
</form>
