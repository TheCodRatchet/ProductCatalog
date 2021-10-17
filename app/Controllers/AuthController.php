<?php

namespace App\Controllers;

use App\Authorisation;
use App\Models\User;
use App\Redirect;
use App\Repositories\Users\MysqlUsersRepository;
use App\Repositories\Users\UsersRepository;
use App\View;
use Ramsey\Uuid\Uuid;

class AuthController
{
    private UsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }


    public function showRegisterForm(): View
    {
        return new View('Users/register.twig', []);
    }

    public function register()
    {
        $this->usersRepository->save(
            new User(
                Uuid::uuid4(),
                $_POST['name'],
                $_POST['email'],
                password_hash($_POST['password_confirmation'], PASSWORD_DEFAULT)
            )
        );

        Redirect::url('/');;
    }

    public function showLoginForm(): View
    {
        if (Authorisation::loggedIn()) Redirect::url('/');

        return new View('Users/login.twig', []);
    }

    public function login()
    {
        if (Authorisation::loggedIn()) Redirect::url('/');

        $user = $this->usersRepository->getByEmail($_POST['email']);

        if ($user !== null && password_verify($_POST['password'], $user->getPassword())) {
            $_SESSION['id'] = $user->getId();
            Redirect::url('/');
            exit;
        }

        Redirect::url('/login');;
    }

    public function logout()
    {
        unset ($_SESSION['id']);
        Redirect::url('/');;
    }
}