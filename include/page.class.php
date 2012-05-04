<?php

require_once "smarty/Smarty.class.php";
require_once "user.class.php";
require_once "common.php";

abstract class Page {
	protected $allowed_actions;
	
	protected $smarty;
	protected $user;
	protected $db;
	
	public function __construct() {
		ob_start();
		session_start();
		
		$this->db = new DBManager();
		
		$this->user = User::getCurrent($this->db);
		
		$this->smarty = new Smarty();
		
		$this->smarty->display("header.tpl");
		
		//print_r($this->user);
		//echo "<br />";

		if ( isset($_GET["action"]) ) {
			$action = $_GET["action"];
			if (in_array($action, $this->allowed_actions)) {
				$this->$action();
				return;
			}
		}
		$this->main_action();
	}
	
	abstract protected function main_action();

	public function display() {
		$this->smarty->display("footer.tpl");
		ob_end_flush();
	}
	
	protected function redirect($page) {
		$header = "Location: " . getHostURL() . $page;
		header($header);
		die();
	}
	
	protected function errorOut($message = "You do not have permission to access this page.") {
		$this->smarty->assign("error", $message);
		$this->smarty->display("error.tpl");
		$this->display();
		die();
	}
	
	protected function setError($message) {
		$_SESSION['CT_error'] = $message;
	}
	
	protected function checkForErrors() {
		if (isset($_SESSION['CT_error'])) {
			$this->smarty->assign("error", $_SESSION['CT_error']);
			$this->smarty->display("error.tpl");
			unset($_SESSION['CT_error']);
		}
	}
	
	protected function requireAuthorUser() {
		if ( !$this->user || !$this->user->isAuthor() )  {
			$this->errorOut();
		}
	}

	protected function requireAdminUser() {
		if ( !$this->user || !$this->user->isAdmin() )  {
			$this->errorOut();
		}
	}
	
	protected function requireUser() {
		if ( !$this->user )  {
			$this->errorOut();
		}
	}
	
	protected function listComics($page, $dbobject = null) {
		$comics_per_page = 25;
		$comics = Comic::getComics($this->db, $dbobject, false, $page, $comics_per_page);
		$comics = array_reverse($comics);
		$comic_thumbs = array();
		$comic_authors = array();
		$comic_ids = array();
		foreach ($comics as $comic) {
			$comic_thumbs[] = $comic->getThumbFilename();
			$comic_ids[] = $comic->get("id");
		}
		$this->smarty->assign("comic_thumbs", $comic_thumbs);
		$this->smarty->assign("comic_ids", $comic_ids);

		$this->smarty->assign("current_page", $page);		
		$pages = Comic::pages($this->db, $dbobject, $comics_per_page);
		if (!is_numeric($page) || $page > $pages || $page < 1) {
			$page = 1;
		}
		if ($page > 1) {
			$prev_page = $page - 1;
			$this->smarty->assign("prev_page", "$prev_page");
		}
		if ($page < $pages) {
			$next_page = $page + 1;
			$this->smarty->assign("next_page", "$next_page");
		}
		// Setup comic page list
		$page_list = Comic::pageList($this->db, $dbobject, $comics_per_page);
		$page_list = array_reverse($page_list);
		$this->smarty->assign("page_list", $page_list);
	
		$this->smarty->display("comic/list.tpl");

	}

	protected function request($dbobject, $source = "id", $field = "id") {
		if (!isset($_REQUEST[$source])) {
			$this->errorOut("No Object specified.");
		}
		if ($field == "id" && !is_numeric($_REQUEST[$source])) {
			$this->errorOut("Invalid Object specified.");
		}
		$data = $_REQUEST[$source];
		$object = new $dbobject($this->db);
		try {
			$object->fillFromDatabase($field, $data);
		} catch (NonExistantObjectException $exception) {
			$this->errorOut("Invalid Object specified.");
		}
		return $object;
	}
	
	public function __destruct() {
	}
}

?>
