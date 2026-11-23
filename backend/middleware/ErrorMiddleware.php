<?php
require_once __DIR__ . '/../utils/exceptions.php';
require_once __DIR__ . '/../utils/Logger.php';

/** The ONE place where every exception becomes a JSON answer. */
class ErrorMiddleware
{
    public function handle(Throwable $e)
    {
        if ($e instanceof HttpException) {          // thrown by a service or a middleware
            $status = $e->get_status();
            $message = $e->getMessage();
            // rejected requests go to error.log (an external API failure was logged where it happened)
            if ($status === 401 || $status === 403) {
                $this->log($status, $message);
            }
        } else {                                    // PDOException, a bug, ...
            $status = 500;
            $message = 'Something went wrong on the server. Please try again later.';
            // technical details only in the log: class, file and line, never in the answer
            $this->log(500, get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')');
        }
        $this->respond($status, $message);
    }

    /** A URL that matches no route. */
    public function not_found()
    {
        $this->log(404, 'Endpoint not found.');
        $this->respond(404, 'Endpoint not found.');
    }

    private function log($status, $message)
    {
        Logger::error(Flight::request()->method . ' ' . Flight::request()->url . " $status " . Logger::who() . ' ' . $message);
    }

    private function respond($status, $message)
    {
        Flight::response()
            ->clearBody()
            ->status($status)
            ->header('Content-Type', 'application/json')
            ->write(json_encode(['error' => $message]))
            ->send();
    }
}
