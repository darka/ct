<?php

require_once("include/page.class.php");
require_once("include/comic.class.php");
require_once("include/censoredword.class.php");
require_once("include/comicgroup.class.php");

class ComicPage extends Page {
	protected $allowed_actions = array(
		"upload", 
		"reupload",
		"delete",
		"show",
		"random",
		"change_group",
	);
	
	protected function upload() {
		$this->requireAuthorUser();
		if (ComicGroup::getComicGroups($this->db, $this->user, true) <= 0) {
			$this->setError("You have to create a Comic Group first!");
			$this->redirect("comicgroup.php?action=create");
		}
		if ( !isset($_POST["upload"]) ) {
			// If we're not coming from the form page, display the form
			$this->checkForErrors();
			$this->assignComicGroups();
			$this->smarty->display("comic/upload.tpl");
			return;
		}
		try {
			$comic = new Comic($this->db);
		
			$comicgroups = ComicGroup::getComicGroups($this->db, $this->user);
			$comicgroup_ids = array();
			foreach ($comicgroups as $comicgroup) {
				$comicgroup_ids[] = $comicgroup->get("id");
			}
			if (!in_array($_POST["comicgroup"], $comicgroup_ids)) {
				$this->setError("Invalid Comic Group.");
				$this->redirect("comic.php?action=upload");
			}
			$comic->set("comicgroup_id", $_POST["comicgroup"]);


			// We save the last group id in a cookie so we can reuse it in the form
			// next time we upload a comic
			setcookie("CT_LASTCOMICGROUP", $_POST["comicgroup"], time() + 16070400);
			
			$current_date = getCurrentDate();
			$comic->set("post_date", $current_date);
			$comic->set("mod_date", $current_date);
			
			$comic->setImage($_FILES["image"]);
			$comic->save();
			$this->redirect("comic.php");
		} catch (InvalidDataException $exception) {
			// If some of entered data didn't pass the validation test, we
			// show the error and redisplay the form
			$this->setError($comic->getError());
			$this->redirect("comic.php?action=upload");
		} catch (InvalidImageException $exception) {
			$this->setError("Invalid Image.");
			$this->redirect("comic.php?action=upload");
		}
	}
	
	protected function change_group() {
		$this->requireAuthorUser();
		$comic = $this->request("Comic");
		
		if ($this->user->isAdmin()) {
			$user = new User($this->db);
			$user->fillFromDatabase("id", $comic->getAuthorID());
		} else {
			if ($comic->getAuthorID() != $this->user->get("id")) {
				$this->errorOut();
			}
			$user = $this->user;
		}
		if ( !isset($_POST["change_group"]) ) {
			// Display the form
			$this->checkForErrors();
			$this->smarty->assign("id", $comic->get("id"));
			$this->assignComicGroups(true, $user);
			$this->smarty->assign("current_comic", $comic->get("filename"));
			$this->smarty->assign("selected_comicgroup_id", $comic->get("comicgroup_id"));
			$this->smarty->display("comic/change_group.tpl");
			return;
		}
		try {

			$comicgroups = ComicGroup::getComicGroups($this->db, $user);
			$comicgroup_ids = array();
			foreach ($comicgroups as $comicgroup) {
				$comicgroup_ids[] = $comicgroup->get("id");
			}
			if (!in_array($_POST["comicgroup"], $comicgroup_ids)) {
				$this->setError("Invalid Comic Group.");
				$this->redirect("comic.php?action=upload");
			}
			
			$comic->set("comicgroup_id", $_POST["comicgroup"]);
			$comic_id = $comic->get("id");
			$comic->save();
			$this->redirect("comic.php?action=show&id=$comic_id");
		} catch (InvalidDataException $exception) {
			$this->setError($comic->getError());
			$this->redirect("comic.php?action=change_group&id=$comic_id");
		}
	}
	
	private function assignComicGroups($ignore_cookie = false, $user = null) {
		if ($user === null) {
			$comicgroups = ComicGroup::getComicGroups($this->db, $this->user);
		} else {
			$comicgroups = ComicGroup::getComicGroups($this->db, $user);
		}
		$comicgroup_titles = array();
		$comicgroup_ids = array();
		foreach ($comicgroups as $comicgroup) {
			$comicgroup_titles[] = $comicgroup->get("title");
			$comicgroup_ids[] = $comicgroup->get("id");
		}
		$this->smarty->assign("comicgroups_ids", $comicgroup_ids);
		$this->smarty->assign("comicgroups", $comicgroup_titles);
		if ($ignore_cookie === false) {
			if (isset($_COOKIE['CT_LASTCOMICGROUP'])) {
				$last_comicgroup_id = $_COOKIE['CT_LASTCOMICGROUP'];
				if (in_array($last_comicgroup_id, $comicgroup_ids)) {
					$this->smarty->assign("last_comicgroup_id", $last_comicgroup_id);				
				}
			}
		}
	}
		
	protected function reupload() {
		$this->requireAuthorUser();
		$comic = $this->request("Comic");
		// If comic does not belong to the author, we don't allow him here
		// (unless he's admin)
		if (!$this->user->isAdmin()) {
			if ($this->user->get("id") != $comic->getAuthorID()) {
				$this->errorOut();
			}
		}
		if ( !isset($_POST["reupload"]) ) {
			// Display the form
			$this->checkForErrors();
			$this->smarty->assign("current_comic", htmlentities($comic->get("filename")));
			$this->smarty->assign("id", $comic->get("id"));
			$this->smarty->display("comic/reupload.tpl");
			return;
		}
		try {
			$comic->setImage($_FILES["image"]);
			$current_date = getCurrentDate();
			$comic->set("mod_date", $current_date);
			$comic_id = $comic->get("id");
			$comic->save();
			$this->redirect("comic.php?action=show&id=$comic_id");
		} catch (InvalidDataException $exception) {
			$this->setError($comic->getError());
			$this->redirect("comic.php?action=reupload&id=$comic_id");
		} catch (InvalidImageException $exception) {
			$this->setError("Invalid Image.");
			$this->redirect("comic.php?action=reupload&id=$comic_id");
		}
	}
	
	protected function delete() {
		$this->requireAdminUser();
		$comic = $this->request("Comic");
		if ( !isset($_POST["confirm_delete"]) ) {
			$this->smarty->assign("user", $comic->getAuthorName());
			$this->smarty->assign("comic", htmlentities($comic->get("filename")));
			$this->smarty->assign("id", $comic->get("id"));
			$this->smarty->display("comic/delete.tpl");
			return;
		}
		$id = $comic->get("comicgroup_id");
		$comic->delete();
		$this->redirect("comicgroup.php?action=show&id=$id");
	}
	
	protected function main_action() {
		if (isset($_GET["page"])) {
			$page = $_GET["page"];
		} else {
			$page = 1;
		}
		$this->listComics($page);
		if ($page == 1) {
			$comicgroups = ComicGroup::getComicGroups($this->db);
			$comicgroups_ids = array();
			$comicgroups_titles = array();
			foreach ($comicgroups as $comicgroup) {
				$comicgroups_ids[] = $comicgroup->get("id");
				$comicgroups_titles[] = htmlentities($comicgroup->get("title"));
			}
			$this->smarty->assign("comicgroup_ids", $comicgroups_ids);
			$this->smarty->assign("comicgroup_titles", $comicgroups_titles);
			$this->smarty->display("comicgroup/list.tpl");
						
			require_once("include/comment.class.php");
			$comments = Comment::getComments($this->db);
			$comment_ids = array();
			$comment_comic_ids = array();
			$comment_usernames = array();
			foreach ($comments as $comment) {
				$comment_ids[] = $comment->get("id");
				$comment_comic_ids[] = $comment->get("comic_id");
				$comment_usernames[] = $comment->getAuthorName();
			}
			$this->smarty->assign("comment_ids", $comment_ids);
			$this->smarty->assign("comment_comic_ids", $comment_comic_ids);
			$this->smarty->assign("comment_usernames", $comment_usernames);
			$this->smarty->display("comment/latest.tpl");
		}
	}
	
	protected function random() {
		$comic_id = Comic::randomId($this->db);
		$this->redirect("comic.php?action=show&id=$comic_id");
	}
	
	protected function show() {
		$comic = $this->request("Comic");
		$this->smarty->assign("comic_image", htmlentities($comic->get("filename")));
	
		//Setup comments
		$comments = Comment::getComments($this->db, $comic);
		$comment_texts = array();
		$comment_authors = array();
		$comment_ids = array();
		$comment_author_ids = array();
		$censoredwords = CensoredWord::getCensoredWords($this->db);
		foreach ($comments as $comment) {
			$temp_text = $comment->get("text");
			// replace censored words
			foreach ($censoredwords as $censoredword) {
				$temp_text = str_ireplace($censoredword->get("word"), $censoredword->get("replacement"), $temp_text);
			}
			$comment_texts[] = nl2br(htmlentities($temp_text));
			$comment_authors[] = $comment->getAuthorName();
			$comment_ids[] = $comment->get("id");
			$comment_author_ids[] = $comment->get("user_id");
		}
		$this->smarty->assign("comment_texts", $comment_texts);
		$this->smarty->assign("comment_authors", $comment_authors);
		// comment_author_ids only matters to admins, because they get change_access links
		if ($this->user && $this->user->isAdmin()) {
			$this->smarty->assign("comment_author_ids", $comment_author_ids);
		}
		// comment_ids matter to normal people too because they get links on main 
		// page
		$this->smarty->assign("comment_ids", $comment_ids);
		
		// ugly ugly way to display previous/next comic links
		try {
			$prevID = $comic->getPreviousID();
			$this->smarty->assign("prevID", $prevID);
		} catch (NonExistantObjectException $exception) {
		}
		try {
			$nextID = $comic->getNextID();
			$this->smarty->assign("nextID", $nextID);
		} catch (NonExistantObjectException $exception) {
		}
		
		$this->smarty->assign("comic_author", $comic->getAuthorName());
		$this->smarty->assign("comicgroup", htmlentities($comic->getComicGroupTitle()));
		$this->smarty->assign("comicgroup_id", $comic->get("comicgroup_id"));
		$this->smarty->assign("comic_post_date", $comic->get("post_date") . " GMT");
		$this->smarty->assign("comic_mod_date", $comic->get("mod_date") . " GMT");
		if ($this->user) {
			if ($this->user->isAuthor() && $this->user->get("id") == $comic->getAuthorID()) {
				// for author specific stuff
				$this->smarty->assign("display_author_options", 1);
			}
			if ($this->user->isAdmin()) {
				// for admin specific stuff
				$this->smarty->assign("display_author_options", 1);
				$this->smarty->assign("display_admin_options", 1);
			}
			// comment form is only shown to registered users
			// comic group changing link is only showed to real authors of comics
			$this->smarty->assign("comic_id", $comic->get("id"));
		}		
		$this->smarty->display("comic/show.tpl");
		if ($this->user) {
			$this->smarty->display("comment/post.tpl");
		}
	}
}

$page = new ComicPage();
$page->display();

?>
