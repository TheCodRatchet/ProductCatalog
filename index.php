<?php

use App\Middlewares\AuthMiddleware;
use App\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


require_once 'vendor/autoload.php';

session_start();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/products', 'ProductsController@index');
    $r->addRoute('GET', '/products/create', 'ProductsController@create');
    $r->addRoute('POST', '/products', 'ProductsController@store');

    $r->addRoute('POST', '/products/{id}/delete', 'ProductsController@delete');
    $r->addRoute('GET', '/products/{id}/delete', 'ProductsController@deleteForm');

    $r->addRoute('POST', '/products/{id}/edit', 'ProductsController@edit');
    $r->addRoute('GET', '/products/{id}/edit', 'ProductsController@editForm');

    $r->addRoute('GET', '/tags', 'TagsController@index');
    $r->addRoute('GET', '/tags/create', 'TagsController@create');
    $r->addRoute('POST', '/tags', 'TagsController@store');

    $r->addRoute('POST', '/tags/{id}/delete', 'TagsController@delete');
    $r->addRoute('GET', '/tags/{id}/delete', 'TagsController@deleteForm');

    $r->addRoute('POST', '/tags/{id}/edit', 'TagsController@edit');
    $r->addRoute('GET', '/tags/{id}/edit', 'TagsController@editForm');

    $r->addRoute('GET', '/users', 'UsersController@index');

    $r->addRoute('GET', '/register', 'AuthController@showRegisterForm');
    $r->addRoute('POST', '/register', 'AuthController@register');

    $r->addRoute('GET', '/', 'AuthController@showLoginForm');
    $r->addRoute('POST', '/login', 'AuthController@login');

    $r->addRoute('POST', '/logout', 'AuthController@logout');
});

function base_path(): string
{
    return __DIR__;
}

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$loader = new FilesystemLoader(base_path() . '/app/Views');
$templateEngine = new Environment($loader, []);
$templateEngine->addGlobal('session', $_SESSION);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $middlewares = [
            'ProductsController@index' => [
                AuthMiddleware::class
            ],
            'ProductsController@create' => [
                AuthMiddleware::class
            ],
            'ProductsController@store' => [
                AuthMiddleware::class
            ],
            'ProductsController@delete' => [
                AuthMiddleware::class
            ],
            'ProductsController@deleteForm' => [
                AuthMiddleware::class
            ],
            'ProductsController@edit' => [
                AuthMiddleware::class
            ],
            'ProductsController@editForm' => [
                AuthMiddleware::class
            ],
            'TagsController@index' => [
                AuthMiddleware::class
            ],
            'TagsController@create' => [
                AuthMiddleware::class
            ],
            'TagsController@store' => [
                AuthMiddleware::class
            ],
            'TagsController@delete' => [
                AuthMiddleware::class
            ],
            'TagsController@deleteForm' => [
                AuthMiddleware::class
            ],
            'TagsController@edit' => [
                AuthMiddleware::class
            ],
            'TagsController@editForm' => [
                AuthMiddleware::class
            ],
            'UsersController@index' => [
                AuthMiddleware::class
            ]
        ];

        if (array_key_exists($handler, $middlewares)) {
            foreach ($middlewares[$handler] as $middleware) {
                (new $middleware)->handle();
            }
        }


        [$controller, $method] = explode('@', $handler);
        $controller = "App\Controllers\\" . $controller;
        $controller = new $controller();
        $response = $controller->$method($vars);

        if ($response instanceof View) {
            echo $templateEngine->render($response->getTemplate(), $response->getArguments());
        }
        break;
}