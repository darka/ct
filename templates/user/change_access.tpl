<form action="user.php?action=change_access&amp;id={$id}" method="post">
	<fieldset>
		<legend>Change Access Level</legend>

		<input type="hidden" name="change_access" value="1" />
{if $access_level == 5}
{assign var=access_level_name value="Admin"}
{elseif $access_level == 3}
{assign var=access_level_name value="Author"}
{elseif $access_level == 0}
{assign var=access_level_name value="Normal"}
{elseif $access_level == -1}
{assign var=access_level_name value="Banned"}
{else}
{assign var=access_level_name value=$access_level}
{/if}
		<span><b>User:</b> {$username}<br /><b>Current Access Level:</b> {$access_level_name}</span><br />
		
		<label for="access">Set Access Level:</label>
		<select id="access" name="access">
			<option value="5">Admin</option>
			<option value="3">Author</option>
			<option value="0" selected="selected">Normal</option>
			<option value="-1">Banned</option>
		</select><br />

		<input type="submit" value="Save Level" />

	</fieldset>
</form>
