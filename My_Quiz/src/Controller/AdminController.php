<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/utilisateurs", name="utilisateurs")
     */
    public function usersList(UserRepository $user)
    {
        return $this->render("admin/users.html.twig", [
            'users' => $user->findAll()
        ]);
    }

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
     * @IsGranted("ROLE_ADMIN")
     * @Route("/utilisateurs/modifier/{id}", name="modifier_utilisateur")
     */
    public function editUser(Request $request, User $user, EntityManagerInterface $em)
    {

        $form = $this->createForm(EditUserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_utilisateurs');
        }

        return $this->render('admin/editUser.html.twig', ['formUser' => $form->createView()]);
    }

    /**
    *@IsGranted("ROLE_ADMIN")
    *@Route("/user/delete/{id}", name="delete_utilisateur")
    *Method({"DELETE"})
    */
    public function deleteUser($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('admin_utilisateurs');
    }
}
