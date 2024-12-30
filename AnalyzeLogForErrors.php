<?php

/**
 * Анализ лог-файла для поиска записей уровня ERROR.
 *
 * @param string $logDir Директория логов.
 * @param string $logFileName Имя лог-файла.
 * @return array Массив найденных ошибок.
 */
function AnalyzeLogForErrors(string $logDir = '/local/logs/', string $logFileName = 'custom_log.txt'): array
{
    $logPath = $_SERVER['DOCUMENT_ROOT'] . $logDir . $logFileName;

    // Проверяем существование лог-файла
    if (!file_exists($logPath)) {
        throw new Exception("Файл логов $logFileName не найден в директории $logDir");
    }

    // Читаем содержимое лог-файла
    $logContent = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Массив для хранения найденных ошибок
    $errors = [];

    // Проходимся по каждой строке и ищем записи с уровнем ERROR
    foreach ($logContent as $line) {
        if (strpos($line, '[ERROR]') !== false) {
            $errors[] = $line;
        }
    }

    return $errors;
}
