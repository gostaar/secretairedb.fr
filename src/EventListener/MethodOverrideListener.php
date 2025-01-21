<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class MethodOverrideListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        // Permet de traiter le champ `_method` comme une méthode HTTP valide
        if ($request->get('_method')) {
            $request->setMethod($request->get('_method'));
        }
    }
}
