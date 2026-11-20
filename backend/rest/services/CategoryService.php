<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/ProductCategoryDao.php';

class CategoryService extends BaseService
{
    protected $entity_name = 'Category';
    private $product_category_dao;

    /** DAOs can be passed in (used by unit tests); by default real DAOs are created. */
    public function __construct($dao = null, $product_category_dao = null)
    {
        parent::__construct($dao ?? new CategoryDao());
        $this->product_category_dao = $product_category_dao ?? new ProductCategoryDao();
    }

    /** Rule: a category that still contains products cannot be deleted. */
    public function delete($id)
    {
        $this->get_by_id($id);
        $products = $this->product_category_dao->count_by_category($id);
        if ($products > 0) {
            throw new ValidationException("This category still contains $products product(s). Remove them from it first.");
        }
        $this->dao->delete($id);
    }

    protected function validate($category, $id = null)
    {
        Validator::required($category, ['name']);
        Validator::length($category['name'], 'name', 2, 60);

        // Rule: category names are unique
        $existing = $this->dao->get_by_name(trim($category['name']));
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("A category named '{$category['name']}' already exists.");
        }
    }
}