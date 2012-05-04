{if isset($is_admin)}
<a href="censoredword.php">Censor Words</a><br />
<a href="user.php?action=manage">Manage Users</a><br />
<br />
{/if}

{if isset($is_author)}
<a href="comic.php?action=upload">New Comic</a><br />
<a href="comicgroup.php?action=create">New Comic Group</a><br />
<br />
{/if}
<a href="user.php?action=change_password">Change Password</a><br />
<a href="user.php?action=logout">Logout</a>
