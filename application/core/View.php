<?php

class View {
	
	public function render($view, $bindings = array()) {
		
		$file = Config::getAppPath() . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . $view . ".phtml";
		
		if (file_exists($file)) {
			include $file;
		}
		
	}
	
	public function renderJSON(array $obj) {
		return json_encode($obj);		
	}
	
}