<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/equipe')]
final class EquipeController extends AbstractController
{
    #[Route(name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($equipe);
                $entityManager->flush();
                $this->addFlash('success', 'L\'équipe a été créée avec succès.');
                return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de l\'équipe.');
            }
        }
    
        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }
    


    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Validation du nombre de membres
            if (count($equipe->getMembres()) < 2 || count($equipe->getMembres()) > 5) {
                $this->addFlash('error', 'Une équipe doit avoir entre 2 et 5 membres.');
                return $this->render('equipe/edit.html.twig', [
                    'equipe' => $equipe,
                    'form' => $form,
                ]);
            }
    
            // Validation du niveau d'énergie du chef
            if ($equipe->getChef() && $equipe->getChef()->getNiveauEnergie() <= 80) {
                $this->addFlash('error', 'Le chef doit avoir un niveau d\'énergie supérieur à 80.');
                return $this->render('equipe/edit.html.twig', [
                    'equipe' => $equipe,
                    'form' => $form,
                ]);
            }
    
            try {
                $entityManager->flush();
                $this->addFlash('success', 'L\'équipe a été modifiée avec succès.');
                return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de l\'équipe.');
            }
        }
    
        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($equipe);
                $entityManager->flush();
                $this->addFlash('success', 'L\'équipe a été supprimée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'équipe.');
            }
        }

        return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
    }
}