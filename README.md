# Currency Converter

1. [About](#about)
2. [Routing setup](#routing-setup)   
  i. [index file](#index-file)   
  ii. [Router](#router)  
  iii. [routes](#routes)  
  iv. [Requests](#requests)  
  v. [bootstrap](#bootstrap) 
3. [Controllers](#controllers)  
  i. [Currency Controller](#currency-controller)
  

## About

Currency converter is a PHP project that allows currency conversion using [Foreign exchange rates api](https://exchangeratesapi.io/). 
 
## Routing setup 

### Index file

<details><summary>Logic</summary>
  
- index.php is the entry point into the application, responsible for setting up and bootstrapping the project
- calls the `load` method on the router
- the `load` method loads `routes.php` file
- then it calls the `direct` method on the router 
- the `direct` method parameters are Requests' `get` and `post` methods that return the URI and the method
- the `direct` method returns the controller and the method
- bootstrap file loads the view specified in the controller
</details>

```php
<?php
require '../vendor/autoload.php';
require '../core/bootstrap.php';

use App\core\{Router, Requests};

Router::load('../routes.php')
	->direct(Requests::uri(), Requests::method());
```
  
### Router

<details><summary>Setting attribues</summary>
  
```php
protected $routes = [
  // array for get routes
  'GET' => [],
  //array for post routes
  'POST' => []
];
```
</details>
<details><summary>Methods</summary>
<details><summary>load</summary>
  
- new router instance
- requires the file passed in (in this case it's `routes.php` file)
- returns the router
  
```php
public static function load($file)
{
  //initiates a new instance of the router
  $router = new static;
  //require the file from which to load the routes
  require $file;

  //returns the router
  return $router;
}
```
</details>
<details><summary>get</summary>
  
- if get method is called, the $uri=>$controller pair is saved in the $routes['get'] property
public function get($uri, $controller)
  
```php
// if get method is called, the $uri=>$controller pair is saved in the $routes['get'] property
public function get($uri, $controller)
{	
  // sets the routes array key=>value pairs
  $this->routes['GET'][$uri] = $controller;
}
```
</details>
<details><summary>post</summary>
  
- if post method is called, the $uri=>$controller pair is saved in the $routes['post'] property
public function get($uri, $controller)
  
```php
// if get method is called, the $uri=>$controller pair is saved in the $routes['post'] property
public function get($uri, $controller)
{	
  // sets the routes array key=>value pairs
  $this->routes['POST'][$uri] = $controller;
}
```
</details>
<details><summary>direct</summary>
  
- looks inside the appropriate property - $routes['get'] / $routes['post'] - depending on the $requestType passed in (Requests.php class determines the method used and passes it to the Router.php)
- explodes the controller passed in and calls the callAction method (passing in the parameters e.g. callAction('CurrencyController', ''))
- throws error if no route found
  
```php
public function direct($uri, $requestType)
{	
  // only look inside the appropriate property - $routes['get'] / $routes['post'] - depending on the $requestType passed in (Requests.php class determines the method used and passes it to the Router.php)
  if (array_key_exists($uri, $this->routes[$requestType]))
    {	
      // explodes the controller passed in and calls the callAction method (passing in the parameters e.g. callAction('CurrencyController', '')) 
      return $this->callAction(
        ...explode('@', $this->routes[$requestType][$uri])
      );
    }
  // throws error if no route found
  throw new \Exception('No route defined for this URI.');
}
```
</details>
<details><summary>callMethod</summary>
  
- instantiates a new controller and calls the method based on the value associated with the uri in the routes file
- returns the call to the method
  
```php
//instantiates a new controller and call the method based on the value associated with the uri in the routes file
protected function callMethod($controller, $method)
{
  // creates new controller instance
  $controller = "App\\controllers\\{$controller}"; 
  $controller = new $controller;

  // checks if method exists
  if (! method_exists($controller, $method))
  {
    throw new \Exception("The controller does not respond to the action.");	
  }

  // calls the method
  return $controller->$method();
}
```
</details>
</details>

### Routes

<details><summary>defines the uri, controller and action to load</summary>

```php
<?php
$router->get('','CurrencyController@home');
$router->get('converted', 'CurrencyController@getResults');
```
</details>

### Requests

- returns trimmed uri and request method

<details><summary>Methods</summary>
<details><summary>uri</summary>
  
- returns the trimmed uri

```php
// returns the trimmed uri
public static function uri()	
{		
	// trims the / at beginning and end of url, accesses the request uri in the global variable
	// parses the url - returns only the path (without the query parameters etc.)
	return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); 
}
```
</details>

<details><summary>method</summary>
  
- returns the request method

```php
// returns the request method by accessing the global variable 
public static function method()
{	
	return $_SERVER['REQUEST_METHOD'];		
}
```
</details>
</details>

### bootstrap 

- accepts the name of the view argument and just returns the path to the view file (so we don't need to require the entire path inside every method in the Controller classes

```php
<?php

//accepts the name of the view argument and just returns the path to the view file (so we don't need to require the entire path inside every method in the Controller classes
function view($name, $data=[])
{
  // extracts the data if there is any - parameters passed to the view
  extract($data);

  // return and require the path to the view
  return require "../App/views/{$name}.view.php";
}
```
## Controllers

### Currency controller

- returns the home view with a list of currencies 
- returns the show view with the converted amount

<details><summary>Methods</summary>

<details><summary>home</summary>

```php
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
```
</details>
<details><summary>getResults</summary>

```php
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
  return view('show', ['converted' => $converted]);
}
```
</details>
<details><summary>make request</summary>

```php
// makes request helper method
public function makeRequest($method, $url, $query=[])
{
  // instantiating a new guzzle client object
  $client = new \GuzzleHttp\Client;

  // making the request
  $response = $client->request($method, $url, $query);

  return $response;
}
```
</details>
</details
