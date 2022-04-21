<?php

namespace App\DataProvider;

use Ramsey\Uuid\Uuid;
use App\Entity\Dependency;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;

class DependencyDataProvider implements ContextAwareCollectionDataProviderInterface,RestrictedDataProviderInterface,ItemDataProviderInterface{

    public function __construct(private string $rootPath)
    {
        
    }

    private function getDependecies(): array{
        $path = $this->rootPath . '/composer.json';
        $json = json_decode(file_get_contents($path),true);
        return $json['require'];
    }

    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = [])
    {
        $depencies = $this->getDependecies();
        foreach($depencies as $name => $version){
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
            if($uuid === $id){
                return new Dependency($uuid, $name, $version);
            }
        }
        return null;
    }

    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = [])
    {
        $path = $this->rootPath . '/composer.json';
        $json = json_decode(file_get_contents($path),true);
        $items = [];
        foreach($this->getDependecies() as $name => $version)
        {
            $items[] = new Dependency(Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString(), $name, $version);
        }
        return $items;
    }


    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Dependency::class;
    }
}