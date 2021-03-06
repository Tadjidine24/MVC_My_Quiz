<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    // /**
    //  * @Route("/inscription", name="security_registration")
    // * @param AuthenticationUtils $authenticationUtils
    // * @param Request $request
    // * @param UserPasswordEncoderInterface $encoder
    // * @param MailerService
    // * @param \Swift_Mailer
    // * @return Response
    // * @throws \Exception
    // */
    // public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    // {
    //     $user = new User();

    //     $form = $this->createForm(RegistrationType::class,$user);

    //     $form->handleRequest($request);

    //     $mailer = \Swift_Mailer::class;
    //     $mailerservice = MailerService::class;

    //     if ($form->isSubmitted() && $form->isValid())
    //     {
    //         $hash = $encoder->encodePassword($user, $user->getPassword());
    //         $user->setPassword($hash);

    //         $user->setConfirmationToken($this->generateToken());
    //         $manager = $this->getDoctrine()->getManager();
    //         $manager->persist($user);
    //         $manager->flush();
    //         $token = $user->getConfirmationToken();
    //         $email = $user->getEmail();
    //         $username = $user->getUsername();
    //         $mailerservice->send($mailer, $token, $email, $username, 'registration.html.twig');
    //         $this->addFlash('user-error', 'Votre inscription a été validée, vous aller recevoir un email de confirmation pour activer votre compte et pouvoir vous connecté');

    //         return $this->redirectToRoute('security_login');
    //     }

    //     return $this->render('security/registration.html.twig', [
    //         'form' => $form->createView()
    //     ]);

    // }

     /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder,  \Swift_Mailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $token = bin2hex(random_bytes(10));
            $user->setToken($token);
            $hashToken = $encoder->encodePassword($user, $user->getToken());
            $user->setToken($hashToken);

            $em->persist($user);
            $em->flush();

            $emailUser = $user->getEmail();


            $url = $this->generateUrl('confirm_touken', ['token' => $token, 'id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Email de Confirmation'))
                ->setSubject('Confirmation d\'adresse email')
                ->setFrom('tadjidinetamou@gmail.com')
                ->setTo($emailUser)
                ->setBody(
                    $this->renderView('sendemail/hello.html.twig', [
                        'user' => $user,
                        'url' => $url,
                        'token' => $token
                    ]),
                    'text/html'
                );
            $mailer->send($message);
            $this->addFlash('message', 'Un mail de confirmation vous a été envoyé');

            return $this->redirectToRoute('confirm_mail');
        }
        return $this->render('security/registration.html.twig', ['form' => $form->createView()]);
    }

      /**
     * @Route("/confirm_token/{token}", name="confirm_touken")
     */
    public function confirm_token(string $token, AuthenticationUtils $authenticationUtils)
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render(
            'sendemail/confirmationToken.html.twig',
            [
                'token' => $token,
                'lastusername' => $lastUsername
            ]
        );
    }

     /**
     * @Route("/confirm_mail", name="confirm_mail")
     */

    public function confirm_mail()
    {

        $user =  $this->getDoctrine()
            ->getRepository(User::class);

        return $this->render('sendemail/confemail.html.twig', ['user' => $user]);
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
