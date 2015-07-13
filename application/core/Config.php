<?php

class Config {
	
	private static $routes = array();
	private static $appPath;
	private static $basePath;
	private static $baseUrl;
	private static $database;
	private static $defaultController;
	private static $autoloadPaths;
	
	public static function load($configFile) {

		$file = file_get_contents($configFile);
		$config = json_decode($file, true);
		
		self::$appPath 		 	 = $config["app_path"];
		self::$autoloadPaths 	 = $config["autoload_paths"];
		self::$defaultController = $config["default_controller"];
		self::$basePath 		 = $config["base_path"];
		self::$baseUrl	 		 = $config["base_url"];
		self::$database	 		 = $config["database"];
		
	}

	public static function getDatabase() {
		return self::$database;
	}
	
	public static function getAppPath() {
		return self::$appPath;
	}
	
	public static function getBasePath() {
		return self::$basePath;
	}
	
	public static function getBaseUrl() {
		return self::$baseUrl;
	}
	
	public static function getDefaultController() {
		return self::$defaultController;
	}
	
	public static function getAutoloadPaths() {
		return self::$autoloadPaths;
	}
	
}