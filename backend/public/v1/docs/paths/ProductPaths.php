<?php
use OpenApi\Attributes as OA;

class ProductPaths
{
    #[OA\Get(
        path: '/products',
        tags: ['products'],
        summary: 'List products (search or filter by category)',
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Text in the name or description', example: 'choc', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category_id', in: 'query', required: false, description: 'Only products of this category', example: 2, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of products', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Product'))),
            new OA\Response(response: 400, description: 'Invalid search or category_id', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getProducts() {}

    #[OA\Get(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Get one product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getProductsById() {}

    #[OA\Post(
        path: '/products',
        tags: ['products'],
        summary: 'Create a product',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Admins only', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postProducts() {}

    #[OA\Put(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Update a product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Admins only', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putProductsById() {}

    #[OA\Delete(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Delete a product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'This product is part of orders', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Admins only', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteProductsById() {}

    #[OA\Get(
        path: '/products/{id}/reviews',
        tags: ['products', 'reviews'],
        summary: 'Reviews of one product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of reviews', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ProductReview'))),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getProductsByIdReviews() {}
}
