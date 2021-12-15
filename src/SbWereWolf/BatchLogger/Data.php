<?php

namespace SbWereWolf\BatchLogger;

class Data implements IData
{
    private $unixTime;
    private int $level;
    private string $parent;
    private string $child;
    private string $message;
    private array $context;

    public function __construct(
        string $message,
        int $level,
        string $parent,
        string $child,
        float $unixTime,
        array $context
    ) {
        $this->message = $message;
        $this->level = $level;
        $this->parent = $parent;
        $this->child = $child;
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
    public function getParent(): string
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getChild(): string
    {
        return $this->child;
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