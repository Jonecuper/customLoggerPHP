# Кастомный логгер

### Кастомная функция для записи логов с уровнями, ротацией, именем файла и разделителями.

функция `CustomLog`

параметры:

| Параметр | Значение<br />по-умолчанию | Описание                                                                               |
| ---------------- | --------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| $message         |                                               | Сообщение для логирования                                               |
| $level           | 'INFO'                                        | Уровень логирования (INFO, ERROR, DEBUG)                                     |
| $logDir          | '/local/logs/'                                | Директория для логов (по умолчанию /local/logs/)                  |
| $logFileName     | 'custom_log.txt'                              | Имя лог-файла (по умолчанию custom_log.txt)                              |
| $maxFileSize     | 1048576                                       | Максимальный размер файла в байтах (по умолчанию 1MB) |

Пример использования:

```php
try {
    $oldFile = $_SERVER['DOCUMENT_ROOT'] . '/test/oldFile.txt';
    $newFile = $_SERVER['DOCUMENT_ROOT'] . '/test/newFile.txt';

    if (!file_exists($oldFile)) {
        throw new Exception("Файл $oldFile не найден.");
    }

    if (!rename($oldFile, $newFile)) {
        throw new Exception("Ошибка переименования файла $oldFile в $newFile.");
    }

    // Логирование успешного выполнения
    CustomLog("Файл успешно переименован: $oldFile -> $newFile", 'INFO', '/local/logs/', 'rename_agent_log.txt');
} catch (Exception $e) {
    // Логирование ошибок
    CustomLog("Ошибка: " . $e->getMessage(), 'ERROR', '/local/logs/', 'rename_log.txt');
}
```

### Функция для анализа лог-файла для поиска записей уровня ERROR.

функция `AnalyzeLogForErrors`

параметры:

| Параметр | Значение<br />по-умолчанию | Описание                |
| ---------------- | --------------------------------------------- | ------------------------------- |
| $logDir          | '/local/logs/'                                | Директория логов |
| $logFileName     | 'custom_log.txt'                              | Имя лог-файла        |

функция возвращает `массив найденных ошибок`

Пример использования

```php
try {
    $errors = AnalyzeLogForErrors('/local/logs/test_logs/', 'test_log_with_separator.txt');

    if (!empty($errors)) {
        echo "Найдены ошибки:\n";
        foreach ($errors as $error) {
            echo $error . PHP_EOL;
        }
    } else {
        echo "Ошибки не найдены в лог-файле." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Ошибка анализа логов: " . $e->getMessage() . PHP_EOL;
}

```
