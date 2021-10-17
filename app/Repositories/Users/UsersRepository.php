<?php

namespace App\Repositories\Users;

use App\Models\Collections\UsersCollection;
use App\Models\User;

interface UsersRepository
{
    public function getAll(): UsersCollection;

    public function save(User $user): void;

    public function getByEmail(string $email): ?User;
}