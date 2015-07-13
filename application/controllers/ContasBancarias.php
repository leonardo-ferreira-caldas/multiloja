<?php

class ContasBancarias extends Controller {
	
	public function index() {
		
		$this->view->render("header");
		$this->view->render("contas_bancarias/contas_bancarias_grid");
		$this->view->render("footer");
		
	}
	
	public function listarCarteirasBancos()  {
		$idBanco = Url::post("id_banco");
		
		if (empty($idBanco)) {
			throw new Exception("Informe o código do banco.");
		}
		
		$clientLayer = new ContasBancariasLayer();
		return $clientLayer->buscarCarteirasBancos($idBanco);
	}
	
	public function cadastrar() {
		
		$clientLayer = new ContasBancariasLayer();
		$bancos = $clientLayer->buscarBancos();
		
		$this->view->render("header");
		$this->view->render("contas_bancarias/contas_bancarias_form", array("bancos" => $bancos));
		$this->view->render("footer");
		
	}
	
	public function editar() {
		
		$idCliente = Url::post("id_cliente");
		
		$bindings = array();

		$clientLayer 			  = new ClientesLayer();
		$bindings["data"] 		  = $this->view->renderJSON($clientLayer->buscarDadosCliente($idCliente));
		$bindings["tipos_pessoa"] = $clientLayer->buscarTiposPessoa();
		$bindings["on_edit"]	  = true;
	
		$this->view->render("header");
		$this->view->render("contas_bancarias/contas_bancarias_form", $bindings);
		$this->view->render("footer");
	
	}
	
	public function deletar() {
		$idCliente = Url::post("id_cliente");
		
		$clientLayer = new ClientesLayer();
		$clientLayer->deletar($idCliente);
	}
	
	public function salvar() {
		
		$postData = Url::post();
		
		$clientLayer = new ClientesLayer();
		$result = $clientLayer->salvar($postData);
		
		echo $this->view->renderJSON($result);		
	}
	
	public function listar() {

		$gridLayer = new GridLayer(Url::post());
		$response = $gridLayer->buscarContasBancarias();
	
		echo $this->view->renderJSON($response);
	
	}
	
}