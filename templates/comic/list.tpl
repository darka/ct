<div id="comic_list_before">
<span id="page_list">
{if isset($next_page)}
{if isset($comicgroup_id)}
<a href="comicgroup.php?action=show&amp;id={$comicgroup_id}&amp;page={$next_page}"><img class="arrow" src="style/back.png" alt="Next Page" /></a>
{else}
<a href="comic.php?page={$next_page}"><img class="arrow" src="style/back.png" alt="Next Page" /></a>
{/if}
{/if}


{section name=page loop=$page_list}
{if $page_list[page]==$current_page}
<span id="current_page">{$current_page}</span>
{else}
{if isset($comicgroup_id)}
<a href="comicgroup.php?action=show&amp;id={$comicgroup_id}&amp;page={$page_list[page]}">{$page_list[page]}</a>
{elseif isset($user_id)}
<a href="user.php?action=show&amp;id={$user_id}&amp;page={$page_list[page]}">{$page_list[page]}</a>
{else}
<a href="comic.php?page={$page_list[page]}">{$page_list[page]}</a>
{/if}
{/if}
{/section}


{if isset($prev_page)}
{if isset($comicgroup_id)}
<a href="comicgroup.php?action=show&amp;id={$comicgroup_id}&amp;page={$prev_page}"><img class="arrow" src="style/forward.png" alt="Previous Page" /></a>
{else}
<a href="comic.php?page={$prev_page}"><img class="arrow" src="style/forward.png" alt="Previous Page" /></a>
{/if}
{/if}
</span>


<div id="comics_list">
{section name=comic loop=$comic_thumbs}

	<a href="comic.php?action=show&amp;id={$comic_ids[comic]}">
	<img src="thumbs/{$comic_thumbs[comic]}" alt="Comic" /></a>

{/section}
</div>



</div>
