<?php
require_once __DIR__ . '/BaseDao.php';

class ReviewDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('reviews', ['user_id', 'product_id', 'rating', 'comment']);
    }

    public function get_by_product($product_id)
    {
        return $this->query(
            "SELECT r.*, u.name AS user_name
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.product_id = :product_id
             ORDER BY r.created_at DESC, r.id DESC",
            ['product_id' => $product_id]
        );
    }

    /** Average rating per product . */
    public function average_per_product()
    {
        return $this->query(
            "SELECT p.id, p.name AS product, COUNT(r.id) AS review_count, ROUND(AVG(r.rating), 1) AS average_rating
             FROM products p
             LEFT JOIN reviews r ON r.product_id = p.id
             GROUP BY p.id, p.name
             ORDER BY average_rating DESC, p.name"
        );
    }
}