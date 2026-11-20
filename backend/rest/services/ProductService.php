<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductDao.php';
require_once __DIR__ . '/../dao/OrderItemDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class ProductService extends BaseService
{
    protected $entity_name = 'Product';
    private $order_item_dao;
    private $category_dao;

    public function __construct($dao = null, $order_item_dao = null, $category_dao = null)
    {
        parent::__construct($dao ?? new ProductDao());
        $this->order_item_dao = $order_item_dao ?? new OrderItemDao();
        $this->category_dao = $category_dao ?? new CategoryDao();
    }

    public function add($product)
    {
        return parent::add($this->empty_to_null($product, ['description']));
    }

    public function update($product, $id)
    {
        return parent::update($this->empty_to_null($product, ['description']), $id);
    }

    /** Rule: a product that appears in orders cannot be deleted. */
    public function delete($id)
    {
        $this->get_by_id($id);
        $orders = $this->order_item_dao->count_by_product($id);
        if ($orders > 0) {
            throw new ValidationException("This product is part of $orders order item(s) and cannot be deleted.");
        }
        $this->dao->delete($id);
    }

    protected function validate($product, $id = null)
    {
        Validator::required($product, ['name', 'price', 'stock']);
        Validator::length($product['name'], 'name', 2, 100);
        Validator::number($product['price'], 'price', 0.01, 999.99);
        Validator::integer($product['stock'], 'stock', 0, 100000);
        if (!empty($product['description'])) {
            Validator::length($product['description'], 'description', 0, 500);
        }
    }

    /** Products whose name or description contains the text. */
    public function search($text)
    {
        Validator::length($text, 'search', 1, 100);
        return $this->dao->search(trim($text));
    }

    /** Products of one category (404 if the category does not exist). */
    public function get_by_category($category_id)
    {
        Validator::integer($category_id, 'category_id', 1);
        if (!$this->category_dao->get_by_id($category_id)) {
            throw new NotFoundException("Category not found.");
        }
        return $this->dao->get_by_category($category_id);
    }
}