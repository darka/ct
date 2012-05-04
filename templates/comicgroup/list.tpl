
<div id="comicgroups_list">
	<span><b>Browse by Group:</b>
{section name=comicgroup loop=$comicgroup_ids}
	<a class="list_link" href="comicgroup.php?action=show&amp;id={$comicgroup_ids[comicgroup]}">{$comicgroup_titles[comicgroup]}</a>
{/section}
	</span>
</div>

