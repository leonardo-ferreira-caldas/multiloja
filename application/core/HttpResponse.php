<?php

class HttpResponse {
	
	public static function notFound() {
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	
	public static function forbidden() {
		header('HTTP/1.0 403 Forbidden');
		exit;
	}
	
	public static function unAuthorized() {
		header("HTTP/1.1 403 Unauthorized" );
		exit;
	}
	
}