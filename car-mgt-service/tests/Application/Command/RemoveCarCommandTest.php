<?php

namespace App\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use App\Application\Command\RemoveCarCommand;

class RemoveCarCommandTest extends TestCase
{
    public function testRemoveCarCommand(): void
    {
        $registrationNumber = '1234 AB 56';

        $command = new RemoveCarCommand($registrationNumber);
        
        $this->assertSame($registrationNumber, $command->getCarRegistrationNumber(), 'The registration number should be the same as passed to the constructor.');
    }
}
