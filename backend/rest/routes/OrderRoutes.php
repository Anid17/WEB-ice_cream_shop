<?php
/** /orders?user_id=2 returns the orders of one user, otherwise all orders */
Flight::route('GET /orders', function () {
    $query = Flight::request()->query;

    if (isset($query['user_id'])) {
        Flight::json(Flight::order_service()->get_by_user($query['user_id']));
    } else {
        Flight::json(Flight::order_service()->get_all());
    }
});

Flight::route('GET /orders/@id:[0-9]+', function ($id) {
    Flight::json(Flight::order_service()->get_by_id($id));
});

/** Body: { "user_id": 2, "status": "pending" } */
Flight::route('POST /orders', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_service()->add($data), 201);
});

Flight::route('PUT /orders/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_service()->update($data, $id));
});

Flight::route('DELETE /orders/@id:[0-9]+', function ($id) {
    Flight::order_service()->delete($id);
    Flight::json(['message' => 'Order deleted.']);
});

/** The items of one order */
Flight::route('GET /orders/@id:[0-9]+/items', function ($id) {
    Flight::json(Flight::order_item_service()->get_by_order($id));
});