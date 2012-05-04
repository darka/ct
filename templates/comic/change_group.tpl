<form enctype="multipart/form-data" action="comic.php?action=change_group&amp;id={$id}" method="post">
	<fieldset>
		<legend>Change Comic Group</legend>

		<input type="hidden" name="change_group" value="1" />
		
		<span><b>Comic:</b></span>
		<img src="comics/{$current_comic}" alt="Image" /><br />
		
		<label for="comicgroup">Comic Group:</label>
		<select name="comicgroup" id="comicgroup">
{section name=comicgroup loop=$comicgroups_ids}
{if isset($selected_comicgroup_id) && $selected_comicgroup_id == $comicgroups_ids[comicgroup]}
			<option label="{$comicgroups[comicgroup]}" value="{$comicgroups_ids[comicgroup]}" selected="selected">{$comicgroups[comicgroup]}</option>
{else}
			<option label="{$comicgroups[comicgroup]}" value="{$comicgroups_ids[comicgroup]}">{$comicgroups[comicgroup]}</option>
{/if}
{/section}
		</select><br />
		
		<input type="submit" value="Save Group" />
	</fieldset>
</form>
