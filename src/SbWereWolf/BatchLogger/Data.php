<?php

namespace SbWereWolf\BatchLogger;

class Data implements IData
{
    private $unixTime;
    private int $level;
    private string $global;
    private string $local;
    private string $message;
    private array $context;

    public function __construct(
        string $message,
        int $level,
        string $global,
        string $local,
        float $unixTime,
        array $context
    ) {
        $this->message = $message;
        $this->level = $level;
        $this->global = $global;
        $this->local = $local;
        $this->unixTime = $unixTime;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return int
     */
    public function getUnixTime(): float
    {
        return $this->unixTime;
    }

    /**
     * @return string
     */
    public function getGlobal(): string
    {
        return $this->global;
    }

    /**
     * @return string
     */
    public function getLocal(): string
    {
        return $this->local;
    }

    public function jsonSerialize()
    {
        $fields = get_object_vars($this);
        return $fields;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}