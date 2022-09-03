<?php

namespace KrisnaBeaute\BelajarPhpMvc\Controller;

use KrisnaBeaute\BelajarPhpMvc\App\View;
use KrisnaBeaute\BelajarPhpMvc\Config\Database;
use KrisnaBeaute\BelajarPhpMvc\Repository\SessionRepository;
use KrisnaBeaute\BelajarPhpMvc\Repository\UserRepository;
use KrisnaBeaute\BelajarPhpMvc\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function index(): void
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            View::render('Home/index', [
                'title' => 'PHP Login Management'
            ]);
        } else {
            View::render('Home/dashboard', [
                'title' => 'Dashboard',
                "user" => [
                    "name" => $user->name
                ]
            ]);
        }

    }
}