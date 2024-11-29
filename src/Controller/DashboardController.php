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
/**
 * Action principale pour afficher le tableau de bord.
 *
 * @param MissionRepository $missionRepo Repository pour accéder aux missions.
 * @param SuperHeroRepository $heroRepo Repository pour accéder aux super-héros.
 * @param EquipeRepository $equipeRepo Repository pour accéder aux équipes.
 * @return Response La réponse avec les données du tableau de bord.
 */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(MissionRepository $missionRepo, SuperHeroRepository $heroRepo, EquipeRepository $equipeRepo): Response
    {   
         // Récupérer les missions en cours (statut : "COMMENCÉE") + Super-Héros in/disponibles + Equipes actives + calcul total des Super-Héros 
        $missionsEnCours = $missionRepo->findBy(['statut' => 'COMMENCÉE']);
        $herosDisponibles = $heroRepo->findBy(['estDisponible' => true]);
        $herosIndisponibles = $heroRepo->findBy(['estDisponible' => false]);
        $herosTotal = count($herosDisponibles) + count($herosIndisponibles);
        $equipesActives = $equipeRepo->findBy(['estActive' => true]);
        
          // Statistiques des missions par statut
    $missionsStats = [
        'FINIE' => count($missionRepo->findBy(['statut' => 'FINIE'])),
        'ÉCHOUÉE' => count($missionRepo->findBy(['statut' => 'ÉCHOUÉE'])),
        'EN ATTENTE' => count($missionRepo->findBy(['statut' => 'EN ATTENTE'])),
        'COMMENCÉE' => count($missionRepo->findBy(['statut' => 'COMMENCÉE'])),
    ];

        // Passer les données récupérées à la vue.
        return $this->render('/dashboard/index.html.twig', [
            'missionsEnCours' => $missionsEnCours,
            'herosDisponibles' => $herosDisponibles,
            'herosIndisponibles' => $herosIndisponibles,
            'herosTotal' => $herosTotal,
            'equipesActives' => $equipesActives,
            'missionLabels' => array_keys($missionsStats), // Labels des statuts
            'missionValues' => array_values($missionsStats), // Nombre de missions par statut
        ]);
    }
    
    
}
