<?php

namespace App\core;

class Router

{
	protected $routes = [
		// array for GET routes
		'GET' => [],

		// array for POST routes
		'POST' => []
	];

	public static function load($file)
	{
		//initiates a new instance of the router
		$router = new static;
		//require the  file from which to load the routes
		require $file;
		
		//returns the router
		return $router;
	}

	// if get method is called, the $uri=>$controller pair is saved in the $routes['get'] property
	public function get($uri, $controller)
	{	
		// sets the routes array key=>value pairs
		$this->routes['GET'][$uri] = $controller;
		
	}

	// if get method is called, the $uri=>$controller pair is saved in the $routes['post'] property
	public function post($uri, $controller)
	{	
		// sets the routes array key=>value pairs
		$this->routes['POST'][$uri] = $controller;
	}


	public function direct($uri, $requestType)
	{	
		// only look inside the appropriate property - $routes['get'] / $routes['post'] - depending on the $requestType passed in (Requests.php class determines the method used and passes it to the Router.php)
		if (array_key_exists($uri, $this->routes[$requestType]))
			{	
				// explodes the controller passed in and calls the callAction method (passing in the parameters e.g. callAction('CurrencyController', '')) 
				return $this->callMethod(
					
					...explode('@', $this->routes[$requestType][$uri])
				);
			}
		throw new \Exception('No route defined for this URI.');

	}

	//instantiates a new controller and call the method based on the value associated with the uri in the routes file
	protected function callMethod($controller, $method)
	{
		// creates new controller instance
		$controller = "App\\controllers\\{$controller}"; 
		$controller = new $controller;

		// checks if method exists
		if (! method_exists($controller, $method))
		{
			throw new \Exception(" does not respond to the  action.");	
		}

		// calls the method
		return $controller->$method();
	}

}