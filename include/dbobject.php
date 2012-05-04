<?php

// Exceptions used inside DBObject
class DBObjectException extends Exception {}
class InvalidFieldException extends DBObjectException {}
class DBNotAvailableException extends DBObjectException {}
class InvalidDataException extends DBObjectException {}
class NonExistantObjectException extends DBObjectException {}

abstract class DBObject {

	protected $error;

	protected $id;	
	protected $database_relations;
	
	protected $db;
	
	public function __construct($db) {
		if ($db instanceof DBManager) {
			$this->db = $db;
		} else {
			throw new DBNotAvailableException;
		}
	}
	
	// fills the object with info from database according to requirements
	public function fillFromDatabase($field, $data) {
		if (!isset($this->db)) {
			throw new DBNotAvailableException;
		}
		if ($field != "id" && !in_array($field, $this->database_relations)) {
			throw new InvalidFieldException();
		}
		if ($this->taken($field, $data)) {
			$query = "SELECT * FROM " . $this->getTable() . " WHERE " . $field . "='[data]'";
			$result = $this->db->query($query, array("data" => $data));
			$values = $this->db->fetchArray($result);
			$this->fillFromArray($values);
		} else {
			throw new NonExistantObjectException();
		}
	}
	
	public function fillFromArray(Array $values) {
		
		foreach( $values as $member => $value ) {
			$this->$member = $value;
		}
	}
	
	public function save() {
		if (!isset($this->db)) {
			throw new DBNotAvailableException();
		}
		
		// If doesn't exist - we insert, if exists - we update
		if (!$this->exists()) {
			if ( !$this->isValid($new = true) ) {
				throw new InvalidDataException();
			}
			$query = "INSERT INTO " . $this->getTable() . "(" . implode(",", array_keys($this->database_relations)) . ") " .
					 "VALUES ('" . implode("', '", $this->db->brace(array_keys($this->database_relations))) . "')";
		} else {
			if ( !$this->isValid($new = false) ) {
				throw new InvalidDataException();
			}
			
			// Form a value line, looks like this:
			// " id={id} spam={spam} test={test} "
			$value_line = array();
			foreach ($this->database_relations as $field => $member) {
				$value_line[] = $field . "=" . "'[" . $member . "]'";
			}
			$value_line = implode(", ", $value_line);
			$query = "UPDATE " . $this->getTable() . " SET " . $value_line  . " WHERE id='[id]'";
		}
		$this->db->query($query, $this->getDataToDatabaseMap());
	}
	
	// Gets a value-to-database-field map that looks like this:
	// array( "id" => $this->id, "spam" => $this->spam, "test" => $this->test )
	// for example:
	// array( "id" => 12, "spam" => "spammytext", "test" => "testing_testing" )
	private function getDataToDatabaseMap() {
		$map = array();
		foreach ($this->database_relations as $field => $member) {
			$map[$field] = $this->$member;
		}
		// $this->id is not in $this->database_relations, so we add it manually
		$map["id"] = $this->id;
		
		return $map;
	}
	
	public function getError() {
		return $this->error;
	}
	
	public function get($member) {
		return $this->$member;
	}
	
	public function set($member, $value) {
		$this->$member = $value;
	}

	// !!! Overload This !!!
	// it should return false and set $this->error if it's not valid
	// $new determines if the object is being created or updated
	abstract protected function isValid($new = false);

	protected function exists() {
		if (!isset($this->id)) {
			return false;
		}
		return $this->taken("id", $this->id);
	}
	
	protected function taken($field, $value) {
		if (!isset($this->db)) {
			throw new DBNotAvailableException();
		}
		$query = "SELECT $field FROM " . $this->getTable() . " WHERE $field='[$field]'";
		$result = $this->db->query($query, array($field => $value));
		return $this->db->numRows($result);
	}
	
	public function delete() {
		if (!isset($this->db)) {
			throw new DBNotAvailableException();
		}
		$query = "DELETE FROM " . $this->getTable() . " WHERE id='[id]'";
		$this->db->query($query, array("id" => $this->id));
		$this->cleanup();
	}
	
	protected function cleanup() {
	}
	
	// Make this return the table in DB that the DBObject will use
	abstract protected function getTable();
	
}

?>
