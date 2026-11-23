<?php
use OpenApi\Attributes as OA;

class OrderItemPaths
{
    #[OA\Get(
        path: '/order-items',
        tags: ['order-items'],
        summary: 'List all order items',
        responses: [
            new OA\Response(response: 200, description: 'List of order items', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderItem'))),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Admins only', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getOrderItems() {}

    #[OA\Get(
        path: '/order-items/{id}',
        tags: ['order-items'],
        summary: 'Get one order item',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The order item', content: new OA\JsonContent(ref: '#/components/schemas/OrderItem')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Not your order', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getOrderItemsById() {}

    #[OA\Post(
        path: '/order-items',
        tags: ['order-items'],
        summary: 'Create a order item',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderItemInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new order item', content: new OA\JsonContent(ref: '#/components/schemas/OrderItem')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Not your order', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postOrderItems() {}

    #[OA\Put(
        path: '/order-items/{id}',
        tags: ['order-items'],
        summary: 'Update a order item',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderItemUpdate')),
        responses: [
            new OA\Response(response: 200, description: 'The updated order item', content: new OA\JsonContent(ref: '#/components/schemas/OrderItem')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Not your order', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putOrderItemsById() {}

    #[OA\Delete(
        path: '/order-items/{id}',
        tags: ['order-items'],
        summary: 'Delete a order item',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order item deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The order is already finished', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Not your order', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteOrderItemsById() {}
}
