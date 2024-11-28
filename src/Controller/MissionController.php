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
    
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie et initialise la dateDebut si nécessaire
            $dateDebut = $mission->getDateDebut();
            if (!$dateDebut instanceof \DateTimeImmutable) {
                $dateDebut = new \DateTimeImmutable(); // Initialise avec la date actuelle
                $mission->setDateDebut($dateDebut);
            }
    
            // Vérifie si dateDebut est dans le passé
            $currentDate = new \DateTimeImmutable(); // Date actuelle
            if ($dateDebut < $currentDate) {
                $mission->setDateDebut($currentDate); // Ajuste la dateDebut
                $mission->setStatut(MissionStatus::IN_PROGRESS); // Statut: Commencé
                $this->addFlash('info', 'La date de début a été ajustée à la date actuelle et le statut est défini à "Commencé".');
            } else {
                $mission->setStatut(MissionStatus::PENDING); // Statut: En attente
            }
    
            // Génère une dateFin basée sur dateDebut
            $interval = new \DateInterval('PT2M'); // Intervalle de 2 minutes
            $dateFin = $dateDebut->add($interval); // Utilise `add` pour calculer dateFin
            $mission->setDateFin($dateFin);
    
            // Validation métier : L'équipe assignée doit être active
            $equipeAssignee = $mission->getEquipeAssignee();
            if (!$equipeAssignee || !$equipeAssignee->isEstActive()) {
                $this->addFlash('error', "L'équipe assignée doit être active.");
                return $this->render('mission/new.html.twig', [
                    'form' => $form->createView(),
                    'mission' => $mission,
                ]);
            }
    
            // Rendre l'équipe inactive
            $equipeAssignee->setEstActive(false);
    
            try {
                // Sauvegarde initiale de la mission
                $entityManager->persist($equipeAssignee);
                $entityManager->persist($mission);
                $entityManager->flush();
    
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
