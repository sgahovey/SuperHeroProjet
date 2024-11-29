<?php

namespace App\EventListener;

use App\Repository\MissionRepository;
use App\Entity\MissionStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class MissionStatusListener
{
    private MissionRepository $missionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(MissionRepository $missionRepository, EntityManagerInterface $entityManager)
    {
        $this->missionRepository = $missionRepository;
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Vérifie que ce n'est pas une requête sous forme de sous-requête ou de console
        if (!$event->isMainRequest()) {
            return;
        }

        $currentDate = new \DateTimeImmutable();

        // Missions en attente (PENDING) à commencer
        $pendingMissions = $this->missionRepository->findPendingMissionsToStart($currentDate);
        foreach ($pendingMissions as $mission) {
            $mission->setStatut(MissionStatus::IN_PROGRESS);
        }
    
        // Récupère les missions "Commencées" (IN_PROGRESS) dont la date de fin est dépassée
        $inProgressMissions = $this->missionRepository->findInProgressMissionsToUpdate($currentDate);
        foreach ($inProgressMissions as $mission) {
            // Définir un statut aléatoire
            $possibleStatuses = [MissionStatus::COMPLETED, MissionStatus::FAILED];
            $randomStatus = $possibleStatuses[array_rand($possibleStatuses)];
            $mission->setStatut($randomStatus); // Met à jour le statut de la mission

            // Réactive l'équipe associée à la mission
            $equipe = $mission->getEquipeAssignee();
            if ($equipe) {
                $equipe->setEstActive(true);
            }
        }
        // Sauvegarder les changements en base de données
        $this->entityManager->flush();
    }
}
