<?php
Flight::route('GET /product-categories', function () {
    Flight::json(Flight::product_category_service()->get_all());
});

Flight::route('GET /product-categories/@id:[0-9]+', function ($id) {
    Flight::json(Flight::product_category_service()->get_by_id($id));
});

/** Body: { "product_id": 1, "category_id": 3 } */
Flight::route('POST /product-categories', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::product_category_service()->add($data), 201);
})->addMiddleware(new RoleMiddleware([Roles::ADMIN]));

Flight::route('PUT /product-categories/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::product_category_service()->update($data, $id));
})->addMiddleware(new RoleMiddleware([Roles::ADMIN]));

Flight::route('DELETE /product-categories/@id:[0-9]+', function ($id) {
    Flight::product_category_service()->delete($id);
    Flight::json(['message' => 'Product category link deleted.']);
})->addMiddleware(new RoleMiddleware([Roles::ADMIN]));
