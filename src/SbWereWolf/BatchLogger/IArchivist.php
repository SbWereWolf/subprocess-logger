<?php

namespace SbWereWolf\BatchLogger;

use Psr\Log\LoggerInterface;

interface IArchivist extends LoggerInterface
{
    public function start(
        string $levelName,
        string $message
    ): IArchivist;

    public function writeDetails(
        string $levelName,
        string $message
    ): IArchivist;

    public function writeBrief(
        string $levelName,
        string $message
    ): IArchivist;
}