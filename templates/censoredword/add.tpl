<form action="censoredword.php?action=add" method="post">
	<fieldset>
		<legend>Censor New Word</legend>

		<input type="hidden" name="censor_word" value="1" />
		
		<label for="word">Word:</label>
		<input type="text" name="word" id="word" maxlength="50" /><br />

		<label for="replacement">Replacement:</label>
		<input type="text" name="replacement" id="replacement" maxlength="50" /><br />

		<input type="submit" value="Censor" />
	</fieldset>
</form>
