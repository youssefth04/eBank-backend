<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Security\SessionToken;

class LoginController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['username']) || empty($data['password'])) {
            return $this->json([
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);

        if (!$user || !password_verify($data['password'], $user->getPassword())) {
            return $this->json([
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Generate session token
        $sessionToken = $this->generateSessionToken();

        // Create a custom session token
        $token = new SessionToken($sessionToken);

        // Store session token in the token storage
        $this->tokenStorage->setToken($token);

        return $this->json([
            'message' => 'Login successful',
            'session_token' => $sessionToken,
            'username' => $user->getUsername(),
        ], Response::HTTP_OK);
    }

    private function generateSessionToken(): string
    {
        // Generate a unique session token (you can use JWT, UUID, or any other method)
        return bin2hex(random_bytes(32)); // Example: Generating a random hex token
    }
}