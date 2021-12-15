<?php

namespace SbWereWolf\BatchLogger;

use Psr\Log\LoggerInterface;

interface IArchivist extends LoggerInterface
{

    public function start(
        string $levelName,
        string $message
    ): IArchivist;

    public function failure(
        string $levelName,
        string $message
    ): IArchivist;

    public function success(
        string $levelName,
        string $message
    ): IArchivist;
}