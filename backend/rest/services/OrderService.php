<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class OrderService extends BaseService
{
    const STATUSES = ['pending', 'preparing', 'delivered', 'cancelled'];
    /** Orders in these statuses are finished and cannot be changed any more. */
    const FINISHED = ['delivered', 'cancelled'];

    protected $entity_name = 'Order';
    private $user_dao;

    public function __construct($dao = null, $user_dao = null)
    {
        parent::__construct($dao ?? new OrderDao());
        $this->user_dao = $user_dao ?? new UserDao();
    }

    /** A customer sees only their own order (403 for another one). */
    public function get_by_id($id, $current_user = null)
    {
        $order = parent::get_by_id($id);
        $this->check_owner($order, $current_user);
        return $order;
    }

    /** Admin: all orders (or one user's orders). Customer: only their own, whatever user_id is sent. */
    public function get_for_user($current_user, $user_id = null)
    {
        if ($current_user['role'] !== Roles::ADMIN) {
            return $this->dao->get_by_user($current_user['id']);
        }
        return $user_id ? $this->get_by_user($user_id) : $this->dao->get_all();
    }

    public function add($order, $current_user = null)
    {
        // A customer always orders for themselves, and a new order is always pending
        if ($current_user !== null && $current_user['role'] !== Roles::ADMIN) {
            $order['user_id'] = $current_user['id'];
            $order['status'] = 'pending';
        }
        $order['status'] = $order['status'] ?? 'pending';
        // Rule: the total is calculated from the order items, the client cannot choose it
        $order['total_amount'] = 0;
        return parent::add($order);
    }

    public function update($order, $id)
    {
        // Rule: the total can only change through order items
        unset($order['total_amount']);
        return parent::update($order, $id);
    }

    protected function validate($order, $id = null)
    {
        Validator::required($order, ['user_id', 'status']);
        Validator::integer($order['user_id'], 'user_id', 1);
        Validator::one_of($order['status'], 'status', self::STATUSES);

        if (!$this->user_dao->get_by_id($order['user_id'])) {
            throw new ValidationException("The selected user does not exist.");
        }

        // Rule: a delivered or cancelled order cannot be changed
        if ($id !== null) {
            $current = $this->dao->get_by_id($id);
            if (in_array($current['status'], self::FINISHED, true)) {
                throw new ValidationException("A {$current['status']} order cannot be changed.");
            }
        }
    }

    /** Orders of one user (404 if the user does not exist). */
    public function get_by_user($user_id)
    {
        Validator::integer($user_id, 'user_id', 1);
        if (!$this->user_dao->get_by_id($user_id)) {
            throw new NotFoundException("User not found.");
        }
        return $this->dao->get_by_user($user_id);
    }
}
