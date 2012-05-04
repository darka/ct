<?php

require_once "dbmanager.php";
require_once "dbobject.php";
require_once "common.php";

class CensoredWord extends DBObject {

	protected $table = "ct_censoredwords"; 
	
	protected $word;
	protected $replacement;
	
	protected $database_relations = array (
		"word" => "word",
		"replacement" => "replacement"
	);
	
	public function isValid($new = false) {
		$this->valid = false;
		if (strlen($this->word) > 50) {
			$this->error = "Word is too long.";
			return false;
		}
		if (strlen($this->replacement) > 50) {
			$this->error = "Replacement is too long.";
			return false;
		}
		if (!($this->word)) {
			$this->error = "No word filled-in.";
			return false;
		}
		if (!($this->replacement)) {
			$this->error = "No replacement word filled-in.";
			return false;
		}
		unset($this->error);
		return true;
	}
	public static function getCensoredWords($db) {
		global $censoredword_table;

		// If no dbobject, we simply take the last comics
		$query = "SELECT * FROM " . $censoredword_table . " ORDER BY 'id'";

		// If $amount_only is true, we ignore $pages and just return number of rows 
		$result = $db->query($query);
				
		$censoredwords = array();
		
		// Collect the comicgroups
		while ($data = $db->fetchArray($result)) {
			$censoredword = new CensoredWord($db);
			$censoredword->fillFromArray($data);
			array_push($censoredwords, $censoredword);
		}
		return $censoredwords;
	}
	
	protected function getTable() {
		global $censoredword_table;
		return $censoredword_table;
	}
}

?>
