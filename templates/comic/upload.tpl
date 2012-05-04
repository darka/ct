<form enctype="multipart/form-data" action="comic.php?action=upload" method="post">
	<fieldset>
		<legend>Upload Comic</legend>

		<input type="hidden" name="upload" value="1" />
		<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
		
		<label for="image">Image:</label>
		<input name="image" id="image" type="file" /><br />

		<label for="comicgroup">Comic Group:</label>
		<select name="comicgroup" id="comicgroup">
{section name=comicgroup loop=$comicgroups_ids}
{if isset($last_comicgroup_id) && $last_comicgroup_id == $comicgroups_ids[comicgroup]}
			<option label="{$comicgroups[comicgroup]}" value="{$comicgroups_ids[comicgroup]}" selected="selected">{$comicgroups[comicgroup]}</option>
{else}
			<option label="{$comicgroups[comicgroup]}" value="{$comicgroups_ids[comicgroup]}">{$comicgroups[comicgroup]}</option>
{/if}
{/section}
		</select><br />
		
		<input type="submit" value="Upload" />
	</fieldset>
</form>
