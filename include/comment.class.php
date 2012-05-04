<?php

require_once "dbmanager.php";
require_once "dbobject.php";
require_once "common.php";

class Comment extends DBObject {

	protected $table = "ct_comments";
	protected $user_table = "ct_users";
	protected $comic_table = "ct_comics";
	protected $comicgroups_table = "ct_comicgroups";
	
	protected $text;
	protected $post_date;
	protected $mod_date;
	protected $user_id;
	protected $comic_id;

	
	protected $database_relations = array (
		"text" => "text",
		"post_date" => "post_date",
		"mod_date" => "mod_date",
		"user_id" => "user_id",
		"comic_id" => "comic_id",
	);

	public function isValid($new = false) {
		$this->valid = false;
		if (strlen($this->text) > 200) {
			$this->error = "Comment is too long.";
			return false;
		}
		if (!$this->text) {
			$this->error = "No comment filled-in.";
			return false;
		}
		if (!is_numeric($this->user_id)) {
			$this->error = "Invalid user id.";
			return false;
		}
		if (!is_numeric($this->comic_id)) {
			$this->error = "Invalid comic id.";
			return false;
		}
		unset($this->error);
		return true;
	}

	public function getAuthorName() {
		global $user_table, $comic_table, $comicgroup_table;
		$query = "SELECT $user_table.username FROM " .
				 "$user_table WHERE " .
				 "$user_table.id=[user_id]";
		$result = $this->db->query($query, array("user_id" => $this->user_id));
		$result = $this->db->fetchArray($result);
		return $result["username"];
	}

	public static function getComments($db, $dbobject = null) {
		global $comment_table;
		$query = "SELECT * FROM " . $comment_table . " ORDER BY 'id' DESC LIMIT 10";
		if ($dbobject instanceof Comic) {
			$comic_id = $dbobject->get("id");
			$query = "SELECT * FROM " . $comment_table . " WHERE $comic_id=" . $comment_table . ".comic_id ORDER BY 'id' DESC";
		}

		$result = $db->query($query);
		$comments = array();
		while ($data = $db->fetchArray($result)) {
			$comment = new Comment($db);
			$comment->fillFromArray($data);
			array_push($comments, $comment);
		}
		return $comments;
	}		
	protected function getTable() {
		global $comment_table;
		return $comment_table;
	}
}

?>
