<?php

namespace App\Controller;

use App\Entity\Question;
// use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/quiz", name="quiz")
     */
    // public function index(EntityManagerInterface $em): Response
    // {
    //     $repo = $em->getRepository(Quiz::class);
    //     $quizs = $repo->findAll();
    //     return $this->render('quiz/index.html.twig', [
    //         'quiz' => $quizs,
    //     ]);
    // }
    public function listCategorie()
    {
        $posts = $this->getDoctrine()->getRepository(Question::class)->findAll();

        return $this->render('quiz/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
