<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductCategoryDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class ProductCategoryService extends BaseService
{
    protected $entity_name = 'Product category link';
    private $product_dao;
    private $category_dao;

    public function __construct($dao = null, $product_dao = null, $category_dao = null)
    {
        parent::__construct($dao ?? new ProductCategoryDao());
        $this->product_dao = $product_dao ?? new ProductDao();
        $this->category_dao = $category_dao ?? new CategoryDao();
    }

    protected function validate($link, $id = null)
    {
        Validator::required($link, ['product_id', 'category_id']);
        Validator::integer($link['product_id'], 'product_id', 1);
        Validator::integer($link['category_id'], 'category_id', 1);

        if (!$this->product_dao->get_by_id($link['product_id'])) {
            throw new ValidationException("The selected product does not exist.");
        }
        if (!$this->category_dao->get_by_id($link['category_id'])) {
            throw new ValidationException("The selected category does not exist.");
        }

        // Rule: a product is linked to a category only once
        $existing = $this->dao->get_by_pair($link['product_id'], $link['category_id']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("This product is already in this category.");
        }
    }
}