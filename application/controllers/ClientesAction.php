<?php

class ClientesAction extends Action {
	
	public function index() {
		
		$this->view->render("header");
		$this->view->render("clientes");
		$this->view->render("footer");
		
	}
	
	public function consultar() {
		
		$idCliente = Action::post("id_cliente");
		
		$bindings = array();

		$clientLayer = new ClientesBO(); 
		$dados       = $clientLayer->buscarDadosCliente($idCliente);

		echo $this->view->renderJSON($dados);
	
	}
	
	public function deletar() {
		$idCliente = Action::post("id_cliente");
		
		$clientLayer = new ClientesLayer();
		$clientLayer->deletar($idCliente);
		
		echo $this->view->renderJSON(array("success" => true));
	}
	
	public function listar() {

		$gridLayer = new GridLayer(Action::post());
		$response = $gridLayer->buscarClientes();
	
		echo $this->view->renderJSON($response);
	
	}
	
}