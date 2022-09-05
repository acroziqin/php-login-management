<?php

namespace KrisnaBeaute\BelajarPhpMvc\Middleware;

use KrisnaBeaute\BelajarPhpMvc\App\View;
use KrisnaBeaute\BelajarPhpMvc\Config\Database;
use KrisnaBeaute\BelajarPhpMvc\Repository\SessionRepository;
use KrisnaBeaute\BelajarPhpMvc\Repository\UserRepository;
use KrisnaBeaute\BelajarPhpMvc\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user != null){
            View::redirect('/');
        }
    }
}