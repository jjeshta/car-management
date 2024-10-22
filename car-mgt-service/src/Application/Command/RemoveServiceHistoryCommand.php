<?php

namespace App\Application\ServiceHistory\Command;

class RemoveServiceHistoryCommand
{
    public function __construct(
        private int $serviceHistoryId 
    ) {}

    public function getServiceHistoryId(): int
    {
        return $this->serviceHistoryId;
    }
}
