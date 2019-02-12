<?php

class JournalException extends Exception {
	private $level;
	
	public function __construct($message,$level = NULL, $code = 0, $previous = NULL) {
		parent::__construct($message, $code, $previous);
		$this->level = $level;
	}
	public function getLevel() {
		return $this->level;
	}
	
}


?>