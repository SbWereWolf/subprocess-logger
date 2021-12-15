<?php

namespace SbWereWolf\BatchLogger;

use Exception;

class DataFactory implements IDataFactory
{
    private int $level;
    private string $parent;
    private string $child;
    private string $message;
    private array $context = [];
    private bool $isLevelSet;
    private ILevel $handler;

    /**
     * @param string $parent
     * @param string $child
     * @param ILevel $handler
     * @throws Exception
     */
    public function __construct(
        string $parent,
        string $child,
        ILevel $handler
    ) {
        if (empty($parent)) {
            throw new Exception(
                "The parent identity of process should not be empty",
                -670
            );
        }
        if (empty($child)) {
            throw new Exception(
                "The child identity of process should not be empty",
                -671
            );
        }
        $this->parent = $parent;
        $this->child = $child;
        $this->handler = $handler;
    }

    public function make(): IData
    {
        if (
            !$this->isLevelSet ||
            empty($this->message) ||
            empty($this->parent) ||
            empty($this->child)
        ) {
            throw new Exception(
                "The message `{$this->message}`" .
                " or the parent id `{$this->parent}`" .
                " or the child id `{$this->child}`" .
                " should not be empty",
                -666
            );
        }
        $result = new Data(
            $this->message,
            $this->level,
            $this->parent,
            $this->child,
            microtime(
                true
            ),
            $this->context
        );
        $this->isLevelSet = false;
        $this->message = '';
        $this->context = [];

        return $result;
    }

    /**
     * @param string $message
     * @return DataFactory
     */
    public function setMessage(string $message): static
    {
        if (empty($message)) {
            throw new Exception(
                "The message should not be empty",
                -667
            );
        }

        $this->message = $message;

        return $this;
    }

    /**
     * @param string $levelName
     * @return DataFactory
     */
    public function setLevel(string $levelName): static
    {
        if (empty($levelName)) {
            throw new Exception(
                "The level should be not empty",
                -668
            );
        }

        $this->handler->testName($levelName);
        $level = $this->handler->asLevel($levelName);
        $this->level = $level;
        $this->isLevelSet = true;

        return $this;
    }

    /**
     * @param array $context
     * @return DataFactory
     */
    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }
}