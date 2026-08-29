<?php

namespace App\Services;

class CommonMarkConverter extends \League\CommonMark\CommonMarkConverter
{
    public function convert(string $commonMark): string
    {
        return $this->convertToHtml($commonMark);
    }
}
