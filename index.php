<?php

require 'vendor/autoload.php';
use App\Core\Layout;
use App\Core\Routes;
use Dotenv\Dotenv;

session_start();
// Load the .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/** @var string */
const GET = "GET";
const POST = "POST";

// Load the routes
$layout = new Layout();
$method = $_SERVER['REQUEST_METHOD'];
$request = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_FILENAME);
routes($method, $request);

function routes($method, $request)
{
    if ($method == GET) {
        $routes = Routes::getRoutes(GET);
    } elseif ($method == POST) {
        $routes = Routes::getRoutes(POST);
    }

    if (! empty($routes)) {
        foreach ($routes as $route) {
            if (strstr($request, $route['url'])) {
                $controller = $route['controller'];
                $method = $route['method'];
                $object = new $controller();
                $object->$method();
            }
        }
    }
}
