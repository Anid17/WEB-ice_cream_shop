<?php
use OpenApi\Attributes as OA;

class OrderPaths
{
    #[OA\Get(
        path: '/orders',
        tags: ['orders'],
        summary: 'List orders (all, or of one user)',
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, description: 'Only orders of this user', example: 2, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of orders', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Order'))),
            new OA\Response(response: 400, description: 'Invalid user_id', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'User not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getOrders() {}

    #[OA\Get(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Get one order',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The order', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getOrdersById() {}

    #[OA\Post(
        path: '/orders',
        tags: ['orders'],
        summary: 'Create a order',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new order', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postOrders() {}

    #[OA\Put(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Update a order',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated order', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putOrdersById() {}

    #[OA\Delete(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Delete a order',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteOrdersById() {}

    #[OA\Get(
        path: '/orders/{id}/items',
        tags: ['orders', 'order-items'],
        summary: 'Items of one order',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of order items', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderItemDetail'))),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getOrdersByIdItems() {}
}