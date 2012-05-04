<form action="comment.php?action=delete&amp;id={$comment_id}" method="post">
	<fieldset>
		<legend>Delete Comment</legend>
		<input type="hidden" name="confirm_delete" value="1" />
		<span>Are you really sure you want to delete this comment by <i>{$user}</i>?<br />
		<b>Content:</b> <i>{$text}</i></span>
		<input type="submit" value="Yep, Delete this Comment" />
	</fieldset>
</form>
