<?php

require_once "../vendor/autoload.php";
// require_once "../views/template.twig";


require_once "../App/Models/Player.php";
use App\Models\Player;

// require_once "../views/template.twig";
// require_once "template.twig";


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
    'database'  => 'torneo',
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
$loader = new \Twig\Loader\FilesystemLoader('../views');
$twig = new \Twig\Environment($loader, [
  "debug" => true,
  "cache" => false
]);

/*Router-----------------------------------------------------*/
$router = new RouterContainer();
$map = $router->getMap();
$map->get('torneo.list', '/CrudPHP/index.php', function ($request) use ($twig) {
    $players = Player::all();
    $response = new HtmlResponse($twig->render('template.twig', [
        'players' => $players
    ]));
    return $response;
});
$map->post('torneo.add', '/CrudPHP/add', function ($request) {
  $data = $request->getParsedBody();
  $player = new Player();
  $player->name = $data["name"];
  $player->tel = $data["tel"];
  $player->save();
  $response = new RedirectResponse("/CrudPHP/index.php");
  return $response;
});
/*Check------------------*/
$map->get('torneo.check', '/CrudPHP/check/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $player = Player::find($id);
  $player->done = true;
  $player->save();
  $response = new RedirectResponse("/CrudPHP/index.php");
  return $response;
});
$map->get('torneo.uncheck', '/CrudPHP/uncheck/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $player = Player::find($id);
  $player->done = false;
  $player->save();
  $response = new RedirectResponse("/CrudPHP/index.php");
  return $response;
});

/*-----------------EndCheck*/
$map->get('torneo.delete', '/CrudPHP/delete/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $player = Player::find($id);
  $player->delete();
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