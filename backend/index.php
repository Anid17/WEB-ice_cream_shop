<?php
require __DIR__ . '/vendor/autoload.php';            // loads FlightPHP (installed by Composer)

require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/ProductService.php';
require_once __DIR__ . '/rest/services/ProductCategoryService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/OrderService.php';
require_once __DIR__ . '/rest/services/OrderItemService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';
require_once __DIR__ . '/rest/services/WeatherService.php';

// Register every service ONCE - every route can then call Flight::product_service()
Flight::register('category_service', 'CategoryService');
Flight::register('product_service', 'ProductService');
Flight::register('product_category_service', 'ProductCategoryService');
Flight::register('user_service', 'UserService');
Flight::register('order_service', 'OrderService');
Flight::register('order_item_service', 'OrderItemService');
Flight::register('review_service', 'ReviewService');
Flight::register('weather_service', 'WeatherService');

/* ONE place that turns every exception into a JSON answer */
Flight::map('error', function (Throwable $e) {
    if ($e instanceof HttpException) {          // thrown by a service: 400, 404, ...
        $status = $e->get_status();
        $message = $e->getMessage();
    } else {                                    // PDOException, a bug, ...
        $status = 500;
        $message = 'Something went wrong on the server. Please try again later.';
        error_log($e);                          // details only in the server log
    }
    Flight::response()
        ->clearBody()
        ->status($status)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['error' => $message]))
        ->send();
});

/* A URL that matches no route */
Flight::map('notFound', function () {
    Flight::response()
        ->clearBody()
        ->status(404)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['error' => 'Endpoint not found.']))
        ->send();
});

Flight::route('GET /', function () {
    Flight::json(['app' => 'Specijal API', 'message' => 'The backend is running.']);
});

require_once __DIR__ . '/rest/routes/CategoryRoutes.php';
require_once __DIR__ . '/rest/routes/ProductRoutes.php';
require_once __DIR__ . '/rest/routes/ProductCategoryRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/OrderRoutes.php';
require_once __DIR__ . '/rest/routes/OrderItemRoutes.php';
require_once __DIR__ . '/rest/routes/ReviewRoutes.php';
require_once __DIR__ . '/rest/routes/WeatherRoutes.php';

Flight::start();                                      // always the LAST line