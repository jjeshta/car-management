<?php

namespace App\Tests\Application\Service;

use App\Application\Service\LoggerService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggerServiceTest extends TestCase
{
    private LoggerInterface $appLogger;
    private LoggerInterface $errorLogger;
    private LoggerService $loggerService;

    protected function setUp(): void
    {
        $this->appLogger = $this->createMock(LoggerInterface::class);
        $this->errorLogger = $this->createMock(LoggerInterface::class);
        $this->loggerService = new LoggerService($this->appLogger, $this->errorLogger);
    }

    public function testLogInfo(): void
    {
        $message = "This is an info log message";

        $this->appLogger->expects($this->once())
            ->method('info')
            ->with($message);

        $this->loggerService->logInfo($message);
    }

    public function testLogError(): void
    {
        $message = "This is an error log message";

        $this->errorLogger->expects($this->once())
            ->method('error')
            ->with($message);

        $this->loggerService->logError($message);
    }

    public function testLogDebug(): void
    {
        $message = "This is a debug log message";

        $this->appLogger->expects($this->once())
            ->method('debug')
            ->with($message);

        $this->loggerService->logDebug($message);
    }
}
