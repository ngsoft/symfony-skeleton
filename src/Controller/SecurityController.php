<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Traits\HasOptions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    use HasOptions;

    public static function optionSetup(): array
    {
        return [
            ['user.can_register', true, 'Registration is enabled'],
            ['user.max_register', 0, 'User Registration Limit (0 is unlimited)'],
        ];
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $repository): Response
    {
        if (null !== $this->getUser())
        {
            return $this->redirectToRoute('app_welcome');
        }

        if ( ! $repository->hasUser())
        {
            return $this->redirectToRoute('app_register');
        }

        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'can_register'  => $this->canRegister($repository),
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $repository
    ): Response {
        if (null !== $this->getUser())
        {
            return $this->redirectToRoute('app_welcome');
        }

        if ( ! $this->canRegister($repository))
        {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            if ( ! $repository->hasUser())
            {
                $user->setRoles([
                    'ROLE_SUPER_ADMIN',
                ]);
            }

            $entityManager->persist($user);

            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Svelte / js api access using cookie => Authorization: Bearer <token.token>.
     */
    #[Route('/profile/user-token', 'app_user_token')]
    public function token(UserRepository $repository): JsonResponse
    {
        $user = $this->getUser();

        if ($user instanceof User)
        {
            return $this->json($repository->generateOrGetToken($user));
        }

        throw new BadCredentialsException();
    }

    #[Route('/api/login', name: 'api_login')]
    public function apiLogin(#[CurrentUser] ?User $user, EntityManagerInterface $em): JsonResponse
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
    public function apiUser(): JsonResponse
    {
        return $this->json([
            'result' => null !== $this->getUser(),
        ]);
    }

    protected function canRegister(UserRepository $repository): bool
    {
        if ( ! $this->getOptionManager()->getItem('user.can_register'))
        {
            return false;
        }

        if (0 < ($maxUsers = $this->getOptionManager()->getItem('user.max_register')))
        {
            return $repository->countUsers() < $maxUsers;
        }

        return true;
    }
}
