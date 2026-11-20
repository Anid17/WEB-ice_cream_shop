<?php
require_once __DIR__ . '/BaseDao.php';

class ProductCategoryDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('product_categories', ['product_id', 'category_id']);
    }

    public function get_by_product($product_id)
    {
        return $this->query(
            "SELECT pc.*, c.name AS category_name
             FROM product_categories pc
             JOIN categories c ON c.id = pc.category_id
             WHERE pc.product_id = :product_id
             ORDER BY c.name",
            ['product_id' => $product_id]
        );
    }

    public function get_by_pair($product_id, $category_id)
    {
        return $this->query_unique(
            "SELECT * FROM product_categories WHERE product_id = :product_id AND category_id = :category_id",
            ["product_id" => $product_id, "category_id" => $category_id]
        );
    }

    public function count_by_category($category_id)
    {
        $row = $this->query_unique(
            "SELECT COUNT(*) AS total FROM product_categories WHERE category_id = :category_id",
            ["category_id" => $category_id]
        );
        return (int) $row["total"];
    }
}