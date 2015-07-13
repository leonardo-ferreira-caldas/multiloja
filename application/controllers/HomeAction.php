<?php

class HomeAction extends Action {
	
	public function index() {
		
		$this->view->render("template_1/header");
		$this->view->render("template_1/home");
		$this->view->render("template_1/footer");
		
	}
	
}