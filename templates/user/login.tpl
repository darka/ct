<form action="user.php?action=login" method="post">
	<fieldset>
		<legend>Login</legend>
		<input type="hidden" name="login" value="1" />

		<label for="username">Username:</label>
		<input type="text" name="username" id="username" {if $username}value="{$username}" {/if}maxlength="30" /><br />

		<label for="password">Password:</label>
		<input type="password" name="password" id="password" maxlength="30" /><br />

		<label for="time">Duration:</label>
		<select id="time" name="time">
			<option value="3600">1 Hour</option>
			<option value="7200">2 Hours</option>
			<option value="86400">1 Day</option>
			<option value="604800">1 Week</option>
		</select><br />

		<input type="submit" value="Login" />
		<span>Not registered? <a href="user.php?action=register">Register now!</a></span>
		<br />
	</fieldset>
</form>
