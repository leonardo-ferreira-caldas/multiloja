<?php

class ContasBancariasLayer extends PersistenceLayer {

	public function buscarBancos() {
		return $this->findAll("bancos");
	}

	public function buscarCarteirasBancos($idBanco) {
		return $this->findByExample("carteiras_bancos", array("id_banco" => $idBanco));
	}
	
	public function salvar($dadosContaBancaria) {
		
		$idContaBancaria = $dadosContaBancaria["id_conta_bancaria"];
		unset($dadosContaBancaria["id_conta_bancaria"]);
		
		if (!empty($idContaBancaria)) {
			$this->update("contas_bancarias", $dadosContaBancaria, array("id_cliente" => $idContaBancaria));
			return $dadosContaBancaria;
		}
		
		$resultSet = $this->insert("contas_bancarias", $dadosContaBancaria);
		$dadosContaBancaria["id_conta_bancaria"] = $resultSet->getLastInsertId();
		return $dadosContaBancaria;
	}
	
	public function buscarDadosCliente($idContaBancaria) {
		return $this->findOne("contas_bancarias", array("id_conta_bancaria" => $idContaBancaria));
	}
	
	public function deletar($idContaBancaria) {
		
		if (empty($idCliente)) {
			throw new Exception("Informe o código de cliente.");
		}
		
		$this->delete("contas_bancarias", array("id_conta_bancaria" => $idContaBancaria));
		
	}
	
}