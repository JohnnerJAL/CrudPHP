<?php
namespace App\Controllers;

use App\Models\Player;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

$map->get('torneo.list', '/CrudPHP/', function ($request) use ($twig) {
  $players = Player::all();
  $response = new HtmlResponse($twig->render('index.twig', [
    'players' => $players
  ]));
  return $response;
});

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