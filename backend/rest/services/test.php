<?php
require_once __DIR__ . '/CategoryService.php';
require_once __DIR__ . '/ProductService.php';
require_once __DIR__ . '/ProductCategoryService.php';
require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/OrderService.php';
require_once __DIR__ . '/OrderItemService.php';
require_once __DIR__ . '/ReviewService.php';

/** Run one action and print OK or the exception it threw. */
function attempt($description, $action)
{
    echo str_pad($description, 50, '.') . ' ';
    try {
        $result = $action();
        echo "OK" . (isset($result['id']) ? " (id {$result['id']})" : '') . "\n";
        return $result;
    } catch (HttpException $e) {
        echo get_class($e) . " [{$e->get_status()}]: {$e->getMessage()}\n";
    }
}

$categories = new CategoryService();
$products = new ProductService();
$links = new ProductCategoryService();
$users = new UserService();
$orders = new OrderService();
$items = new OrderItemService();
$reviews = new ReviewService();

echo "=== Categories ===\n";
attempt('Category without a name', fn() => $categories->add([]));
attempt('Category with a 1-letter name', fn() => $categories->add(['name' => 'A']));
attempt('Duplicate category name', fn() => $categories->add(['name' => 'Classic']));
attempt('Update category 1 keeping its own name', fn() => $categories->update(['name' => 'Classic'], 1));
attempt('Delete a category that has products', fn() => $categories->delete(1));
attempt('Get category #999', fn() => $categories->get_by_id(999));
$c = attempt('Add a valid category', fn() => $categories->add(['name' => 'Test category']));
attempt('Delete the test category', fn() => $categories->delete($c['id']));

echo "\n=== Products ===\n";
attempt('Product without a price', fn() => $products->add(['name' => 'Test', 'stock' => 5]));
attempt('Product with a negative stock', fn() => $products->add(['name' => 'Test', 'price' => 2, 'stock' => -1]));
attempt('Product with the price "abc"', fn() => $products->add(['name' => 'Test', 'price' => 'abc', 'stock' => 1]));
attempt('Delete a product that is in orders', fn() => $products->delete(1));
$p = attempt('Add a valid product', fn() => $products->add(['name' => 'Test flavor', 'price' => '2.20', 'stock' => 10, 'description' => '']));
attempt('Link it to category 1', fn() => $l = $links->add(['product_id' => $p['id'], 'category_id' => 1]));
attempt('Link it to category 1 again', fn() => $links->add(['product_id' => $p['id'], 'category_id' => 1]));
attempt('Link to a product that does not exist', fn() => $links->add(['product_id' => 999, 'category_id' => 1]));

echo "\n=== Users ===\n";
attempt('User with an invalid e-mail', fn() => $users->add(['name' => 'Test User', 'email' => 'ana@', 'password' => 'Secret123']));
attempt('User with a weak password', fn() => $users->add(['name' => 'Test User', 'email' => 'test@example.com', 'password' => 'abc']));
attempt('User with a duplicate e-mail (other case)', fn() => $users->add(['name' => 'Test User', 'email' => 'amar@example.com', 'password' => 'Secret123']));
attempt('User with an unknown role', fn() => $users->add(['name' => 'Test User', 'email' => 'test@example.com', 'password' => 'Secret123', 'role' => 'boss']));
$u = attempt('Add a valid user', fn() => $users->add(['name' => 'Test User', 'email' => 'Test@Example.com', 'password' => 'Secret123']));
echo "  stored e-mail: {$u['email']}, role: {$u['role']}, password in answer: " . (isset($u['password']) ? 'YES (bad)' : 'no') . "\n";
$hash = (new UserDao())->get_by_email('test@example.com')['password'];
echo "  hash starts with " . substr($hash, 0, 4) . ", verifies: " . (password_verify('Secret123', $hash) ? 'yes' : 'no') . "\n";

echo "\n=== Orders and order items ===\n";
attempt('Order for a user that does not exist', fn() => $orders->add(['user_id' => 999]));
attempt('Order with an unknown status', fn() => $orders->add(['user_id' => $u['id'], 'status' => 'lost']));
$o = attempt('Add a valid order (total forced to 0)', fn() => $orders->add(['user_id' => $u['id'], 'total_amount' => 999]));
echo "  total_amount: {$o['total_amount']}\n";
attempt('Item with quantity 0', fn() => $items->add(['order_id' => $o['id'], 'product_id' => $p['id'], 'quantity' => 0]));
attempt('Item with more than the stock', fn() => $items->add(['order_id' => $o['id'], 'product_id' => $p['id'], 'quantity' => 50]));
$i = attempt('Add a valid item (3 x 2.20)', fn() => $items->add(['order_id' => $o['id'], 'product_id' => $p['id'], 'quantity' => 3, 'unit_price' => 0.01]));
echo "  unit_price: {$i['unit_price']}, order total: " . $orders->get_by_id($o['id'])['total_amount'] . ", stock: " . $products->get_by_id($p['id'])['stock'] . "\n";
attempt('Change the quantity to 5', fn() => $items->update(['quantity' => 5], $i['id']));
echo "  order total: " . $orders->get_by_id($o['id'])['total_amount'] . ", stock: " . $products->get_by_id($p['id'])['stock'] . "\n";
attempt('Delete the item', fn() => $items->delete($i['id']));
echo "  order total: " . $orders->get_by_id($o['id'])['total_amount'] . ", stock: " . $products->get_by_id($p['id'])['stock'] . "\n";
attempt('Change a delivered order (#1)', fn() => $orders->update(['user_id' => 2, 'status' => 'pending'], 1));
attempt('Add an item to a delivered order (#1)', fn() => $items->add(['order_id' => 1, 'product_id' => $p['id'], 'quantity' => 1]));

echo "\n=== Reviews ===\n";
attempt('Review with rating 7', fn() => $reviews->add(['user_id' => $u['id'], 'product_id' => $p['id'], 'rating' => 7]));
attempt('Second review of the same product', fn() => $reviews->add(['user_id' => 2, 'product_id' => 1, 'rating' => 4]));
$r = attempt('Add a valid review', fn() => $reviews->add(['user_id' => $u['id'], 'product_id' => $p['id'], 'rating' => 5, 'comment' => '']));

echo "\n=== Cleanup ===\n";
attempt('Delete review', fn() => $reviews->delete($r['id']));
attempt('Delete order', fn() => $orders->delete($o['id']));
attempt('Delete the link', fn() => $links->delete((new ProductCategoryDao())->get_by_pair($p['id'], 1)['id']));
attempt('Delete test product', fn() => $products->delete($p['id']));
attempt('Delete test user', fn() => $users->delete($u['id']));