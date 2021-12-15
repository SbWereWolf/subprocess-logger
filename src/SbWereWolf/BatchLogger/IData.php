<?php

namespace SbWereWolf\BatchLogger;

use JsonSerializable;

interface IData extends JsonSerializable
{

    public function getMessage(): string;

    /**
     * @return int
     */
    public function getLevel(): int;

    /**
     * @return int
     */
    public function getUnixTime(): float;

    /**
     * @return string
     */
    public function getGlobal(): string;

    /**
     * @return string
     */
    public function getLocal(): string;

    /**
     * @return array
     */
    public function getContext(): array;
}