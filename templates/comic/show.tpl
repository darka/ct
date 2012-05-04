<div id="comic">

{if $prevID}
	<a href="comic.php?action=show&amp;id={$prevID}">
		<img class="arrow" src="style/back.png" alt="Previous" />
	</a>
{/if}

	<img class="comic" src="comics/{$comic_image}" alt="Comic" />

{if $nextID}
	<a href="comic.php?action=show&amp;id={$nextID}">
		<img class="arrow" src="style/forward.png" alt="Next" />
	</a>
{/if}

	<br />

	<div id="comic_info_box">
		<span class="title">Author:</span> {$comic_author}<br />
		<span class="title">Group:</span> <a href="comicgroup.php?action=show&amp;id={$comicgroup_id}">{$comicgroup}</a> {if ($display_author_options)}(<a href="comic.php?action=change_group&amp;id={$comic_id}">change</a>){/if}<br />
		<span class="title">Posted:</span> {$comic_post_date}{if $comic_post_date != $comic_mod_date}<br />
		<span class="title">Last Modified:</span> {$comic_mod_date}{/if}{if $display_author_options}<br />
		<a href="comic.php?action=reupload&amp;id={$comic_id}">Reupload</a>{/if}
		{if $display_admin_options}<a href="comic.php?action=delete&amp;id={$comic_id}">Delete</a>{/if}
	</div>

	<a id="comments"></a>
{section name=comment loop=$comment_texts}
	<div class="comment">
		<a id="comment{$comment_ids[comment]}"></a>
		<span class="comment_author">{$comment_authors[comment]}</span>

{if $comment_author_ids} 
		<a href="comment.php?action=edit&amp;id={$comment_ids[comment]}">edit</a> 
		<a href="comment.php?action=delete&amp;id={$comment_ids[comment]}">delete</a> 
		<a href="user.php?action=change_access&amp;id={$comment_author_ids[comment]}">admin</a>
{/if}
		<br />
	{$comment_texts[comment]}
	</div>
{/section}
</div>

{if $display_comment_form}
{include file='comment.tpl'}
{/if}


