<?php

namespace SbWereWolf\BatchLogger;

use Psr\Log\LoggerInterface;

class ArchivistFactory
{
    private array $nameToLevel;
    private string $parent;
    private string $child;
    private string $level;

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
     * @param string $global
     * @return ArchivistFactory
     */
    public function setParent(string $global): static
    {
        $this->parent = $global;

        return $this;
    }

    /**
     * @param string $local
     * @return ArchivistFactory
     */
    public function setChild(string $local): static
    {
        $this->child = $local;

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