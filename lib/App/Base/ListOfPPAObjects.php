<?php

namespace App\Base;

class ListOfPPAObjects
{
    protected $prefix;
    protected $config;

    public function __construct(string $prefix, array $config)
    {
        $this->prefix = $prefix;
        $this->config = $config;
    }

    // Example methods
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
