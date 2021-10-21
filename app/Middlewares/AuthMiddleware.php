<?php

namespace App\Middlewares;

use App\Authorisation;

class AuthMiddleware implements Middleware
{
    public function handle(): void
    {
        if (!Authorisation::loggedIn()) {
            header("Location: /");
            exit;
        }
    }
}