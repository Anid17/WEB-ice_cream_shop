<?php
/** Public: a visitor has no token yet. The new user is logged in right away. */
Flight::route('POST /auth/register', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::auth_service()->register($data), 201);
});

Flight::route('POST /auth/login', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::auth_service()->login($data));
});

/** The logged-in user (these two need the token) */
Flight::route('GET /auth/me', function () {
    Flight::json(Flight::auth_service()->get_me(Flight::get('user')));
})->addMiddleware(new AuthMiddleware());

/** Body: { "name": "New Name", "email": "new@example.com", "password": "OptionalNew123" } */
Flight::route('PUT /auth/me', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::auth_service()->update_me($data, Flight::get('user')));
})->addMiddleware(new AuthMiddleware());
