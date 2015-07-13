<?php

class ServiceAction extends Action {
	
	public function json($serviceName, $serviceMethod) {
		
		try {
			$response = array(
				"success" => true,
				"response" => $this->call($serviceName, $serviceMethod)
			);
		} catch (Exception $e) {
			$response = array(
				"success" => false,
				"response" => $e->getTraceAsString()
			);
		}
		
		echo $this->view->renderJSON($response);
	}
	
	private function call($serviceName, $serviceMethod) {
		
		$params = Action::post("args");
		
		if (empty($params)) {
			$params = array();
		}
		
		if (!class_exists($serviceName)) {
			HttpResponse::notFound();
		};
		
		$service = new $serviceName;
		
		if (!method_exists($service, $serviceMethod)) {
			HttpResponse::notFound();
		}
		
		$reflection = new ReflectionMethod($service, $serviceMethod);
		
		$isForbidden = !$reflection->isPublic()
						|| !property_exists($service, 'remote')
						|| !is_array($service->remote)
						|| !in_array($serviceMethod, $service->remote);
		
		if ($isForbidden) {
			HttpResponse::forbidden();
		}
		
		$target = array($service, $serviceMethod);
		
		if (!is_callable($target)) {
			HttpResponse::notFound();
		}
		
		return call_user_func_array($target, $params);
		
	}
	
}