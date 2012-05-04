<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">

<channel>
	<title>{$title}</title>
	<link>{$host}</link>
	<description>{$description}</description>
{section name=item loop=$item_links}
	<item>
		<title>{$item_titles[item]}</title>
		<link>{$item_links[item]}</link>
		<guid isPermaLink="true">{$item_links[item]}</guid>
		<description>{$item_descriptions[item]}</description>
		<pubDate>{$item_dates[item]}</pubDate>
	</item>
{/section}
</channel>

</rss>
