<?php

class PersistenceLayer {
	
	const COUNT_ALIAS = "qtd_rows";
	
	private static $transactionsCount = 0;
	
	private $database;
	private $newTransaction;
	private $isTransactioned = false;
	
	public function __construct($newTransaction = false) {
		$this->$newTransaction = $newTransaction;
		
		if ($newTransaction) {
			$this->database = new Database();
		} else {
			$this->database = Database::getInstance();
		}
	}
	
	public function getConnection() {
		return $this->database;
	}
	
	public function commit() {
		
		if (!$this->isTransactioned) {
			throw new Exception("No transaction active was found to commit.");
		}
		
		if ($this->newTransaction) {
			$this->database->commit();
			return null;
		}
		
		self::$transactionsCount--;
		
		if (self::$transactionsCount == 0) {
			$this->database->commit();
		} else if (self::$transactionsCount < 0) {
			throw new Exception("No transaction active was found to commit.");
		}
		
	}
	
	public function rollback() {
		
		if (!$this->isTransactioned) {
			throw new Exception("No transaction active was found to rollback.");
		}
		
		if (!$this->newTransaction) {	
			self::$transactionsCount = 0;
		}
		
		$this->database->rollBack();
	}
	
	public function beginTransaction() {
		
		if ($this->isTransactioned) {
			throw new Exception("Cannot begin transaction twice.");
		}
		
		if (!$this->newTransaction) {
			self::$transactionsCount++;
		}
		
		$this->isTransactioned = true;
		$this->database->beginTransaction();
	}
	
	public function insert($table, array $data) {
		
		if (empty($data)) {
			throw new Exception("Provide the information to perform the insert.");
		}
		
		$stmt = new SQLStatament(SQLStatament::INSERT);
		$stmt->setTable($table);
		$stmt->setFields($data);
		
		return $this->executeStatament($stmt);
		
	}
	
	public function delete($table, array $example) {
		
		if (empty($example)) {
			throw new Exception("Provide the information to perform the delete.");
		}
		
		$stmt = new SQLStatament(SQLStatament::DELETE);
		$stmt->setTable($table);
		
		foreach ($example as $columnName => $columnValue) {
			$stmt->addWhere($columnName, $columnValue);
		}
		
		return $this->executeStatament($stmt);
		
	}
	
	public function update($table, array $data, array $example) {
		
		$stmt = new SQLStatament(SQLStatament::UPDATE);
		$stmt->setTable($table);
		$stmt->setFields($data);
		
		foreach ($example as $columnName => $columnValue) {
			$stmt->addWhere($columnName, $columnValue);
		}
		
		return $this->executeStatament($stmt);
		
	}
	
	public function findOne($table, array $example) {
		
		$rows = $this->findByExample($table, $example);
		
		if (count($rows) > 1) {
			throw new Exception("More than one result was found.");
		} else if (count($rows) == 0) {
			return array();
		}
		
		return $rows[0];
		
	}
	
	public function findByExample($table, array $example, array $fields = array()) {
		
		if (empty($example)) {
			throw new Exception("Provide the example for the search.");
		}
		
		$stmt = new SQLStatament(SQLStatament::SELECT);
		$stmt->setTable($table);
		$stmt->setSelectColumns($fields);
		
		foreach ($example as $columnName => $columnValue) {
			$stmt->addWhere($columnName, $columnValue);
		}
		
		$resultSet = $this->executeStatament($stmt);
		
		return $resultSet->getRows();
		
	}
	
	public function findAll($table, $fields = array()) {
		
		$stmt = new SQLStatament(SQLStatament::SELECT);
		$stmt->setTable($table);
		$stmt->setSelectColumns($fields);

		$resultSet = $this->executeStatament($stmt);
		
		return $resultSet->getRows();
		
	}
	
	public function executeStatament(SQLStatament $stmt, $fetchType = PDO::FETCH_ASSOC) {
		
		$sql  = $stmt->getSQL();
		$data = $stmt->getBindingValues();
		
		$prepared = $this->database->prepare($sql);
		
		$prepared->execute($data);
		
		switch ($stmt->getType()) {
			case SQLStatament::SELECT:
				
				$fetch 		 = $prepared->fetchAll(PDO::FETCH_ASSOC);
				$sqlCount	 = $stmt->getSQLCount();
				
				$preparedCount = $this->database->prepare($sqlCount);
				$preparedCount->execute($data);
				
				$countFetch = $preparedCount->fetch(PDO::FETCH_ASSOC);
				
				return new ResultSet($sql, $data, null, $fetch, $sqlCount, $countFetch[PersistenceLayer::COUNT_ALIAS]);
				
			
			case SQLStatament::INSERT:
				return new ResultSet($sql, $data, $this->database->lastInsertId());
				
			case SQLStatament::UPDATE:
			case SQLStatament::DELETE:
				return new ResultSet($sql, $data);
				
			default:
				throw new Exception("The type of sql instruction informed is invalid.");
		}
		
	}
	
}