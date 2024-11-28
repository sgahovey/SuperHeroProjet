<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use App\Repository\MissionRepository;
use App\Repository\SuperHeroRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(MissionRepository $missionRepo, SuperHeroRepository $heroRepo, EquipeRepository $equipeRepo): Response
    {
        $missionsEnCours = $missionRepo->findBy(['statut' => 'COMMENCÉE']);
        $herosDisponibles = $heroRepo->findBy(['estDisponible' => true]);
        $herosIndisponibles = $heroRepo->findBy(['estDisponible' => false]);
        $herosTotal = count($herosDisponibles) + count($herosIndisponibles);
    
        $equipesActives = $equipeRepo->findBy(['estActive' => true]);
    
        return $this->render('/dashboard/index.html.twig', [
            'missionsEnCours' => $missionsEnCours,
            'herosDisponibles' => $herosDisponibles,
            'herosIndisponibles' => $herosIndisponibles,
            'herosTotal' => $herosTotal,
            'equipesActives' => $equipesActives,
        ]);
    }
    
    
}
