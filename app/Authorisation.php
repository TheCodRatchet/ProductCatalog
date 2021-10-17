<?php

namespace App;

class Authorisation
{
    public static function loggedIn(): bool
    {
        return isset($_SESSION['id']);
    }
}