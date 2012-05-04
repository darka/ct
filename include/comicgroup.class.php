<?php

require_once "dbmanager.php";
require_once "dbobject.php";
require_once "common.php";
require_once "user.class.php";

class ComicGroup extends DBObject {

	protected $table = "ct_comicgroups"; 
	
	protected $title;
	protected $user_id;
	
	protected $database_relations = array (
		"title" => "title",
		"user_id" => "user_id",
	);
	
	public function isValid($new = false) {
		$this->valid = false;
		if (strlen($this->title) > 50) {
			$this->error = "Title is too long.";
			return false;
		}
		if (!($this->title)) {
			$this->error = "No title filled-in.";
			return false;

		}
		if (!is_numeric($this->user_id)) {
			$this->error = "Invalid user id.";
			return false;
		}
		unset($this->error);
		return true;
	}
	public static function getComicGroups($db, $dbobject = null, $amount_only = false) {
		global $comicgroup_table;

		// If no dbobject, we simply take the last comics
		$query = "SELECT * FROM " . $comicgroup_table . " ORDER BY 'id'";
		if ($dbobject instanceof User) {
			// If we received a comicgroup, we take its last comics
			$query = "SELECT * FROM " . $comicgroup_table . " WHERE user_id='" . 
					 $dbobject->get("id") . "' ORDER BY 'id'";
		}
		
		// If $amount_only is true, we ignore $pages and just return number of rows 
		if ($amount_only) {
			// Just return the amount then
			$result = $db->query($query);
			return $db->numRows($result);
		}

		$result = $db->query($query);
				
		$comicgroups = array();
		
		// Collect the comicgroups
		while ($data = $db->fetchArray($result)) {
			$comicgroup = new ComicGroup($db);
			$comicgroup->fillFromArray($data);
			array_push($comicgroups, $comicgroup);
		}
		return $comicgroups;
	}

	public function getAuthorName() {
		global $user_table, $comicgroup_table;
		$query = "SELECT $user_table.username FROM " .
				 "$user_table, $comicgroup_table WHERE " .
				 "$comicgroup_table.id='[id]' AND " .
				 "$user_table.id=$comicgroup_table.user_id";
		//echo $query;
		$result = $this->db->query($query, array("id" => $this->id));
		$result = $this->db->fetchArray($result);
		return $result["username"];
	}
	
	protected function getTable() {
		global $comicgroup_table;
		return $comicgroup_table;
	}
}

?>
