<?php
header('Content-type: application/rss+xml; charset=ISO-8859-1');

require_once "include/smarty/Smarty.class.php";
require_once "include/comment.class.php";
require_once "include/common.php";

$db = new DBManager();
$comments = Comment::getComments($db);

$host = getHostURL();

$item_titles = array();
$item_links = array();
$item_descriptions = array();
$item_dates = array();

foreach ($comments as $comment) {
	$item_titles[] = "Comment by " . $comment->getAuthorName();
	$item_links[] = $host . "comic.php?action=show&amp;id=" . $comment->get("comic_id") . "#comment" . $comment->get("id");
	$item_descriptions[] = $comment->getAuthorName() . " has commented on a comic.";
	$item_dates[] = datetimeToPubDate($comment->get("mod_date"));
}

$smarty = new Smarty();
$smarty->assign("title", "Comic Terminal Comments");
$smarty->assign("host", $host);
$smarty->assign("description", "Comic Terminal Comment RSS Feed");

$smarty->assign("item_titles", $item_titles);
$smarty->assign("item_links", $item_links);
$smarty->assign("item_descriptions", $item_descriptions);
$smarty->assign("item_dates", $item_dates);
$smarty->display("rss.tpl");

?>
