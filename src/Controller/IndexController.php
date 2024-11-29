<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    //  Cette méthode renvoie la page d'accueil de l'application.
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        // Rend la vue Twig située dans "templates/index/index.html.twig".
        return $this->render('index/index.html.twig', [
            // Passe des variables à la vue Twig :
            'title' => 'Bienvenue sur l\'application de gestion des Super Héros',
            'controller_name' => 'IndexController',
        ]);
    }
}
