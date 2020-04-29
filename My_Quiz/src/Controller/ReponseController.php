<?php

namespace App\Controller;

use App\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
