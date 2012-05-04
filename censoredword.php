<?php

require_once("include/page.class.php");

require_once("include/censoredword.class.php");

class CensoredWordPage extends Page {
	protected $allowed_actions = array(
		"add", 
		"delete", 
	);
	
	protected function add() {
		$this->requireAdminUser();
		if ( !isset($_POST["censor_word"]) ) {
			$this->checkForErrors();
			// If we're not coming from the form page, display the form
			$this->smarty->display("censoredword/add.tpl");
			return;
		}
		$censoredword = new CensoredWord($this->db);
		$censoredword->set("word", $_POST["word"]);
		$censoredword->set("replacement", $_POST["replacement"]);
		// Set random cookie id for security
		try {
			$censoredword->save();
			// Redirect to login screen
			$this->redirect("censoredword.php");
		} catch (InvalidDataException $exception) {
			// If some of entered data didn't pass the validation test, we
			// show the error and redisplay the form
			$this->setError($censoredword->getError());
			$this->redirect("censoredword.php?action=add");
		}
	}

	
	protected function main_action() {
		$this->requireAdminUser();
		$censoredwords = CensoredWord::getCensoredWords($this->db);
		$censoredword_words = array();
		$censoredword_replacements = array();
		$censoredword_ids = array();
		foreach ($censoredwords as $censoredword) {
			$censoredword_words[] = htmlentities($censoredword->get("word"));
			$censoredword_replacements[] = htmlentities($censoredword->get("replacement"));
			$censoredword_ids[] = $censoredword->get("id");
		}
		$this->smarty->assign("censoredword_ids", $censoredword_ids);
		$this->smarty->assign("censoredword_replacements", $censoredword_replacements);
		$this->smarty->assign("censoredword_words", $censoredword_words);
		$this->smarty->display("censoredword/list.tpl");
		$this->smarty->display("censoredword/add.tpl");
	}
	
	protected function delete() {
		$this->requireAdminUser();
		$censoredword = $this->request("CensoredWord");
		$censoredword->delete();
		$this->redirect("censoredword.php");	
	}
}

$page = new CensoredWordPage();
$page->display();

?>
