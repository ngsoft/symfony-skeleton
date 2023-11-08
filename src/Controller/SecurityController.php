<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Utils\ApiError;
use App\Utils\ApiPayload;
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

/**
 * @phan-file-suppress PhanUndeclaredMethod, PhanTypeMismatchArgument
 */
class SecurityController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository) {}

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser())
        {
            return $this->redirectToRoute('app_welcome');
        }

        $canRegister  = $this->userRepository->canRegister();

        if ( ! $this->userRepository->hasUser())
        {
            return $this->redirectToRoute('app_register');
        }

        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [

            'can_register'  => $canRegister,
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if (null !== $this->getUser())
        {
            return $this->redirectToRoute('app_welcome');
        }

        $hasUsers = $this->userRepository->hasUser();

        if ( ! $this->userRepository->canRegister())
        {
            if ($hasUsers)
            {
                return $this->redirectToRoute('app_login');
            }
            $closed = true;
        }

        $user     = new User();
        $form     = $this->createForm(RegistrationFormType::class, $user);

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

            if ( ! $hasUsers)
            {
                $user->setRoles([
                    'ROLE_SUPER_ADMIN',
                ]);
            }

            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', 'Your account has been created.');

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
            'hasUsers'         => $hasUsers,
            'closed'           => isset($closed),
        ]);
    }

    #[Route('/profile', 'app_profile', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user         = $this->getUser();

        $formProfile  = $this->createForm(ProfileType::class, $user);
        $formPassword = $this->createForm(ChangePasswordType::class);

        $formProfile->handleRequest($request);
        $formPassword->handleRequest($request);

        if ($formProfile->isSubmitted() && $formProfile->isValid())
        {
            if (
                ! empty($email = $formProfile->get('email')->getData())
                && empty($user->getEmail())
            ) {
                $user->setEmail($email);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Your account has been updated.');
            return $this->redirectToRoute('app_profile');
        }

        if ($formPassword->isSubmitted() && $formPassword->isValid())
        {
            if ($userPasswordHasher->isPasswordValid(
                $user,
                $formPassword->get('oldPassword')->getData()
            ))
            {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $formPassword->get('newPassword')->getData()
                    )
                );
                $entityManager->persist($user);

                $entityManager->flush();
                $this->addFlash('success', 'Your password has been changed.');
                return $this->redirectToRoute('app_profile');
            }

            $this->addFlash('danger', 'Your password is incorrect.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('security/profile.html.twig', [
            'formProfile'  => $formProfile->createView(),
            'formPassword' => $formPassword->createView(),
            'hasEmail'     => ! empty($user->getEmail()),
        ]);
    }

    /**
     * Svelte / js api access using cookie => Authorization: Bearer <token.token>.
     */
    #[Route('/profile/user-token', 'app_user_token', methods: ['GET'])]
    public function token(): JsonResponse
    {
        $user = $this->getUser();

        if ($user instanceof User)
        {
            return $this->json(
                ApiPayload::new($this->userRepository->generateOrGetToken($user))
            );
        }

        throw new BadCredentialsException();
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(#[CurrentUser] ?User $user, EntityManagerInterface $em): JsonResponse
    {
        if (null === $user)
        {
            return $this->json(ApiPayload::withError(ApiError::UNAUTHENTICATED), Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->getToken() ?? new AccessToken($user);
        $token->renewExpiresAt();

        $em->persist($token);
        $em->flush();

        return $this->json(ApiPayload::new($token));
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY', statusCode: 401)]
    #[Route('/api/user', name: 'api_user', methods: ['GET'])]
    public function apiUser(): JsonResponse
    {
        return $this->json(
            ApiPayload::new(null !== $this->getUser())
        );
    }
}
