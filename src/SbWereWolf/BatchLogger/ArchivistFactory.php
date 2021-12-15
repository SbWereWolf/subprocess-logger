<?php

namespace SbWereWolf\BatchLogger;

use Psr\Log\AbstractLogger;

class ArchivistFactory
{
    private array $nameToLevel;
    private string $global;
    private string $local;
    private string $maximal;

    public function make(AbstractLogger $logger): IArchivist
    {
        $handler = new Level($this->nameToLevel);
        $factory = new DataFactory(
            $this->global,
            $this->local,
            $handler
        );

        $printer = new Printer($this->maximal, $handler, $logger);
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
    public function setGlobal(string $global): static
    {
        $this->global = $global;
        return $this;
    }

    /**
     * @param string $local
     * @return ArchivistFactory
     */
    public function setLocal(string $local): static
    {
        $this->local = $local;
        return $this;
    }

    /**
     * @param string $maximal
     * @return ArchivistFactory
     */
    public function setMaximal(string $maximal): static
    {
        $this->maximal = $maximal;
        return $this;
    }

}