<?php

namespace SbWereWolf\BatchLogger;

interface ILevel
{
    function testName(string $name): bool;

    function checkName(string $name): bool;

    function testLevel(int $level): bool;

    function checkLevel(int $level): bool;

    public function asLevel(string $name): int;

    public function asName(int $level): string;
}