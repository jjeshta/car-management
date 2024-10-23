<?php

namespace App\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use App\Application\Command\AddServiceHistoryCommand;
use App\Application\DTO\ServiceHistoryDTO;

class AddServiceHistoryCommandTest extends TestCase
{
    public function testAddServiceHistoryCommand(): void
    {
        $serviceHistoryDTO = $this->createMock(ServiceHistoryDTO::class);
        
        $command = new AddServiceHistoryCommand($serviceHistoryDTO);
        
        $this->assertSame($serviceHistoryDTO, $command->getServiceHistoryDTO(), 'The ServiceHistoryDTO should be the same as passed to the constructor.');
    }
}
