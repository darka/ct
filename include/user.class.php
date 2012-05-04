<?php

require_once "dbmanager.php";
require_once "dbobject.php";
require_once "common.php";
		
class User extends DBObject {

	protected $table = "ct_users"; 
	
	protected $username;
	protected $password;
	protected $password_confirm;
	protected $access;
	protected $cookieid;
	
	protected $database_relations = array (
		"username" => "username",
		"password" => "password",
		"access" => "access",
		"cookieid" => "cookieid",
	);

	public function isValid($new = false) {
		$this->valid = false;
		if (!$this->username) {
			$this->error = "No username filled-in.";
			return false;
		}
		if (!$this->password || $this->password == $this->encrypt("")) {
			$this->error = "No password filled-in.";
			return false;
		}
		if (strlen($this->username) > 32 || ereg("[^A-Za-z0-9]", $this->username)) {
			$this->error = "Invalid username.";
			return false;
		} 
		if (isset($this->password_confirm) && $this->password !== $this->password_confirm) {
			$this->error = "Passwords do not match.";
			return false;
		}
		if (!is_numeric($this->access) || $this->access > 5 || $this->access < -1) {
			$this->error = "Invalid access rights.";
			return false;
		}
		if ($new) {
			if ( $this->taken("username", $this->username) ) {
				$this->error = "Username taken.";
				return false;
			}
		} else {
			if ( !$this->taken("username", $this->username) ) {
				$this->error = "No such username.";
				return false;
			}
		}
		unset($this->error);
		return true;
	}
	
	public function isAdmin() {
		if ($this->access >= 5) {
			return true;
		} else {
			return false;
		}
	}

	public function isAuthor() {
		if ($this->access >= 2) {
			return true;
		} else {
			return false;
		}
	}
		
	public static function getCurrent($db) {
		if ($db) {
			$user = new User($db);
		}
		try {
			if (isset($_SESSION['CT_loggedin']) && isset($_SESSION['CT_userid']) && isset($_SESSION['CT_username'])) {
				$user->fillFromDatabase("id", $_SESSION['CT_userid']);
			} else if (isset($_COOKIE['CT_ID'])) {
				$user->fillFromDatabase("cookieid", $_COOKIE['CT_ID']);
				$user->setupSession();
			} else {
				return;
			}
		} catch (NonExistantObjectException $exception) {
			return;
		}
		if ($user->isValid() && $user->get("access") != -1) {
			return $user;
		}
	}
	
	public function login($time) {
		$cookieid = $this->generateCookieID();
		$this->cookieid = $cookieid;
		$this->save();
		$this->setupCookie($cookieid, $time);
		$this->setupSession();
	}
	
	public function secureCookieID() {
		$this->cookieid = $this->generateCookieID();
	}
	
	public function encrypt($sth) {
		return md5($sth);
	}
	
	private function generateCookieID() {
		return md5(getRandomString(32));
	}
	
	private function setupSession() {
		$_SESSION['CT_loggedin'] = 1;
		$_SESSION['CT_userid'] = $this->id;
		$_SESSION['CT_username'] = $this->username;
	}
	
	private function setupCookie($key, $time) {
		setcookie("CT_ID", $key, time() + $time);
	}
	
	public function logout() {
		setcookie(session_name(), '', time()-42000, '/');
		setcookie("CT_ID", $key, time()-42000);
		session_destroy();
	}

	protected function getTable() {
		global $user_table;
		return $user_table;
	}
	public static function getUsers($db) {
		global $user_table;
		$query = "SELECT * FROM " . $user_table . " ORDER BY 'id'";
		$result = $db->query($query);
				
		$users = array();
		// Collect the users
		while ($data = $db->fetchArray($result)) {
			$user = new User($db);
			$user->fillFromArray($data);
			array_push($users, $user);
		}
		return $users;
	}
	public function disable() {
		//$this->cookieid = $this->generateCookieID();
		//$this->password = md5(getRandomString(32));
		$this->access = -1;
		$this->save();
	}
	
}

?>
