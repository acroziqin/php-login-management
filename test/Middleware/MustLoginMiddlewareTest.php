<?php

namespace KrisnaBeaute\BelajarPhpMvc\App {

    function header(string $value)
    {
        echo $value;
    }
}

namespace KrisnaBeaute\BelajarPhpMvc\Middleware {

    use KrisnaBeaute\BelajarPhpMvc\Config\Database;
    use KrisnaBeaute\BelajarPhpMvc\Domain\Session;
    use KrisnaBeaute\BelajarPhpMvc\Domain\User;
    use KrisnaBeaute\BelajarPhpMvc\Repository\SessionRepository;
    use KrisnaBeaute\BelajarPhpMvc\Repository\UserRepository;
    use KrisnaBeaute\BelajarPhpMvc\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustLoginMiddlewareTest extends TestCase
    {
        private MustLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->middleware = new MustLoginMiddleware();
            putenv("mode=test");

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();

            $this->expectOutputRegex("[Location: login]");
        }

        public function testBeforeLoginUser()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = 'rahasia';
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->middleware->before();

            $this->expectOutputString("");
        }
    }
}