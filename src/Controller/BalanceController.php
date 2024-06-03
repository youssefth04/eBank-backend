<?php



namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BalanceController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/balance', name: 'get_balance', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getBalance(Request $request): JsonResponse
    {
        // Retrieve the currently logged-in user
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Invalid user'], 400);
        }

        return new JsonResponse([
            'balance' => $user->getBalance(),
            'currency' => 'USD' // Replace with actual currency logic if needed
        ], 200);
    }
}