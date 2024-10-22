<?php

namespace App\Application\Command;

use App\Application\DTO\CarDTO;

class AddCarCommand
{

    public function __construct(private readonly CarDTO $carDTO)
    {}

    public function getCarDTO(): CarDTO
    {
        return $this->carDTO;
    }
}
