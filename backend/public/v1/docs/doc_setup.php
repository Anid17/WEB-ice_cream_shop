<?php
use OpenApi\Attributes as OA;

// General information, the security scheme and the reusable schemas of the Specijal API.
// Comments here are plain // comments, because swagger-php turns a /** doc comment into a description.

#[OA\Info(
    title: 'Specijal API',
    description: 'REST API of Specijal, an ice cream shop - IT 2001 Web Programming. Log in with POST /auth/login, click Authorize and paste the token (without the word Bearer).',
    version: '1.0'
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', bearerFormat: 'JWT')]
#[OA\OpenApi(security: [['bearerAuth' => []]])]
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'error', type: 'string', example: 'Field \'name\' is required.')
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Category deleted.')]
)]
#[OA\Schema(
    schema: 'CategoryInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Frozen Yogurt')
    ]
)]
#[OA\Schema(
    schema: 'Category',
    allOf: [new OA\Schema(ref: '#/components/schemas/CategoryInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 5)
    ]
)]
#[OA\Schema(
    schema: 'ProductInput',
    required: ['name', 'price', 'stock'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Mango Sorbet'),
        new OA\Property(property: 'description', type: 'string', example: 'Sweet and fresh mango.'),
        new OA\Property(property: 'price', type: 'number', example: 2.9),
        new OA\Property(property: 'stock', type: 'integer', example: 20)
    ]
)]
#[OA\Schema(
    schema: 'Product',
    allOf: [new OA\Schema(ref: '#/components/schemas/ProductInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 5)
    ]
)]
#[OA\Schema(
    schema: 'ProductCategoryInput',
    required: ['product_id', 'category_id'],
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'category_id', type: 'integer', example: 3)
    ]
)]
#[OA\Schema(
    schema: 'ProductCategory',
    allOf: [new OA\Schema(ref: '#/components/schemas/ProductCategoryInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 6)
    ]
)]
#[OA\Schema(
    schema: 'UserInput',
    required: ['name', 'email', 'role'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Ema Kovac'),
        new OA\Property(property: 'email', type: 'string', example: 'ema@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'Secret123', description: 'Required when creating, optional when updating'),
        new OA\Property(property: 'role', type: 'string', example: 'customer', enum: ['admin', 'customer'])
    ]
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'name', type: 'string', example: 'Ema Kovac'),
        new OA\Property(property: 'email', type: 'string', example: 'ema@example.com'),
        new OA\Property(property: 'role', type: 'string', example: 'customer'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-17 21:50:00')
    ]
)]
#[OA\Schema(
    schema: 'OrderInput',
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 2, description: 'Admins only. A customer always orders for themselves.'),
        new OA\Property(property: 'status', type: 'string', example: 'pending', description: 'Admins only. A new order of a customer is always pending.')
    ]
)]
#[OA\Schema(
    schema: 'Order',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 3),
        new OA\Property(property: 'user_id', type: 'integer', example: 2),
        new OA\Property(property: 'status', type: 'string', example: 'pending'),
        new OA\Property(property: 'total_amount', type: 'number', example: 8.4),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-17 21:50:00')
    ]
)]
#[OA\Schema(
    schema: 'OrderItemInput',
    required: ['order_id', 'product_id', 'quantity'],
    properties: [
        new OA\Property(property: 'order_id', type: 'integer', example: 3),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'quantity', type: 'integer', example: 2)
    ]
)]
#[OA\Schema(
    schema: 'OrderItemUpdate',
    required: ['quantity'],
    properties: [
        new OA\Property(property: 'quantity', type: 'integer', example: 3)
    ]
)]
#[OA\Schema(
    schema: 'OrderItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'order_id', type: 'integer', example: 3),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
        new OA\Property(property: 'unit_price', type: 'number', example: 2.5)
    ]
)]
#[OA\Schema(
    schema: 'OrderItemDetail',
    allOf: [new OA\Schema(ref: '#/components/schemas/OrderItem')],
    properties: [
        new OA\Property(property: 'product_name', type: 'string', example: 'Vanilla Bean')
    ]
)]
#[OA\Schema(
    schema: 'ReviewInput',
    required: ['product_id', 'rating'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 2, description: 'Ignored: the author is always the logged-in user'),
        new OA\Property(property: 'product_id', type: 'integer', example: 4),
        new OA\Property(property: 'rating', type: 'integer', example: 5),
        new OA\Property(property: 'comment', type: 'string', example: 'Great!')
    ]
)]
#[OA\Schema(
    schema: 'Review',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'user_id', type: 'integer', example: 2),
        new OA\Property(property: 'product_id', type: 'integer', example: 4),
        new OA\Property(property: 'rating', type: 'integer', example: 5),
        new OA\Property(property: 'comment', type: 'string', example: 'Great!'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-17 21:50:00')
    ]
)]
#[OA\Schema(
    schema: 'ProductReview',
    allOf: [new OA\Schema(ref: '#/components/schemas/Review')],
    properties: [
        new OA\Property(property: 'user_name', type: 'string', example: 'Amar Hodzic')
    ]
)]
#[OA\Schema(
    schema: 'Weather',
    properties: [
        new OA\Property(property: 'city', type: 'string', example: 'Zenica'),
        new OA\Property(property: 'temperature', type: 'number', example: 24.3),
        new OA\Property(property: 'weather_code', type: 'integer', example: 2),
        new OA\Property(property: 'description', type: 'string', example: 'Partly cloudy'),
        new OA\Property(property: 'category', type: 'string', example: 'warm', enum: ['cold', 'mild', 'warm', 'hot']),
        new OA\Property(property: 'message', type: 'string', example: 'A flavor tip for this temperature')
    ]
)]
#[OA\Schema(
    schema: 'RegisterInput',
    required: ['name', 'email', 'password'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Ema Kovac'),
        new OA\Property(property: 'email', type: 'string', example: 'ema@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'Secret123', description: 'At least 8 characters, a letter and a digit')
    ]
)]
#[OA\Schema(
    schema: 'LoginInput',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', example: 'admin@specijal.ba'),
        new OA\Property(property: 'password', type: 'string', example: 'Admin123!')
    ]
)]
#[OA\Schema(
    schema: 'ProfileInput',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Ema Novak'),
        new OA\Property(property: 'email', type: 'string', example: 'ema@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'NewSecret123', description: 'Optional: only when you want a new password')
    ]
)]
#[OA\Schema(
    schema: 'AuthResponse',
    properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...')
    ]
)]
class OpenApiSpec {}
