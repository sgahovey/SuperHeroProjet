<?php

namespace App\Controller;

use App\Entity\Pouvoir;
use App\Form\PouvoirType;
use App\Repository\PouvoirRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Contrôleur pour gérer les pouvoirs (CRUD).
 */
    #[Route('/pouvoir')]
    final class PouvoirController extends AbstractController
    {
/**
 * Affiche la liste des pouvoirs.
 *
 * @param PouvoirRepository $pouvoirRepository Le dépôt des pouvoirs
 * @return Response La réponse contenant la vue des pouvoirs
*/
    #[Route(name: 'app_pouvoir_index', methods: ['GET'])]
    public function index(PouvoirRepository $pouvoirRepository): Response
    {
        return $this->render('pouvoir/index.html.twig', [
            'pouvoirs' => $pouvoirRepository->findAll(),
        ]);
    }


/**
 * Crée un nouveau pouvoir.
 *
 * @param Request $request La requête HTTP
 * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
 * @return Response La réponse contenant le formulaire ou la redirection
 */
    #[Route('/new', name: 'app_pouvoir_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $pouvoir = new Pouvoir();
    $form = $this->createForm(PouvoirType::class, $pouvoir);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Synchroniser les super-héros associés au pouvoir
        foreach ($pouvoir->getSuperHeroes() as $superHero) {
            if (!$superHero->getPouvoirs()->contains($pouvoir)) {
                $superHero->addPouvoir($pouvoir);
            }
            $entityManager->persist($superHero); // S'assurer que chaque super-héros est synchronisé
        }
        $entityManager->persist($pouvoir); // Persister le pouvoir
        $entityManager->flush(); // Appliquer les modifications

        $this->addFlash('success', 'Pouvoir créé avec succès !'); // Notification de succès
        return $this->redirectToRoute('app_pouvoir_index');
    }

    return $this->render('pouvoir/new.html.twig', [
        'pouvoir' => $pouvoir,
        'form' => $form,
    ]);
}


/**
 * Affiche les détails d'un pouvoir spécifique.
 *
 * @param Pouvoir $pouvoir Le pouvoir à afficher
 * @return Response La réponse contenant les détails du pouvoir
 */
    #[Route('/{id}', name: 'app_pouvoir_show', methods: ['GET'])]
    public function show(Pouvoir $pouvoir): Response
    {
        return $this->render('pouvoir/show.html.twig', [
            'pouvoir' => $pouvoir,
        ]);
    }


/**
 * Modifie un pouvoir existant.
 *
 * @param Request $request La requête HTTP
 * @param Pouvoir $pouvoir Le pouvoir à modifier
 * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
 * @return Response La réponse contenant le formulaire ou la redirection
 */
    #[Route('/{id}/edit', name: 'app_pouvoir_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Pouvoir $pouvoir, EntityManagerInterface $entityManager): Response
{
    // Stocker les anciens super-héros associés pour détecter les changements
    $originalSuperHeroes = new ArrayCollection($pouvoir->getSuperHeroes()->toArray());

    $form = $this->createForm(PouvoirType::class, $pouvoir);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            try {
                // Synchroniser les nouveaux super-héros associés
                foreach ($pouvoir->getSuperHeroes() as $superHero) {
                    if (!$originalSuperHeroes->contains($superHero)) {
                        $superHero->addPouvoir($pouvoir);
                        $entityManager->persist($superHero);
                    }
                }

                // Supprimer les anciens super-héros non sélectionnés
                foreach ($originalSuperHeroes as $originalSuperHero) {
                    if (!$pouvoir->getSuperHeroes()->contains($originalSuperHero)) {
                        $originalSuperHero->removePouvoir($pouvoir);
                        $entityManager->persist($originalSuperHero);
                    }
                }
                $entityManager->flush();  // Sauvegarder les modifications
                $this->addFlash('success', 'Le pouvoir a été mis à jour avec succès.');
                return $this->redirectToRoute('app_pouvoir_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
        }
    }

    return $this->render('pouvoir/edit.html.twig', [
        'pouvoir' => $pouvoir,
        'form' => $form,
    ]);
}


/**
 * Liste les héros ayant un pouvoir donné.
 *
 * @param Pouvoir $pouvoir Le pouvoir en question
 * @return Response La réponse contenant les héros
 */
    #[Route('/{id}/heros', name: 'app_pouvoir_heros', methods: ['GET'])]
    public function heros(Pouvoir $pouvoir): Response
    {
        return $this->render('pouvoir/heros.html.twig', [
            'pouvoir' => $pouvoir,
            'super_heroes' => $pouvoir->getSuperHeroes(),
        ]);
    }   


/**
 * Supprime un pouvoir.
 *
 * @param Request $request La requête HTTP
 * @param Pouvoir $pouvoir Le pouvoir à supprimer
 * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
 * @return Response La réponse contenant la redirection
 */
    #[Route('/{id}', name: 'app_pouvoir_delete', methods: ['POST'])]
    public function delete(Request $request, Pouvoir $pouvoir, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pouvoir->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pouvoir);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pouvoir_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
