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

/**
* Contrôleur pour gérer les super-héros (CRUD).
 */
#[Route('/superhero')]
final class SuperHeroController extends AbstractController
{
    #[Route(name: 'app_super_hero_index', methods: ['GET'])]
    public function index(SuperHeroRepository $superHeroRepository): Response
    {
        return $this->render('super_hero/index.html.twig', [
            'super_heroes' => $superHeroRepository->findAll(), // Récupère tous les super-héros
        ]);
    }


/**
 * Crée un nouveau super-héros.
 * 
 * @param Request $request La requête HTTP
 * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
 * @return Response La réponse HTTP contenant le formulaire ou la redirection
 */
    #[Route('/new', name: 'app_super_hero_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une instance de SuperHero et définir sa disponibilité par défaut
        $superHero = new SuperHero();
        $superHero->setEstDisponible(true);  // Le super-héros est disponible par défaut
        // Créer le formulaire
        $form = $this->createForm(SuperHeroType::class, $superHero);
        $form->handleRequest($request);
        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($superHero);
            $entityManager->flush();
            $this->addFlash('success', 'Le Super Héros a été créé avec succès.'); // Notification de succès
            return $this->redirectToRoute('app_super_hero_index', [], Response::HTTP_SEE_OTHER); // Redirection
        }
        // Affiche le formulaire
        return $this->render('super_hero/new.html.twig', [
            'super_hero' => $superHero,
            'form' => $form,
        ]);
    }


/**
 * Affiche les détails d'un super-héros spécifique.
 * 
 * @param SuperHero $superHero Le super-héros à afficher
 * @return Response La réponse HTTP contenant les détails du super-héros
 */
    #[Route('/{id}', name: 'app_super_hero_show', methods: ['GET'])]
    public function show(SuperHero $superHero): Response
    {
        return $this->render('super_hero/show.html.twig', [
            'super_hero' => $superHero,
        ]);
    }


/**
 * Modifie un super-héros existant.
 * 
 * @param Request $request La requête HTTP
 * @param SuperHero $superHero Le super-héros à modifier
 * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
 * @return Response La réponse HTTP contenant le formulaire ou la redirection
 */
    #[Route('/{id}/edit', name: 'app_super_hero_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SuperHero $superHero, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuperHeroType::class, $superHero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le Super Héros a été mis à jour avec succès.');
            return $this->redirectToRoute('app_super_hero_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('super_hero/edit.html.twig', [
            'super_hero' => $superHero,
            'form' => $form,
        ]);
    }


/**
 * Supprime un super-héros.
 * 
 * @param Request $request La requête HTTP
 * @param SuperHero $superHero Le super-héros à supprimer
 * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
 * @return Response La réponse HTTP contenant la redirection
 */
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
