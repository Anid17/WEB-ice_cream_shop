<?php
require_once __DIR__ . '/BaseDao.php';

class ProductDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('products', ['name', 'description', 'price', 'stock']);
    }

    public function get_by_category($category_id)
    {
        return $this->query(
            "SELECT p.*
             FROM products p
             JOIN product_categories pc ON pc.product_id = p.id
             WHERE pc.category_id = :category_id
             ORDER BY p.name",
            ['category_id' => $category_id]
        );
    }

    public function search($text)
    {
        return $this->query(
            "SELECT * FROM products WHERE name LIKE :name OR description LIKE :description ORDER BY name",
            ['name' => '%' . $text . '%', 'description' => '%' . $text . '%']
        );
    }
}