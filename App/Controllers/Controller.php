<?php
namespace App\Controllers;

require_once "../App/Models/Model.php";

use Laminas\Diactoros\ServerRequestFactory;
use Aura\Router\RouterContainer;

/*psr7------------------------------------------------------*/
$request = ServerRequestFactory::fromGlobals(
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

$router = new RouterContainer();
$map = $router->getMap();