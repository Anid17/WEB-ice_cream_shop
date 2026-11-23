<?php
/** Admin: all orders, or ?user_id=2. Customer: only their own orders (a user_id in the URL is ignored). */
Flight::route('GET /orders', function () {
    $query = Flight::request()->query;
    Flight::json(Flight::order_service()->get_for_user(Flight::get('user'), $query['user_id'] ?? null));
});

Flight::route('GET /orders/@id:[0-9]+', function ($id) {
    Flight::json(Flight::order_service()->get_by_id($id, Flight::get('user')));
});

/** Body: { "user_id": 2, "status": "pending" } */
Flight::route('POST /orders', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_service()->add($data, Flight::get('user')), 201);
});

Flight::route('PUT /orders/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_service()->update($data, $id));
})->addMiddleware(new RoleMiddleware([Roles::ADMIN]));

Flight::route('DELETE /orders/@id:[0-9]+', function ($id) {
    Flight::order_service()->delete($id);
    Flight::json(['message' => 'Order deleted.']);
})->addMiddleware(new RoleMiddleware([Roles::ADMIN]));

/** The items of one order (your own order, or any order for an admin) */
Flight::route('GET /orders/@id:[0-9]+/items', function ($id) {
    Flight::json(Flight::order_item_service()->get_by_order($id, Flight::get('user')));
});
