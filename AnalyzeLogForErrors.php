<?php

use Bitrix\Main\Mail\Event;

/**
 * Анализ лог-файла для поиска новых записей уровня ERROR и отправка уведомлений администратору.
 *
 * @param string $logDir Директория логов.
 * @param string $logFileName Имя лог-файла.
 * @param string $adminEmail Email администратора для уведомления.
 * @param string $stateFile Путь к файлу состояния для хранения обработанных записей.
 */
function AnalyzeLogAndNotifyOnlyNewErrors(
    string $logDir,
    string $logFileName,
    string $adminEmail,
    string $stateFile = '/local/logs/error_state.json'
) {
    $logPath = $_SERVER['DOCUMENT_ROOT'] . $logDir . $logFileName;

    // Проверяем существование лог-файла
    if (!file_exists($logPath)) {
        throw new Exception("Файл логов $logFileName не найден в директории $logDir");
    }

    // Читаем содержимое лог-файла
    $logContent = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Загружаем состояние из файла
    $statePath = $_SERVER['DOCUMENT_ROOT'] . $stateFile;
    $lastProcessedTime = 0;

    if (file_exists($statePath)) {
        $stateData = json_decode(file_get_contents($statePath), true);
        $lastProcessedTime = $stateData['last_processed_time'] ?? 0;
    }

    // Массив для хранения новых ошибок
    $newErrors = [];

    // Проходимся по каждой строке, ищем новые записи с уровнем ERROR
    foreach ($logContent as $line) {
        if (strpos($line, '[ERROR]') !== false) {
            // Получаем временную метку из строки (например, из [2024-12-30 12:05:00])
            preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches);
            if (!empty($matches[1])) {
                $timestamp = strtotime($matches[1]);

                // Если ошибка новая, добавляем её в список
                if ($timestamp > $lastProcessedTime) {
                    $newErrors[] = $line;
                }
            }
        }
    }

    // Если найдены новые ошибки, отправляем уведомление
    if (!empty($newErrors)) {
        $message = "Найдены новые ошибки в лог-файле:\n\n" . implode("\n", $newErrors);

        Event::send([
            "EVENT_NAME" => "ERROR_NOTIFICATION",
            "LID" => "s1", // Идентификатор сайта
            "C_FIELDS" => [
                "EMAIL_TO" => $adminEmail,
                "SUBJECT" => "Новые ошибки в логах",
                "MESSAGE" => $message,
            ],
        ]);

        // Обновляем файл состояния с последним обработанным временем
        $latestTime = max(array_map(function ($line) {
            preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches);
            return !empty($matches[1]) ? strtotime($matches[1]) : 0;
        }, $newErrors));

        file_put_contents($statePath, json_encode(['last_processed_time' => $latestTime]));
    }
}
