<?php

namespace SbWereWolf\BatchLogger;

use Psr\Log\LoggerInterface;

interface IJournal extends LoggerInterface
{
    public function unload(): array;

    public function reset(): IJournal;

}