<?php

require_once __DIR__.'/../vendor/autoload.php';

use KrisnaBeaute\BelajarPhpMvc\App\Router;
use KrisnaBeaute\BelajarPhpMvc\Config\Database;
use KrisnaBeaute\BelajarPhpMvc\Controller\HomeController;
use KrisnaBeaute\BelajarPhpMvc\Controller\UserController;
use KrisnaBeaute\BelajarPhpMvc\Middleware\MustLoginMiddleware;
use KrisnaBeaute\BelajarPhpMvc\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

// Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);

Router::run();