<?php

namespace App\Controller;

use App\Entity\SuperHero;
use App\Form\SuperHeroType;
use App\Repository\SuperHeroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/superhero')]
final class SuperHeroController extends AbstractController
{
    #[Route(name: 'app_super_hero_index', methods: ['GET'])]
    public function index(SuperHeroRepository $superHeroRepository): Response
    {
        return $this->render('super_hero/index.html.twig', [
            'super_heroes' => $superHeroRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_super_hero_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer un  SuperHero
        $superHero = new SuperHero();
        $superHero->setEstDisponible(true); // Super Hero mis automatiquement disponible
        $form = $this->createForm(SuperHeroType::class, $superHero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($superHero);
            $entityManager->flush();

            $this->addFlash('success', 'Le Super Héros a été créé avec succès.'); // Succes
            return $this->redirectToRoute('app_super_hero_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('super_hero/new.html.twig', [
            'super_hero' => $superHero,
            'form' => $form,
        ]);
    }

        // Rechercher un  SuperHero par rapport à son id
    #[Route('/{id}', name: 'app_super_hero_show', methods: ['GET'])]
    public function show(SuperHero $superHero): Response
    {
        return $this->render('super_hero/show.html.twig', [
            'super_hero' => $superHero,
        ]);
    }

        // Modifier un  SuperHero par rapport à son id
    #[Route('/{id}/edit', name: 'app_super_hero_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SuperHero $superHero, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuperHeroType::class, $superHero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le Super Héros a été mis à jour avec succès.'); //Succes
            return $this->redirectToRoute('app_super_hero_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('super_hero/edit.html.twig', [
            'super_hero' => $superHero,
            'form' => $form,
        ]);
    }

        // Supprimer un SuperHero

    #[Route('/{id}', name: 'app_super_hero_delete', methods: ['POST'])]
    public function delete(Request $request, SuperHero $superHero, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$superHero->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($superHero);
            $entityManager->flush();
            $this->addFlash('success', 'Le Super Héros a été supprimé avec succès.'); // Succes

        }

        return $this->redirectToRoute('app_super_hero_index', [], Response::HTTP_SEE_OTHER); // Echec
    }
}
