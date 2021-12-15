<?php

namespace SbWereWolf\BatchLogger;

use Exception;

class Level implements ILevel
{
    public function __construct(array $nameToLevel)
    {
        $levels = array_flip($nameToLevel);
        if (count($levels) != count($nameToLevel)) {
            throw new Exception(
                "The values of nameToLevel MUST BE unique",
                -671
            );
        }
        foreach ($nameToLevel as $key => $value) {
            if (!is_string($key)) {
                throw new Exception(
                    "The keys of nameToLevel MUST BE string",
                    -672
                );
            }
            if (!is_int($value)) {
                throw new Exception(
                    "The values of nameToLevel MUST BE integer",
                    -673
                );
            }
        }
        $this->toLevel = $nameToLevel;
        $this->toName = $levels;
    }

    function testName(string $name): bool
    {
        $isAllowable = $this->checkName($name);

        if (!$isAllowable) {
            $values = '`' .
                implode('`,`', $this->toName) .
                '`';
            throw new Exception(
                "The name of level should be in {$values}",
                -669
            );
        }

        return $isAllowable;
    }

    function checkName(string $name): bool
    {
        $isAllowable = in_array($name, $this->toName);

        return $isAllowable;
    }

    function testLevel(int $level): bool
    {
        $isAllowable = $this->checkLevel($level);

        if (!$isAllowable) {
            $values = '`' .
                implode('`,`', $this->toLevel) .
                '`';
            throw new Exception(
                "The value of level should be in {$values}",
                -669
            );
        }

        return $isAllowable;
    }

    function checkLevel(int $level): bool
    {
        $isAllowable = in_array($level, $this->toLevel);

        return $isAllowable;
    }

    public function asLevel(string $name): int
    {
        $result = $this->toLevel[$name];

        return $result;
    }

    public function asName(int $level): string
    {
        $result = $this->toName[$level];

        return $result;
    }
}