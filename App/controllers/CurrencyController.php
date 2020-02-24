<?php

namespace App\controllers;

use App\core\App;
use GuzzleHttp\Client;

class CurrencyController 
{
	// returns the home view with a list of currencies
	public function home()
	{
		// making the request to get the list of currencies
		$response = $this->makeRequest('GET', 'https://api.exchangeratesapi.io/latest');

		/* Returning all the currencies as an associative array
		- accessing the body of the response
		- decoding the json to a php object
		- accessing the rates property where the array with the symbols lives 
		- turning from obj to array*/
		$currencies = get_object_vars(json_decode($response->getBody())->rates);

		// adding the EUR value to the array
		$currencies['EUR'] = '';

		// sorting array by keys
		ksort($currencies);

		// getting the array keys
		$currencies = array_keys($currencies);

		view('home', ['currencies' => $currencies]);
	}

	//returns the show view with the converted amount
	public function getResults()
	{
		// checking if there are parameters,  if the value is present
		if (empty($_GET)) {

			header('Location: http://ncurrencycalc.test/currency-input.php');
			die();

		} elseif (empty($_GET['value'])) {

			echo 'Please choose a value.';
			die();
		}

		/*
		Making a get request to the api endpoint with guzzle client:
		- contcatenating the date passed in with the api endpoint - the date is a part of the endpoint, not a parameter
		- passing parameters via the query parameter
		*/
		$response = $this->makeRequest('GET', 'https://api.exchangeratesapi.io/latest', [

			'query' => [
			'base' => $_GET['from'],
			'symbols' => $_GET['to'],

			]
		]);

		// retrieving the json body and storing it into a variable
		$jsonBody = json_decode($response->getBody());

		// checking if the request was successful by accessing the response status code
		if ($response->getStatusCode() !== 200) {

			echo 'There has been an error: <br> Please try again.' ;
			die();
		}

		/* calculating the converted amount - number format function casts the values to int and rounds the numbers to 2 decimals 
		mind the curly braces that make possible to access methods of an object from a class dynamically*/
		$converted = number_format($_GET['value'] * $jsonBody->rates->{$_GET['to']}, 2);

		view('show', ['converted' => $converted]);
	}

	public function makeRequest($method, $url, $query=[])
	{
		// instantiating a new guzzle client object
		$client = new \GuzzleHttp\Client;

		// making the request
		$response = $client->request($method, $url, $query);

		return $response;
	}
}