<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\ForgotPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class SecurityController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    
    private $router;
    private $client;
    private $gmailService;
    private $mailer;

    public function __construct(
        EntityManagerInterface $em, 
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer,
        UrlGeneratorInterface $router, 
    )
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $request->query->get('last_username', $authenticationUtils->getLastUsername());

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
        $referer = $request->headers->get('referer');
        if ($referer && strpos($referer, '/login') !== false) {
            $request->getSession()->remove('message');
        }

        if($request->getSession()->get('message')){
            $message = $request->getSession()->get('message');
            $this->addFlash($message['alert'], $message['content']);
        }

        $email = $request->request->get('email');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        if ($request->isMethod('POST')) {
            // Vérifier si l'utilisateur existe
            $user = $this->em->getRepository(\App\Entity\User::class)->findOneBy(['email' => $email]);

            if ($user) {
                $userId = $user->getId();
                $request->getSession()->set('user_id', $userId);
                // Appeler le service pour envoyer le lien de réinitialisation du mot de passe
                $userId = $request->getSession()->get('user_id');

                $user = $this->em->getRepository(\App\Entity\User::class)->find($userId);

                $resetToken = Uuid::v4()->toRfc4122();  // Génération d'un token unique
                $user->setPasswordResetToken($resetToken);
                $user->setPasswordResetExpiresAt(new \DateTimeImmutable('+10 minutes'));  // Expiration dans 10 minutes
                $this->em->flush();

                $resetUrl = $this->router->generate('app_reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);

                $subject = 'Reinitialisation de votre mot de passe';
                
                $email = (new Email())
                    ->from('dbsecretaire@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Reinitialisation de votre mot de passe')
                    ->html('<p>Bonjour'.$user->getNom().',</p><p>Vous avez demandé à réinitialiser votre mot de passe sur le site secretairedb.fr.</p><p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant: <a href="' . $resetUrl . '">Réinitialiser mon mot de passe</a></p><p>Ce lien est valable 10 minutes.</p>');

                $this->mailer->send($email);
                
                $this->addFlash('success', 'Le message a bien été envoyé, vérifiez votre boîte mail.');

                return $this->render('security/login.html.twig', ['last_username' => $user->getEmail(), 'error' => null]);
            } else {
                $this->addFlash('danger', 'Aucun utilisateur trouvé avec cet e-mail. Retour vers la page de connexion. <a href="' . $this->generateUrl('app_login', ['last_username' => null, 'error' => null]) . '">Cliquez ici</a>');
                return $this->render('security/forgot_password.html.twig');
            }
        }
        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, string $token): Response
    {
        $user = $this->em->getRepository(\App\Entity\User::class)->findOneBy(['passwordResetToken' => $token]);

        if (!$user || $user->getPasswordResetExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('danger', 'Ce lien de réinitialisation est expiré ou invalide.');

            return $this->redirectToRoute('app_login', ['last_username' => $user->getEmail(), 'error' => null]);
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');

            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $this->userRepository->updatePassword($user, $hashedPassword);

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez vous connecter avec votre nouveau mot de passe.');

            return $this->redirectToRoute('app_login', ['last_username' => $user->getEmail(), 'error' => null]);
        }
       
        return $this->render('security/reset_password.html.twig');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->invalidate();
        $response = new Response();
        $response->headers->clearCookie('PHPSESSID');

        return new RedirectResponse('/');
    }
}
