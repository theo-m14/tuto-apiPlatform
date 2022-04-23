<?php
namespace App\Serializer;

use Exception;
use App\Entity\UserOwnedInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class UserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface{
    
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED_DENORMALIZER = 'UserOwnedDenormalizerCalled';

    public function __construct(private Security $security, private UserRepository $userManager)
    {
        
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []) :mixed
    {
        $context[self::ALREADY_CALLED_DENORMALIZER] = true;
        /** @var UserOwnedInterface $object */
        $object =$this->denormalizer->denormalize($data,$type,$format,$context);
        $object->setUser($this->userManager->find($this->security->getUser()->getId()));
        return $object;
    }
    
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []) : bool
    {
        $already_called = $context[self::ALREADY_CALLED_DENORMALIZER] ?? false;
        return is_a($type, 'App\Entity\UserOwnedInterface', true)  && !$already_called;
    }
     
}