<?php

class Action {
	
	protected $view;
	
	public function __construct() {
		$this->view = new View();
	}
	
	protected static function post($val = null) {
		if (!empty($val)) {
			return !empty($_POST[$val]) ? $_POST[$val] : null;
		}
		return $_POST;
	}
	
	protected static function get() {
		return $_GET;
	}
	
	protected static function request() {
		return $_REQUEST;
	}
	
}