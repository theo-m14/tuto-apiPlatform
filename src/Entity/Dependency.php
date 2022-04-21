<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    itemOperations: ['get'],
    collectionOperations: ['get'],
    paginationEnabled: false
)]
class Dependency{

    #[ApiProperty(
        identifier: true,
    )]
    private string $uuid;

    #[ApiProperty(
        description: 'Nom de la dépendance',
    )]
    private string $name;

    #[ApiProperty(
        description: 'Version de la dépendance',
        openapiContext: [
            'example' => '5.2.*'
        ]
    )]
    private string $version;

    public function __construct(string $uuid, string $name, string $version)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * Get the value of uuid
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}