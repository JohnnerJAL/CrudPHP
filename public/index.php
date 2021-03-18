<?php

require_once "../vendor/autoload.php";

require_once "../App/Models/Player.php";
use App\Models\Player;

use Aura\Router\RouterContainer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Relay\Relay;

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

$map->get('torneo.list', '/CrudPHP/', function ($request) use ($twig) {
  $players = Player::all();
  $response = new HtmlResponse($twig->render('index.twig', [
    'players' => $players
  ]));
  return $response;
});
/*edit---------*/
$map->get('torneo.row_edit', '/CrudPHP/edit/{id}', function ($request) use ($twig) {
  $id = $request->getAttribute("id");
  $rows = Player::where("id", $id)
    ->get();
  $response = new HtmlResponse($twig->render('edit.twig', [
    'rows' => $rows
  ]));
  return $response;
});

$map->post('torneo.row_edit_post', '/CrudPHP/edited/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $data = $request->getParsedBody();
  $player = Player::find($id);
  $player->match_no = $data["match_no"];
  $player->team_id = $data["team_id"];
  $player->player_id = $data["player_id"];
  $player->in_out = $data["in_out"];
  $player->time_in_out = $data["time_in_out"];
  $player->play_schedule = $data["play_schedule"];
  $player->play_half = $data["play_half"];
  $player->save();
  $response = new RedirectResponse("/CrudPHP/edit/$id");
  return $response;
});
/*-------endEdit*/

$map->post('torneo.add', '/CrudPHP/add', function ($request) {
  $data = $request->getParsedBody();
  $player = new Player();
  $player->match_no = $data["match_no"];
  $player->team_id = $data["team_id"];
  $player->player_id = $data["player_id"];
  $player->in_out = $data["in_out"];
  $player->time_in_out = $data["time_in_out"];
  $player->play_schedule = $data["play_schedule"];
  $player->play_half = $data["play_half"];
  $player->save();
  $response = new RedirectResponse("/CrudPHP/");
  return $response;
});
/*Check------------------*/
$map->get('torneo.check', '/CrudPHP/check/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $player = Player::find($id);
  $player->done = true;
  $player->save();
  $response = new RedirectResponse("/CrudPHP/");
  return $response;
});
$map->get('torneo.uncheck', '/CrudPHP/uncheck/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $player = Player::find($id);
  $player->done = false;
  $player->save();
  $response = new RedirectResponse("/CrudPHP/");
  return $response;
});
/*-----------------EndCheck*/
$map->get('torneo.delete', '/CrudPHP/delete/{id}', function ($request) {
  $id = $request->getAttribute("id");
  $player = Player::find($id);
  $player->delete();
  $response = new RedirectResponse("/CrudPHP/");
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