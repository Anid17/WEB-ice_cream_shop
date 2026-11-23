<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderItemDao.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';

class OrderItemService extends BaseService
{
    protected $entity_name = 'Order item';
    private $order_dao;
    private $product_dao;

    public function __construct($dao = null, $order_dao = null, $product_dao = null)
    {
        parent::__construct($dao ?? new OrderItemDao());
        $this->order_dao = $order_dao ?? new OrderDao();
        $this->product_dao = $product_dao ?? new ProductDao();
    }

    /** An item belongs to the user who owns its order. */
    public function get_by_id($id, $current_user = null)
    {
        $item = parent::get_by_id($id);
        $this->check_owner($this->order_dao->get_by_id($item['order_id']), $current_user);
        return $item;
    }

    /**
     * Add a product to an order. Rules:
     * - the order must be yours (403) and still open (pending or preparing)
     * - there must be enough stock
     * - the unit price is taken from the product, never from the request
     * - the item, the stock and the order total change together (transaction)
     */
    public function add($item, $current_user = null)
    {
        $this->validate($item);
        $order = $this->open_order($item['order_id'], $current_user);
        $product = $this->product_dao->get_by_id($item['product_id']);
        if ($product['stock'] < $item['quantity']) {
            throw new ValidationException("Only {$product['stock']} of '{$product['name']}' left in stock.");
        }

        $item['unit_price'] = $product['price'];

        $this->dao->begin_transaction();
        try {
            $created = $this->dao->add($item);
            $this->apply_change($order['id'], $product, (int) $item['quantity']);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }
        return $this->dao->get_by_id($created['id']);
    }

    /** Only the quantity of an item can be changed. */
    public function update($item, $id, $current_user = null)
    {
        $current = $this->get_by_id($id);
        $order = $this->open_order($current['order_id'], $current_user);
        Validator::required($item, ['quantity']);
        Validator::integer($item['quantity'], 'quantity', 1, 100);
        $product = $this->product_dao->get_by_id($current['product_id']);

        $delta = (int) $item['quantity'] - (int) $current['quantity'];
        if ($delta > 0 && $product['stock'] < $delta) {
            throw new ValidationException("Only {$product['stock']} more of '{$product['name']}' left in stock.");
        }

        $this->dao->begin_transaction();
        try {
            $this->dao->update(['quantity' => $item['quantity']], $id);
            $this->apply_change($order['id'], $product, $delta, $current['unit_price']);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }
        return $this->dao->get_by_id($id);
    }

    /** Removing an item gives the stock back and lowers the order total. */
    public function delete($id, $current_user = null)
    {
        $current = $this->get_by_id($id);
        $order = $this->open_order($current['order_id'], $current_user);
        $product = $this->product_dao->get_by_id($current['product_id']);

        $this->dao->begin_transaction();
        try {
            $this->dao->delete($id);
            $this->apply_change($order['id'], $product, -(int) $current['quantity'], $current['unit_price']);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }
    }

    protected function validate($item, $id = null)
    {
        Validator::required($item, ['order_id', 'product_id', 'quantity']);
        Validator::integer($item['order_id'], 'order_id', 1);
        Validator::integer($item['product_id'], 'product_id', 1);
        Validator::integer($item['quantity'], 'quantity', 1, 100);

        if (!$this->order_dao->get_by_id($item['order_id'])) {
            throw new ValidationException("The selected order does not exist.");
        }
        if (!$this->product_dao->get_by_id($item['product_id'])) {
            throw new ValidationException("The selected product does not exist.");
        }
    }

    /** The order must exist, belong to the user (403) and not be finished (400). */
    private function open_order($order_id, $current_user = null)
    {
        $order = $this->order_dao->get_by_id($order_id);
        if (!$order) {
            throw new ValidationException("The selected order does not exist.");
        }
        $this->check_owner($order, $current_user);
        if (in_array($order['status'], OrderService::FINISHED, true)) {
            throw new ValidationException("A {$order['status']} order cannot be changed.");
        }
        return $order;
    }

    /** Change stock and order total by $quantity_delta items (negative = give back). */
    private function apply_change($order_id, $product, $quantity_delta, $unit_price = null)
    {
        $unit_price = $unit_price ?? $product['price'];
        if ($quantity_delta !== 0) {
            if ($this->product_dao->change_stock($product['id'], -$quantity_delta) === 0 && $quantity_delta > 0) {
                throw new ValidationException("'{$product['name']}' is out of stock.");
            }
            $this->order_dao->change_total($order_id, $quantity_delta * $unit_price);
        }
    }

    /** Items of one order (404 if the order does not exist, 403 if it is not yours). */
    public function get_by_order($order_id, $current_user = null)
    {
        $order = $this->order_dao->get_by_id($order_id);
        if (!$order) {
            throw new NotFoundException("Order not found.");
        }
        $this->check_owner($order, $current_user);
        return $this->dao->get_by_order($order_id);
    }
}
