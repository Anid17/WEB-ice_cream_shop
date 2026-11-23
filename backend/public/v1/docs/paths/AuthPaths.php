<?php
use OpenApi\Attributes as OA;

class AuthPaths
{
    #[OA\Post(
        path: '/auth/register',
        tags: ['auth'],
        summary: 'Register a new customer (you are logged in right away)',
        security: [],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RegisterInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new user and a token', content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')),
            new OA\Response(response: 400, description: 'Invalid input or e-mail already used', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postAuthRegister() {}

    #[OA\Post(
        path: '/auth/login',
        tags: ['auth'],
        summary: 'Log in and receive a token',
        security: [],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/LoginInput')),
        responses: [
            new OA\Response(response: 200, description: 'The user and a token', content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')),
            new OA\Response(response: 400, description: 'Missing e-mail or password', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Invalid email or password', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postAuthLogin() {}

    #[OA\Get(
        path: '/auth/me',
        tags: ['auth'],
        summary: 'The logged-in user',
        responses: [
            new OA\Response(response: 200, description: 'The user', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getAuthMe() {}

    #[OA\Put(
        path: '/auth/me',
        tags: ['auth'],
        summary: 'Edit your own profile (the role never changes)',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProfileInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated user', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 400, description: 'Invalid input', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 401, description: 'Missing, invalid or expired token', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putAuthMe() {}
}
