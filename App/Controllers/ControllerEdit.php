<?php
namespace App\Controllers;

use App\Models\Player;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

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