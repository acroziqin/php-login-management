<?php

namespace KrisnaBeaute\BelajarPhpMvc\Controller {

    require_once __DIR__ . '/../Helper/helper.php';

    use KrisnaBeaute\BelajarPhpMvc\Config\Database;
    use KrisnaBeaute\BelajarPhpMvc\Domain\Session;
    use KrisnaBeaute\BelajarPhpMvc\Domain\User;
    use KrisnaBeaute\BelajarPhpMvc\Repository\SessionRepository;
    use KrisnaBeaute\BelajarPhpMvc\Repository\UserRepository;
    use KrisnaBeaute\BelajarPhpMvc\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
        }

        public function testPostRegisterSuccess()
        {
            $_POST['id'] = 'roziqin';
            $_POST['name'] = 'Roziqin';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testPostRegisterValidationError()
        {
            $_POST['id'] = '';
            $_POST['name'] = 'Roziqin';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[Id, Name, Password can not blank]');
        }

        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = 'rahasia';

            $this->userRepository->save($user);

            $_POST['id'] = 'roziqin';
            $_POST['name'] = 'Roziqin';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[User Id already exists]');
        }

        public function testLogin()
        {
            $this->userController->login();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
        }

        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'roziqin';
            $_POST['password'] = 'rahasia';

            $this->userController->postLogin();

            $this->expectOutputRegex('[Location: /]');
            $this->expectOutputRegex('[X-KRB-SESSION: ]');
        }

        public function testLoginValidationError()
        {
            $_POST['id'] = '';
            $_POST['password'] = '';

            $this->userController->postLogin();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id, Password can not blank]');
        }

        public function testLoginUserNotFound()
        {
            $_POST['id'] = 'notfound';
            $_POST['password'] = 'notfound';

            $this->userController->postLogin();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id or password is wrong]');
        }

        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'roziqin';
            $_POST['password'] = 'salah';

            $this->userController->postLogin();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id or password is wrong]');
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-KRB-SESSION: ]");
        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[$user->id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[$user->name]");
        }

        public function testPostUpdateProfileSuccess()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "Budi";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            self::assertEquals($_POST['name'], $result->name);
        }

        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[$user->id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Name can not blank]");
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[$user->id]");
        }

        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'rahasia';
            $_POST['newPassword'] = 'budi';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);

            self::assertTrue(password_verify($_POST['newPassword'], $result->password));
        }

        public function testPostUpdateValidationError()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[$user->id]");
            $this->expectOutputRegex("[Old Password or New Password can not blank]");
        }

        public function testPostUpdateWrongOldPassword()
        {
            $user = new User();
            $user->id = 'roziqin';
            $user->name = 'Roziqin';
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'salah';
            $_POST['newPassword'] = 'budi';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[$user->id]");
            $this->expectOutputRegex("[Old Password id wrong]");
        }
    }
}