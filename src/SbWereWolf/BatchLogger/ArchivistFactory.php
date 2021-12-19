<?php

namespace SbWereWolf\BatchLogger;

use Exception;
use Psr\Log\LoggerInterface;

class ArchivistFactory
{
    private array $nameToLevel;
    private string $parent;
    private string $child;
    private string $level;

    /**
     * @param LoggerInterface $logger
     * @return IArchivist
     * @throws Exception
     */
    public function make(LoggerInterface $logger): IArchivist
    {
        $handler = new Level($this->nameToLevel);
        $factory = new DataFactory(
            $this->parent,
            $this->child,
            $handler
        );

        $printer = new Printer($this->level, $handler, $logger);
        $journal = new Journal($factory);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $archivist = new Archivist($journal, $printer);

        return $archivist;
    }

    /**
     * @param array $nameToLevel
     * @return ArchivistFactory
     */
    public function setConverting(array $nameToLevel): static
    {
        $this->nameToLevel = $nameToLevel;

        return $this;
    }

    /**
     * @param string $parent
     * @return ArchivistFactory
     */
    public function setParent(string $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param string $child
     * @return ArchivistFactory
     */
    public function setChild(string $child): static
    {
        $this->child = $child;

        return $this;
    }

    /**
     * @param string $level
     * @return ArchivistFactory
     */
    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

}