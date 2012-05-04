<?php

require_once("include/page.class.php");
require_once("include/comic.class.php");
require_once("include/comment.class.php");

class CommentPage extends Page {
	protected $allowed_actions = array(
		"post", 
		"edit",
		"delete",
	);
	
	protected function post() {
		$this->requireUser();
		$comic = $this->request("Comic", "comic_id");
		if ( !isset($_POST["comment"]) ) {
			$this->checkForErrors();
			$this->smarty->assign("comic_id", $comic->get("id"));
			$this->smarty->display("comment/post.tpl");
			return;
		}
	
		$comment = new Comment($this->db);
		$comment->set("text", $_POST["text"]);
		$comment->set("user_id", $this->user->get("id"));
		$comment->set("comic_id", $comic->get("id"));
		$current_date = getCurrentDate();
		$comment->set("post_date", $current_date);
		$comment->set("mod_date", $current_date);
		try {
			$comic_id = $comment->get("comic_id");
			$comment->save();
			$this->redirect("comic.php?action=show&id=$comic_id#comments");
		} catch (InvalidDataException $exception) {
			//$this->smarty->assign("comic_id", $comic->get("id"));
			$this->setError($comment->getError());
			$this->redirect("comment.php?action=post&comic_id=$comic_id");
		}
	}
	
	protected function edit() {
		$this->requireAdminUser();
		$comment = $this->request("Comment");
		if ( !isset($_POST["edit_comment"]) ) {
			$this->checkForErrors();
			$this->smarty->assign("text", htmlentities($comment->get("text")));
			$this->smarty->assign("comment_id", $comment->get("id"));
			$this->smarty->display("comment/edit.tpl");
			return;
		}
		try {
			$comment->set("text", $_POST["text"]);
			$current_date = getCurrentDate();
			$comment->set("mod_date", $current_date);
			$comment->save();
			$comic_id = $comment->get("comic_id");
			$comment_id = $comment->get("id");
			$this->redirect("comic.php?action=show&id=$comic_id#comment$comment_id");
		} catch (InvalidDataException $exception) {
			$id = $comment->get("id");
			$this->setError($comment->getError());
			$this->redirect("comment.php?action=edit&id=$id");
		}
	}
	
	protected function delete() {
		$this->requireAdminUser();
		$comment = $this->request("Comment");
		if ( !isset($_POST["confirm_delete"]) ) {
			$this->smarty->assign("user", $comment->getAuthorName());
			$this->smarty->assign("text", htmlentities($comment->get("text")));
			$this->smarty->assign("comment_id", $comment->get("id"));
			$this->smarty->display("comment/delete.tpl");
			return;
		}
		$comic_id = $comment->get("comic_id");
		$comment->delete();
		$this->redirect("comic.php?action=show&id=$comic_id#comments");
	}
	
	protected function main_action() {
		return;
	}
}

$page = new CommentPage();
$page->display();

?>
