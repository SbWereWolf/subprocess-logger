<?php

namespace SbWereWolf\BatchLogger;

use Exception;
use Psr\Log\AbstractLogger;
use Stringable;

class Journal extends AbstractLogger implements IJournal
{
    private array $history = [];
    private IDataFactory $factory;

    public function __construct(
        IDataFactory $factory
    )
    {
        $this->factory = $factory;
    }

    /**
     * @param mixed $level
     * @param Stringable|string $message
     * @param array $context
     * @throws Exception
     */
    public function log(
        $level,
        Stringable|string $message,
        array $context = []
    ): void
    {
        $record = $this->factory
            ->setLevel($level)
            ->setMessage($message)
            ->make();
        $this->history[] = $record;
    }

    /**
     * @return array
     */
    public function unload(): array
    {
        $result = $this->history;

        return $result;
    }

    public function reset(): IJournal
    {
        $this->history = [];

        return $this;
    }
}