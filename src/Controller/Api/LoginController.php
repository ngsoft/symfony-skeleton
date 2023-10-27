<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\AccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login')]
    public function index(#[CurrentUser] ?User $user, EntityManagerInterface $em): JsonResponse
    {
        if (null === $user)
        {
            return $this->json([
                'error' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->getToken() ?? new AccessToken($user);
        $token->renewExpiresAt();

        $em->persist($token);
        $em->flush();

        return $this->json($token);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY', statusCode: 401)]
    #[Route('/api/user', name: 'api_user')]
    public function user(): JsonResponse
    {
        return $this->json([
            'user' => $this->getUser(),
        ]);
    }
}
