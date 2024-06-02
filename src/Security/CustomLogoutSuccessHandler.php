<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomLogoutSuccessHandler
{
    public function handleLogoutSuccess(Request $request): Response
    {
        return new JsonResponse(['message' => 'Logged out successfully'], Response::HTTP_OK);
    }
}