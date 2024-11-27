<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionType;
use App\Repository\EquipeRepository;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
            // Validation : l'équipe assignée doit être active
            $equipeAssignee = $mission->getEquipeAssignee();
            if (!$equipeAssignee->isEstActive()) {
                $this->addFlash('error', "L'équipe assignée doit être active.");
                return $this->render('mission/new.html.twig', [
                    'form' => $form,
                    'mission' => $mission,
                ]);
            }
    
            // Validation : vérifier que les pouvoirs requis existent
            if ($mission->getPouvoirsRequis()->isEmpty()) {
                $this->addFlash('error', "Vous devez sélectionner au moins un pouvoir requis pour cette mission.");
                return $this->render('mission/new.html.twig', [
                    'form' => $form,
                    'mission' => $mission,
                ]);
            }
    
            // Validation : la date de début doit être antérieure à la date de fin
            if ($mission->getDateDebut() >= $mission->getDateFin()) {
                $this->addFlash('error', "La date de début doit être antérieure à la date de fin.");
                return $this->render('mission/new.html.twig', [
                    'form' => $form,
                    'mission' => $mission,
                ]);
            }
    
            // Persist the new mission
            try {
                $entityManager->persist($mission);
                $entityManager->flush();
                $this->addFlash('success', 'La mission a été créée avec succès.');
                return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de la mission : ' . $e->getMessage());
            }
        }
    
        return $this->render('mission/new.html.twig', [
            'mission' => $mission,
            'form' => $form,
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

    #[Route('/api/equipes-compatibles', name: 'api_equipes_compatibles', methods: ['POST'])]
public function equipesCompatibles(Request $request, EquipeRepository $equipeRepository): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $pouvoirsIds = $data['pouvoirs'] ?? [];

    // Récupérer les équipes ayant des membres ou un chef avec les pouvoirs requis
    $equipes = $equipeRepository->createQueryBuilder('e')
        ->leftJoin('e.membres', 'm')
        ->leftJoin('m.pouvoirs', 'p')
        ->leftJoin('e.chef', 'c')
        ->leftJoin('c.pouvoirs', 'cp')
        ->where('p.id IN (:pouvoirs) OR cp.id IN (:pouvoirs)')
        ->andWhere('e.estActive = true') // Équipes actives uniquement
        ->setParameter('pouvoirs', $pouvoirsIds)
        ->getQuery()
        ->getResult();

    // Préparer la réponse JSON
    $response = array_map(fn($equipe) => [
        'id' => $equipe->getId(),
        'nom' => $equipe->getNom(),
    ], $equipes);

    return new JsonResponse(['equipes' => $response]);
}

}
