# How To Install
`composer require sbwerewolf/subprocess-logger`

# Пакетный логгер для микро операций
При разработке для себя, при обкатке каких то идей, в логи смотрят,
только при сбоях.

В таких ситуациях, хочется видеть подробные логи только если ошибка
произошла, если ошибки не было, то и забивать логи сообщениями не
хочется. Но когда пишешь в логи, ты ещё не знаешь, будет сбой или нет,
поэтому писать приходиться всё подряд.

Но мы ведь можем сохранять не всё и не всегда. Мы можем сохранять логи
только при ошибках, только при сбоях.

Именно для такого использования был разработан
`SbWereWolf\BatchLogger\Archivist`.

Работать с ним очень просто. У него всего три метода.

Создание списка сообщений `Archivist::start()`.

Сохранения всех сообщений, если произошел сбой `Archivist::failure()`.

Если сбой не произошёл, то используем метод для записи в логи
сообщения об успешном завершении процесса (алгоритма)
`Archivist::success()`.

# Пример использования
```php
        $filePath = date('Ymd') . '.log';
        $file = fopen($filePath, 'a');        
        $logger = new FileLogger($file);
        
        $toLevel = [
                LogLevel::DEBUG => self::DEBUG,
                LogLevel::INFO => self::INFO,
                LogLevel::NOTICE => self::NOTICE,
                LogLevel::WARNING => self::WARNING,
                LogLevel::ERROR => self::ERROR,
                LogLevel::CRITICAL => self::CRITICAL,
                LogLevel::ALERT => self::ALERT,
                LogLevel::EMERGENCY => self::EMERGENCY,
            ];
        
        $archivist = (new ArchivistFactory())
            ->setConverting($toLevel)
            ->setGlobal('Global process')
            ->setLocal('Example of Archivist using')
            ->setMaximal(LogLevel::DEBUG)
            ->make($logger);        
        try {
            $archivist->start(LogLevel::NOTICE, 'start test');
            /* The FileLogger will write logs only
            if exception will be occur */
            $archivist->debug('some debug info');
            /* some algorithm steps */
            $archivist->success(
                LogLevel::NOTICE, 
                'finish with success'
            );
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
```
Если код отработает без сбоев, то в логах будет две записи:
- start test
- finish with success

Если при работе кода произойдёт сбой, то в логи будет записано всё:
- start test
- some debug info
- message: `exception`, trace: `exception code trace`
- finish with failure

Для более наглядной демонстрации работы написаны тесты, просто
запустите, и сразу увидите в каких случаях, сколько записей попадает в
логи.

# Уровни логирования и другие опции
Уровни логирования задаются по выбору пользователя, каждому названию
уровня (строковое значение) должен быть сопоставлен его уровень 
(целочисленное значение).

Уровни логирования устанавливаются с помощью массива методом
`ArchivistFactory::setConverting(array(string=>int))`.

С помощью метода `ArchivistFactory::setLevel(string)` можно
ограничить уровень сообщений которые попадут в логи. Если уровень
сообщения выше заданного, то такое сообщение будет отброшено и не
будет записано в логи.

Родительский и дочерний идентификаторы можно использовать, что бы
строить дерево выполнения процесса в целом.

Либо можно использовать родительский идентификатор как сквозной, а
дочерние идентификаторы для отдельных шагов алгоритма.

Родительский идентификатор процесса устанавливается методом
`ArchivistFactory::setParent(string)`

Дочерний идентификатор процесса устанавливается методом
`ArchivistFactory::setChild(string)`

Для непосредственной записи в логи, необходимо передать экземпляр
поддерживающий интерфейс `LoggerInterface` в метод
`ArchivistFactory::make()` - для непосредственного создания экземпляра
`Archivist`.

# Особенности
Поскольку логи сохраняются в оперативную память, использовать
`Archivist` следует только для коротких процессов, для которых
обязательно будут вызваны методы `Archivist::success()` или
`Archivist::failure()`.

Если эти методы не будут вызваны, то вся доступная оперативная память
будет забита логами. Очистка списка сообщений происходит только при
вызове этих двух методов.
# Контакты
```
Вольхин Николай
e-mail ulfnew@gmail.com
phone +7-902-272-65-35
Telegram @sbwerewolf
```

[Telegram chat with me](https://t.me/SbWereWolf) 