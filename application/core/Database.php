<?php

class Database extends PDO {
	
	private static $instance;
	
	public function __construct() {
		
		$database = Config::getDatabase();
		
		parent::__construct(sprintf("mysql:host=%s;dbname=%s;charset=utf8", $database["host"], $database["db_name"]), $database["user"], $database["password"]);
	
		try {
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
		} catch (PDOException $e) {
			die($e->getMessage());
		}
		
	}
	
	public static function getInstance() {
		
		 if (!isset(self::$instance)) {
			self::$instance = new Database();	
		}
		
		return self::$instance;
		
	}
	
}