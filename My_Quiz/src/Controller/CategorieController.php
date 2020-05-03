<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



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
    // /**
    // *@Route("/categorie/{id}", name="categorie_showCategorie")
    // */
        /**
     * @Route("/categorie", name="categorie")
     */
    public function showCategorie()
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        return $this->render('categorie/showCategorie.html.twig', array('categorie' => $categorie));
    }

    /**
    *@Route("/categorie/{id}", name="categorie_show")
    */
    public function show($id)
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);
        return $this->render('categorie/showdetails.html.twig', array('categorie' => $categorie));
    }

    /**
    *@IsGranted("ROLE_ADMIN")
    *@Route("/categorie/new/create", name="new_categorie")
    *Method({"GET", "POST"})
    */
    public function new(Request $request)
    {
        $categorie = new Categorie();
        $form = $this->createFormBuilder($categorie)
        ->add('name',TextType::class)
        ->add('save',SubmitType::class, array('label' => 'Créer'))
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $categorie = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('categorie');
        }
        return $this->render('categorie/new.html.twig',['form' => $form->createView()]);
    }


    /**
    *@IsGranted("ROLE_ADMIN")
    *@Route("/categorie/edit/{id}", name="edit_categorie")
    *Method({"GET","POST"})
    */
    public function edit(Request $request, $id)
    {
        $categorie = new Categorie();
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);
        $form = $this->createFormBuilder($categorie)
        ->add('categorie',TextType::class)
        ->add('save',SubmitType::class, array('label' => 'Modifier'))
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('categorie_list');
        }
        return $this->render('categorie/edit.html.twig',['form' => $form->createView()]);
    }

    /**
    *@IsGranted("ROLE_ADMIN")
    *@Route("/categorie/delete/{id}", name="delete_categorie")
    *Method({"DELETE"})
    */
    public function delete($id)
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('categorie');
    }
}
