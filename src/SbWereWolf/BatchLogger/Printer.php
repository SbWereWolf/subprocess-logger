<?php

namespace SbWereWolf\BatchLogger;

use Psr\Log\LoggerInterface;

class Printer implements IPrinter
{
    private LoggerInterface $logger;
    private ILevel $level;
    private int $maximal;

    /**
     * @param string $maximalLevel
     * @param ILevel $level
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $maximalLevel,
        ILevel $level,
        LoggerInterface $logger
    ) {
        $this->maximal = $level->asLevel($maximalLevel);
        $this->logger = $logger;
        $this->level = $level;
    }

    public function print(array $messages)
    {
        foreach ($messages as $message) {
            $isInstance = $message instanceof IData;
            $level = PHP_INT_MAX;
            if ($isInstance) {
                $level = $message->getLevel();
            }
            if ($isInstance && $level <= $this->maximal) {
                /* @var IData $message */
                $name = $this->level->asName($level);
                $this->logger->log(
                    $name,
                    $message->getMessage(),
                    [
                        'UnixTime' => $message->getUnixTime(),
                        'Level' => $message->getLevel(),
                        'Global' => $message->getGlobal(),
                        'Local' => $message->getLocal(),
                    ]
                );
            }
        }
    }
}