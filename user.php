<?php

require_once("include/page.class.php");

require_once("include/comic.class.php");

class UserPage extends Page {
	protected $allowed_actions = array(
		"register", 
		"login", 
		"logout", 
		"change_password", 
		"change_access",
		"manage",
		"show",
	);
	
	protected function register() {
		if ( !isset($_POST["register"]) ) {
			$this->checkForErrors();
			// If we're not coming from the form page, display the form
			$this->smarty->display("user/register.tpl");
			return;
		}
		$user = new User($this->db);
		$user_data = array(
			"username" => $_POST["username"],
			"password" => $user->encrypt($_POST["password"]),
			"password_confirm" => $user->encrypt($_POST["password_confirm"]),
		);
		// Access defaults to 0
		$user->set("access", 0);
		// Set random cookie id for security
		$user->secureCookieID();
		$user->fillFromArray($user_data);
		try {
			$user->save();
			// Redirect to login screen
			$this->redirect("user.php?action=login");
		} catch (InvalidDataException $exception) {
			// If some of entered data didn't pass the validation test, we
			// show the error and redisplay the form
			$this->setError($user->getError());
			$this->redirect("user.php?action=register");
		}
	}
	
	protected function login() {
		if ( !isset($_POST["login"]) ) {
			$this->checkForErrors();
			// Display the form
			$this->smarty->display("user/login.tpl");
			return;
		}
		$user = new User($this->db);
		try {
			$user->fillFromDatabase("username", $_POST["username"]);
			if ($user->encrypt($_POST["password"]) != $user->get("password")) {
				throw new DBObjectException();
			}
			$user->login($_POST["time"]);
			$this->redirect("user.php");
		} catch (DBObjectException $exception) {
			// The only possible error is this, so we catch all exceptions
			$this->setError("Wrong username or password.");
			$this->redirect("user.php?action=login");
		}
	}
	
	protected function logout() {
		if ($this->user) {
			$this->user->logout();
			$this->redirect("user.php?action=login");
		}
	}
	
	protected function change_password() {
		// TODO: add admin level password changing
		$this->requireUser();
		if ( !isset($_POST["change_password"]) ) {
			$this->checkForErrors();
			$this->smarty->display("user/change_password.tpl");
			return;
		}
		$this->user->set("password", $this->user->encrypt($_POST["password"]));
		$this->user->set("password_confirm", $this->user->encrypt($_POST["password_confirm"]));			
		try {
			$this->user->save();
			$this->redirect("user.php");
		} catch (InvalidDataException $exception) {
			$this->setError($this->user->getError());
			$this->redirect("user.php?action=change_password");
		}
	}
	
	protected function change_access() {
		$this->requireAdminUser();
		$user = $this->request("User");
		if ( !isset($_POST["change_access"]) ) {
			$this->checkForErrors();
			$this->smarty->assign("id", $user->get("id"));
			$this->smarty->assign("username", $user->get("username"));
			$this->smarty->assign("access_level", $user->get("access"));
			$this->smarty->display("user/change_access.tpl");
			return;
		}
		try {
			$user->set("access", $_POST["access"]);
			$user->save();
			$this->redirect("user.php?action=manage");
		} catch (InvalidDataException $exception) {
			$this->smarty->assign("id", $user->get("id"));
			$this->smarty->assign("username", $user->get("username"));
			$this->smarty->assign("access_level", $user->get("access"));
			$this->setError($user->getError());
			$this->redirect("user.php?action=change_access&id=$id");
		}
	}
	
	protected function manage() {
		$this->requireAdminUser();
		$users = User::getUsers($this->db);
		$user_usernames = array();
		$user_ids = array();
		foreach ($users as $user) {
			$user_usernames[] = $user->get("username");
			$user_ids[] = $user->get("id");
		}
		$this->smarty->assign("user_usernames", $user_usernames);
		$this->smarty->assign("user_ids", $user_ids);
		$this->smarty->display("user/list.tpl");
	}
	protected function main_action() {
		if ($this->user) {
			if ($this->user->isAuthor()) {
				$this->smarty->assign("is_author", true);
			}
			if ($this->user->isAdmin()) {
				$this->smarty->assign("is_admin", true);
			}
			$this->smarty->display("user/personal.tpl");
		} else {
			$this->login();
		}
	
	}
	protected function show() {
		$user = $this->request("User");
		$this->smarty->assign("user_id", $user->get("id"));
		if (isset($_GET["page"])) {
			$page = $_GET["page"];
		} else {
			$page = 1;
		}
		$this->listComics($page, $user);	
	}
}

$page = new UserPage();
$page->display();

?>
