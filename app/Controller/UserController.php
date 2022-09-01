<?php

namespace KrisnaBeaute\BelajarPhpMvc\Controller;

use KrisnaBeaute\BelajarPhpMvc\App\View;
use KrisnaBeaute\BelajarPhpMvc\Config\Database;
use KrisnaBeaute\BelajarPhpMvc\Exception\ValidationException;
use KrisnaBeaute\BelajarPhpMvc\Model\UserRegisterRequest;
use KrisnaBeaute\BelajarPhpMvc\Repository\UserRepository;
use KrisnaBeaute\BelajarPhpMvc\Service\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
    }


    public function register()
    {
        View::render('User/register', [
            'title' => 'Register new User'
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $exception->getMessage()
            ]);
        }
    }
}