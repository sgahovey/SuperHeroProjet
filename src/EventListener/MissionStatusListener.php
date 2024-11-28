<?php

namespace App\EventListener;

use App\Repository\MissionRepository;
use App\Entity\MissionStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class MissionStatusListener
{
    private MissionRepository $missionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(MissionRepository $missionRepository, EntityManagerInterface $entityManager)
    {
        $this->missionRepository = $missionRepository;
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        // Vérifie que ce n'est pas une sous-requête ou une requête CLI
        if (!$event->isMainRequest()) {
            return;
        }

        // Récupérer les missions à mettre à jour
        $missions = $this->missionRepository->findInProgressMissionsToUpdate();

        foreach ($missions as $mission) {
            $possibleStatuses = [MissionStatus::CANCELLED, MissionStatus::COMPLETED, MissionStatus::FAILED];
            $randomStatus = $possibleStatuses[array_rand($possibleStatuses)];
            $mission->setStatut($randomStatus);
        }

        $this->entityManager->flush(); // Sauvegarde en base
    }
}
