<?php

require_once("include/page.class.php");
require_once("include/comicgroup.class.php");
require_once("include/comic.class.php");

class ComicGroupPage extends Page {
	protected $allowed_actions = array(
		"create", 
		"edit_title",
		"show",
	);
	
	protected function create() {
		$this->requireAuthorUser();
		if ( !isset($_POST["create_group"]) ) {
			// If we're not coming from the form page, display the form
			$this->checkForErrors();
			$this->smarty->display("comicgroup/create.tpl");
			return;
		}
		try {
			$comicgroup = new ComicGroup($this->db);
			$comicgroup->set("user_id", $this->user->get("id"));
			$comicgroup->set("title", $_POST["title"]);
			$comicgroup->save();
			$this->redirect("comic.php?action=upload");
		} catch (InvalidDataException $exception) {
			// If some of entered data didn't pass the validation test, we
			// show the error and redisplay the form
			$this->setError($comicgroup->getError());
			$this->redirect("comicgroup.php?action=create");
		}
	}
	
	protected function edit_title() {
		$this->requireAuthorUser();
		$comicgroup = $this->request("ComicGroup");
		// Check if user is admin, and if he's not, check if it's his comic group
		if (!$this->user->isAdmin()) {
			if ($comicgroup->get("user_id") != $this->user->get("id")) {
				$this->errorOut();
			}
		}
		if ( !isset($_POST["edit_title"]) ) {
			// If we're not coming from the form page, display the form
			$this->checkForErrors();
			$this->smarty->assign("id", $comicgroup->get("id"));
			$this->smarty->assign("current_title", htmlentities($comicgroup->get("title")));
			$this->smarty->display("comicgroup/edit.tpl");
			return;
		}
		$comicgroup->set("title", $_POST["title"]);
		$id = $comicgroup->get("id");
		try {
			$comicgroup->save();
			$this->redirect("comicgroup.php?action=show&id=$id");
		} catch (InvalidDataException $exception) {
			// If some of entered data didn't pass the validation test, we
			// show the error and redisplay the form
			$this->setError($comicgroup->getError());
			$this->redirect("comicgroup.php?action=edit_title&id=$id");
		}
	}

	protected function main_action() {
		return;
	}
	
	protected function show() {
		$comicgroup = $this->request("ComicGroup");
		$this->smarty->assign("comicgroup_id", $comicgroup->get("id"));
		if (isset($_GET["page"])) {
			$page = $_GET["page"];
		} else {
			$page = 1;
		}
		$this->listComics($page, $comicgroup);
		$this->smarty->assign("comicgroup_title", htmlentities($comicgroup->get("title")));
		$this->smarty->assign("comicgroup_author", $comicgroup->getAuthorName());
		if ($this->user) {
			if ($this->user->isAuthor() || $this->user->isAdmin()) {
				$this->smarty->assign("display_author_options", 1);
			}
		}
		
		$this->smarty->display("comicgroup/show.tpl");
	}
}

$page = new ComicGroupPage();
$page->display();

?>
