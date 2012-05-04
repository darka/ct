<?php
header('Content-type: application/rss+xml; charset=ISO-8859-1');

require_once "include/smarty/Smarty.class.php";
require_once "include/comic.class.php";
require_once "include/common.php";

$db = new DBManager();
$comics = Comic::getComics($db, null, false, 1, 10);

$host = getHostURL();

$item_titles = array();
$item_links = array();
$item_descriptions = array();
$item_dates = array();

foreach ($comics as $comic) {
	$name = $comic->getAuthorName();
	$item_titles[] = "Comic by " . $name;
	$item_links[] = $host . "comic.php?action=show&amp;id=" . $comic->get("id");
	$item_descriptions[] = $name . " has uploaded a new comic to \"". $comic->getComicGroupTitle() . "\" group.";
	$item_dates[] = datetimeToPubDate($comic->get("mod_date"));
}
//print_r($comic_dates);
$smarty = new Smarty();
$smarty->assign("title", "Comic Terminal");
$smarty->assign("host", $host);
$smarty->assign("description", "Comic Terminal RSS Feed");

$smarty->assign("item_titles", $item_titles);
$smarty->assign("item_links", $item_links);
$smarty->assign("item_descriptions", $item_descriptions);
$smarty->assign("item_dates", $item_dates);

$smarty->display("rss.tpl");

?>
