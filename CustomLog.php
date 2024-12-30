<?php

/**
 * Кастомная функция для записи логов с уровнями, ротацией, именем файла и разделителями.
 *
 * @param string $message Сообщение для логирования.
 * @param string $level Уровень логирования (INFO, ERROR, DEBUG).
 * @param string $logDir Директория для логов (по умолчанию /local/logs/).
 * @param string $logFileName Имя лог-файла (по умолчанию custom_log.txt).
 * @param int $maxFileSize Максимальный размер файла в байтах (по умолчанию 1MB).
 */
function CustomLog(
    string $message,
    string $level = 'INFO',
    string $logDir = '/local/logs/',
    string $logFileName = 'custom_log.txt',
    int $maxFileSize = 1048576
) {
    // Абсолютный путь к директории логов
    $logPath = $_SERVER['DOCUMENT_ROOT'] . $logDir;

    // Создаем директорию, если она отсутствует
    if (!is_dir($logPath)) {
        mkdir($logPath, 0755, true);
    }

    // Путь к лог-файлу
    $logFile = $logPath . $logFileName;

    // Разделитель для визуального разделения записей
    $divider = str_repeat('-', 80) . PHP_EOL;
    $header = "[" . date("Y-m-d H:i:s") . "] [$level]" . PHP_EOL;

    // Форматирование сообщения
    $formattedMessage = $divider . $header . $message . PHP_EOL;

    // Проверяем размер файла и выполняем ротацию, если превышен лимит
    if (file_exists($logFile) && filesize($logFile) > $maxFileSize) {
        $backupFile = $logPath . pathinfo($logFileName, PATHINFO_FILENAME) . '_' . date("Y-m-d_H-i-s") . '.txt';
        rename($logFile, $backupFile);
    }

    // Запись в лог
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}
