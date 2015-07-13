<?php

class Router {

	protected $basePath;
	protected $tokens;

	/**
	  * Create router in one call from config.
	  *
	  * @param array $routes
	  * @param string $basePath
	  * @param array $matchTypes
	  */
	public function __construct($basePath = '') {
		$this->setBasePath($basePath);
	}
	
	/**
	 * Set the base path.
	 * Useful if you are running your application from a subdirectory.
	 */
	public function setBasePath($basePath) {
		$this->basePath = $basePath;
	}

	public function read() {
		$uri = ltrim($_SERVER['REQUEST_URI'], "/");
		$this->tokens = explode("/", $uri);
	}
	
	public function getNextToken() {
		return array_shift($this->tokens);
	}
	
	public function normalizeController($action) {
		
		$action = explode("-", $action);
		
		foreach ($action as &$each) {
			$each = ucfirst($each);
		}
		
		return implode("", $action);
		
	}
	
	public static function call() {
		
		$router = new Router(Config::getBasePath());	
		$router->read();
		
		$controller = $router->getNextToken();
		
		if (empty($controller)) {
			$controller = Config::getDefaultController();
		} else {
			$controller = $router->normalizeController($controller);
		}
		
		$controller .= "Action";
		
		if (!class_exists($controller)) {
			HttpResponse::notFound();
		};
		
		$controller = new $controller;
		
		$method = $router->getNextToken();
		
		if (empty($method)) {
			$method = "index";
		}
		
		if (!method_exists($controller, $method)) {
			HttpResponse::notFound();
		}
		
		$reflection = new ReflectionMethod($controller, $method);

		if (!$reflection->isPublic()) {
			HttpResponse::forbidden();
		}

		$arguments = array();
		
		while($param = $router->getNextToken()) {
			$arguments[] = $param;
		}

		$target = array(new $controller, $method);
			
		if (!is_callable($target)) {
			HttpResponse::notFound();
		}
		
		call_user_func_array($target, $arguments);
		
	}
	
}
