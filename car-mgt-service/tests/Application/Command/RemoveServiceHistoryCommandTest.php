<?php

namespace App\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use App\Application\Command\RemoveServiceHistoryCommand;

class RemoveServiceHistoryCommandTest extends TestCase
{
    public function testRemoveServiceHistoryCommand(): void
    {
        $serviceHistoryId = 123;

        $command = new RemoveServiceHistoryCommand($serviceHistoryId);
        
        $this->assertSame($serviceHistoryId, $command->getServiceHistoryId(), 'The service history ID should be the same as passed to the constructor.');
    }
}
