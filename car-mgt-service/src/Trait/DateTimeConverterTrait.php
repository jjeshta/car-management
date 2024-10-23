<?php

namespace App\Trait;

trait DateTimeConverterTrait
{
    private function convertToDateTime(string $datetime): \DateTimeInterface
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);

        if ($dateTime === false) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $datetime);
        }

        if ($dateTime === false) {
            throw new \InvalidArgumentException("Invalid date format: {$datetime}");
        }
        return $dateTime;
    }
}
