<?php

namespace App\Controller;
use App\Form\EcoleType;
use App\Entity\Ecole;
use App\Repository\EcoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class EcoleController extends AbstractController
{
    #[Route('/acceuil', name: 'acceuil')]
    public function index(): Response
    {
        return $this->render('acceuil.html.twig', [
            'controller_name' => 'EcoleController',
        ]);
    }

 
    #[Route("/ecole/add", "methods={POST, GET}")]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        
        $ecole = new Ecole();

        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //----------------------------------//
            $userId = $this->getUser()->getId();
            $ecole->setUser($this->getUser());
            //---------------------------------//
            $d= $form->getData();

            $em->persist($d);
            $em->flush();

            return $this->redirectToRoute("app_ecole_liste");
       }

        return $this->render('ecole/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

     #[Route("/ecole/liste", "methods={POST, GET}")]
     public function liste(Request $request,EcoleRepository $ecoleRepo, EntityManagerInterface $em): Response
     {
         $ecole=$ecoleRepo->findAll();
        return $this->render('ecole/liste.html.twig', compact('ecole'));
     }

    #[Route("/ecole/edit/{id}", "methods={POST, GET}", name:"edit_ecole")]
    public function edit(Request $request,Ecole $eco, EntityManagerInterface $em): Response
    {
        $forme = $this->createForm(EcoleType::class, $eco);
         $forme->handleRequest($request);
        if ($forme->isSubmitted() && $forme->isValid())
        {
            $em->flush();
            return $this->redirectToRoute('app_ecole_liste');
        }

        return $this->render('ecole/edit.html.twig', [
            'eco' => $eco,
            'form' => $forme->createView(),
        ]);
    }

    #[Route("/ecole/liste/{id}", "methods=DELETE", name:"delete_ecole")]
    public function suppression(Request $request,Ecole $eco, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'. $eco->getId(), $request->get('_token'))){

            $em->remove($eco);
            $em->flush();
            return $this->redirectToRoute('app_ecole_liste');
        }


    }

}
