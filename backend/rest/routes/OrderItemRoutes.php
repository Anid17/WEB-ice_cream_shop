<?php
/** The list of ALL order items is for admins; a customer reads the items of an order with GET /orders/{id}/items */
Flight::route('GET /order-items', function () {
    Flight::json(Flight::order_item_service()->get_all());
})->addMiddleware(new RoleMiddleware([Roles::ADMIN]));

Flight::route('GET /order-items/@id:[0-9]+', function ($id) {
    Flight::json(Flight::order_item_service()->get_by_id($id, Flight::get('user')));
});

/** Body: { "order_id": 2, "product_id": 1, "quantity": 2 } */
Flight::route('POST /order-items', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_item_service()->add($data, Flight::get('user')), 201);
});

Flight::route('PUT /order-items/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_item_service()->update($data, $id, Flight::get('user')));
});

Flight::route('DELETE /order-items/@id:[0-9]+', function ($id) {
    Flight::order_item_service()->delete($id, Flight::get('user'));
    Flight::json(['message' => 'Order item deleted.']);
});
