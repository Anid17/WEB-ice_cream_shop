<?php
require_once __DIR__ . '/BaseDao.php';

class OrderDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('orders', ['user_id', 'status', 'total_amount']);
    }

    public function get_by_user($user_id)
    {
        return $this->query(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC, id DESC",
            ['user_id' => $user_id]
        );
    }

    public function get_by_status($status)
    {
        return $this->query(
            "SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC, id DESC",
            ['status' => $status]
        );
    }
}