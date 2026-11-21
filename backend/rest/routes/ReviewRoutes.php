<?php
Flight::route('GET /reviews', function () {
    Flight::json(Flight::review_service()->get_all());
});

Flight::route('GET /reviews/@id:[0-9]+', function ($id) {
    Flight::json(Flight::review_service()->get_by_id($id));
});

/** Body: { "product_id": 4, "rating": 5, "comment": "Great!" } */
Flight::route('POST /reviews', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::review_service()->add($data, Flight::get('user')), 201);
});

Flight::route('PUT /reviews/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::review_service()->update($data, $id, Flight::get('user')));
});

Flight::route('DELETE /reviews/@id:[0-9]+', function ($id) {
    Flight::review_service()->delete($id, Flight::get('user'));
    Flight::json(['message' => 'Review deleted.']);
});
