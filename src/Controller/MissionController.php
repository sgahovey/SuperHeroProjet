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

/**
 * Crée une nouvelle mission.
 */
    #[Route('/new', name: 'app_mission_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $mission = new Mission();

    $form = $this->createForm(MissionType::class, $mission);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $dateDebut = $mission->getDateDebut(); // Récupère la date de début
        $currentDate = new \DateTimeImmutable(); // Date actuelle

        // Vérifie ou ajuste la dateDebut
        if (!$dateDebut instanceof \DateTimeImmutable) {
            $dateDebut = $currentDate;
            $mission->setDateDebut($dateDebut);
        }

        // Ajustement de la date de début et du statut
        if ($dateDebut < $currentDate) {
            $mission->setDateDebut($currentDate);
            $mission->setStatut(MissionStatus::IN_PROGRESS); // La mission commence immédiatement
            $this->addFlash('info', 'La date de début a été ajustée et le statut est "Commencé".');
        } else {
            $mission->setStatut(MissionStatus::PENDING);
        }

         // Calcul de la date de fin (2 minutes après le début, exemple fictif)
        $dateFin = $mission->getDateDebut()->add(new \DateInterval('PT2M')); // +2 minutes
        $mission->setDateFin($dateFin);

       // Vérification de l'équipe assignée
        $equipeAssignee = $mission->getEquipeAssignee();
        if (!$equipeAssignee || !$equipeAssignee->isEstActive()) {
            $this->addFlash('error', "L'équipe assignée doit être active.");
            return $this->render('mission/new.html.twig', [
                'form' => $form->createView(),
                'mission' => $mission,
            ]);
        }

        // L'équipe devient inactive une fois assignée
        $equipeAssignee->setEstActive(false);

        try {
            $entityManager->persist($mission); // Persister la mission
            $entityManager->persist($equipeAssignee); // Persister l'état de l'équipe
            $entityManager->flush(); // Sauvegarder les modifications

            $this->addFlash('success', 'La mission a été créée avec succès.');
            return $this->redirectToRoute('app_mission_index');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    return $this->render('mission/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


/**
 * Affiche les détails d'une mission.
 */
    #[Route('/{id}', name: 'app_mission_show', methods: ['GET'])]
    public function show(Mission $mission): Response
    {
        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

    
/**
 * Modifie une mission existante.
 */    
    #[Route('/{id}/edit', name: 'app_mission_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(MissionType::class, $mission);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $dateDebut = $mission->getDateDebut();
        $currentDate = new \DateTimeImmutable();

        // Vérifie si la mission est en attente
        if ($mission->getStatut() === MissionStatus::PENDING) {
            if ($dateDebut < $currentDate) {
                $mission->setDateDebut($currentDate); // Ajuster à la date actuelle
                $mission->setStatut(MissionStatus::IN_PROGRESS); // Mettre le statut à "Commencé"
                $this->addFlash('info', 'La mission est passée au statut "Commencé" car la date de début a été dépassée.');
            } else {
                $this->addFlash('info', 'La mission reste en attente.');
            }
        } else {
            // Si le statut n'est pas en attente, appliquez la logique existante
            if ($dateDebut < $currentDate) {
                $mission->setDateDebut($currentDate);
                $mission->setStatut(MissionStatus::IN_PROGRESS);
                $this->addFlash('info', 'La date de début a été ajustée à la date actuelle et le statut est défini à "Commencé".');
            } else {
                $mission->setStatut(MissionStatus::PENDING);
            }
        }

        // // Met à jour la date de fin  + 2 min
        $dateFin = $mission->getDateDebut()->modify('+2 minutes');
        $mission->setDateFin($dateFin);

        try {
            $entityManager->flush();
            $this->addFlash('success', 'La mission a été mise à jour avec succès.');
            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    return $this->render('mission/edit.html.twig', [
        'mission' => $mission,
        'form' => $form->createView(),
    ]);
}


/**
 * Annule une mission.
 */
    #[Route('/{id}/cancel', name: 'app_mission_cancel', methods: ['POST'])]
    public function cancel(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        // Vérifie la validité du token CSRF
        if ($this->isCsrfTokenValid('cancel' . $mission->getId(), $request->request->get('_token'))) {
            // Met le statut de la mission à "ANNULÉE"
            $mission->setStatut(MissionStatus::CANCELLED);

            // Rendre l'équipe active
            $equipeAssignee = $mission->getEquipeAssignee();
            if ($equipeAssignee) {
                $equipeAssignee->setEstActive(true);
            }
            // Sauvegarder les modifications
            $entityManager->flush();
            $this->addFlash('success', 'La mission a été annulée et l\'équipe a été rendue active.');
        } else {
            $this->addFlash('error', 'Échec de l\'annulation de la mission. Token CSRF invalide.');
        }
        return $this->redirectToRoute('app_mission_index');
    }


/**
 * Supprime une mission.
 */
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


/**
 * API pour suggérer des équipes en fonction des pouvoirs requis.
 */
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
