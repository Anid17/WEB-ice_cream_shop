<?php
require_once __DIR__ . '/../utils/Logger.php';

/** Writes one line per request to info.log: method, URL, status, user, role and the time it took. */
class RequestLogMiddleware
{
    public function before()
    {
        $start = microtime(true);
        $method = Flight::request()->method;
        $url = Flight::request()->url;

        // The status is only known at the very end of the request
        register_shutdown_function(function () use ($start, $method, $url) {
            $status = http_response_code() ?: 200;
            $ms = round((microtime(true) - $start) * 1000);
            Logger::info("$method $url $status " . Logger::who() . " {$ms}ms");
        });
    }
}
