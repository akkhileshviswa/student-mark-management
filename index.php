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
$request = slugToCamelCase(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
routes($method, $request);

function routes($method, $request)
{
    if ($method == GET) {
        $routes = Routes::getRoutes(GET);
    } elseif ($method == POST) {
        $routes = Routes::getRoutes(POST);
    }

    $found = false;
    if (! empty($routes)) {
        foreach ($routes as $route) {
            if ($request === $route['url']) {
                $controller = $route['controller'];
                $method = $route['method'];
                $object = new $controller();
                $object->$method();
                $found = true;

                break;
            }
        }
    }

    if (! $found) {
        Routes::load('404Page');
    }
}

/**
 * Convert slug to camelCase for routing purposes.
 */
function slugToCamelCase($_slug)
{
    return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $_slug))));
}
