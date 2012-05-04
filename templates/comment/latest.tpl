
<div id="latest_comments">
	<span><b>Latest Comments:</b>
{section name=comment loop=$comment_comic_ids}
	<a class="list_link" href="comic.php?action=show&amp;id={$comment_comic_ids[comment]}#comment{$comment_ids[comment]}">{$comment_usernames[comment]}</a> 
{/section}
	</span>
</div>
