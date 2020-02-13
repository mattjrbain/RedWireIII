<?php


namespace App\EventListener;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    /**
     * @var Session
     */
    private $session;

    /**
     * LoginListener constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $userName = $event->getAuthenticationToken()->getUser()->getLastName();
        $this->session->getFlashBag()->add('success', 'Bienvenue ' . $userName . ', vous êtes connecté !');
    }

}