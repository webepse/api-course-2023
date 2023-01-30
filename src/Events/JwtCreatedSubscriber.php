<?php

namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber
{
    public function updateJwtData(JWTCreatedEvent $event)
    {
        // recup l'utilisateur (pour avoir firstName et le lastName)
        $user = $event->getUser(); 
        $data = $event->getData(); // récup un tableau qui contient les données de base sur l'utilisateur dans le JWT

        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();

        $event->setData($data); // on donne le tableau data modifié

    }
}