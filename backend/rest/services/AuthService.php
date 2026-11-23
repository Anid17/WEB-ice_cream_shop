<?php
use Firebase\JWT\JWT;

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/Logger.php';
require_once __DIR__ . '/../../utils/Roles.php';

/** Registration, login and the token. */
class AuthService
{
    private $user_dao;
    private $user_service;

    public function __construct($user_dao = null, $user_service = null)
    {
        $this->user_dao = $user_dao ?? new UserDao();
        $this->user_service = $user_service ?? new UserService();
    }

    public function register($data)
    {
        // A client must never be able to register as an admin by sending "role": "admin"
        $data['role'] = Roles::CUSTOMER;

        $user = $this->user_service->add($data);        // validates and hashes the password
        Logger::info("REGISTER user={$user['id']} role={$user['role']}");

        return ['user' => $user, 'token' => $this->create_token($user)];
    }

    public function login($data)
    {
        Validator::required($data, ['email', 'password']);
        $user = $this->user_dao->get_by_email(strtolower(trim($data['email'])));

        // the same message for "unknown e-mail" and "wrong password"
        if (!$user || !password_verify($data['password'], $user['password'])) {
            Logger::error('LOGIN FAILED email=' . strtolower(trim($data['email'])));
            throw new UnauthorizedException('Invalid email or password.');
        }

        unset($user['password']);                       // never send the hash to the client
        Logger::info("LOGIN user={$user['id']} role={$user['role']}");

        return ['user' => $user, 'token' => $this->create_token($user)];
    }

    /** The logged-in user, read fresh from the database. */
    public function get_me($current_user)
    {
        return $this->user_service->get_by_id($current_user['id']);
    }

    /** A user edits their own profile: name, e-mail and optionally a new password. The role never changes. */
    public function update_me($data, $current_user)
    {
        $current = $this->user_service->get_by_id($current_user['id']);

        $update = [
            'name' => $data['name'] ?? $current['name'],
            'email' => $data['email'] ?? $current['email'],
            'role' => $current['role'],
        ];
        if (!empty($data['password'])) {
            $update['password'] = $data['password'];
        }
        return $this->user_service->update($update, $current['id']);
    }

    private function create_token($user)
    {
        // The payload is only encoded, NOT encrypted: never put a password or a hash into it
        $payload = [
            'user' => ['id' => $user['id'], 'email' => $user['email'], 'name' => $user['name'], 'role' => $user['role']],
            'iat' => time(),
            'exp' => time() + (int) Config::JWT_EXPIRATION(),
        ];
        return JWT::encode($payload, Config::JWT_SECRET(), 'HS256');
    }
}
