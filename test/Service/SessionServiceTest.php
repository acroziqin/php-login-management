<?php

namespace KrisnaBeaute\BelajarPhpMvc\Service;

use KrisnaBeaute\BelajarPhpMvc\Config\Database;
use KrisnaBeaute\BelajarPhpMvc\Domain\Session;
use KrisnaBeaute\BelajarPhpMvc\Domain\User;
use KrisnaBeaute\BelajarPhpMvc\Repository\SessionRepository;
use KrisnaBeaute\BelajarPhpMvc\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

function setcookie(string $name, string $value)
{
    echo "$name: $value";
}

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "roziqin";
        $user->name = "Roziqin";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("roziqin");

        $this->expectOutputRegex("[X-KRB-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals("roziqin", $result->userId);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "roziqin";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-KRB-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "roziqin";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }
}
