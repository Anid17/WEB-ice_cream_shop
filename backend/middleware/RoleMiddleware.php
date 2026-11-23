<?php
require_once __DIR__ . '/../utils/exceptions.php';

/** Allows only the listed roles, for example new RoleMiddleware([Roles::ADMIN]). Wrong role -> 403. */
class RoleMiddleware
{
    private $allowed_roles;

    public function __construct($allowed_roles)
    {
        $this->allowed_roles = $allowed_roles;
    }

    public function before($params)
    {
        $user = Flight::get('user');
        if (!in_array($user['role'] ?? null, $this->allowed_roles, true)) {
            throw new ForbiddenException('You do not have permission to do this.');
        }
    }
}
