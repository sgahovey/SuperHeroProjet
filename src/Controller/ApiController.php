<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
/**
 * Endpoint pour obtenir des équipes suggérées en fonction des pouvoirs reçus.
 *
 * @Route('/api/equipes-suggerees', name="api_equipes_suggerees", methods={"POST"})
 * 
 * @param Request $request La requête HTTP contenant les données (JSON).
 * @param EquipeRepository $equipeRepository Repository pour accéder aux entités Équipe.
 * @return JsonResponse La réponse JSON avec les équipes suggérées ou un message d'erreur.
 */
    #[Route('/api/equipes-suggerees', name: 'api_equipes_suggerees', methods: ['POST'])]
    public function equipesSuggerees(Request $request, EquipeRepository $equipeRepository): JsonResponse
    {
        // Décodage des données JSON envoyées dans la requête
        $data = json_decode($request->getContent(), true);
        // Vérification des données reçues
        if (!isset($data['pouvoirs']) || !is_array($data['pouvoirs'])) {
            // Retourne une réponse d'erreur si le champ "pouvoirs" est manquant ou n'est pas un tableau
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Données invalides. "pouvoirs" est requis.',
            ], 400); // Code HTTP 400 : Requête invalide
        }

        // Exemple : Retourne simplement les pouvoirs reçus pour tester
        return new JsonResponse([
            'status' => 'success',
            'pouvoirs_reçus' => $data['pouvoirs'],
        ]);
    }
}
