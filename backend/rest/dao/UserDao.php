<?php
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao
{
    private const PUBLIC_COLUMNS = "id, name, email, role, created_at";

    public function __construct()
    {
        parent::__construct('users', ['name', 'email', 'password', 'role']);
    }

    public function get_all()
    {
        return $this->query("SELECT " . self::PUBLIC_COLUMNS . " FROM users ORDER BY name");
    }

    public function get_by_id($id)
    {
        return $this->query_unique("SELECT " . self::PUBLIC_COLUMNS . " FROM users WHERE id = :id", ['id' => $id]);
    }

    /** Includes the password hash - used only to check a login. */
    public function get_by_email($email)
    {
        return $this->query_unique("SELECT * FROM users WHERE email = :email", ['email' => $email]);
    }
}