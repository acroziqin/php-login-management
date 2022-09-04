<?php

namespace KrisnaBeaute\BelajarPhpMvc\App {

    function header(string $value)
    {
        echo $value;
    }
}

namespace KrisnaBeaute\BelajarPhpMvc\Service {

    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}

namespace KrisnaBeaute\BelajarPhpMvc\Controller {

    use KrisnaBeaute\BelajarPhpMvc\Config\Database;
    use KrisnaBeaute\BelajarPhpMvc\Domain\User;
    use KrisnaBeaute\BelajarPhpMvc\Repository\SessionRepository;
    use KrisnaBeaute\BelajarPhpMvc\Repository\UserRepository;
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
    }
}