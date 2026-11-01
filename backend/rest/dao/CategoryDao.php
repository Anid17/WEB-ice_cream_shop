<?php
require_once __DIR__ . '/BaseDao.php';

class CategoryDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('categories', ['name']);
    }

    /** Number of products in each category. */
    public function count_products()
    {
        return $this->query(
            "SELECT c.id, c.name, COUNT(pc.id) AS product_count
             FROM categories c
             LEFT JOIN product_categories pc ON pc.category_id = c.id
             GROUP BY c.id, c.name
             ORDER BY c.id"
        );
    }
}