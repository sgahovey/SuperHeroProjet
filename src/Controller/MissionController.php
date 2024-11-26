<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionType;
use App\Entity\MissionStatus;
use App\Repository\EquipeRepository;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\DataTransformer\EquipeToIdTransformer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/mission')]
final class MissionController extends AbstractController
{
    #[Route(name: 'app_mission_index', methods: ['GET'])]
    public function index(MissionRepository $missionRepository): Response
    {
        return $this->render('mission/index.html.twig', [
            'missions' => $missionRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_mission_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $mission = new Mission();
    $mission->setDateDebut(new \DateTime()); // Définit la date actuelle par défaut

    // Création du formulaire
    $form = $this->createForm(MissionType::class, $mission);

    // Gestion de la requête
    $form->handleRequest($request);

    // Si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        $equipeAssignee = $mission->getEquipeAssignee();
        $currentDate = new \DateTime(); // Date actuelle

        // Validation métier : Ajuster la date de début si nécessaire
        if ($mission->getDateDebut() < $currentDate) {
            $mission->setDateDebut($currentDate); // Ajuste la date de début
            $this->addFlash('info', 'La date de début a été ajustée à la date actuelle.');
        }

        // Déterminer le statut de la mission
        if ($mission->getDateDebut() <= $currentDate) {
            $mission->setStatut(MissionStatus::IN_PROGRESS); // Mission commencée
        } else {
            $mission->setStatut(MissionStatus::PENDING); // Mission en attente
        }

        // Validation métier : L'équipe assignée doit être active
        if (!$equipeAssignee || !$equipeAssignee->isEstActive()) {
            $this->addFlash('error', "L'équipe assignée doit être active.");
            return $this->render('mission/new.html.twig', [
                'form' => $form->createView(),
                'mission' => $mission,
            ]);
        }

        // Validation métier : Vérifier que des pouvoirs sont requis
        if ($mission->getPouvoirsRequis()->isEmpty()) {
            $this->addFlash('error', "Vous devez sélectionner au moins un pouvoir requis pour cette mission.");
            return $this->render('mission/new.html.twig', [
                'form' => $form->createView(),
                'mission' => $mission,
            ]);
        }

        // Validation métier : Vérifier la cohérence des dates
        if ($mission->getDateDebut() >= $mission->getDateFin()) {
            $this->addFlash('error', "La date de début doit être antérieure à la date de fin.");
            return $this->render('mission/new.html.twig', [
                'form' => $form->createView(),
                'mission' => $mission,
            ]);
        }

        // Mise à jour de l'équipe assignée
        $equipeAssignee->setEstActive(false);
        $equipeAssignee->setMissionActuelle($mission);

        try {
            // Sauvegarde des données
            $entityManager->persist($equipeAssignee);
            $entityManager->persist($mission);
            $entityManager->flush();

            $this->addFlash('success', 'La mission a été créée avec succès et l\'équipe assignée a été mise à jour.');
            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la création de la mission : ' . $e->getMessage());
        }
    }

    // Affichage du formulaire
    return $this->render('mission/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_mission_show', methods: ['GET'])]
    public function show(Mission $mission): Response
    {
        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mission/edit.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mission_delete', methods: ['POST'])]
    public function delete(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        // Vérifier la validité du token CSRF
        if ($this->isCsrfTokenValid('delete'.$mission->getId(), $request->request->get('_token'))) {
            $entityManager->remove($mission);
            $entityManager->flush();
            $this->addFlash('success', 'Mission supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Échec de la suppression de la mission. Token CSRF invalide.');
        }
    
        return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/equipes-suggerees', name: 'api_equipes_suggerees', methods: ['POST'])]
    public function equipesSuggerees(Request $request, EquipeRepository $equipeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pouvoirsIds = $data['pouvoirs'] ?? [];
    
        // Récupérer les équipes dont les membres ou le chef possèdent les pouvoirs sélectionnés
        $equipesCompatibles = $equipeRepository->createQueryBuilder('e')
            ->leftJoin('e.membres', 'm')
            ->leftJoin('m.pouvoirs', 'p')
            ->leftJoin('e.chef', 'c')
            ->leftJoin('c.pouvoirs', 'cp')
            ->where('p.id IN (:pouvoirs) OR cp.id IN (:pouvoirs)')
            ->andWhere('e.estActive = true') // Filtrer uniquement les équipes actives
            ->setParameter('pouvoirs', $pouvoirsIds)
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    
        // Récupérer toutes les autres équipes actives
        $autresEquipes = $equipeRepository->createQueryBuilder('e')
            ->where('e.estActive = true')
            ->andWhere('e NOT IN (:compatibles)')
            ->setParameter('compatibles', $equipesCompatibles)
            ->getQuery()
            ->getResult();
    
        // Préparer la réponse JSON
        $response = [
            'suggested' => array_map(fn($equipe) => [
                'id' => $equipe->getId(),
                'nom' => $equipe->getNom(),
                'chef' => $equipe->getChef() ? $equipe->getChef()->getNom() : null,
                'membres' => array_map(fn($membre) => $membre->getNom(), $equipe->getMembres()->toArray()),
            ], $equipesCompatibles),
            'others' => array_map(fn($equipe) => [
                'id' => $equipe->getId(),
                'nom' => $equipe->getNom(),
                'chef' => $equipe->getChef() ? $equipe->getChef()->getNom() : null,
                'membres' => array_map(fn($membre) => $membre->getNom(), $equipe->getMembres()->toArray()),
            ], $autresEquipes),
        ];
    
        return new JsonResponse($response);
    }
    

}
