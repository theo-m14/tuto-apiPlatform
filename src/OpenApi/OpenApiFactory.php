<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
        
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        foreach($openApi->getPaths()->getPaths() as $key => $path){
            if($path->getGet() && $path->getGet()->getSummary() === 'hidden'){
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }

        $schemas = $openApi->getComponents()->getSecuritySchemes();
        // $schemas['cookieAuth'] = new \ArrayObject([
        //     'type' => 'apiKey',
        //     'in' => 'cookie',
        //     'name' => 'PHPSESSID'
        // ]);
        $schemas['bearer'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ]);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'john@doe.fr'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '0000'
                ]
            ]
        ]);

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string'
                ]
            ]
        ]);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['User'],
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                ),
                responses: [
                    200 => [
                       'description' => 'Token JWT',
                       'content' => [
                           'application/json' => [
                               'schema' => [
                                   '$ref' => '#/components/schemas/Token'
                               ]
                           ]
                       ] 
                    ]
                ]
            )
        );

        $openApi->getPaths()->addPath('api/login', $pathItem);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogout',
                tags: ['User'],
                responses: [
                    20 => [
                       'description' => 'Utilisateur déconnecté'
                    ]
                ]
            )
        );

        $openApi->getPaths()->addPath('/logout', $pathItem);

        $meOperation = $openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
        $mePath = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $mePath);

        return $openApi;
    }
}