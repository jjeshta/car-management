<?php
namespace App\Application\Service;

use Psr\Log\LoggerInterface;

class LoggerService
{
    public function __construct(private LoggerInterface $appLogger, private LoggerInterface $errorLogger) {}

    public function logInfo(string $message): void
    {
        $this->appLogger->info($message);
    }

    public function logError(string $message): void
    {
        $this->errorLogger->error($message);
    }

    public function logDebug(string $message): void
    {
        $this->appLogger->debug($message);
    }
}
