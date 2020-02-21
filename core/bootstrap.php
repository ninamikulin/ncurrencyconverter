<?php

//accepts the name of the view argument and just returns the path to the view file (so we don't need to require the entire path inside every method in the Controller classes
function view($name, $data=[])
{
	// extracts the data if there is any - parameters passed to the view
	extract($data);

	// return and require the path to the view
	return require   "../App/views/{$name}.view.php";

}