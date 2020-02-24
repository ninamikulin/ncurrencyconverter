<?php

namespace App\core;

class Requests
{
	// gets the url, trims it to uri and returns it
	public static function uri()	
	{		
		// trims the / at beginning and end of url, accesses the request uri in the global variable
		// parses the url - returns only the path (without the query parameters etc.)
		return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); 
	}

	// returns the request method by accessing the global variable 
	public static function method()
	{	
		return $_SERVER['REQUEST_METHOD'];		
	}
}