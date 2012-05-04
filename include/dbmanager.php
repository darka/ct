<?php

require_once "common.php";
require_once "settings.php";


// Helper functions used in DBManager
function addBracesArray(&$item) {
	$item = "[" . $item . "]";
}
function filterArray(&$item) {
	if (get_magic_quotes_gpc() != 1) {
		$item = mysql_real_escape_string($item);
	}
}

// DBManager automatically manages MySQL connections

class DBManager {

	private $link;
	
	public function __construct() {
		global $db_host, $db_username, $db_password, $db_name;
		$this->link = mysql_connect($db_host, $db_username, $db_password)
			or $this->errorOut('Could not connect to database');
		mysql_select_db($db_name) or $this->errorOut('Could not select database');
		/*echo "NEW DB!!";
		debug_print_backtrace();
		echo "<br/>";*/
	}
	
	// Filters either an array or a string from evil characters 
	// !!! MUST be used before querying or inserting anything into DB !!!
	// prevents SQL "injections" (hopefully ;-)
	public function filter(&$sth) {
		if ( is_string($sth) ) {
			if (get_magic_quotes_gpc() != 1) {
				$sth = mysql_real_escape_string($sth);
			}
		} elseif ( is_array($sth) ) {
			array_walk($sth, "filterArray");
		}
		return $sth;
	}

	// Adds braces around sth
	// Can be used on arrays
	public function brace(&$sth) {
		if ( is_string($sth) ) {
			$sth = "[" . $sth . "]";
		} elseif ( is_array($sth) ) {
			array_walk($sth, "addBracesArray");
		}
		return $sth;
	}

	// Queries the database
	// Use this instead of mysql_query
	public function query($query, Array $replacements=null) {
		if ($replacements !== null) {
			//print_r($this->brace(array_keys($replacements)));
			//print_r($this->filter($replacements));
			$keys = $this->brace(array_keys($replacements));
			$values = $this->filter($replacements);
			$query = str_replace($keys, $values, $query);
		}
		$result = mysql_query($query) or $this->errorOut('Could not query database');
		return $result;
	}

	// Fetches an array with info requested by $query
	public function fetchArray($query_result) {
		return mysql_fetch_assoc($query_result);
	}

	// Fetches a row with info requested by $query
	public function fetchRow($query_result) {
		return mysql_fetch_row($query_result);
	}

	// Returns number of rows according to $query
	public function numRows($query_result) {
		return mysql_num_rows($query_result);
	}

	private function errorOut($message, $addMySQLError=true) {
		// TODO: replace with exceptions
		if ($addMySQLError) {
			die($message . ": " . mysql_error());
		} else {
			die($message);
		}
	}
	
	public function __destruct() {
		// Not necessary
		//mysql_close($this->link);
	}
}

?>
