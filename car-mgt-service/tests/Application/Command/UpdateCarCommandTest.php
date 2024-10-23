<?php

namespace App\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use App\Application\Command\UpdateCarCommand;

class UpdateCarCommandTest extends TestCase
{
    public function testUpdateCarCommand(): void
    {
        $registrationNumber = '1234 AB 56';
        $make = 'Toyota';
        $model = 'Corolla';

        $command = new UpdateCarCommand($registrationNumber, $make, $model);

        $this->assertSame($registrationNumber, $command->getRegistrationNumber(), 'The registration number should match the input.');

        $this->assertSame($make, $command->getMake(), 'The make should match the input.');

        $this->assertSame($model, $command->getModel(), 'The model should match the input.');
    }

    public function testUpdateCarCommandWithNullValues(): void
    {
        $registrationNumber = '1234 AB 56';
        $make = null;
        $model = null;

        $command = new UpdateCarCommand($registrationNumber, $make, $model);

        $this->assertSame($registrationNumber, $command->getRegistrationNumber(), 'The registration number should match the input.');

        $this->assertNull($command->getMake(), 'The make should be null as input.');

        $this->assertNull($command->getModel(), 'The model should be null as input.');
    }
}
