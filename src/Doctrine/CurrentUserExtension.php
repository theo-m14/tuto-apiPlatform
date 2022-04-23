<?php

namespace App\Doctrine;

use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use App\Entity\Post;
use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function __construct(private Security $security){}

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->addWhere($resourceClass,$queryBuilder);
    }

    

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->addWhere($resourceClass,$queryBuilder);
    }

    private function addWhere(string $resourceClass, QueryBuilder $queryBuilder)
    {
        if(is_a($resourceClass, 'App\Entity\UserOwnedInterface', true)){
                $alias = $queryBuilder->getRootAliases()[0];
                $queryBuilder->andWhere($alias . '.user = :current_user')
                            ->setParameter('current_user', $this->security->getUser()->getId());
            }
        }
        
    
}