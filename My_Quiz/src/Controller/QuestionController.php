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
use Symfony\Component\Form\Extension\Core\Type\TextType;

class QuestionController extends AbstractController
{
    // /**
    //  * @Route("/quiz", name="quiz")
    //  */
    // public function index(EntityManagerInterface $em): Response
    // {
    //     $repo = $em->getRepository(Quiz::class);
    //     $quizs = $repo->findAll();
    //     return $this->render('quiz/index.html.twig', [
    //         'quiz' => $quizs,
    //     ]);
    // }
    // public function listCategorie()
    // {
    //     $posts = $this->getDoctrine()->getRepository(Question::class)->findAll();

    //     return $this->render('quiz/index.html.twig', [
    //         'posts' => $posts
    //     ]);
    // }

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
    *@Route("/question",name="question_list")
    */
    public function home()
    {
        //récupérer tous les question de la table question de la BD
        //et les mettre dans le tableau $question
        $question = $this->getDoctrine()->getRepository(Question::class)->findAll();
        return $this->render('quiz/MesQuestions.html.twig',['question'=>$question]);
    }

    /**
    *@Route("/question/{id}", name="question_show")
    */
    public function show($id)
    {
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        return $this->render('quiz/show.html.twig', array('question' => $question));
    }

    /**
    *@Route("/question/new/create", name="new_question")
    *Method({"GET", "POST"})
    */
    public function new(Request $request)
    {
        $question = new Question();
        $form = $this->createFormBuilder($question)
        ->add('question',TextType::class)
        ->add('save',SubmitType::class, array('label' => 'Créer'))
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $question = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();
            return $this->redirectToRoute('question_list');
        }
        return $this->render('quiz/new.html.twig',['form' => $form->createView()]);
    }

    /**
    *@Route("/question/edit/{id}", name="edit_question")
    *Method({"GET","POST"})
    */
    public function edit(Request $request, $id)
    {
        $question = new Question();
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        $form = $this->createFormBuilder($question)
        ->add('question',TextType::class)
        ->add('save',SubmitType::class, array('label' => 'Modifier'))
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('question_list');
        }
        return $this->render('quiz/edit.html.twig',['form' => $form->createView()]);
    }

    /**
    *@Route("/question/delete/{id}", name="delete_question")
    *Method({"DELETE"})
    */
    public function delete(Request $request, $id)
    {
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($question);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('question_list');
    }
    

}

