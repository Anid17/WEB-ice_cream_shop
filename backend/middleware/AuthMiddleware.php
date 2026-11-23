<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/exceptions.php';

/** Runs before every protected route: no valid token -> 401, the route never runs. */
class AuthMiddleware
{
    public function before($params)
    {
        $token = $this->read_token();
        if (!$token) {
            throw new UnauthorizedException('Missing authentication token.');
        }

        try {
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
        } catch (ExpiredException $e) {
            throw new UnauthorizedException('Your session has expired. Please log in again.');
        } catch (Exception $e) {
            throw new UnauthorizedException('Invalid authentication token.');
        }

        // Every route can now use Flight::get('user')['id'] and Flight::get('user')['role']
        Flight::set('user', (array) $decoded->user);
    }

    /** Reads "Authorization: Bearer <token>". */
    private function read_token()
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if ($header === '' && function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                if (strtolower($name) === 'authorization') {
                    $header = $value;
                }
            }
        }
        return preg_match('/^Bearer\s+(\S+)$/i', trim($header), $m) ? $m[1] : null;
    }
}
