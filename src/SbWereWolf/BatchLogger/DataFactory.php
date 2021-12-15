<?php

namespace SbWereWolf\BatchLogger;

use Exception;

class DataFactory implements IDataFactory
{
    private int $level;
    private string $global;
    private string $local;
    private string $message;
    private array $context = [];
    private bool $isLevelSet;
    private ILevel $handler;

    /**
     * @param string $global
     * @param string $local
     * @param ILevel $handler
     * @throws Exception
     */
    public function __construct(
        string $global,
        string $local,
        ILevel $handler
    ) {
        if (empty($global)) {
            throw new Exception(
                "The global identity of process should not be empty",
                -670
            );
        }
        if (empty($local)) {
            throw new Exception(
                "The local identity of process should not be empty",
                -671
            );
        }
        $this->global = $global;
        $this->local = $local;
        $this->handler = $handler;
    }

    public function make(): IData
    {
        if (
            !$this->isLevelSet ||
            empty($this->message) ||
            empty($this->global) ||
            empty($this->local)
        ) {
            throw new Exception(
                "The message `{$this->message}`" .
                " or the global id `{$this->global}`" .
                " or the local id `{$this->local}`" .
                " should not be empty",
                -666
            );
        }
        $result = new Data(
            $this->message,
            $this->level,
            $this->global,
            $this->local,
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