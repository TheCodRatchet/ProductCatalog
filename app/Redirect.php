<?php

namespace App;

class Redirect
{
    public static function url(string $url)
    {
        header("Location: $url");
    }
}