<?php

class SQLStatament {
	
	const SELECT = 1;
	const UPDATE = 2;
	const INSERT = 3;
	const DELETE = 4;
	
	const ORDER_ASC  = "ASC";
	const ORDER_DESC = "DESC";
	
	const JOIN_TYPE_LEFT  = "LEFT";
	const JOIN_TYPE_RIGHT = "RIGHT";
	const JOIN_TYPE_INNER = "INNER";
	
	const FILTER_AND 		 = "AND";
	const FILTER_OR 		 = "OR";
	const FILTER_IN 		 = "IN";
	const FILTER_IS_NOT_NULL = "IS_NOT_NULL"; 
	const FILTER_IS_NULL 	 = "IS_NULL";
	
	private $table;
	private $type;
	private $filters = array();
	private $joinFilters = array();
	private $joins = array();
	private $order;
	private $fields;
	private $groupBy;
	private $limit;
	private $isCreated = false;
	private $selectColumns = "*";
	
	private $sqlInstruction;
	private $sqlCountInstruction;
	private $bindingNames  = array();
	private $bindingValues = array();
	private $bindings      = array();
	
	
	public function __construct($type = self::SELECT) {
		$this->type = $type;
		$this->order = sprintf("ORDER BY 1 %s", self::ORDER_DESC);
	}
	
	public function setSelectColumns(array $columns) {
		if (!empty($columns)) {
			$this->selectColumns = $columns;
		}
	}
	
	public function setFields(array $fields) {
		$this->fields = $fields;
	}

	public function setTable($table) {
		$this->table = $table;
	}

	public function addWhere($column, $value, $type = self::FILTER_AND, $table = null) {
		$this->filters[] = array(
			"column" => $column,
			"value"  => $value,
			"type"   => $type,
			"table"  => $table
		);
	}
	
	public function addJoin($table, $on, $columns = array(), $type = self::JOIN_TYPE_INNER, $alias = null) {
		$this->joins[] = array(
			"table"   => $table,
			"on"	  => $on,
			"columns" => $columns,
			"type"    => $type,
			"alias"   => $alias
		);
	}
	
	public function setLimit($limitBegin, $limitEnd) {
		$this->limit = array(
			"begin" => $limitBegin,
			"end"   => $limitEnd
		);
	}
	
	public function setOrderBy($field, $way = self::ORDER_ASC, $table = null) {
		$this->order = array(
			"column" => $column,
			"table"  => $table,
			"way"	 => $way
		);
	}
	
	public function setGroupBy($column, $table = null) {
		$this->groupBy = array(
			"column" => $column,
			"table"  => $table
		);
	}
	
	private function addBinding($name, $value) {
		$this->bindingNames[] = $name;
		$this->bindingValues[$name] = $value;
		$this->bindings[":" . $name] = $value;
	}
	
	private function createSQLInstruction() {
		if ($this->isCreated) {
			return true;
		}
		
		$this->isCreated = true;
		
		switch ($this->type) {
			case self::SELECT:
				$this->createSQLSelect();
				break;
			case self::INSERT:
				$this->createSQLInsert();
				break;
			case self::DELETE:
				$this->createSQLDelete();
				break;
			case self::UPDATE:
				$this->createSQLUpdate();
				break;
			default: 
				throw new Exception("The type of sql instruction informed is invalid.");
		}
		
	}
	
	private function createSQLUpdate() {
		
		$processFilter = $this->processFilters();
		$filterSQL = implode(" AND ", $processFilter);
		
		if (count($processFilter) == 0) {
			throw new Exception("No filters provided to perform delete operation.");
		}
		
		$set = array();
		
		foreach ($this->fields as $filterName => $fieldValue) {
			$this->addBinding($filterName, $fieldValue);
			$set[] = sprintf("%s = %s", $filterName, sprintf(":%s", $filterName));
		}

		$sql = sprintf("UPDATE %s SET %s WHERE %s", $this->table, implode(", ", $set), $filterSQL);
		
		$this->sqlInstruction = $sql;
		
	}
	
	private function createSQLDelete() {
		
		$processFilter = $this->processFilters();
		$filterSQL = implode(" AND ", $processFilter);
		
		if (count($processFilter) == 0) {
			throw new Exception("No filters provided to perform delete operation.");
		}

		$sql = sprintf("DELETE FROM %s WHERE %s", $this->table, $filterSQL);
		
		$this->sqlInstruction = $sql;
		
	}
	
	private function createSQLInsert() {
		
		foreach ($this->fields as $keyField => $keyValue) {
			$this->addBinding($keyField, $keyValue);
		}
		
		$columns = implode(", ", $this->getBindingNames());
		$bindings = implode(", ", array_keys($this->getBindings()));
		
		$sql = sprintf("INSERT INTO %s (%s) VALUES(%s)", $this->table, $columns, $bindings);
		
		$this->sqlInstruction = $sql;
		
	}
	
	private function createSQLSelect() {
		
		$this->selectColumns = sprintf("%s.%s", $this->table, $this->selectColumns);
		$selectColumns = array($this->selectColumns);
		
		if (is_array($this->selectColumns) && !empty($this->selectColumns)) {
			$selectColumns = $this->selectColumns;
		}
		
		$joins = array();
		
		if (!empty($this->joins)) {
			
			foreach ($this->joins as $key => $join) {
				
				$alias = $join["table"];
				
				if (!empty($join["alias"])) {
					$alias = $join["alias"];
				}
				
				$join["on"] = str_replace($join["table"], $alias, $join["on"]);
				
				$joins[] = sprintf("%s JOIN %s AS %s ON %s", $join["type"], $join["table"], $alias, $join["on"]);
				
				if (!empty($join["columns"])) {
					foreach ($join["columns"] as $joinColumn) {
						$selectColumns[] = sprintf("%s.%s", $alias, $joinColumn);
					}
				}
			}
		}
		
		$selectColumns = implode(", ", $selectColumns);
		$sql = sprintf("SELECT %s FROM %s ", $selectColumns, $this->table);
		
		if (!empty($joins)) {
			$sql .= implode(" ", $joins);
		}
		
		if (!empty($this->filters)) {
			$filtersSQL = " WHERE 1=1 ";
			
			$filtersSQL .= "AND " . implode(" AND ", $this->processFilters());
			
			$sql .= $filtersSQL;
			
		}
		
		if (!empty($this->groupBy) && is_array($this->groupBy)) {
			$tableGroup = !empty($this->groupBy["table"]) ? $this->groupBy["table"] : $this->table;
			$sql .= sprintf(" GROUP BY %s.%s", $tableGroup, $this->groupBy["column"]);
		}
		
		if (!empty($this->order) && is_array($this->order)) {
			$tableOrder = !empty($this->order["table"]) ? $this->order["table"] : $this->table;
			$sql .= sprintf(" ORDER BY %s.%s %s", $tableOrder, $this->order["column"], $this->order["way"]);
		}
		
		$this->sqlCountInstruction = sprintf("SELECT count(1) as %s FROM (%s) as result", PersistenceLayer::COUNT_ALIAS, $sql);
		
		if (!empty($this->limit) && is_array($this->limit)) {
			$sql .= sprintf(" LIMIT %d, %d", $this->limit["begin"], $this->limit["end"]);
		}
		
		$this->sqlInstruction = $sql;
		
	}
	
	private function processFilters() {
		
		$arr = array();
		
		foreach ($this->filters as $filter) {
		
			switch ($filter["type"]) {
				case self::FILTER_AND:
					$this->addBinding($filter["column"], $filter["value"]);
					$arr[] = sprintf("%s = %s", $filter["column"], ":" . $filter["column"]);
					break;
				case self::FILTER_OR:
					$orFilter = array();
					foreach ($value["column"] as $keyOR => $valueColumn) {
						$columnBind = $filter["column"] . "_or" . $keyOR;
						$this->addBinding($columnBind, $valueColumn);
						$orFilter[] = sprintf("%s = %s", $filter["column"], ":" . $columnBind);
					}
					$arr[] = sprintf("(%s)", implode(" OR ", $orFilter));
					break;
				case self::FILTER_IS_NOT_NULL:
					$arr[] = sprintf("%s IS NOT NULL", $filter["column"]);
					break;
				case self::FILTER_IS_NULL:
					$arr[] = sprintf("%s IS NULL", $filter["column"]);
					break;
				case self::FILTER_IN:
					$inArray = array();
					foreach ($filter["value"] as $inKey => $inValue) {
						$columnBind = $filter["column"] . "_in" . $inKey;
						$inArray[] = ":" . $columnBind;
						$this->addBinding($columnBind, $inValue);
					}
						
					$arr[] = sprintf("%s IN(%s)", $filter["column"], implode(", ", $inArray));
					break;
		
			}
		
		}
		
		return $arr;
		
	}
	
	public function getSQL() {
		$this->createSQLInstruction();
		return $this->sqlInstruction;
	}
	
	public function getSQLCount() {
		$this->createSQLInstruction();
		return $this->sqlCountInstruction;
	}
	
	public function getBindingNames() {
		$this->createSQLInstruction();
		return $this->bindingNames;
	}
	
	public function getBindingValues() {
		$this->createSQLInstruction();
		return $this->bindingValues;
	}

	public function getBindings() {
		$this->createSQLInstruction();
		return $this->bindings;
	}

	public function getType() {
		return $this->type;
	}
	
}