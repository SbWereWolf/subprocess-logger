<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace Integration;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use SbWereWolf\BatchLogger\DataFactory;
use SbWereWolf\BatchLogger\Journal;
use SbWereWolf\BatchLogger\Level;
use SbWereWolf\BatchLogger\Printer;
use Throwable;

class JournalTest extends TestCase
{
    const EMERGENCY = 0;
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;
    private $toLevel = [
        LogLevel::DEBUG => self::DEBUG,
        LogLevel::INFO => self::INFO,
        LogLevel::NOTICE => self::NOTICE,
        LogLevel::WARNING => self::WARNING,
        LogLevel::ERROR => self::ERROR,
        LogLevel::CRITICAL => self::CRITICAL,
        LogLevel::ALERT => self::ALERT,
        LogLevel::EMERGENCY => self::EMERGENCY,
    ];

    public function testLevelsFilter()
    {
        $handler = new Level($this->toLevel);
        $factory = new DataFactory(
            'testLevelsFilter',
            uniqid(),
            $handler
        );
        $journal = new Journal($factory);
        $journal->notice('start test');

        $journal->debug('some debug info');
        $journal->emergency('some critical');

        $journal->notice('finish test');

        $messages = $journal->unload();
        $this->writeToFile($messages, LogLevel::DEBUG, 'only-debug');
        $this->writeToFile($messages, LogLevel::CRITICAL, 'only-critical');

        $this->assertTrue(true);
    }

    /**
     * @param string $prefix
     * @param array $messages
     * @throws Exception
     */
    private function writeToFile($messages, $maximal, string $prefix): bool
    {
        $filePath = __DIR__ .
            DIRECTORY_SEPARATOR .
            date('Ymd') .
            '-' .
            $prefix .
            '-' .
            uniqid() .
            '.log';

        $file = fopen($filePath, 'a');
        $logger = new FileLogger($file);
        $levelHandler = new Level([
            LogLevel::DEBUG => self::DEBUG,
            LogLevel::INFO => self::INFO,
            LogLevel::NOTICE => self::NOTICE,
            LogLevel::WARNING => self::WARNING,
            LogLevel::ERROR => self::ERROR,
            LogLevel::CRITICAL => self::CRITICAL,
            LogLevel::ALERT => self::ALERT,
            LogLevel::EMERGENCY => self::EMERGENCY,
        ]);
        (new Printer($maximal, $levelHandler, $logger))
            ->print($messages);
        $result = fclose($file);

        return $result;
    }

    public function testWithSuccess()
    {
        $handler = new Level($this->toLevel);
        $factory = new DataFactory('testWithSuccess', uniqid(), $handler);
        $journal = new Journal($factory);
        try {
            $journal->notice('start test');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::NOTICE, 'with-success-first');
            $journal->reset();

            $journal->debug('some debug info');

            $journal->reset();
            $journal->notice('finish with success');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::NOTICE, 'with-success-second');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}, trace:{$e->getTraceAsString()}";
            $journal->critical($message);
            $journal->notice('finish with failure');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::NOTICE, 'with-success-second');
        }

        $this->assertTrue(true);
    }

    public function testWithFailure()
    {
        $handler = new Level($this->toLevel);
        $factory = new DataFactory('testWithFailure', uniqid(), $handler);
        $journal = new Journal($factory);
        try {
            $journal->notice('start test');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::NOTICE, 'with-failure-first');
            $journal->reset();

            $journal->debug('some debug info');
            throw new Exception('ALERT');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}, trace:{$e->getTraceAsString()}";
            $journal->critical($message);
            $journal->notice('finish with failure');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::DEBUG, 'with-failure-second');
        }

        $this->assertTrue(true);
    }

    public function testPassLogger()
    {
        $handler = new Level($this->toLevel);
        $factory = new DataFactory('testPassLogger', uniqid(), $handler);
        $journal = new Journal($factory);
        try {
            $journal->notice('start test');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::NOTICE, 'pass-logger-first');
            $journal->reset();

            $this->withLogger($journal);

            $journal->notice('finish test');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::NOTICE, 'pass-logger-second');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}, trace:{$e->getTraceAsString()}";
            $journal->critical($message);
            $journal->notice('finish with failure');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::DEBUG, 'pass-logger-second');
        }

        $this->assertTrue(true);
    }

    /**
     * @param Journal $journal
     */
    private function withLogger(Journal $journal): void
    {
        $journal->info('sub function start');
        $journal->debug('some debug info');
        $journal->info('sub function ready to throw exception');
        throw new Exception('ALERT');
    }

    public function testIndependentLogger()
    {
        $global = 'testIndependentLogger';
        $handler = new Level($this->toLevel);
        $factory = new DataFactory($global, uniqid(), $handler);
        $journal = new Journal($factory);
        try {
            $journal->notice('start test');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::NOTICE, 'independent-logger-1');
            $journal->reset();

            $this->independentLogger($global);

            $journal->notice('finish test');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::NOTICE, 'independent-logger-2');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}, trace:{$e->getTraceAsString()}";
            $journal->critical($message);
            $journal->notice('finish with failure');
            $messages = $journal->unload();
            $this->writeToFile($messages, LogLevel::DEBUG, 'independent-logger-2');
        }

        $this->assertTrue(true);
    }

    private function independentLogger(string $global): void
    {
        $handler = new Level($this->toLevel);
        $factory = new DataFactory($global, uniqid(), $handler);
        $independent = new Journal($factory);

        try {
            $independent->info('sub function start');
            $messages = $independent->unload();
            $this->writeToFile($messages, LogLevel::DEBUG, 'independent-logger-3');
            $independent->reset();

            $independent->debug('some debug info');
            $independent->info('sub function ready to throw exception');
            throw new Exception('CRITICAL');
        } catch (Throwable $e) {
            $message = "message: {$e->getMessage()}, trace:{$e->getTraceAsString()}";
            $independent->emergency($message);
            $independent->notice('sub function fail');
            $messages = $independent->unload();
            $this->writeToFile($messages, LogLevel::DEBUG, 'independent-logger-4');
        }
    }
}
