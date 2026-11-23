<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../../utils/Roles.php';

class UserService extends BaseService
{
    protected $entity_name = 'User';
    private $order_dao;

    public function __construct($dao = null, $order_dao = null)
    {
        parent::__construct($dao ?? new UserDao());
        $this->order_dao = $order_dao ?? new OrderDao();
    }

    public function add($user)
    {
        $user['role'] = $user['role'] ?? Roles::CUSTOMER;
        $this->validate($user);
        Validator::required($user, ['password']);
        Validator::password($user['password']);

        // Rule: passwords are NEVER stored in plain text
        $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        $user['email'] = strtolower(trim($user['email']));

        $created = $this->dao->add($user);
        return $this->dao->get_by_id($created['id']);   // get_by_id does not return the hash
    }

    public function update($user, $id)
    {
        $this->get_by_id($id);
        $this->validate($user, $id);

        // The password is optional on update; if it is sent, it is checked and hashed again
        if (!empty($user['password'])) {
            Validator::password($user['password']);
            $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        } else {
            unset($user['password']);
        }
        $user['email'] = strtolower(trim($user['email']));

        $this->dao->update($user, $id);
        return $this->dao->get_by_id($id);
    }

    protected function validate($user, $id = null)
    {
        Validator::required($user, ['name', 'email', 'role']);
        Validator::length($user['name'], 'name', 2, 100);
        Validator::email($user['email']);
        Validator::length($user['email'], 'email', 5, 150);
        Validator::one_of($user['role'], 'role', [Roles::ADMIN, Roles::CUSTOMER]);

        // Rule: e-mail addresses are unique (stored in lowercase)
        $existing = $this->dao->get_by_email(strtolower(trim($user['email'])));
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("A user with the e-mail '{$user['email']}' already exists.");
        }
    }

    /** Rule: a user who has orders cannot be deleted. */
    public function delete($id, $current_user = null) 
    {
        // Rule: nobody can delete their own account (an admin could lock everybody out)
        if ($current_user !== null && (int) $current_user['id'] === (int) $id) {
            throw new ForbiddenException('You cannot delete your own account.');
        }
        $this->get_by_id($id);
        $orders = count($this->order_dao->get_by_user($id));
        if ($orders > 0) {
            throw new ValidationException("This user has $orders order(s) and cannot be deleted.");
        }
        $this->dao->delete($id);
    }
}
