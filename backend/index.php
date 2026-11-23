<?php
require __DIR__ . '/vendor/autoload.php';            // FlightPHP, firebase/php-jwt, swagger-php (Composer)

require_once __DIR__ . '/utils/Logger.php';
require_once __DIR__ . '/utils/Roles.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/RoleMiddleware.php';
require_once __DIR__ . '/middleware/RequestLogMiddleware.php';
require_once __DIR__ . '/middleware/ErrorMiddleware.php';

require_once __DIR__ . '/rest/services/AuthService.php';
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/ProductService.php';
require_once __DIR__ . '/rest/services/ProductCategoryService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/OrderService.php';
require_once __DIR__ . '/rest/services/OrderItemService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';
require_once __DIR__ . '/rest/services/WeatherService.php';

// Register every service ONCE - every route can then call Flight::product_service()
Flight::register('auth_service', 'AuthService');
Flight::register('category_service', 'CategoryService');
Flight::register('product_service', 'ProductService');
Flight::register('product_category_service', 'ProductCategoryService');
Flight::register('user_service', 'UserService');
Flight::register('order_service', 'OrderService');
Flight::register('order_item_service', 'OrderItemService');
Flight::register('review_service', 'ReviewService');
Flight::register('weather_service', 'WeatherService');

/* Global middleware: every request is logged, every exception becomes JSON */
$request_logger = new RequestLogMiddleware();
Flight::before('start', function () use ($request_logger) {
    $request_logger->before();
});

$error_handler = new ErrorMiddleware();
Flight::map('error', [$error_handler, 'handle']);
Flight::map('notFound', [$error_handler, 'not_found']);

/* Public routes - no token needed */
Flight::route('GET /', function () {
    Flight::json(['app' => 'Specijal API', 'message' => 'The backend is running.']);
});
require_once __DIR__ . '/rest/routes/AuthRoutes.php';

/* Protected routes - AuthMiddleware runs before EVERY route in this group.
   Routes that only admins may call add RoleMiddleware themselves. */
Flight::group('', function () {
    require_once __DIR__ . '/rest/routes/CategoryRoutes.php';
    require_once __DIR__ . '/rest/routes/ProductRoutes.php';
    require_once __DIR__ . '/rest/routes/ProductCategoryRoutes.php';
    require_once __DIR__ . '/rest/routes/UserRoutes.php';
    require_once __DIR__ . '/rest/routes/OrderRoutes.php';
    require_once __DIR__ . '/rest/routes/OrderItemRoutes.php';
    require_once __DIR__ . '/rest/routes/ReviewRoutes.php';
    require_once __DIR__ . '/rest/routes/WeatherRoutes.php';
}, [new AuthMiddleware()]);

Flight::start();                                      // always the LAST line
