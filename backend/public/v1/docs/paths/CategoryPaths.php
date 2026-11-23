<?php
use OpenApi\Attributes as OA;

class CategoryPaths
{
    #[OA\Get(
        path: '/categories',
        tags: ['categories'],
        summary: 'List all categories',
        responses: [
            new OA\Response(response: 200, description: 'List of categories', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Category'))),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCategories() {}

    #[OA\Get(
        path: '/categories/{id}',
        tags: ['categories'],
        summary: 'Get one category',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCategoriesById() {}

    #[OA\Post(
        path: '/categories',
        tags: ['categories'],
        summary: 'Create a category',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Admins only', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postCategories() {}

    #[OA\Put(
        path: '/categories/{id}',
        tags: ['categories'],
        summary: 'Update a category',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Admins only', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putCategoriesById() {}

    #[OA\Delete(
        path: '/categories/{id}',
        tags: ['categories'],
        summary: 'Delete a category',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'This category still contains products', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Admins only', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteCategoriesById() {}
}
