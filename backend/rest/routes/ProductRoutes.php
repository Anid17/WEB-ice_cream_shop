<?php
/** /products?search=vanilla  or  /products?category_id=2  or all products */
Flight::route('GET /products', function () {
    $query = Flight::request()->query;

    if (isset($query['search'])) {
        Flight::json(Flight::product_service()->search($query['search']));
    } elseif (isset($query['category_id'])) {
        Flight::json(Flight::product_service()->get_by_category($query['category_id']));
    } else {
        Flight::json(Flight::product_service()->get_all());
    }
});

Flight::route('GET /products/@id:[0-9]+', function ($id) {
    Flight::json(Flight::product_service()->get_by_id($id));
});

/** Body: { "name": "Mango Sorbet", "description": "Sweet mango", "price": 2.9, "stock": 20 } */
Flight::route('POST /products', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::product_service()->add($data), 201);
});

Flight::route('PUT /products/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::product_service()->update($data, $id));
});

Flight::route('DELETE /products/@id:[0-9]+', function ($id) {
    Flight::product_service()->delete($id);
    Flight::json(['message' => 'Product deleted.']);
});

/** The reviews of one product */
Flight::route('GET /products/@id:[0-9]+/reviews', function ($id) {
    Flight::json(Flight::review_service()->get_by_product($id));
});