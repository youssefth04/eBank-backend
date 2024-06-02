<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(SessionInterface $session): JsonResponse
    {
        // Invalidate the session
        $session->invalidate();

        // Clear the token from the token storage (if you are using Symfony's security component)

        // Optionally, clear the cookie if using cookies for session
        $response = new JsonResponse(['message' => 'Logged out'], Response::HTTP_OK);
        $response->headers->clearCookie('SESSION_ID');

        return $response;
    }
}