<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace Integration;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use SbWereWolf\BatchLogger\Archivist;
use SbWereWolf\BatchLogger\ArchivistFactory;
use Throwable;

class ArchivistTest extends TestCase
{
    const EMERGENCY = 0;
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;
    private array $toLevel = [
        LogLevel::DEBUG => self::DEBUG,
        LogLevel::INFO => self::INFO,
        LogLevel::NOTICE => self::NOTICE,
        LogLevel::WARNING => self::WARNING,
        LogLevel::ERROR => self::ERROR,
        LogLevel::CRITICAL => self::CRITICAL,
        LogLevel::ALERT => self::ALERT,
        LogLevel::EMERGENCY => self::EMERGENCY,
    ];

    /**
     * @throws Exception
     */
    public function testWithSuccess()
    {
        $global = 'testWithSuccess';
        $prefix = 'with-success';
        $maximal = LogLevel::DEBUG;

        $filePath = __DIR__ .
            DIRECTORY_SEPARATOR .
            date('Ymd') .
            '-' .
            $prefix .
            '-' .
            uniqid() .
            '.log';
        $file = fopen($filePath, 'a');
        $archivist = $this->getArchivist($global, $maximal, $file);
        try {
            $archivist->start(LogLevel::NOTICE, 'start test');
            $archivist->debug('some debug info');
            $archivist->success(LogLevel::NOTICE, 'finish with success');
        } catch (Throwable) {
            $archivist->failure(
                LogLevel::NOTICE,
                'finish with failure'
            );
        }
        fclose($file);

        $this->assertTrue(true);
    }

    /**
     * @param string $global
     * @param string $maximal
     * @param $file
     * @return Archivist
     * @throws Exception
     */
    private function getArchivist(
        string $global,
        string $maximal,
        $file
    ): Archivist {
        $logger = new FileLogger($file);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $archivist = (new ArchivistFactory())
            ->setConverting($this->toLevel)
            ->setParent($global)
            ->setChild(uniqid())
            ->setLevel($maximal)
            ->make($logger);

        return $archivist;
    }

    /**
     * @throws Exception
     */
    public function testWithFailure()
    {
        $global = 'testWithFailure';
        $prefix = 'with-failure';
        $maximal = LogLevel::NOTICE;

        $filePath = __DIR__ .
            DIRECTORY_SEPARATOR .
            date('Ymd') .
            '-' .
            $prefix .
            '-' .
            uniqid() .
            '.log';
        $file = fopen($filePath, 'a');
        $archivist = $this->getArchivist($global, $maximal, $file);
        try {
            $archivist->start(LogLevel::NOTICE, 'start test');
            $archivist->debug('some debug info');
            throw new Exception('ALERT');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}," .
                " trace:{$e->getTraceAsString()}";
            $archivist->critical($message);

            $archivist->failure(
                LogLevel::NOTICE,
                'finish with failure'
            );
        }
        fclose($file);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function testPassLogger()
    {
        $global = 'testPassLogger';
        $prefix = 'pass-logger';
        $maximal = LogLevel::DEBUG;

        $filePath = __DIR__ .
            DIRECTORY_SEPARATOR .
            date('Ymd') .
            '-' .
            $prefix .
            '-' .
            uniqid() .
            '.log';
        $file = fopen($filePath, 'a');
        $archivist = $this->getArchivist($global, $maximal, $file);
        try {
            $archivist->start(LogLevel::NOTICE, 'start test');
            $this->withLogger($archivist);
            $archivist->success(LogLevel::NOTICE, 'finish test');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}," .
                " trace:{$e->getTraceAsString()}";
            $archivist->critical($message);
            $archivist->failure(
                LogLevel::NOTICE,
                'finish with failure'
            );
        }
        fclose($file);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    private function withLogger(AbstractLogger $journal): void
    {
        $journal->info('sub function start');
        $journal->debug('some debug info');
        $journal->info('sub function ready to throw exception');
        throw new Exception('ALERT');
    }

    /**
     * @throws Exception
     */
    public function testIndependentLogger()
    {
        $global = 'testIndependentLogger';
        $prefix = 'independent-logger-primary';
        $maximal = LogLevel::DEBUG;

        $filePath = __DIR__ .
            DIRECTORY_SEPARATOR .
            date('Ymd') .
            '-' .
            $prefix .
            '-' .
            uniqid() .
            '.log';
        $file = fopen($filePath, 'a');
        $archivist = $this->getArchivist($global, $maximal, $file);
        try {
            $archivist->start(LogLevel::NOTICE, 'start test');

            $this->independentLogger($global);

            $archivist->success(LogLevel::NOTICE, 'finish test');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}," .
                " trace:{$e->getTraceAsString()}";
            $archivist->critical($message);
            $archivist->failure(
                LogLevel::NOTICE,
                'finish with failure'
            );
        }
        fclose($file);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    private function independentLogger(string $global): void
    {
        $prefix = 'independent-logger-sub';
        $maximal = LogLevel::DEBUG;

        $filePath = __DIR__ .
            DIRECTORY_SEPARATOR .
            date('Ymd') .
            '-' .
            $prefix .
            '-' .
            uniqid() .
            '.log';
        $file = fopen($filePath, 'a');
        $archivist = $this->getArchivist($global, $maximal, $file);

        try {
            $archivist->start(LogLevel::INFO, 'sub function start');

            $archivist->debug('some debug info');
            $archivist->info('sub function ready to throw exception');
            throw new Exception('CRITICAL');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}," .
                " trace:{$e->getTraceAsString()}";
            $archivist->emergency($message);
            $archivist->failure(LogLevel::NOTICE, 'sub function fail');
        }
        fclose($file);
    }
}
