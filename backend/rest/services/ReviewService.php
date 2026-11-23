<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ReviewDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';

class ReviewService extends BaseService
{
    protected $entity_name = 'Review';
    private $user_dao;
    private $product_dao;

    public function __construct($dao = null, $user_dao = null, $product_dao = null)
    {
        parent::__construct($dao ?? new ReviewDao());
        $this->user_dao = $user_dao ?? new UserDao();
        $this->product_dao = $product_dao ?? new ProductDao();
    }

    public function add($review)
    {
        return parent::add($this->empty_to_null($review, ['comment']));
    }

    public function update($review, $id)
    {
        return parent::update($this->empty_to_null($review, ['comment']), $id);
    }

    protected function validate($review, $id = null)
    {
        Validator::required($review, ['user_id', 'product_id', 'rating']);
        Validator::integer($review['user_id'], 'user_id', 1);
        Validator::integer($review['product_id'], 'product_id', 1);
        Validator::integer($review['rating'], 'rating', 1, 5);
        if (!empty($review['comment'])) {
            Validator::length($review['comment'], 'comment', 0, 500);
        }

        if (!$this->user_dao->get_by_id($review['user_id'])) {
            throw new ValidationException("The selected user does not exist.");
        }
        if (!$this->product_dao->get_by_id($review['product_id'])) {
            throw new ValidationException("The selected product does not exist.");
        }

        // Rule: a user can review a product only once
        $existing = $this->dao->get_by_user_and_product($review['user_id'], $review['product_id']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("You have already reviewed this product.");
        }
    }

    /** Reviews of one product (404 if the product does not exist). */
    public function get_by_product($product_id)
    {
        if (!$this->product_dao->get_by_id($product_id)) {
            throw new NotFoundException("Product not found.");
        }
        return $this->dao->get_by_product($product_id);
    }
}