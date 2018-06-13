<?php

use Aura\SqlQuery\QueryFactory;
use DI\ContainerBuilder;
use Delight\Auth\Auth;
use FastRoute\RouteCollector;
use League\Plates\Engine;

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions([
    Engine::class    =>  function() {
        return new Engine('../app/Views');
    },

    Swift_Mailer::class => function() {
        $transport = (new Swift_SmtpTransport(
            config('mail.smtp'),
            config('mail.port'),
            config('mail.encryption')
        ))
            ->setUsername(config('mail.email'))
            ->setPassword(config('mail.password'));
        return new Swift_Mailer($transport);
    },

    PDO::class => function() {
        $driver = config('database.driver');
        $host = config('database.host');
        $database_name = config('database.database_name');
        $username = config('database.username');
        $password = config('database.password');

        return new PDO("$driver:host=$host;dbname=$database_name", $username, $password);
    },

    Delight\Auth\Auth::class   =>  function($container) {
        return new Auth($container->get('PDO'));
    },

    QueryFactory::class  =>  function() {
        return new QueryFactory('mysql');
    }
]);

$container = $containerBuilder->build();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $route) {
    $route->get('/', ['App\Controllers\HomeController', 'index']);
    $route->get('/category/{id:\d+}', ['App\Controllers\HomeController', 'category']);
    $route->get('/user/{id:\d+}', ['App\Controllers\HomeController', 'user']);
    $route->get('/image/{id:\d+}', ['App\Controllers\HomeController', 'image']);


    $route->get('/register', ['App\Controllers\RegisterController', 'showForm']);
    $route->get('/login', ['App\Controllers\LoginController', 'showForm']);
    $route->get('/password-recovery', ['App\Controllers\ResetPasswordController', 'showForm']);
    $route->post('/password-recovery', ['App\Controllers\ResetPasswordController', 'recovery']);
    $route->get('/password-recovery/form', ['App\Controllers\ResetPasswordController', 'showSetForm']);
    $route->post('/password-recovery/change', ['App\Controllers\ResetPasswordController', 'change']);
    $route->get('/email-verification', ['App\Controllers\VerificationController', 'showForm']);
    $route->get('/verify_email', ['App\Controllers\VerificationController', 'verify']);

    $route->post('/register', ['App\Controllers\RegisterController', 'register']);
    $route->post('/login', ['App\Controllers\LoginController', 'login']);
    $route->get('/logout', ['App\Controllers\LoginController', 'logout']);


    $route->get('/profile/info', ['App\Controllers\ProfileController', 'showInfo']);
    $route->post('/profile/info', ['App\Controllers\ProfileController', 'postInfo']);

    $route->get('/profile/security', ['App\Controllers\ProfileController', 'showSecurity']);
    $route->post('/profile/security', ['App\Controllers\ProfileController', 'postSecurity']);

    $route->get('/images', ['App\Controllers\ImagesController', 'index']);
    $route->get('/images/{id:\d+}', ['App\Controllers\ImagesController', 'show']);
    $route->get('/images/{id:\d+}/download', ['App\Controllers\ImagesController', 'download']);
    $route->get('/images/create', ['App\Controllers\ImagesController', 'create']);
    $route->post('/images/store', ['App\Controllers\ImagesController', 'store']);
    $route->get('/images/{id:\d+}/edit', ['App\Controllers\ImagesController', 'edit']);
    $route->post('/images/{id:\d+}/update', ['App\Controllers\ImagesController', 'update']);
    $route->get('/images/{id:\d+}/delete', ['App\Controllers\ImagesController', 'delete']);

// роутинг для админ-панели
    $route->addGroup('/admin', function (RouteCollector $route) {
        $route->get('', ['App\Controllers\Admin\HomeController', 'index']);

        $route->get('/categories', ['App\Controllers\Admin\CategoriesController', 'index']);
        $route->get('/categories/create', ['App\Controllers\Admin\CategoriesController', 'create']);
        $route->post('/categories/store', ['App\Controllers\Admin\CategoriesController', 'store']);
        $route->get('/categories/{id:\d+}/edit', ['App\Controllers\Admin\CategoriesController', 'edit']);
        $route->post('/categories/{id:\d+}/update', ['App\Controllers\Admin\CategoriesController', 'update']);
        $route->get('/categories/{id:\d+}/delete', ['App\Controllers\Admin\CategoriesController', 'delete']);

        $route->get('/users', ['App\Controllers\Admin\UsersController', 'index']);
        $route->get('/users/create', ['App\Controllers\Admin\UsersController', 'create']);
        $route->post('/users/store', ['App\Controllers\Admin\UsersController', 'store']);
        $route->get('/users/{id:\d+}/edit', ['App\Controllers\Admin\UsersController', 'edit']);
        $route->post('/users/{id:\d+}/update', ['App\Controllers\Admin\UsersController', 'update']);
        $route->get('/users/{id:\d+}/delete', ['App\Controllers\Admin\UsersController', 'delete']);


        $route->get('/images', ['App\Controllers\Admin\ImagesController', 'index']);
        $route->get('/images/create', ['App\Controllers\Admin\ImagesController', 'create']);
        $route->post('/images/store', ['App\Controllers\Admin\ImagesController', 'store']);
        $route->get('/images/{id:\d+}/edit', ['App\Controllers\Admin\ImagesController', 'edit']);
        $route->post('/images/{id:\d+}/update', ['App\Controllers\Admin\ImagesController', 'update']);
        $route->get('/images/{id:\d+}/delete', ['App\Controllers\Admin\ImagesController', 'delete']);
    });
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        abort(404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        dd('Метод запроса не верный');
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $container->call($handler, $vars);
        break;
}