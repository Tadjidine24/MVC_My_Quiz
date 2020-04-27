<?php

namespace App\Controller;

use App\Entity\Question;
// use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

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

/**
*@Route("/question/save")
*/
    public function save()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $question = new Question();
        $question->setQuestion('Question');
        $entityManager->persist($question);
        $entityManager->flush();
        return new Response('question enregisté avec id'.$question->getId());
    }

    /**
    *@Route("/",name="question_list")
    */
    public function home()
    {
        //récupérer tous les question de la table question de la BD
        //et les mettre dans le tableau $question
        $question = $this->getDoctrine()->getRepository(Question::class)->findAll();
        return $this->render('quiz/MesQuestions.html.twig',['question'=>$question]);
    }
/**
*@Route("/quiz/new", name="new_question")
*Method({"GET", "POST"})
*/
    public function new(Request $request)
    {
        $question = new Question();
        $form = $this->createFormBuilder($question)
        ->add('question',TypeTextType::class)
        ->add('save',SubmitType::class, array('label' => 'Créer'))
        ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $question = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();
            return $this->redirectToRoute('question_list');
        }
        return $this->render('quiz/new.html.twig',['form' => $form->createView()]);
    }
}

