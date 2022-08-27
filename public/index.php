<?php

require_once __DIR__.'/../vendor/autoload.php';

use KrisnaBeaute\BelajarPhpMvc\App\Router;
use KrisnaBeaute\BelajarPhpMvc\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index', []);

Router::run();