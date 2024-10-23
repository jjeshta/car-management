<?php

namespace App\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use App\Application\Command\AddCarCommand;
use App\Application\DTO\CarDTO;

class AddCarCommandTest extends TestCase
{
    public function testAddCarCommand(): void
    {
        $carDTO = $this->createMock(CarDTO::class);
        
        $command = new AddCarCommand($carDTO);
        
        $this->assertSame($carDTO, $command->getCarDTO(), 'The CarDTO should be the same as passed to the constructor.');
    }
}
