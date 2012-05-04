<div id="users_list">
{section name=user loop=$user_usernames}
	<a href="user.php?action=change_access&amp;id={$user_ids[user]}">{$user_usernames[user]}</a>
	<br />
{/section}
</div>
