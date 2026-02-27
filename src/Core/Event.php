<?php
namespace App\Core;

class Event
{
    private string $name;
    private mixed $data;

    public function __construct(string $name, mixed $data = null)
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
