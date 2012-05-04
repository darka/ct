<form action="user.php?action=register" method="post">
	<fieldset>
		<legend>Registration</legend>

		<input type="hidden" name="register" value="1" />

		<label for="username">Username:</label>
		<input type="text" name="username" id="username" {if $username}value="{$username}" {/if}maxlength="30" /><br />

		<label for="password">Password:</label>
		<input type="password" name="password" id="password" maxlength="30" /><br />

		<label for="password_confirm">Repeat Password:</label>
		<input type="password" name="password_confirm" id="password_confirm" maxlength="30" /><br />

		<input type="submit" value="Register" />
	</fieldset>
</form>
