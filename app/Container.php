<?php

namespace App;

class Container
{
    public array $container = [];

    public function __construct(array $container)
    {
        $this->container = $container;
    }
}