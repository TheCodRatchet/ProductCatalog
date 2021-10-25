<?php


namespace App\Controllers;

use App\Container;
use App\Repositories\Users\UsersRepository;
use App\View;

class UsersController
{
    private UsersRepository $usersRepository;

    public function __construct(Container $container)
    {
        $this->usersRepository = $container->container[UsersRepository::class];
    }

    public function index(): View
    {
        $users = $this->usersRepository->getAll();

        return new View('users/index.twig', [
            'users' => $users
        ]);
    }
}