<?php
namespace App\Trait;

trait DateTimeConverterTrait
{
    private function convertToDateTime(string $datetime): \DateTime
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    }
}
