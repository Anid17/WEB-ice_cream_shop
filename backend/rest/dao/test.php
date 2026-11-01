<?php
require_once __DIR__ . '/UserDao.php';
require_once __DIR__ . '/CategoryDao.php';
require_once __DIR__ . '/ProductDao.php';
require_once __DIR__ . '/ProductCategoryDao.php';
require_once __DIR__ . '/OrderDao.php';
require_once __DIR__ . '/OrderItemDao.php';
require_once __DIR__ . '/ReviewDao.php';

echo "=== READ ===\n";
echo "Users: "      . count((new UserDao())->get_all()) . "\n";
echo "Categories: " . count((new CategoryDao())->get_all()) . "\n";
echo "Products: "   . count((new ProductDao())->get_all()) . "\n";
echo "Orders: "     . count((new OrderDao())->get_all()) . "\n";
echo "Order items: ". count((new OrderItemDao())->get_all()) . "\n";
echo "Reviews: "    . count((new ReviewDao())->get_all()) . "\n";
print_r((new ProductDao())->get_by_id(1));
print_r((new UserDao())->get_by_email('admin@specijal.ba'));
echo "Sorbet products: " . count((new ProductDao())->get_by_category(2)) . "\n";
echo "Orders of user 2: " . count((new OrderDao())->get_by_user(2)) . "\n";
echo "Items of order 1: " . count((new OrderItemDao())->get_by_order(1)) . "\n";
print_r((new ReviewDao())->average_per_product());
print_r((new CategoryDao())->count_products());
print_r((new ProductDao())->search("' OR 1=1 -- "));

echo "\n=== CREATE (the 'hacker' key must be dropped) ===\n";
$dao = new CategoryDao();
$category = $dao->add(['name' => 'Test category', 'hacker' => 'DROP TABLE']);
print_r($category);

echo "\n=== UPDATE ===\n";
$dao->update(['name' => 'Updated category'], $category['id']);
print_r($dao->get_by_id($category['id']));

echo "\n=== DELETE (must print NULL) ===\n";
$dao->delete($category['id']);
var_dump($dao->get_by_id($category['id']));