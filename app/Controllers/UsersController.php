<?php


namespace App\Controllers;

use App\Repositories\Users\MysqlUsersRepository;
use App\Repositories\Users\UsersRepository;
use App\View;

class UsersController
{
    private UsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }

    public function index(): View
    {
        $users = $this->usersRepository->getAll();

        return new View('users/index.twig', [
            'users' => $users
        ]);
    }
}