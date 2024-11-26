<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/equipes-suggerees', name: 'api_equipes_suggerees', methods: ['POST'])]
    public function equipesSuggerees(Request $request, EquipeRepository $equipeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['pouvoirs']) || !is_array($data['pouvoirs'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Données invalides. "pouvoirs" est requis.',
            ], 400);
        }

        // Exemple : Retourne simplement les pouvoirs reçus pour tester
        return new JsonResponse([
            'status' => 'success',
            'pouvoirs_reçus' => $data['pouvoirs'],
        ]);
    }
}
