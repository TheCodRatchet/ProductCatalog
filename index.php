<?php

use App\Container;
use App\Middlewares\AuthMiddleware;
use App\Repositories\Products\MysqlProductsRepository;
use App\Repositories\Products\ProductsRepository;
use App\Repositories\Tags\MysqlTagsRepository;
use App\Repositories\Tags\TagsRepository;
use App\Repositories\Users\MysqlUsersRepository;
use App\Repositories\Users\UsersRepository;
use App\View;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';

session_start();

$container = new Container([
    TagsRepository::class => new MysqlTagsRepository(),
    ProductsRepository::class => new MysqlProductsRepository(),
    UsersRepository::class => new MysqlUsersRepository()
]);

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
        $middlewares = [];

        [$controller, $method] = explode('@', $handler);

        if ($controller != 'AuthController') {
            $middlewares = [
                $handler => [
                    AuthMiddleware::class
                ]
            ];
        }

        if (array_key_exists($handler, $middlewares)) {
            foreach ($middlewares[$handler] as $middleware) {
                (new $middleware)->handle();
            }
        }

        $controller = "App\Controllers\\" . $controller;
        $controller = new $controller($container);
        $response = $controller->$method($vars);

        if ($response instanceof View) {
            try {
                echo $templateEngine->render($response->getTemplate(), $response->getArguments());
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
            }
        }
        break;
}