<form action="user.php?action=change_password" method="post">
	<fieldset>
		<legend>Change Password</legend>

		<input type="hidden" name="change_password" value="1" />

		<label for="password">New Password:</label>
		<input type="password" name="password" id="password" maxlength="30" /><br />

		<label for="password_confirm">Repeat New Password:</label>
		<input type="password" name="password_confirm" id="password_confirm" maxlength="30" /><br />

		<input type="submit" value="Save Password" />
	</fieldset>
</form>
