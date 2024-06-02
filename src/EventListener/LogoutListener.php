<?php 
namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Routing\RouterInterface;

class LogoutListener
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onLogoutEvent(LogoutEvent $event): void
    {
        // Custom logout logic here
        // For example, log the logout event

        // Optionally override the default logout behavior
        $response = new RedirectResponse($this->router->generate('home'));
        $event->setResponse($response);
    }
}