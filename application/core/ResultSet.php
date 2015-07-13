<?php

class ResultSet {
	
	private $sql;
	private $fetch;
	private $sqlCount;
	private $rowsCount;
	private $bindings;
	private $lastInsertId;
	
	public function __construct($sql, $bindings, $lastInsertId = null, $fetch = array(), $sqlCount = null, $rowsCount = null) {
		$this->fetch 		= $fetch;
		$this->sql 			= $sql;
		$this->sqlCount 	= $sqlCount;
		$this->rowsCount 	= $rowsCount;
		$this->bindings 	= $bindings;
		$this->lastInsertId = $lastInsertId;
	}
	
	public function getRows() {
		return $this->fetch;
	}
	
	public function getSQL() {
		return $this->sql;
	}
	
	public function getSQLCount() {
		return $this->sqlCount;
	}
	
	public function getCountRows() {
		return $this->rowsCount;
	}
	
	public function getBindings() {
		return $this->bindings;
	}
	
	public function getLastInsertId() {
		return $this->lastInsertId;
	}
	
}