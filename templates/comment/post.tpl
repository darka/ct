<a id="comment_form"></a>
<form action="comment.php?action=post&amp;comic_id={$comic_id}" method="post">
	<fieldset>
		<legend>Post a Comment</legend>

		<input type="hidden" name="comment" value="1" />
		
		<label for="text">Text:</label><br />
		<textarea name="text" id="text" rows="10" cols="30"></textarea><br />
		
		<input type="submit" value="Comment" />
	</fieldset>
</form>
