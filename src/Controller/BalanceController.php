<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class BalanceController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/balance', name: 'get_balance', methods: ['GET'])]
    public function getBalance(Request $request): JsonResponse
    {
        // Get the Authorization header
        $authHeader = $request->headers->get('Authorization');

        // Check if the Authorization header is present
        if (!$authHeader) {
            return new JsonResponse(['error' => 'Authorization header not found'], 401);
        }

        // Extract the token from the Bearer scheme
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return new JsonResponse(['error' => 'Invalid Authorization header format'], 401);
        }

        $sessionToken = $matches[1];

        // Validate the session token
        $session = $this->entityManager->getRepository(Session::class)->findOneBy(['sessionToken' => $sessionToken]);

        if (!$session) {
            return new JsonResponse(['error' => 'Invalid session token'], 401);
        }

        // Retrieve the user associated with the session token
        $user = $session->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'balance' => $user->getBalance(),
            'currency' => 'USD' // Replace with actual currency logic if needed
        ], 200);
    }
}