<?php

$router->get('', 'CurrencyController@home');
$router->get('converted', 'CurrencyController@getResults');

