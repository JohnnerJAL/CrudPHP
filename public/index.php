<?php

require_once "../vendor/autoload.php";

require_once "../App/Controllers/Controller.php";
require_once "../App/Controllers/ControllerIndex.php";
require_once "../App/Controllers/ControllerEdit.php";

use Relay\Relay;

$relay = new Relay([
  new Middlewares\AuraRouter($router),
  new Middlewares\RequestHandler()
]);

$response = $relay->handle($request);

foreach ($response->getHeaders() as $name => $values) {
  foreach ($values as $value) {
    header(sprintf('%s: %s', $name, $value), false);
  }
}

echo $response->getBody();