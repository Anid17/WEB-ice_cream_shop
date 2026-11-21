<?php
use OpenApi\Attributes as OA;

class RootPaths
{
    #[OA\Get(
        path: '/',
        tags: ['system'],
        summary: 'Check that the API is running',
        security: [],
        responses: [
            new OA\Response(response: 200, description: 'The API is running', content: new OA\JsonContent(properties: [new OA\Property(property: 'app', type: 'string', example: 'Specijal API'), new OA\Property(property: 'message', type: 'string', example: 'The backend is running.')]))
        ]
    )]
    public function getRoot() {}
}
