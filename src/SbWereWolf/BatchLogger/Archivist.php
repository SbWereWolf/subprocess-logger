<?php

namespace SbWereWolf\BatchLogger;

use Psr\Log\AbstractLogger;
use Stringable;

class Archivist extends AbstractLogger implements IArchivist
{
    private IJournal $journal;
    private IPrinter $printer;

    public function __construct(IJournal $journal, IPrinter $printer)
    {
        $this->journal = $journal;
        $this->printer = $printer;
    }

    public function start(string $levelName, string $message): static
    {
        $this->log($levelName, $message);

        $messages = $this->journal->unload();
        $this->journal->reset();

        $this->printer->print($messages);

        return $this;
    }

    public function log(
        $level,
        Stringable|string $message,
        array $context = []
    ): void {
        $this->journal->log($level, $message, $context);
    }

    public function writeDetails(string $levelName, string $message): static
    {
        $this->log($levelName, $message);

        $messages = $this->journal->unload();
        $this->journal->reset();

        $this->printer->print($messages);

        return $this;
    }

    public function writeBrief(string $levelName, string $message): static
    {
        $this->journal->reset();

        $this->log($levelName, $message);
        $messages = $this->journal->unload();

        $this->printer->print($messages);

        return $this;
    }

}