<?php

namespace Integration;

use Psr\Log\AbstractLogger;
use Stringable;

class FileLogger extends AbstractLogger
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $detail = '';
        if ($context) {
            $detail = json_encode($context);
            $detail = ", detail: $detail";
        }

        fwrite(
            $this->file,
            "$level $message$detail" .
            PHP_EOL
        );
    }
}