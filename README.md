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

Работать с ним очень просто.

Делаем запись о начале работы `Archivist::start()`.

Если произошел сбой, то сохраняем подробный журнала сообщений, 
`Archivist::writeDetails()`.

Если сбой не произошёл, то делаем короткую запись об успехе выполнения
`Archivist::writeBrief()`.

# Пример использования

```php
use Integration\FileLogger;
use SbWereWolf\BatchLogger\ArchivistFactory;
     
$filePath = date('Ymd') . '.log';
$file = fopen($filePath, 'a');        
$logger = new FileLogger($file);

$toLevel = [
        'debug' => 7,
        'info' => 6,
        'notice' => 5,
        'warning' => 4,
        'error' => 3,
        'critical' => 2,
        'alert' => 1,
        'emergency' => 0,
    ];

$archivist = (new ArchivistFactory())
    ->setParent('Global process')
    ->setChild('Example of Archivist using')
    ->setConverting($toLevel)
    ->setLevel('debug')
    ->make($logger);        
try {
    $archivist->start('notice', 'start process');
    /* Add to journal some algorithm notes */
    $archivist->debug('some debug info');
    /* If process finish with no errors - write brief to logs */
    $archivist->writeBrief(
        'notice', 
        'process finish with success'
    );
} catch (Throwable $e) {
    $message = "message: {$e->getMessage()}," .
        " trace:{$e->getTraceAsString()}";
    $archivist->critical($message);

    /* If some exception will occur - write detail logs */
    $archivist->writeDetails(
        'notice',
        'process finish with failure'
    );
}
fclose($file);
```
Если код отработает без сбоев, то в логах будет два сообщения:
- start process
- process finish with success

Если при работе кода произойдёт сбой, то в логи будут записаны все
сообщения:
- start process
- some debug info
- message: `exception`, trace: `exception code trace`
- process finish with failure

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
`ArchivistFactory::make()` (создания экземпляра `Archivist`).

# Особенности
Поскольку логи сохраняются в оперативную память, использовать
`Archivist` следует только для коротких процессов, для которых
обязательно будут вызваны методы `Archivist::writeBrief()` или
`Archivist::writeDetails()`.

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