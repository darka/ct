<div id="censoredword_list">
{section name=word loop=$censoredword_words}
	<span>{$censoredword_words[word]} -&gt; {$censoredword_replacements[word]} (<a href="censoredword.php?action=delete&amp;id={$censoredword_ids[word]}">delete</a>)</span>
	<br />
{/section}
</div>
