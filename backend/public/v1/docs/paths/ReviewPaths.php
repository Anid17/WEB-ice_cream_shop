<?php
use OpenApi\Attributes as OA;

class ReviewPaths
{
    #[OA\Get(
        path: '/reviews',
        tags: ['reviews'],
        summary: 'List all reviews',
        responses: [
            new OA\Response(response: 200, description: 'List of reviews', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Review'))),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getReviews() {}

    #[OA\Get(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Get one review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The review', content: new OA\JsonContent(ref: '#/components/schemas/Review')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Review not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getReviewsById() {}

    #[OA\Post(
        path: '/reviews',
        tags: ['reviews'],
        summary: 'Create a review',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReviewInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new review', content: new OA\JsonContent(ref: '#/components/schemas/Review')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postReviews() {}

    #[OA\Put(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Update a review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReviewInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated review', content: new OA\JsonContent(ref: '#/components/schemas/Review')),
            new OA\Response(response: 400, description: 'Invalid input or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Not your review', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Review not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putReviewsById() {}

    #[OA\Delete(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Delete a review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Review deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Not your review', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Review not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteReviewsById() {}
}
