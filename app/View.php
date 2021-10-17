<?php

namespace App;

class View
{
    private string $template;
    private array $arguments;

    public function __construct(string $template, array $arguments)
    {
        $this->template = $template;
        $this->arguments = $arguments;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}