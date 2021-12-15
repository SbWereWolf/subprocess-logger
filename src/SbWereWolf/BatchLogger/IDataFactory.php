<?php

namespace SbWereWolf\BatchLogger;

interface IDataFactory
{
    public function make(): IData;

    /**
     * @param string $message
     * @return DataFactory
     */
    public function setMessage(string $message): DataFactory;

    /**
     * @param string $levelName
     * @return DataFactory
     */
    public function setLevel(string $levelName): DataFactory;

    /**
     * @param array $context
     * @return DataFactory
     */
    public function setContext(array $context): DataFactory;
}