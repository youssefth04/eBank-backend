<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TransferController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/send-money', name: 'send_money', methods: ['POST'])]
    public function sendMoney(Request $request): JsonResponse
    {
        // Get the session token from the Authorization header
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return new JsonResponse(['error' => 'Missing or invalid Authorization header'], 401);
        }

        $sessionToken = $matches[1];
        
        // Find the session by token
        $session = $this->entityManager->getRepository(Session::class)->findOneBy(['sessionToken' => $sessionToken]);
        if (!$session) {
            return new JsonResponse(['error' => 'Invalid session token'], 401);
        }

        // Get the authenticated user
        $user = $session->getUser();
        if (!$user) {
            throw new AccessDeniedException('User not authenticated');
        }

        $data = json_decode($request->getContent(), true);

        // Log the received payload
        $this->logger->info('Received payload: ' . json_encode($data));

        // Validate required fields
        if (empty($data['receiver'])) {
            return new JsonResponse(['error' => 'Missing receiver field'], 400);
        }
        if (empty($data['amount'])) {
            return new JsonResponse(['error' => 'Missing amount field'], 400);
        }

        try {
            $sender = $user;
            $receiver = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $data['receiver']]);

            if (!$receiver) {
                throw new \Exception('Invalid receiver');
            }

            $this->logger->info('Sender balance: ' . $sender->getBalance());
            $this->logger->info('Transfer amount: ' . $data['amount']);

            if ($sender->getBalance() < $data['amount']) {
                throw new \Exception('Insufficient funds');
            }

            // Update balances
            $sender->setBalance($sender->getBalance() - $data['amount']);
            $receiver->setBalance($receiver->getBalance() + $data['amount']);

            $this->entityManager->persist($sender);
            $this->entityManager->persist($receiver);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Money sent successfully!'], 200);
        } catch (\Exception $e) {
            $this->logger->error('Error during money transfer: ' . $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}