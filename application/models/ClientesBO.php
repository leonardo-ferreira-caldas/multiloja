<?php

class ClientesBO extends PersistenceLayer {
	
	public $remote = array("buscarTiposPessoa", "salvar", "buscarDadosCliente");

	public function buscarTiposPessoa() {
		return $this->findAll("tipos_pessoa");
	}
	
	public function salvar($dadosCliente) {
		
		$idCliente = $dadosCliente["id_cliente"];
		unset($dadosCliente["id_cliente"]);
		
		$dadosCliente["documento"] = str_replace(array("-", ".", "/"), "", $dadosCliente["documento"]);
		
		if (!empty($idCliente)) {
			$this->update("clientes", $dadosCliente, array("id_cliente" => $idCliente));
			return $dadosCliente;
		}
		
		$resultSet = $this->insert("clientes", $dadosCliente);
		$dadosCliente["id_cliente"] = $resultSet->getLastInsertId();
		
		return $dadosCliente;
	}
	
	public function buscarDadosCliente($idCliente) {
		return $this->findOne("clientes", array("id_cliente" => $idCliente));
	}
	
	public function deletar($idCliente) {
		
		if (empty($idCliente)) {
			throw new Exception("Informe o código de cliente.");
		}
		
		$this->delete("clientes", array("id_cliente" => $idCliente));
		
	}
	
}