<?php

namespace App\Application\Command;

use App\Application\DTO\ServiceHistoryDTO;

class AddServiceHistoryCommand
{

    public function __construct(private readonly ServiceHistoryDTO $serviceHistoryDTO)
    {}

    public function getServiceHistoryDTO(): ServiceHistoryDTO
    {
        return $this->serviceHistoryDTO;
    }
}
