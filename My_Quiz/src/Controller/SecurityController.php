<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
    * @param AuthenticationUtils $authenticationUtils
    * @param Request $request
    * @param UserPasswordEncoderInterface $encoder
    * @param MailerService
    * @param \Swift_Mailer
    * @return Response
    * @throws \Exception
    */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        $mailer = \Swift_Mailer::class;
        $mailerservice = MailerService::class;

        if ($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $user->setConfirmationToken($this->generateToken());
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            $token = $user->getConfirmationToken();
            $email = $user->getEmail();
            $username = $user->getUsername();
            $mailerservice->send($mailer, $token, $email, $username, 'registration.html.twig');
            $this->addFlash('user-error', 'Votre inscription a été validée, vous aller recevoir un email de confirmation pour activer votre compte et pouvoir vous connecté');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);

    }

       /**
    * @Route("/account/confirm/{token}/{username}", name="confirm_account")
    * @param $token
    * @param $username
    * @return Response
    */
   public function confirmAccount($token, $username): Response
   {
       $em = $this->getDoctrine()->getManager();
       $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
       $tokenExist = $user->getConfirmationToken();
       if($token === $tokenExist) {
          $user->setConfirmationToken(null);
          $user->setEnabled(true);
          $em->persist($user);
          $em->flush();
          return $this->redirectToRoute('security_login');
       } else {
           return $this->render('registration/token-expire.html.twig');
       }
   }

   /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
    return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
           // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',  [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        
    }
}
