<?php

require_once "vendor/autoload.php";
require_once "User.php";

use Aura\Router\RouterContainer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Relay\Relay;

/*Eloquent-----------------------------------------------*/
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'SuperBase',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

/*psr7------------------------------------------------------*/
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
  $_SERVER,
  $_GET,
  $_POST,
  $_COOKIE,
  $_FILES
);

/*Template engine--------------------------------------------*/
$loader = new \Twig\Loader\FilesystemLoader('.');
$twig = new \Twig\Environment($loader, [
  "debug" => true,
  "cache" => false
]);

/*Router-----------------------------------------------------*/
$router = new RouterContainer();
$map = $router->getMap();
$map->get('SuperBase.list', '/CrudPHP/index.php', function ($request) use ($twig) {
    $users = User::all();
    $response = new HtmlResponse($twig->render('template.twig', [
        'users' => $users
    ]));
    return $response;
});
$map->post('SuperBase.add', '/CrudPHP/add', function ($request) {
  $data = $request->getParsedBody();
  $user = new User();
  $user->name = $data["name"];
  $user->tel = $data["tel"];
  $user->save();
  $response = new RedirectResponse("/CrudPHP/index.php");
  return $response;
});
/*Check------------------*/
$map->get('SuperBase.check', '/CrudPHP/check/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $user = User::find($id);
  $user->done = true;
  $user->save();
  $response = new RedirectResponse("/CrudPHP/index.php");
  return $response;
});
$map->get('SuperBase.uncheck', '/CrudPHP/uncheck/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $user = User::find($id);
  $user->done = false;
  $user->save();
  $response = new RedirectResponse("/CrudPHP/index.php");
  return $response;
});
$map->get('SuperBase.delete', '/CrudPHP/delete/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $user = User::find($id);
  $user->delete();
  $response = new RedirectResponse("/CrudPHP/index.php");
  return $response;
});

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