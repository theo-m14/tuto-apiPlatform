<?php

namespace App\DataProvider;

use Ramsey\Uuid\Uuid;
use App\Entity\Dependency;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use App\Repository\DependencyRepository;

class DependencyDataProvider implements ContextAwareCollectionDataProviderInterface,RestrictedDataProviderInterface,ItemDataProviderInterface{

    public function __construct(private DependencyRepository $repository)
    {
        
    }

    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = [])
    {
        return $this->repository->find($id);
    }

    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = [])
    {
        return $this->repository->findAll();
    }


    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Dependency::class;
    }
}