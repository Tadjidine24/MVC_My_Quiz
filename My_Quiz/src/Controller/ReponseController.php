<?php

namespace App\Controller;

use App\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ReponseRepository as rep;
use Symfony\Component\Routing\Annotation\Route;

class ReponseController extends AbstractController
{
    /**
     * @Route("/reponse", name="reponse")
     */
    public function showReponse()
    {
        $reponse = $this->getDoctrine()->getRepository(Reponse::class)->findAll();
        return $this->render('reponse/showReponse.html.twig', array('reponse' => $reponse));
    }

        /**
    *@Route("/response", name="response")
    */
    public function quiz(rep $reponses)
    {
        $reponses = $this->getDoctrine()
        ->getManager()
        ->getRepository('App:Reponse')
        ->findByReponse(1, 1);

        return $this->render('reponse/index.html.twig', ['reponses' => $reponses]);
    }
}
