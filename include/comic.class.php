<?php

require_once "dbmanager.php";
require_once "dbobject.php";
require_once "common.php";

require_once "image.class.php";
require_once "comment.class.php";
require_once "user.class.php";
		
class Comic extends DBObject {

	protected $filename;
	protected $comicgroup_id;
	protected $post_date;
	protected $mod_date;
	
	protected $database_relations = array (
		"filename" => "filename",
		"comicgroup_id" => "comicgroup_id",
		"post_date" => "post_date",
		"mod_date" => "mod_date",
	);

	public function setImage($file) {
		$image = new Image();
		$image->upload($file);
		$this->filename = $image->getFilename();
	}
	
	public function getThumbFilename() {
		return Image::getThumbFilename($this->filename);
	}
	
	public function isValid($new = false) {
		$this->valid = false;
		if (!is_numeric($this->comicgroup_id)) {
			$this->error = "Invalid comic group id.";
			return false;
		}
		unset($this->error);
		return true;
	}
	
	protected function getTable() {
		global $comic_table;
		return $comic_table;
	}
	
	protected function cleanup() {
		global $comment_table;
		if (!isset($this->db)) {
			throw new DBNotAvailableException();
		}
		$query = "DELETE FROM " . $comment_table . " WHERE comic_id='[id]'";
		$this->db->query($query, array("id" => $this->id));
	}

	public function getNextID() {
		$query = "SELECT id FROM " . $this->getTable() . " WHERE id > [id] AND comicgroup_id=[comicgroup_id] ORDER BY id ASC LIMIT 1";
		$result = $this->db->query($query, array("id" => $this->id, "comicgroup_id" => $this->comicgroup_id));
		$databaseInfo = $this->db->fetchArray($result);
		if (!$databaseInfo) {
			throw new NonExistantObjectException();
		}
		return $databaseInfo["id"];
	}
		
	public function getPreviousID() {
		$query = "SELECT id FROM " . $this->getTable() . " WHERE id < [id] AND comicgroup_id=[comicgroup_id] ORDER BY id DESC LIMIT 1";
		$result = $this->db->query($query, array("id" => $this->id, "comicgroup_id" => $this->comicgroup_id));
		$databaseInfo = $this->db->fetchArray($result);
		if (!$databaseInfo) {
			throw new NonExistantObjectException();
		}
		return $databaseInfo["id"];
	}
	
	public function getAuthorName() {
		global $user_table, $comicgroup_table;
		$query = "SELECT $user_table.username FROM " .
				 "$user_table, $comicgroup_table WHERE " .
				 "$comicgroup_table.id='[comicgroup_id]' AND " .
				 "$user_table.id=$comicgroup_table.user_id";
		$result = $this->db->query($query, array("comicgroup_id" => $this->comicgroup_id));
		$result = $this->db->fetchArray($result);
		return $result["username"];
	}

	public function getAuthorID() {
		global $user_table, $comicgroup_table;
		$query = "SELECT $user_table.id FROM " .
				 "$user_table, $comicgroup_table WHERE " .
				 "$comicgroup_table.id='[comicgroup_id]' AND " .
				 "$user_table.id=$comicgroup_table.user_id";
		$result = $this->db->query($query, array("comicgroup_id" => $this->comicgroup_id));
		$result = $this->db->fetchArray($result);
		return $result["id"];
	}
	
	public function getComicGroupTitle() {
		global $comicgroup_table;
		$query = "SELECT $comicgroup_table.title FROM " .
				 "$comicgroup_table WHERE " .
				 "$comicgroup_table.id='[comicgroup_id]'";
		$result = $this->db->query($query, array("comicgroup_id" => $this->comicgroup_id));
		$result = $this->db->fetchArray($result);
		return $result["title"];
	}
	
	public static function getComics($db, $dbobject = null, $amount_only = false, $page = 1, $per_page = 15) {
		global $comic_table;

		// If no dbobject, we simply take the last comics
		$query = "SELECT * FROM " . $comic_table . " ORDER BY id";
		if ($dbobject instanceof ComicGroup) {
			// If we received a comicgroup, we take its last comics
			$query = "SELECT * FROM " . $comic_table . " WHERE comicgroup_id='" . 
					 $dbobject->get("id") . "' ORDER BY id";
		} elseif ($dbobject instanceof User) {
			// If we received a user, we take its last comics
			global $comicgroup_table;
			$query = "SELECT $comic_table.* FROM $comic_table, $comicgroup_table WHERE " .
					 "$comicgroup_table.user_id='" . $dbobject->get("id") . "' AND " .
					 "$comicgroup_table.id=$comic_table.comicgroup_id " . 
					 "ORDER BY id";
		}
		
		// If $amount_only is true, we ignore $pages and just return number of rows 
		if ($amount_only) {
			// Just return the amount then
			$result = $db->query($query);
			return $db->numRows($result);
		}
		$pages = Comic::pages($db, $per_page);

		if ($page > 1 && $page <= $pages) {
			// If pages isn't 0 and is valid, we limit the result to it
			
			// We have to subtract 1 from pages since that's how it is in db
			$page = $page - 1;
			$query .= " DESC LIMIT " . $page*$per_page . ", " . $per_page;
		} else {
			// Otherwise we just show the last 15
			$query .= " DESC LIMIT " . $per_page;
		}
		//print $query;
		$result = $db->query($query);
				
		$comics = array();
		// Collect the comics
		while ($data = $db->fetchArray($result)) {
			$comic = new Comic($db);
			$comic->fillFromArray($data);
			array_push($comics, $comic);
		}
		return $comics;
	}
	
	public static function pages($db, $dbobject = null, $per_page = 15) {
		$comics = Comic::getComics($db, $dbobject, true, 0, $per_page);
		$pages = $comics/$per_page;
		if ($pages != (int) $pages)
			$pages = (int) $pages + 1;
		return $pages;
	}
	
	public static function pageList($db, $dbobject = null, $per_page = 15) {
		$pages = Comic::pages($db, $dbobject, $per_page);
		$page_list = array();
		for ($page = 1; $page <= $pages; $page++) {
			$page_list[] = $page;
		}
		return $page_list;
	}
	
	public static function randomID($db) {
		global $comic_table;
		$query = "SELECT id FROM " . $comic_table . " ORDER BY id DESC";
		$result = $db->query($query);
		$data = $db->fetchArray($result);
		$maxID = $data["id"];
		
		$randomID = null;
		while ($randomID == null) {
			$comic = new Comic($db);
			try {
				$tempID = rand(1, $maxID);
				$comic->fillFromDatabase("id", $tempID);
				$randomID = $tempID;
			} catch (NonExistantObjectException $exception) {
			}
		}
		return $randomID;
	}
}

?>
