<?php
/**
 * Writes one line per event to backend/logs/info.log or backend/logs/error.log.
 * A line says WHO did WHAT (user id, URL, status) - never a password or a token.
 */
class Logger
{
    public static function info($message)
    {
        self::write('info.log', 'INFO', $message);
    }

    public static function error($message)
    {
        self::write('error.log', 'ERROR', $message);
    }

    /** "user=2 role=customer" for the logged-in user, "user=- role=-" for a visitor. */
    public static function who()
    {
        $user = Flight::get('user');
        return 'user=' . ($user['id'] ?? '-') . ' role=' . ($user['role'] ?? '-');
    }

    private static function write($file, $level, $message)
    {
        $folder = __DIR__ . '/../logs';
        if (!is_dir($folder)) {
            @mkdir($folder, 0775, true);
        }
        $line = '[' . date('Y-m-d H:i:s') . "] $level " . str_replace(["\r", "\n"], ' ', $message) . PHP_EOL;
        // A problem with the log file must never break a request
        @file_put_contents($folder . '/' . $file, $line, FILE_APPEND | LOCK_EX);
    }
}
