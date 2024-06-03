<?php

namespace App\Controller;

use App\Entity\User; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

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
        $data = json_decode($request->getContent(), true);

        // Set default senderEmail if not provided
        if (empty($data['senderEmail'])) {
        }

        // Log the received payload
        $this->logger->info('Received payload: ' . json_encode($data));

        if (empty($data['receiver'])) {
            return new JsonResponse(['error' => 'Missing receiver field'], 400);
        }
        if (empty($data['amount'])) {
            return new JsonResponse(['error' => 'Missing amount field'], 400);
        }
        if (empty($data['currency'])) {
            return new JsonResponse(['error' => 'Missing currency field'], 400);
        }

        try {
            $sender = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['senderEmail']]);
            $receiver = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['receiver']]);

            if (!$sender || !$receiver) {
                throw new \Exception('Invalid sender or receiver');
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