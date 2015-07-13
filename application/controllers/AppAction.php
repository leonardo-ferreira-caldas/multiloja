<?php

class AppAction extends Action {
	
	public function home() {
		
		$this->view->render("header");
		$this->view->render("dashboard");
		$this->view->render("footer");
		
	}
	
}