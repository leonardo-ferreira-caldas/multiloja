<?php

class GridLayer extends PersistenceLayer {
	
	private $page;
	private $records;
	private $rowsPerPage;
	private $filters;
	private $order;
	
	public function __construct($data = array()) {
		$this->page		   = !empty($data["page"]) ? $data["page"] : 1;
		$this->rowsPerPage = $data["rowsPerPage"];
		$this->filters     = !empty($data["filters"]) ? $data["filters"] : array();
		$this->order       = !empty($data["order"]) ? $data["order"] : array();
		parent::__construct();
	}
	
	public function buscarClientes() {
		
		$sql = new SQLStatament(SQLStatament::SELECT);
		$sql->setTable("clientes");
		$sql->setLimit($this->getLimitBegin(), $this->getLimitEnd());
		
		foreach ($this->filters as $column => $value) {
			if (!empty($value)) {
				$sql->addWhere($column, $value);
			}
		}
		
		$sql->addJoin("tipos_pessoa", "tipos_pessoa.id_tipo_pessoa = clientes.id_tipo_pessoa", array("des_tipo_pessoa"), SQLStatament::JOIN_TYPE_LEFT);
		
		$resultSet = $this->executeStatament($sql);
		return $this->formatResponseGrid($resultSet);
		
	}
	
	public function buscarContasBancarias() {
		
		$sql = new SQLStatament(SQLStatament::SELECT);
		$sql->setTable("contas_bancarias");
		$sql->setLimit($this->getLimitBegin(), $this->getLimitEnd());
		
		foreach ($this->filters as $column => $value) {
			if (!empty($value)) {
				$sql->addWhere($column, $value);
			}
		}
		
		$sql->addJoin("bancos", "bancos.id_banco = contas_bancarias.id_banco", array("des_banco"), SQLStatament::JOIN_TYPE_LEFT);
		$sql->addJoin("carteiras_bancos", "carteiras_bancos.id_carteira_banco = contas_bancarias.id_carteira_banco", array("des_carteira_banco"), SQLStatament::JOIN_TYPE_LEFT);
		
		$resultSet = $this->executeStatament($sql);
		return $this->formatResponseGrid($resultSet);
		
	}
	
	private function formatResponseGrid(ResultSet $resultSet) {
		return array(
			"records" => $resultSet->getCountRows(),
			"rows"	  => $resultSet->getRows(),
			"page"	  => $this->page
		);
	}
	
	private function getLimitBegin() {
		return ($this->rowsPerPage * $this->page) - $this->rowsPerPage;
	}
	
	private function getLimitEnd() {
		return $this->rowsPerPage * $this->page;
	}
	
}