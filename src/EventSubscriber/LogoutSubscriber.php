<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function onLogoutEvent(LogoutEvent $event)
    {
        if(in_array('application/json',$event->getRequest()->getAcceptableContentTypes())){
            $event->setResponse(new JsonResponse(null, Response::HTTP_NO_CONTENT));
        }
    }

    public static function getSubscribedEvents() : array|string
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
