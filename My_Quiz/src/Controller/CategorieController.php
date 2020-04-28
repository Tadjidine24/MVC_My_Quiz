<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    // /**
    //  * @Route("/categorie", name="categorie")
    //  */
    // public function index()
    // {
    //     return $this->render('categorie/index.html.twig', [
    //         'controller_name' => 'CategorieController',
    //     ]);
    // }
        /**
    *@Route("/categorie/{id}", name="categorie_showCategorie")
    */
    public function showCategorie($id)
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);
        return $this->render('quiz/showCategorie.html.twig', array('categorie' => $categorie));
    }
}
