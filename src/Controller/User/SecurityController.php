<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\ControllerServices\PasswordResetService;
use App\Form\ForgotPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{

    private PasswordResetService $passwordResetService;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        PasswordResetService $passwordResetService, 
        EntityManagerInterface $em, 
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
    )
    {
        $this->passwordResetService = $passwordResetService;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->generateUrl('app_logout'));
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);

    }

    #[Route('/app_forgot_password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function reset(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            // Vérifier si l'utilisateur existe
            $user = $this->em->getRepository(\App\Entity\User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Appeler le service pour envoyer le lien de réinitialisation du mot de passe
                $this->passwordResetService->sendResetLink($user);
                $this->addFlash('success', 'Un e-mail avec un lien de réinitialisation a été envoyé. Retour vers la page de connexion. <a href="' . $this->generateUrl('app_login', ['last_username' => null, 'error' => null]) . '">Cliquez ici</a>');
            }

            $this->addFlash('danger', 'Aucun utilisateur trouvé avec cet e-mail. Retour vers la page de connexion. <a href="' . $this->generateUrl('app_login', ['last_username' => null, 'error' => null]) . '">Cliquez ici</a>');
        }
        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);

        if (!$user || $user->getPasswordResetExpiresAt() < new \DateTime()) {
            $this->addFlash('danger', 'Ce lien de réinitialisation est expiré ou invalide. Retour vers la page de connexion. <a href="' . $this->generateUrl('app_login', ['last_username' => null, 'error' => null]) . '">Cliquez ici</a>');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');

            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $this->userRepository->updatePassword($user, $hashedPassword);

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Retour vers la page de connexion. <a href="' . $this->generateUrl('app_login', ['last_username' => null, 'error' => null]) . '">Cliquez ici</a>');
        }

        return $this->render('security/reset_password.html.twig');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(Request $request): Response
    {
        return new RedirectResponse('/');
    }
}
