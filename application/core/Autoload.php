<?php

class Autoload {
	
	public static function init() {
		
		spl_autoload_extensions(".php");
		spl_autoload_register(array('Autoload', 'load'));

		foreach (Config::getAutoloadPaths() as $path) {
			set_include_path(get_include_path() . PATH_SEPARATOR . Config::getAppPath() . DIRECTORY_SEPARATOR . $path);
		}
		
	}
	
	public static function load($className) {
		foreach (Config::getAutoloadPaths() as $path) {
			$fileDir = Config::getAppPath() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $className . '.php';
			if(file_exists($fileDir)) {
				require_once $fileDir;
			}
		}
	}
	
}