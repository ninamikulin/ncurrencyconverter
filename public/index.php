<?php
require '../vendor/autoload.php';
require '../core/bootstrap.php';

use App\core\{Router, Requests};

Router::load('../routes.php')
	->direct(Requests::uri(), Requests::method());

